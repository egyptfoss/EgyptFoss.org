Feature: User archive a request on his service in the Marketplace so that he can remove inappropriate requests
  In order to archive a request on his service in the Marketplace
  As a user
  I need to navigate to requests page and archive from inside

    
    @Done @javascript @add-services
    Scenario: A requester can't archive his request on a service
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/marketplace/services/service-30"
        When I click on the element with css selector "a.btn-block"
        And I wait for 2 seconds
        And take screenshot
        Then I should see "espace"
        And I should not see an "#archive-thread-button" element

    @Done @javascript
    Scenario: A service provider archiving a request on his published service
        Given I resize window with height 800 and width 2048 in px
        And I am a logged in user with "foss" and "F0$$"
        And I am on "/en/marketplace/services/service-30"
        And I click on the element with css selector "a.btn-block"
        And I wait for 2 seconds
        Then I should see "espace"
        And I click on the element with css selector "a.archive-thread-button"
        And I wait for 2 seconds
        Then I should see "Archiving a request will prevent further replies and you can not undo this action."
        When I press "confirm-archive-thread"
        And I wait for 5 seconds
        Then I should see "Request Archived Successfully"

    @Done @javascript
    Scenario: A service provider sees a warning message in the archived request and can't archive it again
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/marketplace/services/service-30"
        And I click on the element with css selector "a.btn-block"
        And I wait for 2 seconds
        Then I should see "espace"
        And I should see "Request is archived, no further replies"
        And I should not see an "#conv-compose" element

    @Done @javascript
    Scenario: A requester sees his archived request message and can't reply on an archived request
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/marketplace/services/service-30"
        When I click on the element with css selector "a.btn-block"
        And I wait for 5 seconds
        Then I should see "espace"
        And I should see "Request is archived, no further replies"
        And I should not see an "#conv-compose" element