Feature: User view/filter entities and upcoming events in the system on FOSSMap
  In order to view/filter entities and upcoming events in the system on FOSSMap
  As a user
  I need to navigate to FOSSMap Page and view/filter all entities and upcoming events in the system on FOSSMap

    @not_implemented
    Scenario: A user navigating to FOSSMap page
        Given I am on "/en/"
        When I follow "FOSSMap"
        Then I should be on "/en/foss-map/"
        And I should see "FOSS Map"

    @not_implemented
    Scenario: A user viewing active entities that have locations in the system on FOSSMap
        Given I am a logged in user with "espace" and "123456789"
        And I have created "profit" Entity location with "33.33" and "31.52"
        When I go to "/en/foss-map/"
        Then I should see a marker at "33.33" and "31.52"

    @not_implemented
    Scenario: A user viewing active-entities that have locations in the system on FOSSMap
        Given I am a logged in user with "espace" and "123456789"
        And I have created "profit" Entity location at "Louran"
        When I go to "/en/foss-map/"
        Then I should see a marker at "Louran"

    @not_implemented
    Scenario: A user viewing details of an entity by hovering over its marker on the map
        Given I am a logged in user with "espace" and "123456789"
        And I have created "profit" Entity location at "Louran"
        When I go to "/en/foss-map/"
        And I click on "Louran" marker
        Then entity pop-up should display "entity_title", "industry"

    @not_implemented
    Scenario: A user viewing upcoming events on FOSSMap
        Given I am a logged in user with "espace" and "123456789"
        And I have created "Forbes CIO Summit" event with venue location "43.33" and "32.57"
        When I go to "/en/foss-map/"
        Then I should see a marker at "43.33" and "32.57"

    @not_implemented
    Scenario: A user viewing upcoming-events on FOSSMap
        Given I am a logged in user with "espace" and "123456789"
        And I have created "Forbes CIO Summit" event with venue location at "Bibliotheque Alexandria"
        When I go to "/en/foss-map/"
        Then I should see a marker at "Bibliotheque Alexandria"

    @not_implemented
    Scenario: A user viewing details of an upcoming event by hovering over its marker on the map
        Given I am a logged in user with "espace" and "123456789"
        And I have created "Forbes CIO Summit" event with venue location at "Bibliotheque Alexandria"
        When I go to "/en/foss-map/"
        And I click on "Bibliotheque Alexandria" marker
        Then event pop-up should display "venue_name", "event_name", "start_date", "end_date"

    @not_implemented
    Scenario: A user seeing empty message after filtering event on FOSSMap
        Given I resize window with height 800 and width 2048 in px
        And I am a logged in user with "foss" and "F0$$"
        And I am on "/en/foss-map/"
        And I select "event" from "type_filter"
        And I select "healthcare" from "industry_filter"
        And I wait for 1 seconds
        Then I should see "There are no results for your search"

    @not_implemented
    Scenario: A user filtering FOSSMap with type
        Given I resize window with height 800 and width 2048 in px
        And I am on "/en/foss-map/"
        And I select "entity" from "type_filter"
        And I wait for 1 seconds
        Then I should see 2 markers

    @not_implemented
    Scenario: A user filtering FOSSMap with type and subtype
        Given I resize window with height 800 and width 2048 in px
        And I am on "/en/foss-map/"
        And I select "event" from "type_filter"
        And I select "FOSS days" from "subtype_filter"
        And I wait for 1 seconds
        Then I should see 3 markers

    @not_implemented
    Scenario: A user filtering FOSSMap with type and industry
        Given I resize window with height 800 and width 2048 in px
        And I am on "/en/foss-map/"
        And I select "event" from "type_filter"
        And I select "Accounting" from "industry_filter"
        And I wait for 1 seconds
        Then I should see 1 markers

    @not_implemented
    Scenario: A user filtering FOSSMap with type and technology
        Given I resize window with height 800 and width 2048 in px
        And I am on "/en/foss-map/"
        And I select "entity" from "type_filter"
        And I select "Java" from "technology_filter"
        And I wait for 1 seconds
        Then I should see 2 markers

    @not_implemented
    Scenario: A user filtering FOSSMap with type and interest
        Given I resize window with height 800 and width 2048 in px
        And I am on "/en/foss-map/"
        And I select "entity" from "type_filter"
        And I select "Python" from "interest_filter"
        And I wait for 1 seconds
        Then I should see 1 markers