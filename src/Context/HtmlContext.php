<?php

namespace DennisDigital\Behat\Mink\Context;

use Behat\MinkExtension\Context\RawMinkContext;
use Adbar\Dot as Dot;

/**
 * Class HtmlContext
 * @package DennisDigital\Behat\Mink\Context
 */
class HtmlContext extends RawMinkContext {
  /**
   * If an xhtml document has been passed, it gets stored here.
   * @var DOMDocument
   */
  protected $xhtml_doc;

  /**
   * Store values for use within a scenario.
   */
  protected $store = array();

  /**
   * @Then /^I should see a "([^"]*)" element with the "([^"]*)" attribute set with a value of "([^"]*)"$/
   */
  public function iShouldSeeAElementWithTheAttributeSetWithAValueOf($element_type, $attribute_name, $attribute_value) {
    $page = $this->getSession()->getPage();
    $results = $page->findAll('css', $element_type);
    $found_attribute = 0;
    foreach ($results as $result) {
      $attribute = $result->getAttribute($attribute_name);
      if (!empty($attribute)) {
        $found_attribute++;
        if ($attribute == $attribute_value) {
          return TRUE;
        }
      }
    }
    if ($found_attribute) {
      throw new \Exception(sprintf('"%d" "%s" elements were found with attribute "%s". However none had the value of "%s"', $found_attribute, $element_type, $attribute_name, $attribute_value));
    }
    throw new \Exception(sprintf('Element "%s" with attribute "%s" containing "%s" was not found', $element_type, $attribute_name, $attribute_value));
  }

  /**
   * @Then /^I should not see a "([^"]*)" element with the "([^"]*)" attribute set with a value of "([^"]*)"$/
   */
  public function iShouldNotSeeAElementWithTheAttributeSetWithAValueOf($element_type, $attribute_name, $attribute_value) {
    $page = $this->getSession()->getPage();
    $results = $page->findAll('css', $element_type);
    $found_attribute = 0;
    foreach ($results as $result) {
      $attribute = $result->getAttribute($attribute_name);
      if (!empty($attribute)) {
        $found_attribute++;
        if ($attribute == $attribute_value) {
          throw new \Exception(sprintf('Element "%s" with attribute "%s" containing "%s" was found', $element_type, $attribute_name, $attribute_value));
        }
      }
    }
    return;
  }

  /**
   * @Then /^I should see a "([^"]*)" element with the "([^"]*)" attribute which matches "(?P<regex>(?:[^"]|\\")*)"$/
   */
  public function iShouldSeeAElementWithTheAttributeWhichMatches($element_type, $attribute_name, $regex) {
    $page = $this->getSession()->getPage();
    $results = $page->findAll('css', $element_type);
    $found_attribute = 0;
    foreach ($results as $result) {
      $attribute = $result->getAttribute($attribute_name);
      if (!empty($attribute)) {
        $found_attribute++;
        if (preg_match($regex, $attribute)) {
          return TRUE;
        }
      }
    }
    if ($found_attribute) {
      throw new \Exception(sprintf('"%d" "%s" elements were found with attribute "%s". However none had the value of "%s"', $found_attribute, $element_type, $attribute_name, $regex));
    }
    throw new \Exception(sprintf('Element "%s" with attribute "%s" containing "%s" was not found', $element_type, $attribute_name, $regex));
  }

  /**
   * @Then /^the meta "(?P<attribute_key>[^"]*)" "(?P<attribute_value>[^"]*)" should contain "(?P<value>(?:[^"]|\\")*)"$/
   *
   * eg: the meta "name" "description" should contain "foo"
   * or: the meta "property" "og:description" should contain "bar"
   */
  public function theMetaShouldContain($attribute_key, $attribute_value, $value) {
    $page = $this->getSession()->getPage();
    $results = $page->findAll('css', 'meta');
    foreach ($results as $result) {
      if ($result->getAttribute($attribute_key) == $attribute_value) {
        $content = $result->getAttribute('content');
        if (stripos($content, $value) !== FALSE) {
          return;
        }
      }
    }
    throw new \Exception(sprintf('Meta "%s" "%s" with content containing "%s" was not found', $attribute_key, $attribute_value, $value));
  }

  /**
   * @Then /^the link "(?P<attribute_key>[^"]*)" "(?P<attribute_value>[^"]*)" should end with "(?P<value>(?:[^"]|\\")*)"$/
   */
  public function theLinkShouldEndWith($attribute_key, $attribute_value, $value) {
    $page = $this->getSession()->getPage();
    $results = $page->findAll('css', 'link');
    foreach ($results as $result) {
      if ($result->getAttribute($attribute_key) == $attribute_value) {
        $content = $result->getAttribute('href');
        if (preg_match('/' . $value . '$/', $content)) {
          return;
        }
      }
    }
    throw new \Exception(sprintf('Meta "%s" "%s" with content containing "%s" was not found', $attribute_key, $attribute_value, $value));
  }


  /**
   * @Then /^the meta "(?P<attribute_key>[^"]*)" "(?P<attribute_value>[^"]*)" should not contain "(?P<value>(?:[^"]|\\")*)"$/
   *
   * eg: the meta "name" "description" should not contain "foo"
   * or: the meta "property" "og:description" should not contain "bar"
   */
  public function theMetaShouldNotContain($attribute_key, $attribute_value, $value) {
    $page = $this->getSession()->getPage();
    $results = $page->findAll('css', 'meta');
    $found_tag = FALSE;
    foreach ($results as $result) {
      if ($result->getAttribute($attribute_key) == $attribute_value) {
        $found_tag = TRUE;
        $content = $result->getAttribute('content');
        if (stripos($content, $value) !== FALSE) {
          throw new \Exception(sprintf('Meta "%s" "%s" with content containing "%s" was found', $attribute_key, $attribute_value, $value));
        }
      }
    }
    if ($found_tag) {
      return;
    }
    else {
      throw new \Exception(sprintf('Meta "%s" "%s" was not found', $attribute_key, $attribute_value, $value));
    }
  }

  /**
   * Checks the element with the attribute containing the given value exists on the page.
   * eg: I should see the element "img.award_logo" attribute "alt" contain "Choice"
   *
   * @Then /^I should see the element "(?P<element>[^"]*)" attribute "(?P<attribute>(?:[^"]|\\")*)" contain "(?P<value>(?:[^"]|\\")*)"$/
   */
  public function iShouldSeeTheElementAttributeContain($element, $attribute, $value) {
    $page = $this->getSession()->getPage();
    $results = $page->findAll('css', $element);
    foreach ($results as $result) {
      $attr_val = $result->getAttribute($attribute);
      if (stripos($attr_val, $value) !== FALSE) {
        return;
      }
    }
    throw new \Exception(sprintf("No element '%s' with attribute %s containing %s", $element, $attribute, $value));
  }

  /**
   * Checks all the selected elements on the page contain a specific value.
   * eg: Then I should see "4" all of the ".rating-number" elements
   *
   * @Then /^I should see "(?P<value>(?:[^"]|\\")*)" in all of the "(?P<element>[^"]*)" elements$/
   */
  public function iShouldSeeAllOfTheElements($value, $element) {
    $page = $this->getSession()->getPage();
    $results = $page->findAll('css', $element);
    if (count($results)) {
      $i = 0;
      foreach ($results as $result) {
        $i++;
        if (stripos($result->getText(), $value) === FALSE) {
          throw new \Exception(sprintf('The value "%s" could not be found in element number %s', $value, $i));
        }
      }
      return;
    }
    else {
      throw new \Exception('No elements found.');
    }
  }

  /**
   * Checks that an element does not contain more than a specific number of named elements
   * eg: Then I should see no more than "10" ".node" elements within the "#most_popular_block" element
   *
   * @Then /^I should see no more than "([^"]*)" "([^"]*)" elements within the "([^"]*)" element$/
   */
  public function iShouldSeeNoMoreThanElementsWithinTheElement($limit, $el1, $el2) {
    $page = $this->getSession()->getPage();
    if (!$container = $page->find('css', $el2)) {
      throw new \Exception(sprintf('The element "%s" could not be found', $el1));
    }
    $results = $container->findAll('css', $el1);
    if ($results && count($results) > $limit) {
      throw new \Exception(sprintf('There are more than "%s" "%s" elements within the "%s" element', $limit, $el1, $el2));
    }
  }

  /**
   * Checks, that element with specified CSS does not contain specified regular expression.
   *
   * @Then /^an? "(?P<element>[^"]*)" element should not match "(?P<regex>(?:[^"]|\\")*)"$/
   */
  public function assertElementNotMatch($selector, $regex) {
    $page = $this->getSession()->getPage();
    $results = $page->findAll('css', $selector);
    if (count($results) == 0) {
      // No element found so passes the test
      return;
    }
    foreach ($results as $element) {
      $html = $element->getHtml();
      if (preg_match($regex, $html, $matches)) {
        throw new \Exception(sprintf('The regex "%s" was found in the HTML of an element matching "%s".', $regex, $selector));
      }
    }
  }

  /**
   * @Then /^the response should match "(?P<regex>(?:[^"]|\\")*)"$/
   */
  public function theResponseShouldMatch($regex) {
    $this->assertSession()->responseMatches($regex);
  }

  /**
   * @Then /^the response should not match "(?P<regex>(?:[^"]|\\")*)"$/
   */
  public function theResponseShouldNotMatch($regex) {
    $this->assertSession()->responseNotMatches($regex);
  }

  /**
   * @Then /^the response should not contain the "([^"]*)" element$/
   */
  public function theResponseShouldNotContainTheElement($element) {
    $page = $this->getSession()->getPage();
    if ($page->find('css', $element)) {
      throw new \Exception(sprintf('The element "%s" was found in the response.', $element));
    }
  }

  /**
   * @Then /^the "([^"]*)" element should contain the "([^"]*)" element$/
   */
  public function theElementShouldContainTheElement($element1, $element2) {
    $page = $this->getSession()->getPage();

    if (!$item1 = $page->find('css', $element1)) {
      throw new \Exception(sprintf('The element "%s" was not found.', $element1));
    }
    if (!$item2 = $page->find('css', $element2)) {
      throw new \Exception(sprintf('The element "%s" was not found.', $element2));
    }
    if ($item1->has('css', $element2)) {
      return;
    }

    throw new \Exception(sprintf('The element "%s" does not contain the "%s" element.', $element1, $element2));
  }

  /**
   * @Then /^the "([^"]*)" element should not contain the "([^"]*)" element$/
   */
  public function theElementShouldNotContainTheElement($element1, $element2) {
    $page = $this->getSession()->getPage();

    if (!$item1 = $page->find('css', $element1)) {
      throw new \Exception(sprintf('The element "%s" was not found.', $element1));
    }
    if (!$item2 = $page->find('css', $element2)) {
      return;
    }

    if (!$item1->has('css', $element2)) {
      return;
    }

    throw new \Exception(sprintf('The element "%s" does contain the "%s" element.', $element1, $element2));
  }

  /**
   * @Then /^the "([^"]*)" element should be before the "([^"]*)" element$/
   */
  public function theElementShouldBeBeforeTheElement($element1, $element2) {

    $page = $this->getSession()->getPage();
    $page_html = $page->getHtml();

    $item1 = $page->find('css', $element1);
    if (empty($item1) || !$item1_html = $item1->getHtml()) {
      throw new \Exception(sprintf('The element "%s" was not found.', $element1));
    }
    $item2 = $page->find('css', $element2);
    if (empty($item2) || !$item2_html = $item2->getHtml()) {
      throw new \Exception(sprintf('The element "%s" was not found.', $element2));
    }

    $pos1 = strpos($page_html, trim($item1_html));
    $pos2 = strpos($page_html, trim($item2_html));

    if ($pos1 > $pos2) {
      throw new \Exception(sprintf('The element "%s" is not before "%s".', $element1, $element2));
    }

  }

  /**
   * @When /^I click a link in any "([^"]*)" element$/
   */
  public function iClickALinkInAnyElement($element) {
    $page = $this->getSession()->getPage();
    if (!$container_element = $page->find('css', $element)) {
      throw new \Exception(sprintf('The element "%s" was not found.', $container_element));
    }
    if (!$link = $container_element->find('css', 'a')) {
      throw new \Exception(sprintf('No link was not found in element.', $container_element));
    }

    $link->click();
  }

  /**
   * @Then /^the "([^"]*)" element should be after the "([^"]*)" element$/
   */
  public function theElementShouldBeAfterTheElement($element1, $element2) {
    // Call the "before" step definition method with arguments reversed.
    $this->theElementShouldBeBeforeTheElement($element2, $element1);
  }

  /**
   * @Then /^the HTML should contain "([^"]*)"$/
   */
  public function theHtmlShouldContain($value) {
    $page = $this->getSession()->getPage();
    $page_html = $page->getHtml();
    if (stripos($page_html, $value) !== FALSE) {
      return;
    }
    throw new \Exception(sprintf('The HTML does not contain "%s"', $value));
  }

  /**
   * Fills in the textarea field with specified id|name|label|value.
   *
   * @When /^(?:|I )fill in the textarea "(?P<field>(?:[^"]|\\")*)" with:/
   */
  public function fillTextareaField($field, $value) {
    $this->getSession()->getPage()->fillField($field, $value->getRaw());
  }

  /**
   * Follows the link which is inside the element
   * @When /^I follow "([^"]*)" in the "([^"]*)" element$/
   */
  public function iFollowInTheElement($link, $element) {
    $page = $this->getSession()->getPage();

    if (!$item = $page->find('css', $element)) {
      throw new \Exception(sprintf('The element "%s" was not found.', $element));
    }
    if (!$dest = $item->findLink($link)) {
      throw new \Exception(sprintf('The link "%s" was not found in the "%s" element.', $link, $element));
    }

    $dest->click();
  }

  /**
   * Counts the number of occurrences of a string in the page HTML and verifies
   * the count is of the exact value.
   *
   * @Then /^the HTML should contain the string \'([^\']*)\' exactly "([^"]*)" times$/
   */
  public function theHtmlShouldContainTheStringExactlyTimes($string, $count) {
    $page = $this->getSession()->getPage();
    $page_html = $page->getHtml();
    $num_occurrences = substr_count($page_html, $string);
    if ($count == $num_occurrences) {
      return;
    }
    if ($num_occurrences) {
      throw new \Exception(sprintf('The number of occurrences of "%s" does not match \'%d\'', $string, $count));
    }
    else {
      throw new \Exception(sprintf('The HTML does not contain "%s"', $string));
    }
  }

  /**
   * @Then /^the inner html of the element "([^"]*)" should be empty$/
   */
  public function theElementShouldBeEmpty($element) {
    $page = $this->getSession()->getPage();
    $page_html = $page->getHtml();

    if (!$item = $page->find('css', $element)) {
      throw new \Exception(sprintf('The element "%s" was not found.', $element));
    }

    $innerHtml = $item->getHtml();
    if (empty($innerHtml)) {
      return;
    }

    throw new \Exception(sprintf('The inner html of element "%s" is not empty', $element));
  }

  /**
   * @Given /^the xhtml matches xpath "([^"]*)"$/
   */
  public function theXhtmlMatchesXpath($query) {
    $this->loadXhtml();

    // example 1: for everything with an id
    // $elements = $xpath->query("//*[@id]");

    // example 2: for node data in a selected id
    // $elements = $xpath->query("/html/body/div[@id='yourTagIdHere']");

    // example 3: same as above with wildcard
    // $elements = $xpath->query("*/div[@id='yourTagIdHere']");

    $xpath = new \DOMXpath($this->xhtml_doc);
    $elements = $xpath->query($query);
    if (!$elements || $elements->length == 0) {
      throw new \Exception(sprintf('No xpath matches for: "%s"', $query));
    }
  }

  /**
   * Load the XHTML.
   */
  protected function loadXhtml() {
    $page = $this->getSession()->getPage();
    $this->xhtml_doc = new \DOMDocument();

    // Turn internal errors off as we are dealing with HTML5 and causes warnings.
    libxml_use_internal_errors(TRUE);

    if (!$this->xhtml_doc->loadHTML($page->getContent())) {
      unset($this->xhtml_doc);
      $error = libxml_get_last_error();
      throw new \Exception($error->message);
    }
  }

  /**
   * @Given /^I fill hidden field "([^"]*)" with "([^"]*)"$/
   */
  public function iFillHiddenFieldWith($field, $value) {
    if (!$node = $this->getSession()
      ->getPage()
      ->find('css', 'input[name="' . $field . '"]')) {
      throw new \Exception(sprintf("The hidden field with name '%s' was not found in the DOM", $field));
    }
    $node->setValue($value);
  }

  /**
   * @Then /^I fill "([^"]*)" element with current date$/
   */
  public function iFillElementWithCurrentDate($el) {
    if (!$node = $this->getSession()->getPage()->find('css', $el)) {
      throw new \Exception(sprintf("The element '%s' was not found in the DOM", $el));
    }
    $current_date = date('Y-m-d');
    $node->setValue($current_date);
  }

  /**
   * @Then /^I fill "([^"]*)" element with current time$/
   */
  public function iFillElementWithCurrentTime($el) {
    if (!$node = $this->getSession()->getPage()->find('css', $el)) {
      throw new \Exception(sprintf("The element '%s' was not found in the DOM", $el));
    }
    $current_time = date('H:i');
    $node->setValue($current_time);
  }

  /**
   * @Then I follow the rel :arg link
   */
  public function iFollowTheRelLink($arg) {
    $selector = sprintf("link[rel='%s']", $arg);
    $elements = $this->getSession()->getPage()->findAll('css', $selector);
    if ($linkElement = reset($elements)) {
      $href = $linkElement->getAttribute('href');
      $this->getSession()->visit($href);
    }
    else {
      throw new \Exception(sprintf('Could not find rel %s link element', $arg));
    }
  }

  /**
   * @Given /^a json-?ld "([^"]*)" value should match "(?P<regex>(?:[^"]|\\")*)"$/
   * @Given /^the json ld value "([^"]*)" should match "([^"]*)"$/
   */
  public function assertJsonldMatch($jsonKey, $regex) {
    $page = $this->getSession()->getPage();
    $results = $page->findAll('css', "script[type='application/ld+json']");
    if (count($results)) {
      foreach ($results as $element) {
        $text = $element->getText();
        $json = new Dot(json_decode($text, TRUE));
        $value = $json->get($jsonKey);
        if (preg_match($regex, $value)) {
          return;
        }
      }
    }
    throw new \Exception(sprintf('The regex "%s" was not found in a JSON-LD element "%s".', $regex, $jsonKey));
  }


  /**
   * @Given /^a json value "([^"]*)" should match "([^"]*)"$/
   *
   * @param $jsonKey
   * @param $regex
   * @throws \Exception
   */
  public function aJsonValueShouldMatch($jsonKey, $regex) {
    $page = $this->getSession()->getPage();
    if ($results = $page->findAll('css', "script[type='application/json']")) {
      foreach ($results as $item) {
        $json_element_text = trim($item->getText());

        // Get value and check regex.
        $json = new Dot(json_decode($json_element_text, TRUE));
        $value = $json->get($jsonKey);

        if (preg_match($regex, $value)) {
          return;
        }
      }
    }

    // Throw exception if a match could not be found.
    throw new \Exception(sprintf('The regex "%s" was not found in the JSON LD element "%s".', $regex, $jsonKey));
  }

  /**
   * @Given /^a json value "([^"]*)" in the "([^"]*)" element should match "([^"]*)"$/
   *
   * @param $jsonKey
   * @param $element
   * @param $regex
   *
   * @throws \Exception
   */
  public function aJsonValueInTheElementShouldMatch($jsonKey, $element, $regex) {
    $page = $this->getSession()->getPage();
    if ($results = $page->findAll('css', $element)) {
      foreach ($results as $item) {
        $json_element_text = trim($item->getAttribute('json'));

        // Get value and check regex.
        $json = new Dot(json_decode($json_element_text, TRUE));
        $value = $json->get($jsonKey);

        if (preg_match($regex, $value)) {
          return;
        }
      }
    }

    // Throw exception if a match could not be found.
    throw new \Exception(sprintf('The regex "%s" was not found in the %s element "%s".', $regex, $element, $jsonKey));
  }


  /**
   * Store, the result of the regex on the element with specified CSS.
   *
   * @Then /^store the result of "(?P<regex>(?:[^"]|\\")*)" for the element "(?P<selector>[^"]*)"$/
   */
  public function storeElementMatches($selector, $regex) {
    if ($matches = $this->getElementMatches($selector, $regex)) {
      $this->storeSet($matches);
    }
  }

  /**
   * Store, the result of the regex on the element with specified CSS as specified key.
   *
   * @Then /^store the result of "(?P<regex>(?:[^"]|\\")*)" for the element "(?P<selector>[^"]*)" as "(?P<key>[^"]*)"$/
   */
  public function storeElementMatchesAs($selector, $regex, $key) {
    if ($matches = $this->getElementMatches($selector, $regex)) {
      $this->storeSet($key, $matches);
    }
  }

  /**
   * Set store value.
   *
   * @param $key
   * @param $val
   */
  protected function storeSet($key, $val = NULL) {
    if ($val == NULL) {
      // Use numerical keys
      $this->store[] = $key;
    }
    else {
      $this->store[$key] = $val;
    }
  }

  /**
   * Get store value.
   *
   * @param $key
   * @return mixed|null
   */
  protected function storeGet($key) {
    if (isset($this->store[$key])) {
      return $this->store[$key];
    }
    return NULL;
  }

  /**
   * Get the element matches for selector.
   *
   * @param $selector
   * @param $regex
   * @return array
   * @throws \Exception
   */
  public function getElementMatches($selector, $regex) {
    $page = $this->getSession()->getPage();
    $results = $page->findAll('css', $selector);
    if (count($results) == 0) {
      throw new \Exception(sprintf('The element "%s" was not found.', $selector));
    }

    foreach ($results as $element) {
      $html = $element->getHtml();
      if (preg_match($regex, $html, $matches)) {
        return $matches;
      }
    }

    throw new \Exception(sprintf('The regex "%s" was not found in the HTML of any element matching "%s".', $regex, $selector));
  }

  /**
   * Checks, that element with specified CSS contains specified regular expression.
   *
   * @Then /^an? "(?P<element>[^"]*)" element should match "(?P<regex>(?:[^"]|\\")*)"$/
   */
  public function assertElementMatches($selector, $regex) {
    try {
      $this->storeElementMatches($selector, $regex);
    } catch (\Exception $e) {
      throw $e;
    }
  }


  /**
   * @Transform /(.*?{match:.*?)$/
   *
   * Replace any stored matches if the argument is a string.
   */
  public function replaceMatchTokens($argument) {
    if (is_string($argument)) {
      preg_match_all('~{match:(.*?)}~', $argument, $matches);
      if (isset($matches[0]) && isset($matches[1])) {
        foreach ($matches[0] as $i => $match) {
          // Get value using stored key e.g. 0:1 or node:1
          $matchValue = $this->getStoredMatch($matches[1][$i]);
          // Replace the token e.g. {match:0:1} with value.
          $argument = str_ireplace($match, $matchValue, $argument);
        }
      }
    }
    return $argument;
  }

  /**
   * Returns the value for the specifed match.
   *
   * @param $result
   * @return mixed
   * @throws \Exception
   */
  public function getStoredMatch($result) {
    $parts = explode(':', $result);
    if (count($parts) == 1) {
      $store_key = 0;
      $regex_key = $result;
    }
    else {
      $store_key = $parts[0];
      $regex_key = $parts[1];
    }
    $regex_result = $this->storeGet($store_key);
    if (isset($regex_result[$regex_key])) {
      return $regex_result[$regex_key];
    }
    throw new \Exception(sprintf('The match "%s" has not been set.', $result));
  }

  /**
   * @Then /^the "([^"]*)" element should contain match result "([^"]*)"$/
   *
   * A scenario can call assertElementContainsRegex multiple times, each time the result is stored for later use.
   * The number of the store is incremented by one for each call to assertElementContainsRegex in the scenario.
   *
   * This function checks against the regex result in the store.
   * The result parameter can be a colon seperated string,
   * the first is the number of the store, the second the regex array value to check.
   *
   * Eg: Then the "#search_count" element should contain regex result "0:1"
   */
  public function theElementShouldContainMatchResult($element, $result) {
    $resultValue = $this->getStoredMatch($result);
    $this->assertSession()->elementContains('css', $element, $resultValue);
  }


  /**
   * Follows the stylesheet.
   * @When /^I follow the "([^"]*)" stylesheet$/
   */
  public function iFollowTheStylesheet($stylesheet_name) {
    $page = $this->getSession()->getPage();
    $stylesheet_url = FALSE;
    $css_elements = $page->findAll('css', 'style');

    foreach ($css_elements as $element) {
      preg_match_all('|url\("([^(]*?' . $stylesheet_name . '[^(]*?)"\)|', $element->getHtml(), $matches);
      if (!empty($matches[1])) {
        foreach ($matches[1] as $match) {
          $stylesheet_url = $match;
        }
      }
    }

    if (!$stylesheet_url) {
      $css_elements = $page->findAll('css', 'head');
      foreach ($css_elements as $element) {
        preg_match_all('|rel="stylesheet" href="([^>]*?' . $stylesheet_name . '[^>]*?)"|', $element->getHtml(), $matches);
        if (!empty($matches[1])) {
          foreach ($matches[1] as $match) {
            $stylesheet_url = $match;
          }
        }
      }
    }

    if ($stylesheet_url) {
      $this->getSession()->visit($stylesheet_url);
    }
    else {
      throw new \Exception(sprintf('The stylesheet "%s" was not found.', $stylesheet_name));
    }

  }
}
