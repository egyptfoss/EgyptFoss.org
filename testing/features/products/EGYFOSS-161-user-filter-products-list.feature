Feature: User filter list of all published products in the system
  In order to filter all published product in the system
  As an User
  I need to be able to navigate to products list page and filter them with different filters

    @javascript @not_working
    Scenario: A user seeing empty list after filtering
        Given I resize window with height 800 and width 2048 in px
        And I am a logged in user with "foss" and "F0$$"
        And I am on "/en/products/?industry=Development"
        And I wait for 1 seconds
        And I click on the element with css selector "div.type-filter"
        And I click on the element with css selector "li:contains('empty-type')"
        And I wait for 1 seconds
        Then I should see "There are no products yet, Suggest Product"
        And I should be on "/en/products/"
        And I click on the element with css selector ".noProductsFound a"
        And I wait to be redirected
        Then I should be on "/en/products/add/"
        And I should see "Suggest Product"

    @javascript @not_working
    Scenario: A user filtering the product list with industry
        Given I resize window with height 800 and width 2048 in px
        And I am on "/en/products"
        And I follow "software-engineering"
        And I wait for 1 seconds
        Then I should see 10 ".product-card" elements
        And I should be on "/en/products/?industry=Software Engineering"

    @javascript @not_working
    Scenario: A user filtering the product list with type
        Given I resize window with height 800 and width 2048 in px
        And I am on "/en/products"
        And I follow "software-engineering"
        And I click on the element with css selector "div.type-filter"
        And I click on the element with css selector "li:contains('application')"
        Then I should see 10 ".product-card" elements
        And I should be on "/en/products/?type=application"

    @javascript @not_working
    Scenario: A user filtering the product list with platform
        Given I resize window with height 800 and width 2048 in px
        And I am on "/en/products"
        When I follow "software-engineering"
        And I wait for 1 seconds
        And I click on the element with css selector "div.platform-filter"
        And I click on the element with css selector "li:contains('linux')"
        And I wait for 1 seconds
        Then I should see 10 ".product-card" elements
        And I should be on "/en/products/?platform=linux"

    @javascript @not_working
    Scenario: A user filtering the product list with technology
        Given I resize window with height 800 and width 2048 in px
        And I am on "/en/products"
        When I follow "software-engineering"
        And I click on the element with css selector "div.technology-filter"
        And I click on the element with css selector "li:contains('java')"
        And I wait for 1 seconds
        Then I should see 10 ".product-card" elements
        And I should be on "/en/products/?technology=java"

    @javascript @not_working
    Scenario: A user filtering the product list with license
        Given I resize window with height 800 and width 2048 in px
        And I am on "/en/products"
        When I follow "software-engineering"
        And I click on the element with css selector "div.license-filter"
        And I click on the element with css selector "li:contains('MIT')"
        And I wait for 1 seconds
        Then I should see 10 ".product-card" elements
        And I should see "MIT" in the ".product-card" element
        And I should be on "/en/products/?license=MIT"

    @javascript @not_working
    Scenario: A user filtering the product list with industry, type, paltform, technology and license
        Given I resize window with height 800 and width 2048 in px
        And I am on "/en/products"
        When I follow "software-engineering"
        And I click on the element with css selector "div.license-filter"
        And I click on the element with css selector "li:contains('MIT')"
        And I click on the element with css selector "div.technology-filter"
        And I click on the element with css selector "li:contains('java')"
        And I click on the element with css selector "div.platform-filter"
        And I click on the element with css selector "li:contains('linux')"
        And I click on the element with css selector "div.type-filter"
        And I click on the element with css selector "li:contains('application')"
        Then I should see 10 ".product-card" elements
        And I should see "MIT" in the ".product-card" element
        And I should be on "/en/products/?type=application&license=MIT&platform=linux&technology=java&industry=software-engineering"

    @javascript @Done
    Scenario: A user resetting the filteration in product list
        Given I resize window with height 800 and width 2048 in px
        And I am on "/en/products/?type=application&license=MIT&platform=linux&technology=java&industry=software-engineering"
        And I wait for 1 seconds
        When I follow "software-engineering"
        When I click on the element with css selector "button.reset-filters"
        Then I should be on "/en/products/?industry=software-engineering"

    @javascript @Done
    Scenario: A logged-in user saving the product filteration criteria from last time automatically
        Given I resize window with height 800 and width 2048 in px
        And I am a logged in user with "foss" and "F0$$"
        And I am on "/en/products/?type=application&platform=linux"
        When I go to "/en/members/foss/profile"
        And I go to "/en/wiki/FOSSPedia"
        And I go to "/en/products"
        Then I should be on "/en/products/?type=application&platform=linux"

    @javascript @working
    Scenario: A user filtering the product list with industry and results excced 10 products
        Given I resize window with height 800 and width 2048 in px
        And I am on "/en/products"
        When I follow "software-engineering"
        And I wait for 1 seconds
        And I click on the element with css selector "div.type-filter"
        And I click on the element with xpath "//*[@id="select2-0w8v-result-2xjd-29"]"
        Then I should see 10 ".product-card" elements
        And I should be on "/en/products/?type=application"
        And I should see "Show more"