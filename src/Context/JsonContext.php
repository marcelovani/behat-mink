<?php

namespace DennisDigital\Behat\Mink\Context;

use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Gherkin\Node\PyStringNode;
use Adbar\Dot;

/**
 * Class JsonContext
 * @package DennisDigital\Behat\Mink\Context
 */
class JsonContext extends RawMinkContext {

  /**
   * @Then /^the response should match json schema:/
   */
  public function theResponseMatchesJsonSchema($value) {
    // Convert to PyStringNode for multiple lines.
    $value = new PyStringNode($value);

    // Get schema JSON.
    $schema = json_decode($value->getRaw());
    if (is_null($schema)) {
      throw new \Exception("Provided schema is not valid JSON.");
    }

    // Validate JSON response against schema.
    $this->validateJsonResponse($schema);
  }

  /**
   * Get the decoded JSON response.
   */
  protected function getJsonResponse($assoc = TRUE) {
    // Get JSON response.
    $rawResponse = $this->getSession()->getPage()->getContent();
    $json = json_decode($rawResponse, $assoc);
    if (is_null($json)) {
      throw new \Exception("Response is not valid JSON.");
    }
    return $json;
  }

  /**
   * @Then /^the response should match json schema "([^"]*)"/
   */
  public function theResponseMatchesJsonSchemaUrl($url) {
    // Get schema JSON.
    $this->getSession()->visit($url);
    $schema = $this->getJsonResponse(FALSE);

    // Return to the previous page so that testing can resume.
    $this->getSession()->back();

    // Validate JSON response against schema.
    $this->validateJsonResponse($schema);
  }

  /**
   * Validate JSON response against provided schema.
   *
   * @param $schema
   * @throws \Exception
   */
  public function validateJsonResponse($schema) {
    // Get JSON response.
    $json = $this->getJsonResponse(FALSE);

    // Validate response.
    $validate = \Jsv4::validate($json, $schema);
    // Output validation errors.
    if (!empty($validate->errors)) {
      foreach ($validate->errors as $error) {
        $message = array(
          $error->getMessage() . ' at ' . $error->dataPath,
          $this->getSession()->getCurrentUrl(),
          $this->getSession()->getPage()->getContent()
        );
        throw new \Exception(implode("\n\n", $message), $error->code);
      }
    }
  }

  /**
   * Get the JSON value using provide key.
   *
   * @param $json_key
   * @param $json_array
   * @return mixed
   * @throws \Exception
   */
  public function getJsonValue($json_key, $json_array) {
    // Convert prop[0] to prop.0
    $json_key = str_replace(['[', ']'], ['.', ''], $json_key);

    $json = new Dot($json_array);
    if (!$json->has($json_key)) {
      throw new \Exception($json_key . " could not be found in response JSON.");
    }

    return $json->get($json_key);
  }

  /**
   * @Given /^the json value "([^"]*)" matches "(?P<regex>(?:[^"]|\\")*)"$/
   */
  public function theJsonValueMatches($jsonKey, $regex) {
    $value = $this->getJsonValue($jsonKey, $this->getJsonResponse());
    if (preg_match($regex, $value, $matches)) {
      return TRUE;
    }
    throw new \Exception(sprintf('No matches for "%s" in value "%s"', $regex, $value));
  }

  /**
   * Get xpath matches from JSON response value.
   *
   * @param $jsonKey
   * @param $query
   * @throws \Exception
   */
  protected function theJsonXhtmlGetXpathMatches($jsonKey, $query) {
    // Get JSON response.
    $json = $this->getJsonResponse();

    // Get the json value.
    $jsonValue = $this->getJsonValue($jsonKey, $json);

    // Load the JSON value as XML.
    try {
      $xml = new \DOMDocument();
      // Turn internal errors off as we are dealing with HTML5 and causes warnings.
      libxml_use_internal_errors(TRUE);
      if (!$xml->loadHTML($jsonValue, LIBXML_NOERROR)) {
        $error = libxml_get_last_error();
        throw new \Exception($error->message);
      }
    } catch (\Exception $e) {
      throw $e;
    }

    // Execute the xpath query.
    $xpath = new \DOMXpath($xml);
    return $xpath->query($query);
  }

  /**
   * @Given /^the json xhtml value "([^"]*)" matches xpath "([^"]*)"$/
   */
  public function theJsonXhtmlValueMatchesXpath($jsonKey, $query) {
    $elements = $this->theJsonXhtmlGetXpathMatches($jsonKey, $query);
    if (!$elements || $elements->length == 0) {
      throw new \Exception(sprintf('No xpath matches for: "%s"', $query));
    }
  }

  /**
   * @Given /^the json xhtml value "([^"]*)" does not match xpath "([^"]*)"$/
   */
  public function theJsonXhtmlValueDoesNotMatchXpath($jsonKey, $query) {
    $elements = $this->theJsonXhtmlGetXpathMatches($jsonKey, $query);
    if ($elements && $elements->length > 0) {
      throw new \Exception(sprintf('xpath matches for: "%s"', $query));
    }
  }
}
