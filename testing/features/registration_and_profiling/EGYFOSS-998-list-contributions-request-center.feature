Feature: User view list of all requests/responses contributions in the system
  In order to list all requests/responses contributions in the system
  As an User
  I need to be able to navigate to requests/responses contributions list page and load more contributions

    @Done @add-requests
    Scenario: A logged-in user navigating to requests/responses Contributions list
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/contributions/"
        When I follow "request-center"
        And I wait to be redirected
        Then I should be on "/en/members/foss/contributions/request-center/"

    @Done
    Scenario: A logged-in user navigating to Empty requests/responses Contributions list
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/espace/contributions/request-center/"
        Then I should see "There are no requests added by espace"

    @Done
    Scenario: A logged-in user navigating to Empty requests/responses Contributions list
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/contributions/request-center/"
        Then I should see "There are no responses by foss"

    @Done
    Scenario: A logged-in user viewing requests title and date in the requests card
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/contributions/request-center/"
        Then "product-info" should be visible
        And "fa-clock-o" should be visible

    @Done
    Scenario: A logged-in user viewing Pending requests
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/contributions/request-center/"
        Then "product-info" should be visible
        And "fa-clock-o" should be visible
        And "pending-approval" should be visible

    @Done
    Scenario: A logged-in user viewing 20 requests contributions per page
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/contributions/request-center/"
        Then I should see 20 ".profile-card" elements
        And I should see "Show more"

    @javascript @Done
    Scenario: A logged-in user loading more requests contribution in my profile
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/contributions/request-center/"
        When I follow "Show more"
        And I wait for 7 seconds
        Then I should see more "20" or more ".profile-card" elements
        And I should not see "Show more"

    @Done
    Scenario: A Not logged-in user should not view Pending requests
        Given I am on "/en/members/foss/contributions/request-center/"
        Then "product-info" should be visible
        And "fa-clock-o" should be visible
        And I should not see "pending-approval"

    @Done
    Scenario: A logged-in user viewing other profile contributions
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/foss/contributions/request-center/"
        Then "product-info" should be visible
        And "fa-clock-o" should be visible
        And I should not see "pending-approval"

    @Done
    Scenario: A logged-in user viewing his profile requests/responses tabs
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/contributions/request-center/"
        Then "product-info" should be visible
        And "fa-clock-o" should be visible
        And "pending-approval" should be visible
        And I should see "Requests"
        And I should see "Responses"

    @Done
    Scenario: A Not logged-in user should not view tabs requests/repsonses
        Given I am on "/en/members/foss/contributions/request-center/"
        Then "product-info" should be visible
        And "fa-clock-o" should be visible
        And I should not see "pending-approval"
        And I should not see "Responses"

    @Done
    Scenario: A logged-in user viewing other profile contributions should not view tabs requests/repsonses
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/foss/contributions/request-center/"
        Then "product-info" should be visible
        And "fa-clock-o" should be visible
        And I should not see "pending-approval"
        And I should not see "Responses"

    @Done
    Scenario: A logged-in user viewing his profile responses contributions
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/contributions/request-center/"
        Then "fa-clock-o" should be visible
        And I should not see "pending-approval"
        And I should see "Requests"
        And I should see "Responses"

    @Done
    Scenario: A logged-in user view his reponses on his profile
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/contributions/request-center/"
        Then I should see more "2" or more ".profile-card" elements
        And I should not see "Show more"