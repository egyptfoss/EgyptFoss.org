Feature: User view a single request in the request center in the system
  In order to respond to this resquest
  As an User
  I want to respond to a request in the Request Center so that I can express my interest or request more information

    @Done @add-requests
    Scenario: A not logged-in user trying to responding to a request
        Given I am on "/en/request-center/request-1"
        When I follow "Respond to request"
        Then I should be on "/en/login/?redirected=respondtorequest"
        And I should see "Please log in to respond to a request"

    @Done @javascript @add-requests
    Scenario: A logged-in requester trying to list my empty responses
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/request-center/request-1"
        And I click on "Responses"
        And I am on "/en/request-center/request-1"

    @Done @javascript @add-requests
    Scenario: A logged-in user trying to respond to a request
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/request-center/request-1"
        And I follow "Respond to request"
        And I wait to be redirected
        Then I should see "foss"
        When I fill in "message" with "hi foss"
        And I press "submit_response"
        And I wait for 2 seconds
        Then I should see "hi foss"

    @Done @javascript @add-requests
    Scenario: A logged-in user trying to check their response and reply to it
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/request-center/request-1"
        And I follow "Check your response"
        And I wait to be redirected
        Then I should see "foss"
        When I fill in "message" with "here?"
        And I press "submit_response"
        And I wait for 2 seconds
        Then I should see "here?"

    @Done @javascript @add-requests
    Scenario: A logged-in requester trying to reply to one of my responses
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/request-center/request-1"
        And I follow "Responses"
        And I wait to be redirected
        Then I should see "espace"
        When I fill in "message" with "yes I am here"
        And I press "submit_response"
        And I wait for 2 seconds
        Then I should see "yes I am here"