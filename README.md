# Behat Mink Contexts
Provides reusable Behat steps for any website

## HTML
```
DennisDigital\Behat\Mink\Context\HtmlContext
```
### Step Definitions
```gherkin
##### Finding elements

Then /^I should see a "([^"]*)" element with the "([^"]*)" attribute set with a value of "([^"]*)"$/
Then /^I should not see a "([^"]*)" element with the "([^"]*)" attribute set with a value of "([^"]*)"$/
Then /^I should see a "([^"]*)" element with the "([^"]*)" attribute which matches "(?P<regex>(?:[^"]|\\")*)"$/
Then /^I should see the element "(?P<element>[^"]*)" attribute "(?P<attribute>(?:[^"]|\\")*)" contain "(?P<value>(?:[^"]|\\")*)"$/
Then /^I should see "(?P<value>(?:[^"]|\\")*)" in all of the "(?P<element>[^"]*)" elements$/
Then /^I should see no more than "([^"]*)" "([^"]*)" elements within the "([^"]*)" element$/
Then I should see the following picture mappings for the :arg1 element

##### Matching specific attributes

Then /^the meta "(?P<attribute_key>[^"]*)" "(?P<attribute_value>[^"]*)" should contain "(?P<value>(?:[^"]|\\")*)"$/
Then /^the link "(?P<attribute_key>[^"]*)" "(?P<attribute_value>[^"]*)" should end with "(?P<value>(?:[^"]|\\")*)"$/
Then /^the meta "(?P<attribute_key>[^"]*)" "(?P<attribute_value>[^"]*)" should not contain "(?P<value>(?:[^"]|\\")*)"$/
Then /^an? "(?P<element>[^"]*)" element should not match "(?P<regex>(?:[^"]|\\")*)"$/
Then /^the response should match "(?P<regex>(?:[^"]|\\")*)"$/
Then /^the response should not match "(?P<regex>(?:[^"]|\\")*)"$/
Then /^the response should not contain the "([^"]*)" element$/
Then /^the "([^"]*)" element should contain the "([^"]*)" element$/
Then /^the "([^"]*)" element should not contain the "([^"]*)" element$/
Then /^the "([^"]*)" element should be before the "([^"]*)" element$/
Then /^the "([^"]*)" element should be after the "([^"]*)" element$/
Then /^the HTML should contain "([^"]*)"$/
Given /^the xhtml matches xpath "([^"]*)"$/
Then /^the HTML should contain the string \'([^\']*)\' exactly "([^"]*)" times$/
Then /^the "([^"]*)" element should contain match result "([^"]*)"$/
Then /^the inner html of the element "([^"]*)" should be empty$/
Then /^an? "(?P<element>[^"]*)" element should match "(?P<regex>(?:[^"]|\\")*)"$/


##### User interactions

When /^I click a link in any "([^"]*)" element$
When /^(?:|I )fill in the textarea "(?P<field>(?:[^"]|\\")*)" with:/
When /^I follow "([^"]*)" in the "([^"]*)" element$/
Given /^I fill hidden field "([^"]*)" with "([^"]*)"$/
Then /^I fill "([^"]*)" element with current date$/
Then /^I fill "([^"]*)" element with current time$/
Then I follow the rel :arg link
When /^I follow the "([^"]*)" stylesheet$/
Then /^store the result of "(?P<regex>(?:[^"]|\\")*)" for the element "(?P<selector>[^"]*)"$/
Then /^store the result of "(?P<regex>(?:[^"]|\\")*)" for the element "(?P<selector>[^"]*)" as "(?P<key>[^"]*)"$/


##### jsonld checks

Given /^a json-?ld "([^"]*)" value should match "(?P<regex>(?:[^"]|\\")*)"$/
Given /^the json ld value "([^"]*)" should match "([^"]*)"$/
Given /^a json value "([^"]*)" should match "([^"]*)"$/
Given /^a json value "([^"]*)" in the "([^"]*)" element should match "([^"]*)"$/


```

## JavaScript
```
DennisDigital\Behat\Mink\Context\JsContext:
  parameters:
    element_wait_timeout: (optional) {default:5}
    breakpoints: (optional)
      mobile: {default:380}
      desktop: {default:1090}
      narrow: {default:870}
      wide: {default:1205}
```
### Step Definitions
```gherkin

##### Wait 

Then I wait a maximum of :arg1 seconds for the :arg2 element
Then I wait for the :arg1 element
Then /^I wait for "([^"]*)" milliseconds for "([^"]*)"$/
Then /^I wait for "([^"]*)" milliseconds$/

##### User interactions

Then /^I click the "([^"]*)" element$/
Then I scroll to the bottom of the page
Given I press the :key key


##### Finding elements

Then the :arg1 array should contain no duplicates
Then /^the js "([^"]*)" element should contain "([^"]*)"$/
Then /^the "([^"]*)" javascript expression should be true$/
Then /^the javascript expression should be true:$/
Then /^the "([^"]*)" element should be visible$/
Then /^the "([^"]*)" element should be invisible$/
Then /^the "([^"]*)" element should have an attribute "([^"]*)" contain "([^"]*)"$/
Then /^the "([^"]*)" element should have an attribute "([^"]*)" of "([^"]*)"$/
Then /^the "([^"]*)" element should have css property "([^"]*)" of "([^"]*)"$/
Then /^the "([^"]*)" element should not have css property "([^"]*)" of "([^"]*)"$/
Then /^each "([^"]*)" should have css property "([^"]*)" of "([^"]*)"$/
Then /^each "([^"]*)" should not have css property "([^"]*)" of "([^"]*)"$/
Then /^"([^"]*)" should be revealed$/
Then /^"([^"]*)" should be unrevealed$/
Then /^each "([^"]*)" should have been given the "([^"]*)" class$/
Then /^each "([^"]*)" should not have the "([^"]*)" class$/

##### Breakpoint

Then /^I am in breakpoint "([^"]*)"$/
Then /^I am in breakpoint "([^"]*)" with height "([^"]*)"$/
Then /^I set the screen size to "([^"]*)" by "([^"]*)"$/

```

## JSON
```
DennisDigital\Behat\Mink\Context\JsonContext
```
### Step Definitions
```gherkin
Then /^the response should match json schema:/
Then /^the response should match json schema "([^"]*)"/
Given /^the json value "([^"]*)" matches "(?P<regex>(?:[^"]|\\")*)"$/
Given /^the json xhtml value "([^"]*)" matches xpath "([^"]*)"$/
Given /^the json xhtml value "([^"]*)" does not match xpath "([^"]*)"$/
```

## XML
```
DennisDigital\Behat\Mink\Context\XmlContext
```
### Step Definitions
```gherkin
Given /^the xml is valid$/
Given /^the xml matches xpath "([^"]*)"$/
Given /^the xml does not match xpath "([^"]*)"$/
```

## HTTP
```
DennisDigital\Behat\Mink\Context\HttpContext
```
### Step Definitions
```gherkin
Given /^I set the "([^"]*)" header to "([^"]*)"$/
Given I set the cookie :arg1 with :arg2
Then /^the full url should be "(?P<url>[^"]+)"$/
Then /^the url should not be "([^"]*)"$/
Then /^(?:|I )should not be on "(?P<page>[^"]+)"$/
Given /^I am on "([^"]*)" without redirection$/
Then /^the header "([^"]*)" should be "([^"]*)"$/
Then /^the header "([^"]*)" should match "([^"]*)"$/
```