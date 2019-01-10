Feature: User archive his published request in the request center in the system
  so that he/she doesn't receive more reponses
  As an User
  I want to archive my published request in the Request Center so that I don't receive more reponses

    @Done @add-requests
    Scenario: A not logged-in user navigating to request thread page
        Given I am on "/en/request-center/request-1"
        And I should not see "Archive" in the ".respond-btns" element

    @Done @javascript @add-requests
    Scenario: A logged-in user can't archive his pending request
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/request-center/request-4"
        And I should not see "Archive" in the ".respond-btns" element

    @Done @javascript @add-requests
    Scenario: A logged-in requester trying to archive my request
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/request-center/request-8"
        Then I should see "Archive"
        And I follow "Archive"
        And I wait for 2 seconds
        Then I should see "Archiving a request will prevent further responses and you can not undo this action."
        And I press "confirm-archive-request"
        And I wait for 5 seconds
        Then I must see "Request Archived Successfully" in ".request-archived"


    @Done @javascript @add-requests
    Scenario: A logged-in user navigating to an archived request page
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/request-center/request-8"
        Then I should see "foss"
        And I should see "Request is archived, no further responses"
