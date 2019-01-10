Feature: User list his services in his profile, each with category and status
  In order to list his services in his profile, each with category and status
  As an User
  I need to be able to navigate to services contributions list page and load more contributions

    @Done
    Scenario: A logged-in user navigating to services contributions list
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/"
        When I follow "Services"
        And I wait to be redirected
        Then I should be on "/en/members/foss/services/"

    @Done
    Scenario: A logged-in user navigating to his empty services Contributions list
        Given I am a logged in user with "nour.tarek" and "FOSS123"
        And I am on "/en/members/nour-tarek/services/"
        Then I should see "There are no services added by nour.tarek"

    @Done
    Scenario: A logged-in user navigating to Empty requests/responses Contributions list
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/nour-tarek/services/"
        Then I should see "There are no services added by nour.tarek"

    @Done  @add-services
    Scenario: A logged-in user viewing service title, category and rate in the service card
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/services/"
        Then "service-panel-content" should be visible
        And "rating-stars" should be visible
        And "technology-tag" should be visible
        And "service-cover" should be visible

    @Done
    Scenario: A logged-in user viewing Pending services
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/services/"
        Then "service-panel-content" should be visible
        And "technology-tag" should be visible
        And "pending-approval" should be visible

    @Done @add-services
    Scenario: A logged-in user viewing 10 service contributions per page
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/services/"
        Then I should see 10 ".service-panel" elements
        And I should see "Show more"

    @javascript @Done @add-services
    Scenario: A logged-in user loading more service contribution in my profile
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/services/"
        When I follow "Show more"
        And I wait for 7 seconds
        Then I should see more "10" or more ".service-panel" elements
        When I follow "Show more"
        And I wait for 7 seconds
        And I should not see "Show more"

    @Done
    Scenario: A Not logged-in user should not view Pending services
        Given I am on "/en/members/foss/services/"
        Then "service-panel-content" should be visible
        And "rating-stars" should be visible
        And "technology-tag" should be visible
        And "service-cover" should be visible
        And I should not see "pending-approval"

    @Done
    Scenario: A logged-in user viewing other profile services contributions
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/foss/services/"
        Then "service-panel-content" should be visible
        And "rating-stars" should be visible
        And "technology-tag" should be visible
        And "service-cover" should be visible
        And I should not see "pending-approval"

    @Done
    Scenario: A logged-in user viewing his profile services tabs
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/services/"
        Then "service-panel-content" should be visible
        And "rating-stars" should be visible
        And "technology-tag" should be visible
        And "service-cover" should be visible
        And I should see "Requests"
        And I should see "Services"

    @Done
    Scenario: A Not logged-in user should not view tabs requests/services
        Given I am on "/en/members/foss/services/"
        Then "service-panel-content" should be visible
        And "rating-stars" should be visible
        And "technology-tag" should be visible
        And "service-cover" should be visible
        And I should not see "pending-approval"
        And I should not see "Requests"

    @Done
    Scenario: A logged-in user viewing other profile contributions should not view tabs requests/repsonses
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/foss/services/"
        Then "service-panel-content" should be visible
        And "rating-stars" should be visible
        And "technology-tag" should be visible
        And "service-cover" should be visible
        And I should not see "pending-approval"
        And I should not see "Requests"

    @Done @add-services
    Scenario: A logged-in user viewing his profile requests contributions
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/services/"
        And take screenshot
        Then "service-panel-content" should be visible
        And "rating-stars" should be visible
        And "technology-tag" should be visible
        And "service-cover" should be visible
        And I should see "Requests"
        And I should see "Services"

    @Done @add-services @javascript
    Scenario: A logged-in user view his reponses on his profile
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/services/"
        When I click on the element with css selector ".chng-email"
        Then I should see more "2" or more ".service-panel" elements
        And I should not see "Show more"