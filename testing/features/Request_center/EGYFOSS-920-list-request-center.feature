Feature: User view list of all requests in request center in the system
  In order to list all requests in the system
  As an User
  I need to be able to navigate to open datasets list page and load more open datasets

    @Done
    Scenario: A not logged-in user navigating to request center list
        Given I am on "/en/request-center/"
        And I should see "Welcome To Requests Center"

    @Done @set-empty-list-request-center @return-list-request-center
    Scenario: A not logged-in user seeing an empty request center list
        Given I am on "/en/request-center/"
        Then I should see "There are no Requests in Request Center"

    @Done @javascript @set-empty-list-request-center @return-list-request-center
    Scenario: A logged-in user seeing an empty request center list
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/request-center/"
        Then I should see "There are no Requests in Request Center"

    @Done @add-requests
    Scenario: A not logged-in user viewing 10 request center per page
        Given I am on "/en/request-center/"
        Then I should see 10 ".request-card" elements
        And I should see "Show more"

    @Done @javascript @add-requests
    Scenario: A not logged-in user loading more request center
        Given I am on "/en/request-center/"
        When I follow "Show more"
        And I wait for 5 seconds
        Then I should see more "10" or more ".request-card" elements
        And I should not see "Show more"

    @Done
    Scenario: A not logged-in user viewing request center fields
        Given I am on "/en/request-center/"
        Then "thumb-side" should be visible
        And "card-summary" should be visible

    @Done
    Scenario: A logged-in user viewing 10 request center per page
        Given I am a logged in user with "espace" and "123456789"
        Given I am on "/en/request-center/"
        Then I should see 10 ".request-card" elements
        And I should see "Show more"

    @javascript @Done
    Scenario: A logged-in user loading more request center
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/request-center/"
        When I follow "Show more"
        And I wait for 5 seconds
        Then I should see more "10" or more ".request-card" elements
        And I should not see "Show more"

    @Done
    Scenario: A logged-in user viewing request center fields
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/request-center/"
        Then "thumb-side" should be visible
        And "card-summary" should be visible

    @Done
    Scenario: A logged-in user viewing request center types
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/request-center/"
        Then "type-list" should be visible
        And I should see "business relationship request"

    @javascript @Done
    Scenario: A user seeing empty list after filtering
        Given I resize window with height 800 and width 2048 in px
        And I am a logged in user with "foss" and "F0$$"
        And I am on "/en/request-center/?type=service request"
        And I wait for 1 seconds
        Then I should see "There are no Requests in Request Center"

    @javascript @Done @add-requests
    Scenario: A user filtering the request center list with type
        Given I resize window with height 800 and width 2048 in px
        And I am on "/en/request-center/"
        And I follow "dataset request"
        And I wait for 1 seconds
        Then I should see 10 ".request-card" elements
        And I should be on "/en/request-center/?type=dataset request"

    @javascript @Done @add-requests
    Scenario: A user filtering the request center list with theme
        Given I resize window with height 800 and width 2048 in px
        And I am on "/en/request-center/"
        And I follow "dataset request"
        And I click on the element with css selector "div.type-filter"
        And I click on the element with css selector "option:contains('prince')"
        Then I should see 10 ".request-card" elements
        And I should be on "/en/request-center/?type=dataset request&theme=prince"



