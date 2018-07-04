<?php

namespace DennisDigital\Behat\Mink\Context;

use Behat\MinkExtension\Context\RawMinkContext;

/**
 * Class HttpContext
 * @package DennisDigital\Behat\Mink\Context
 */
class HttpContext extends RawMinkContext {
  /**
   * Store headers for use within a scenario.
   */
  private $headers = array();

  /**
   * @Given /^I set the "([^"]*)" header to "([^"]*)"$/
   */
  public function iSetHeaderTo($key, $value) {
    $driver = $this->getSession()->getDriver();
    if ($driver instanceof GoutteDriver) {
      $this->getSession()->getDriver()->getClient()->setHeader($key, $value);
      $this->headers[] = $key;
    }
    else {
      throw new \Exception("Only the goutte driver can set headers.\nUse @mink:goutte as a tag for the scenario.");
    }
  }

  /**
   * Run after every scenario.
   *
   * @AfterScenario
   */
  public function cleanHeaders($event) {
    if (!empty($this->headers)) {
      $driver = $this->getSession()->getDriver();
      if (method_exists($driver, 'getClient')) {
        $client = $driver->getClient();
        foreach ($this->headers as $header) {
          $client->removeHeader($header);
        }
      }
    }
  }

  /**
   * @Given I set the cookie :arg1 with :arg2
   */
  public function iSetTheCookieWith($cookie, $value) {
    $session = $this->getSession();
    $driver = $session->getDriver();
    if ($driver instanceof Selenium2Driver) {
      $session->evaluateScript("(function(){
       document.cookie = '$cookie=$value; path=/'
      })()");
    }
    else {
      $session->setCookie($cookie, $value);
    }
  }

  /**
   * @Then /^the full url should be "(?P<url>[^"]+)"$/
   *
   * Needed because 'should be on "url"' is really only matching the path withut the host
   */
  public function theFullUrlShouldBe($url) {
    $actual = $this->getSession()->getCurrentUrl();
    if ($actual !== $url) {
      throw new \Exception(sprintf('Current full url is "%s", but "%s" expected.', $actual, $url));
    }
  }

  /**
   * @Then /^the url should not be "([^"]*)"$/
   */
  public function theUrlShouldNotBe($url) {
    $actual = parse_url($this->getSession()->getCurrentUrl(), PHP_URL_PATH);
    if ($actual == $url) {
      throw new \Exception(sprintf('Urls are the same.'));
    }
  }

  /**
   * Checks, that current page PATH is NOT equal to specified.
   *
   * @Then /^(?:|I )should not be on "(?P<page>[^"]+)"$/
   */
  public function assertNotPageAddress($page) {
    $this->assertSession()->addressNotEquals($this->locatePath($page));
  }

  /**
   * @Given /^I am on "([^"]*)" without redirection$/
   */
  public function theRedirectionsAreIntercepted($path) {
    $this->getSession()
      ->getDriver()
      ->getClient()
      ->followRedirects(FALSE);

    $this->getSession()->visit($this->locatePath($path));

    $this->getSession()
      ->getDriver()
      ->getClient()
      ->followRedirects(TRUE);
  }

  /**
   * @Then /^the header "([^"]*)" should be "([^"]*)"$/
   */
  public function headerShouldBe($key, $value) {

    $headers = $this->getSession()->getResponseHeaders();

    // Take the first value of the array
    if (is_array($headers[$key])) {
      $header = current($headers[$key]);
      if ($header != $value) {
        throw new \RuntimeException("$header is not equal to $value.");
      }
    }
  }

  /**
   * @Then /^the header "([^"]*)" should match "([^"]*)"$/
   */
  public function headerShouldMatch($key, $regex) {
    $headers = $this->getSession()->getResponseHeaders();

    // Take the first value of the array
    if (isset($headers[$key]) && is_array($headers[$key])) {
      $header = current($headers[$key]);
      if (!preg_match($regex, $header)) {
        throw new \Exception("The " . $header . " header does not match " . $regex);
      }
    }
    else {
      throw new \Exception("The " . $key . " header is not available");
    }
  }
}
