Feature: User submit a request on a service in the marketplace so that he can request more details and send an email to the provider
  In order to submit a request on a service in the marketplace
  As a user
  I need to navigate to service page and request it from inside

    @Done @javascript @add-services
    Scenario: A service's owner can't initiate a thread request by himself
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/marketplace/services/service-1/"
        When I follow "Requests"
        And I wait for 3 seconds
        Then I should be on "/en/marketplace/services/service-1/"    
        And the response should contain "The service has no requests"

    @Done @javascript
    Scenario: A logged-in user navigate to request form for a published service for the first time
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/marketplace/services/service-1/"
        When I follow "Request Service"
        And I wait for 3 seconds
        Then I should be on "/en/service-thread/?pid=205"
        And the response should contain "Start conversation by writing your request below"
        And the response should contain "View Full Service Details"

    @Done @javascript
    Scenario: A logged-in user can't send empty request on a service 
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/marketplace/services/service-1/"
        And take screenshot
        When I follow "Request Service"
        And I wait for 3 seconds
        And I fill in "message" with ""
        And I press "submit_response"
        Then I should see "This field is required"

    @Done @javascript
    Scenario: A logged-in user can't send special characters only as a request on a service 
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/marketplace/services/service-1/"
        When I follow "Request Service"
        And I wait for 3 seconds
        And I fill in "message" with ",,,,,,,,,,,,,,"
        And I press "submit_response"
        Then I should see "Must at least contain one letter"

    @Done @javascript
    Scenario: A logged-in user can't send numbers only as a request on a service 
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/marketplace/services/service-1/"
        When I follow "Request Service"
        And I wait for 3 seconds
        And I fill in "message" with "654126512"
        And I press "submit_response"
        Then I should see "Must at least contain one letter"

    @Done @javascript
    Scenario: A logged-in user initiate a communication thread with service provider
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/marketplace/services/service-1/"
        When I follow "Request Service"
        And I wait for 3 seconds
        And I fill in "message" with "Good Morning Mr. Foss, please provide more details about the service"
        And I press "submit_response"
        And I wait for 3 seconds
        Then I should see "Me :Good Morning Mr. Foss, please provide more details about the service"
        And I should receive an email with subject "You have got a new reply on service-1 service"
    
    @Done @javascript
    Scenario: A user archiving his published service
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/marketplace/services/service-2/"
        Then I should see "Archive"
        And I follow "Archive"
        And I wait for 2 seconds
        Then I should see "Archiving a service will prevent further requests and you can not undo this action."
        And I press "confirm-archive-request"
        And I wait for 5 seconds
        Then I should see "Service Archived Successfully"

    @Done @javascript
    Scenario: A logged-in user request an archived service for the first time
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/marketplace/services/service-2/"
        When I follow "Request Service"
        And I wait for 3 seconds
        Then I should be on "/en/marketplace/services/service-2/"
        And the response should contain "Service is archived, no further requests"

    @pending @javascript
    Scenario: A logged-in user gets 404 error page when requesting a pending service
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/service-thread/?pid=208"
        Then I should see "Oops! That page can’t be found."