Feature: User edit his request in the system
  In order to edit his request in the system
  As an User
  I need to be able to edit all request attributes

    @Done @add-requests
    Scenario: A logged-in user editing his pending request to the system with valid inputs
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/request-center/request-5/"
        When I follow "Edit"
        And I User Add new request with "request-5" and "business-relationship-request" and "commercial-agreement" and "theme1" and "new request center description" and "new request center requirements" and "new request center constraints" and "new techno" and "new interest" and "2016-05-05" 
        And I press "submit"
        Then I should see "Request edited successfully"
       
    @Done
    Scenario: A logged-in user editing his pending request with only required inputs
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/request-center/request-5-2/"
        When I follow "Edit"
        And I User Add new request with "new request with required" and "business-relationship-request" and "commercial-agreement" and "" and "new request center description" and "" and "" and "" and "" and "" 
        And I press "submit"
        Then I should see "Request edited successfully"

    @javascript @Done
    Scenario: A logged-in user editing request with null inputs
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/request-center/new-request-with-required/"
        When I follow "Edit"
        And I User Add new request with "" and "" and "" and "" and "" and "" and "" and "" and "" and "" 
        And I press "submit"
        Then I should see "Title required"

    @javascript @Done
    Scenario: A logged-in user editing his request with numbers only in the textfields
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/request-center/new-request-with-required/"
        When I follow "Edit"
        And I User Add new request with "123" and "" and "" and "" and "123" and "123" and "" and "" and "" and "" 
        Then I should see "Title must include at least one letter"

    @javascript @Done
    Scenario: A logged-in user editing request with special characters only in the textfields
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/request-center/new-request-with-required/"
        When I follow "Edit"
        And I User Add new request with ";;;" and "" and "" and "" and ";;;" and ";;;;" and "" and "" and "" and "" 
        Then I should see "Title must include at least one letter"

