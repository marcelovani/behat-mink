<?php

namespace DennisDigital\Behat\Mink\Context;

use Behat\MinkExtension\Context\RawMinkContext;

# @todo Waits should be configurable...

/**
 * Class JsContext
 * @package DennisDigital\Behat\Mink\Context
 */
class JsContext extends RawMinkContext {
  /**
   * Default element wait timeout.
   *
   * @var int
   */
  protected $element_wait_timeout = 5;

  /**
   * Initializes context.
   *
   * Every scenario gets its own context object.
   *
   * @param array $parameters .
   *   Context parameters (set them up through behat.yml or behat.local.yml).
   */
  public function __construct($parameters = array()) {
    if (isset($parameters['element_wait_timeout'])) {
      $this->element_wait_timeout = $parameters['element_wait_timeout'];
    }
  }

  /**
   * @Then /^I click the "([^"]*)" element$/
   *
   * @param string $cssSelector
   */
  public function iClickElement($cssSelector) {
    $this->jsWaitForDocumentLoaded();
    // The Selenium driver uses xpath for selectors
    $this->getSession()
      ->getDriver()
      ->click($this->cssSelectorToXpath($cssSelector));
  }

  /**
   * @Then I wait a maximum of :arg1 seconds for the :arg2 element
   *
   * @param $seconds
   * @param $selector
   */
  public function iWaitAMaximumOfSecondsForTheElement($seconds, $selector) {
    // Wait up to specified time for the element.
    $this->getSession()
      ->wait(($seconds * 1000), sprintf("document.querySelector('%s') !== null", addslashes($selector)));
    // Assert element exists.
    $this->assertSession()->elementExists('css', $selector);
  }

  /**
   * @Then I wait for the :arg1 element
   *
   * @param $selector
   */
  public function iWaitForTheElement($selector) {
    $this->iWaitAMaximumOfSecondsForTheElement($this->element_wait_timeout, $selector);
  }

  /**
   * @Then the :arg1 array should contain no duplicates
   */
  public function jsTheArrayShouldContainNoDuplicates($arg1) {
    $this->jsWaitForDocumentLoaded();

    $array = $this->getSession()->evaluateScript('return ' . $arg1);
    if (!is_array($array)) {
      throw new \Exception($arg1 . ' cannot be found on this page.');
    }

    // Look for duplicates in the array.
    $unique = array_unique($array);
    if ($unique != $array) {
      throw new \Exception(sprintf('Duplicates found in "%s". Namely: %s.', $arg1, implode(", ", $unique)));
    }
  }

  /**
   * @Then I scroll to the bottom of the page
   */
  public function iScrollToTheBottomOfThePage() {
    $session = $this->getSession();
    $session->wait(10000, "document.readyState == 'complete'");
    $v = $session->evaluateScript('return !!(jQuery(document).scrollTop(jQuery(document).height()))');
    if ($v != 'true') {
      throw new \Exception("Could not scroll to the bottom of the page.");
    }
  }

  /**
   * @Given I press the :key key
   */
  public function iPressTheKey($key) {
    $key_codes = $this->getKeyCodes();

    // Throw exception if the key is not mapped.
    if (!isset($key_codes[$key])) {
      throw new \Exception('Unknown key "' . $key . '"');
    }

    // Press the key.
    $key_code = $key_codes[$key];
    $driver = $this->getSession()->getDriver();
    $driver->keyDown('//body', $key_code);
    $driver->keyUp('//body', $key_code);
    $driver->keyPress('//body', $key_code);
  }

  /**
   * Get mapping of key codes.
   *
   * @return array
   */
  protected function getKeyCodes() {
    // a-z and A-Z keycodes.
    $key_codes = array_combine(range('a', 'z'), range(97, 122));
    $key_codes += array_combine(range('A', 'Z'), range(65, 90));

    // shift + a-z.
    $shift_a_to_z = array_map(function ($key) {
      return 'shift + ' . $key;
    }, range('a', 'z'));
    $key_codes += array_combine($shift_a_to_z, range(65, 90));

    // 0-9.
    $key_codes += array_combine(range('0', '9'), range(48, 57));

    // Other useful keycodes.
    $key_codes += array(
      '\'' => 39,
      ',' => 44,
      '-' => 45,
      '.' => 46,
      '/' => 47,
      ':' => 58,
      '=' => 61,
      '[' => 91,
      '\\' => 92,
      ']' => 93,
      '`' => 96,
      'return' => 13,
      'escape' => 27,
      'space' => 32,
      'left arrow' => 37,
      'up arrow' => 38,
      'right arrow' => 39,
      'down arrow' => 40,
    );

    return $key_codes;
  }

  /**
   * Wait for the document to be loaded.
   *
   * @see http://www.whatwg.org/specs/web-apps/current-work/multipage/dom.html#current-document-readiness
   *
   * Requires @javascript tag on the scenario.
   */
  public function jsWaitForDocumentLoaded($timeout = 5000) {
    $this->getSession()->wait($timeout, "document.readyState == 'complete'");
  }

  /**
   * @Then /^the js "([^"]*)" element should contain "([^"]*)"$/
   *
   * Requires @javascript tag on the scenario.
   */
  public function jsAssertElementContains($cssSelector, $value) {
    // The Selenium driver uses xpath for selectors
    $text = $this->getSession()
      ->getDriver()
      ->getText($this->cssSelectorToXpath($cssSelector));
    if (strpos($text, $value) === FALSE) {
      throw new \Exception(sprintf("'%s' value '%s' does not contain not '%s'", $cssSelector, $text, $value));
    }
  }

  /**
   * @Then /^the "([^"]*)" javascript expression should be true$/
   *
   * Requires @javascript tag on the scenario.
   *
   * eg; Then the "(window.basePath === '/');" javascript expression should be true
   */
  public function jsAssertExpression($exp) {
    $this->jsWaitForDocumentLoaded();
    $v = $this->getSession()->evaluateScript('return ' . $exp);
    if ($v != 'true') {
      throw new \Exception(sprintf("Expression '%s' does not evaluate to true'", $exp));
    }
  }

  /**
   * @Then /^the javascript expression should be true:$/
   *
   * Requires @javascript tag on the scenario.
   *
   */
  public function jsAssertExpressionMultiline($exp) {
    $this->jsWaitForDocumentLoaded();
    $v = $this->getSession()->evaluateScript('return ' . $exp);
    if ($v != 'true') {
      throw new \Exception(sprintf("Expression '%s' does not evaluate to true'", $exp));
    }
  }

  /**
   * @Then /^the "([^"]*)" element should be visible$/
   *
   * Requires @javascript tag on the scenario.
   */
  public function jsAssertVisible($cssSelector) {
    // The Selenium driver uses xpath for selectors
    if (!$this->getSession()
      ->getDriver()
      ->isVisible($this->cssSelectorToXpath($cssSelector))) {
      throw new \Exception(sprintf("'%s' is not visible", $cssSelector));
    }
  }

  /**
   * @Then /^the "([^"]*)" element should be invisible$/
   *
   * Requires @javascript tag on the scenario.
   */
  public function jsAssertInvisible($cssSelector) {
    // The Selenium driver uses xpath for selectors
    if ($this->getSession()
      ->getDriver()
      ->isVisible($this->cssSelectorToXpath($cssSelector))) {
      throw new \Exception(sprintf("'%s' is visible", $cssSelector));
    }
  }

  /**
   * @Then /^I wait for "([^"]*)" milliseconds for "([^"]*)"$/
   *
   * eg: Then I wait for "3000" milliseconds for "$('.suggestions-results').children().length > 0"
   * or: Then I wait for "3000" milliseconds for "1 == 0"
   * @see http://mink.behat.org/
   *
   * Requires @javascript tag on the scenario.
   */
  public function jsWaitMillisecondsFor($timeout, $condition) {
    $this->getSession()->wait($timeout, $condition);
  }

  /**
   * @Then /^I wait for "([^"]*)" milliseconds$/
   *
   * eg: Then I wait for "3000" milliseconds
   *
   * Requires @javascript tag on the scenario.
   */
  public function jsWaitMilliseconds($timeout) {
    $this->getSession()->wait($timeout, 'null');
  }

  /**
   * @Then /^I set the screen size to "([^"]*)" by "([^"]*)"$/
   *
   * Requires @javascript tag on the scenario.
   */
  public function jsSetScreenSize($width, $height) {
    $this->getSession()->resizeWindow($width, $height, 'current');
  }


  /**
   * Use jquery to get the value of an element's attribute.
   *
   * @param string $jQuerySelector
   * @param string $propertyName
   */
  public function jsGetAttributeValue($jQuerySelector, $propertyName) {
    $this->jsWaitForJquery();
    $script = 'return jQuery("' . $jQuerySelector . '").attr("' . $propertyName . '");';
    return $this->getSession()->evaluateScript($script);
  }

  /**
   * @Then /^the "([^"]*)" element should have an attribute "([^"]*)" contain "([^"]*)"$/
   *
   * @see http://api.jquery.com/attr/
   *
   * Requires @javascript tag on the scenario.
   */
  public function jsAssertAttributeValueContains($jQuerySelector, $propertyName, $value) {
    $actual_value = $this->jsGetAttributeValue($jQuerySelector, $propertyName);
    if (strpos($actual_value, $value) === FALSE) {
      throw new \Exception(sprintf("Atribute '%s' value '%s' does not contain not '%s'", $propertyName, $actual_value, $value));
    }
  }

  /**
   * @Then /^the "([^"]*)" element should have an attribute "([^"]*)" of "([^"]*)"$/
   *
   * @see http://api.jquery.com/attr/
   *
   * Requires @javascript tag on the scenario.
   */
  public function jsAssertAttributeValue($jQuerySelector, $propertyName, $value) {
    $actual_value = $this->jsGetAttributeValue($jQuerySelector, $propertyName);
    if ($actual_value != $value) {
      throw new \Exception(sprintf("Atribute '%s' value is '%s' not '%s'", $propertyName, $actual_value, $value));
    }
  }

  /**
   * Translate a css selector to an xpath one.
   *
   * @param string $cssSelector
   */
  public function cssSelectorToXpath($cssSelector) {
    return $this->getSession()
      ->getSelectorsHandler()
      ->selectorToXpath('css', $cssSelector);
  }


  /**
   * @Then /^the "([^"]*)" element should have css property "([^"]*)" of "([^"]*)"$/
   *
   * @see http://api.jquery.com/css/
   *
   * Requires @javascript tag on the scenario.
   */
  public function jsAssertCssPropertyValue($jQuerySelector, $propertyName, $value) {
    $actual_value = $this->jsGetCssPropertyValue($jQuerySelector, $propertyName);
    if ($actual_value != $value) {
      throw new \Exception(sprintf("CSS property '%s' value is '%s' not '%s'", $propertyName, $actual_value, $value));
    }
  }

  /**
   * @Then /^the "([^"]*)" element should not have css property "([^"]*)" of "([^"]*)"$/
   *
   * @see http://api.jquery.com/css/
   *
   * Requires @javascript tag on the scenario.
   */
  public function jsAssertNotCssPropertyValue($jQuerySelector, $propertyName, $value) {
    $actual_value = $this->jsGetCssPropertyValue($jQuerySelector, $propertyName);
    if ($actual_value == $value) {
      throw new \Exception(sprintf("CSS property '%s' value should not be '%s'", $propertyName, $actual_value));
    }
  }

  /**
   * @Then /^each "([^"]*)" should have css property "([^"]*)" of "([^"]*)"$/
   *
   * Requires @javascript tag on the scenario.
   */
  public function jsAssertEachCssPropertyValue($jQuerySelector, $propertyName, $value) {
    $this->jsWaitForDocumentLoaded();

    $script = <<<JS
return (function(){
  var value = '';
  jQuery( "$jQuerySelector" ).each(function() {
    if (jQuery( this ).css( "$propertyName" ) != "$value") {
      value += "\\n" + jQuery( this ).css( "$propertyName" ) + "\\n";
    }
  });
  return value;
})();
JS;

    $v = $this->getSession()->evaluateScript($script);
    // $value contains the html of the failing element.
    if (strlen($v) > 0) {
      throw new \Exception(sprintf("The '%s' was not found with value '%s'", $propertyName, $value));
    }
  }

  /**
   * @Then /^each "([^"]*)" should not have css property "([^"]*)" of "([^"]*)"$/
   *
   * Requires @javascript tag on the scenario.
   */
  public function jsAssertNotEachCssPropertyValue($jQuerySelector, $propertyName, $value) {
    $this->jsWaitForDocumentLoaded();

    $script = <<<JS
return (function(){
  var value = '';
  jQuery( "$jQuerySelector" ).each(function() {
    if (jQuery( this ).css( "$propertyName" ) == "$value") {
      value += "\\n" + jQuery( this ).css( "$propertyName" ) + "\\n";
    }
  });
  return value;
})();
JS;

    $v = $this->getSession()->evaluateScript($script);
    // $value contains the html of the failing element.
    if (strlen($v) > 0) {
      throw new \Exception(sprintf("The '%s' was found with value '%s'", $propertyName, $value));
    }
  }

  /**
   * @Then /^"([^"]*)" should be revealed$/
   *
   * Visible (to people & bots) when once was not visible.
   *
   * Refers to the @mixin element-invisible-off
   * that sets an element's visibility using clipping etc rather than :visible
   * so that bots can still see the element.
   *
   * @see dennis_base_v2/sass-extensions/dennis-base-shared/stylesheets/shared/_common.scss
   */
  public function jsTheShouldBeElementRevealed($jQuerySelector) {
    $overflow = $this->jsGetCssPropertyValue($jQuerySelector, 'overflow');
    $clip = $this->jsGetCssPropertyValue($jQuerySelector, 'clip');
    if ($clip == 'rect(1px 1px 1px 1px)' && $overflow == 'hidden') {
      throw new \Exception(sprintf("The '%s' element should be revealed", $jQuerySelector));
    }
  }

  /**
   * @Then /^"([^"]*)" should be unrevealed$/
   *
   * Invisible (to people but not bots) but has potential to be visible.
   *
   * Refers to the @mixin element-invisible
   * that sets an element's visibility using clipping etc rather than :visible
   * so that bots can still see the element.
   *
   * @see dennis_base_v2/sass-extensions/dennis-base-shared/stylesheets/shared/_common.scss
   */
  public function jsTheShouldBeElementUnrevealed($jQuerySelector) {
    $overflow = $this->jsGetCssPropertyValue($jQuerySelector, 'overflow');
    $clip = $this->jsGetCssPropertyValue($jQuerySelector, 'clip');
    if ($clip != 'rect(1px 1px 1px 1px)' && $overflow != 'hidden') {
      throw new \Exception(sprintf("The '%s' element should not be revealed", $jQuerySelector));
    }
  }

  /**
   * @Then /^each "([^"]*)" should have been given the "([^"]*)" class$/
   *
   * Check the the class has been added to each element, defined by the selector, by javascript.
   *
   * @param string $element
   * @param string $class
   */
  public function jsAssertEachElementClassSet($jQuerySelector, $class) {
    $this->jsWaitForDocumentLoaded();

    $script = <<<JS
return (function(){
  var value = 'no selector match';
  jQuery( "$jQuerySelector" ).each(function() {
    if (jQuery( this ).hasClass( "$class" )) {
      value = '';
    }
  });
  return value;
})();
JS;

    $value = $this->getSession()->evaluateScript($script);
    if (strlen($value) > 0) {
      if ($value == 'no selector match') {
        throw new \Exception(sprintf("The selector '%s' was not found", $jQuerySelector));
      }
      else {
        throw new \Exception(sprintf("The class '%s' was not found in all elements in selector '%s'", $class, $jQuerySelector));
      }
    }
  }

  /**
   * @Then /^each "([^"]*)" should not have the "([^"]*)" class$/
   *
   * Check the the class has been removed from each element, defined by the selector, by javascript.
   *
   * @param string $element
   * @param string $class
   */
  public function jsAssertNotEachElementClassSet($jQuerySelector, $class) {
    $this->jsWaitForDocumentLoaded();

    $script = <<<JS
return (function(){
  if (jQuery( "$jQuerySelector" ).length == 0) {
    return 'no selector match';
  }
  var value = '';
  jQuery( "$jQuerySelector" ).each(function() {
    if (jQuery( this ).hasClass( "$class" ) == true) {
      value += "\\n" + jQuery( this ).parent().html() + "\\n";
      value += " " + jQuery( this ).attr( "class" );
    }
  });
  return value;
})();
JS;

    $value = $this->getSession()->evaluateScript($script);
    // $value contains the html of the failing element.
    if (strlen($value) > 0) {
      if ($value == 'no selector match') {
        throw new \Exception(sprintf("The selector '%s' was not found", $jQuerySelector));
      }
      else {
        throw new \Exception(sprintf("The class '%s' was found in in selector '%s'", $class, $jQuerySelector));
      }
    }
  }

  /**
   * Use jquery to get the value of a css property.
   *
   * @param string $jQuerySelector
   * @param string $propertyName
   */
  public function jsGetCssPropertyValue($jQuerySelector, $propertyName) {
    $this->jsWaitForDocumentLoaded();
    $script = 'return jQuery("' . $jQuerySelector . '").css("' . $propertyName . '");';
    return $this->getSession()->evaluateScript($script);
  }


  /**
   * Wait jQuery to be ready.
   *
   * NB: the document may not be loaded yet so the UI may not be in a state to be tested yet.
   * Use jsWaitForDocumentLoaded() if all resources are needed.
   *
   * Requires @javascript tag on the scenario.
   */
  public function jsWaitForJquery($timeout = 5000) {
    $this->getSession()->wait($timeout, "typeof jQuery != 'undefined' && jQuery.isReady");
  }

}
