Feature: User adds feedback to the system
  In order to add a feedback in the system
  As an User
  I need to provide all attributes needed to add a feedback in the system

    @Done
    Scenario: A not logged-in user Adding new feedback to the system
        Given I am on "/en/feedback/add"
        And I wait to be redirected
        Then I should be on "/en/login/?redirected=addfeedback&redirect_to=http://egyptfoss.com/en/add-feedback/"
        And I should see "Please log in to suggest a new feedback"

    @javascript @Done
    Scenario: A logged-in user Adding new feedback to the system with valid inputs
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/feedback/add"
        When I User Add new feedback with "new feedback title" and "new feedback description" and "news"
        And I press "submit"   
        Then I should see "added successfully"
       
    @Done
    Scenario: A logged-in user Adding new feedback with only required inputs
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/feedback/add"
        When I User Add new feedback with "new feedback with required" and "new feedback description" and "" 
        And I press "submit"
        Then I should see "added successfully"

    @javascript @Done
    Scenario: A logged-in user Adding new feedback with null inputs
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/feedback/add"
        When I User Add new feedback with "" and "" and "" 
        And I press "submit"
        Then I should see "Title required"
        And I should see "Content required"

    @javascript @Done
    Scenario: A logged-in user Adding new feedback with numbers only in the text fields
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/feedback/add"
        When I User Add new feedback with "123456789" and "123456789" and ""
        And I press "submit"
        Then I should see "Title Must includes at least one letter"
        And I should see "Content Must includes at least one letter"

    @javascript @Done
    Scenario: A logged-in user Adding new feedback with special characters only in the text fields
        Given I am a logged in user with "espace" and "123456789"
         And I am on "/en/feedback/add"
        When I User Add new feedback with ";;;;;;;" and ";;;;;;;;" and ""     
        And I press "submit"
        Then I should see "Title Must includes at least one letter"
        And I should see "Content Must includes at least one letter"
