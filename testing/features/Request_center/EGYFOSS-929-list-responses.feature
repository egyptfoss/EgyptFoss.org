Feature: Requester view list of responses to my request in the request center in the system
  In order to view list of responses
  As an User
  I want to list read/unread reponses on my request in the Request Center so that I can communicate with them or provide more information

    @Done @javascript @add-requests
    Scenario: A logged-in requester trying to list my responses
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/request-center/request-14"
        And I follow "Responses"
        And I wait to be redirected
        Then I should see "espace"
        Then I should see "Responses"

    @Done @javascript @add-requests
    Scenario: A logged-in user trying to list thread responses
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/request-center/request-14"
        And I follow "Check your response"
        And I wait to be redirected
        Then I should see "foss"
        Then I should not see "Responses"
