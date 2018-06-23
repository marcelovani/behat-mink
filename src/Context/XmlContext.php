<?php

namespace DennisDigital\Behat\Mink\Context;

use Behat\MinkExtension\Context\RawMinkContext;

/**
 * Class XmlContext
 * @package DennisDigital\Behat\Mink\Context
 */
class XmlContext extends RawMinkContext {
  /**
   * If an xml document has been passed, it gets stored here.
   * @var DOMDocument
   */
  protected $xml_doc;

  /**
   * @Given /^the xml is valid$/
   */
  public function theXmlIsValid() {
    $this->loadXml();
  }

  /**
   * @Given /^the xml matches xpath "([^"]*)"$/
   */
  public function theXmlMatchesXpath($query) {
    $this->loadXml();

    // example 1: for everything with an id
    // $elements = $xpath->query("//*[@id]");

    // example 2: for node data in a selected id
    // $elements = $xpath->query("/html/body/div[@id='yourTagIdHere']");

    // example 3: same as above with wildcard
    // $elements = $xpath->query("*/div[@id='yourTagIdHere']");

    $xpath = new \DOMXpath($this->xml_doc);
    $elements = $xpath->query($query);
    if (!$elements || $elements->length == 0) {
      throw new \Exception(sprintf('No xpath matches for: "%s"', $query));
    }
  }

  /**
   * @Given /^the xml does not match xpath "([^"]*)"$/
   */
  public function theXmlDoesNotMatchXpath($query) {
    $this->loadXml();

    $xpath = new \DOMXpath($this->xml_doc);
    $elements = $xpath->query($query);
    if ($elements && $elements->length > 0) {
      throw new \Exception(sprintf('xpath matches for: "%s"', $query));
    }
  }

  /**
   * Loads the current XML response.
   *
   * @throws \Exception
   */
  protected function loadXml() {
    $page = $this->getSession()->getPage();
    $this->xml_doc = new \DOMDocument();

    if (!$this->xml_doc->loadXML($page->getContent(), LIBXML_NOERROR)) {
      unset($this->xml_doc);
      $error = libxml_get_last_error();
      throw new \Exception($error->message);
    }
  }
}
