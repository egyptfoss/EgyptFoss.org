Feature: User archive his published service in the Marketplace so that he doesn't receive more requests
  In order to archive his published service in the Marketplace
  As a user
  I need to navigate to service page and archive from inside

    @Done @add-services
    Scenario: A user archiving his pending service
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/marketplace/services/service-4"
        And I should not see an "a.archive-request-button" element
        And I should see an ".btn-light" element

    @Done @javascript
    Scenario: A user archiving his published service
        Given I resize window with height 800 and width 2048 in px
        And I am a logged in user with "foss" and "F0$$"
        And I am on "/en/marketplace/services/service-14"
        And I click on the element with css selector "a.archive-request-button"
        And I wait for 2 seconds
        Then I should see "Archiving a service will prevent further requests and you can not undo this action."
        When I press "confirm-archive-request"
        And I wait for 5 seconds
        Then I should see "Service Archived Successfully"

    @Done
    Scenario: A user sees a warning message in his archived service and can't archive it again
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/marketplace/services/service-14"
        Then I should see "Service is archived, no further requests"
        And I should not see an "a.archive-request-button" element
        And I should not see an "div.respond-btns" element

    @Done
    Scenario: A not logged-in user can't see archive button in published service page
        Given I am on "/en/marketplace/services/service-1"
        And I should not see an "a.archive-request-button" element
        And I should not see an "div.respond-btns" element

    @Done @javascript
    Scenario: A logged-in user sees archived service message and can't request an archived request
        Given I am a logged in user with "maii.test" and "123456789"
        And I am on "/en/marketplace/services/service-14"
        Then I should see "Service is archived, no further requests"
        When I follow "Request Service"
        And I wait for 2 seconds
        Then I should see "Service is archived, no further requests"