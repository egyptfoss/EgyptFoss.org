Feature: User filter marketplace services by category, provider type, provider subtype, theme, technology
  In order to filter marketplace services by category, provider type, provider subtype, theme, technology
  As an User
  I need to be able to navigate to services list page and filter them with different filters

    @javascript @Done @add-services
    Scenario: A user seeing empty services list after filtering
        Given I resize window with height 800 and width 2048 in px
        And I am a logged in user with "foss" and "F0$$"
        And I am on "/en/marketplace/services/?category=all&theme=hii"
        And I wait for ajax return
        Then I should see "There are no services under this criteria, please try different filters."

    @javascript @Done
    Scenario: A user filtering the services list with theme
        Given I resize window with height 800 and width 2048 in px
        And I am on "/en/marketplace/services/"
        And I select "prince" from "service-theme"
        And I wait for 3 seconds
        Then I should see more "1" or more ".service-card" elements
        And I should be on "/en/marketplace/services/?theme=prince"

    @javascript @Done
    Scenario: A user filtering the services list with technology
        Given I resize window with height 800 and width 2048 in px
        And I am on "/en/marketplace/services/"
        And I select "python" from "service-technology"
        And I wait for 3 seconds
        Then I should see 1 ".service-card" elements
        And I should be on "/en/marketplace/services/?technology=python"
     
    @javascript @Done
    Scenario: A user filtering the services list with type
        Given I resize window with height 800 and width 2048 in px
        And I am on "/en/marketplace/services/"
        And I select "Individual" from "service-type"
        And I wait for 3 seconds
        Then I should see more "1" or more ".service-card" elements
        And I should be on "/en/marketplace/services/?type=Individual"

    @javascript @Done
    Scenario: A user filtering the services list with type and sub-type
        Given I resize window with height 800 and width 2048 in px
        And I am on "/en/marketplace/services/"
        And I select "Individual" from "service-type"
        And I select "Business Owner" from "service-subtype"
        And I wait for 3 seconds
        Then I should see more "1" or more ".service-card" elements
        And I should be on "/en/marketplace/services/?type=Individual&subtype=user"

    @javascript @Done
    Scenario: A user resetting the filteration in services list
        Given I resize window with height 800 and width 2048 in px
        And I am on "/en/marketplace/services/?technology=python&theme=prince&type=Individual&subtype=user"
        And I wait for 3 seconds
        When I click on the element with css selector "button.reset-filters"
        Then I should be on "/en/marketplace/services/?category=all"