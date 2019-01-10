Feature: User archive a reponse on his request in the request center in the system
  In order to remove inappropriate reponses
  As an User
  I want to archive a reponse on my request in the Request Center so that I can remove inappropriate reponses

    @Done @add-requests
    Scenario: A not logged-in user navigating to request thread page
        Given I am on "/en/request-center/request-1"
        When I follow "Respond to request"
        Then I should be on "/en/login/?redirected=respondtorequest"
        And I should see "Please log in to respond to a request"

    @Done @javascript
    Scenario: A logged-in user navigating to request thread page, can't archive this request
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/request-center/request-3"
        And I follow "Respond to request"
        And I wait to be redirected
        Then I should see "foss"
        And I should not see "Archive"
        When I fill in "message" with "hey foss"
        And I press "submit_response"
        And I wait for 9 seconds
        Then I should see "hey foss"        

    @Done @javascript
    Scenario: A logged-in requester trying to archive my request
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/request-center/request-3"
        And I follow "Responses"
        And I wait to be redirected
        Then I should see "espace"
        And I follow "Archive"
        And I wait for 2 seconds
        And I press "confirm-archive-thread"
        And I wait for 5 seconds
        Then I must see "Response Archived Successfully" in ".thread-archived"

    @Done @javascript
    Scenario: A logged-in user navigating to request archived thread page
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/request-center/request-3"
        And I follow "Check your response"
        And I wait to be redirected
        Then I should see "foss"
        And I should see "Response is archived, no further replies"
        