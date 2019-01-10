Feature: User adds request center to the system
  In order to add a request center in the system
  As an User
  I need to provide all attributes needed to add a request center in the system

    @Done
    Scenario: A not logged-in user Adding new request center to the system
        Given I am on "/en/request-center/add"
        And I wait to be redirected
        Then I should be on "/en/login/?redirect_to=http%3A%2F%2Ffosstesting.espace.ws/%2Fen%2Frequest-center%2Fadd%2F"
        And I should see "Please log in to suggest a new request"

    @javascript @Done
    Scenario: A logged-in user Adding new request center to the system with valid inputs
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/request-center/add"
        When I User Add new request with "new request center title" and "business-relationship-request" and "commercial-agreement" and "theme1" and "new request center description" and "new request center requirements" and "new request center constraints" and "new techno" and "new interest" and "2016-05-05" 
        And I press "submit"   
        Then I should see "added successfully"
       
    @Done
    Scenario: A logged-in user Adding new request with only required inputs
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/request-center/add"
        When I User Add new request with "new request with required" and "business-relationship-request" and "commercial-agreement" and "" and "new request center description" and "" and "" and "" and "" and "" 
        And I press "submit"
        Then I should see "added successfully"

    @javascript @Done
    Scenario: A logged-in user Adding new request with null inputs
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/request-center/add"
        When I User Add new request with "" and "" and "" and "" and "" and "" and "" and "" and "" and "" 
        And I press "submit"
        Then I should see "Title required"
        And I should see "Description required"
        And I should see "Type required"
        And I should see "Target business relationship required"

    @javascript @Done
    Scenario: A logged-in user Adding new request with numbers only in the textfields
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/request-center/add"
        When I User Add new request with "123456789" and "" and "" and "" and "123456789" and "" and "" and "" and "" and ""     
        And I press "submit"
        Then I should see "Title must include at least one letter"
        And I should see "Description must include at least one letter"

    @javascript @Done
    Scenario: A logged-in user Adding new request with special characters only in the textfields
        Given I am a logged in user with "espace" and "123456789"
         And I am on "/en/request-center/add"
        When I User Add new request with ";;;;;;;" and "" and "" and "" and ";;;;;;;;" and "" and "" and "" and "" and ""     
        And I press "submit"
        Then I should see "Title must include at least one letter"
        And I should see "Description must include at least one letter"