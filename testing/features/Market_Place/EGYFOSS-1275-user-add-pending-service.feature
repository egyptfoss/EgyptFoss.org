Feature: User adds service to the market place
  In order to add a service to the market place
  As an User
  I need to provide all attributes needed to add a service in the system

    @Done
    Scenario: A not logged-in user Adding new service to the system
        Given I am on "/en/marketplace/services/add"
        And I wait to be redirected
        Then I should be on "/en/login/?redirect_to=http%3A%2F%2Ffosstesting.espace.ws/%2Fen%2Fmarketplace%2Fservice%2Fadd%2F"
        And I should see "Please log in to suggest a new service"

    @Done @javascript
    Scenario: A logged-in user Adding new service to the system with valid inputs
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/marketplace/services/add"
        When I User Add new service with "new service title" and "mobile" and "prince" and "new service description" and "new service constraints" and "new service conditions"
        And I add "new techno" to a auto-select "technology"
        And I add "new interest" to a auto-select "interest"
        And I attach the file "testImages/logo.png" to "service_image" with relative path
        And I press "submit"
        Then I should see "added successfully"
       
    @Done
    Scenario: A logged-in user Adding new service with only required inputs
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/marketplace/services/add"
        When I User Add new service with "new service with required" and "mobile" and "" and "new service description" and "" and ""
        And I attach the file "testImages/logo.png" to "service_image" with relative path
        And I press "submit"
        Then I should see "added successfully"

    @javascript @Done
    Scenario: A logged-in user Adding new service with null inputs
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/marketplace/services/add"
        When I User Add new service with "" and "" and "" and "" and "" and ""
        And I press "submit"
        Then I should see "Title required"
        And I should see "Description required"
        And I should see "Category required"
        And I should see "Image required"

    @javascript @Done
    Scenario: A logged-in user Adding new service with numbers only in the textfields
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/marketplace/services/add"
        When I User Add new service with "1234567891" and "" and "" and "1234567891" and "1234567891" and "1234567891"
        And I press "submit"
        Then I should see "Title must include at least one letter"
        And I should see "Description must include at least one letter"
        And I should see "Constraints must include at least one letter"
        And I should see "Conditions must include at least one letter"

    @javascript @Done
    Scenario: A logged-in user Adding new service with special characters only in the textfields
        Given I am a logged in user with "espace" and "123456789"
         And I am on "/en/marketplace/services/add"
        When I User Add new service with ";;;;;;;;;;;" and "" and "" and ";;;;;;;;;;;" and ";;;;;;;;;;;" and ";;;;;;;;;;;"
        And I press "submit"
        Then I should see "Title must include at least one letter"
        And I should see "Description must include at least one letter"
        And I should see "Constraints must include at least one letter"
        And I should see "Conditions must include at least one letter"

    @javascript @Done
    Scenario: A logged-in user Adding new service to the system with invalid image
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/marketplace/services/add"
        When I User Add new service with "new service title" and "mobile" and "prince" and "new service description" and "new service constraints" and "new service conditions"
        And I attach the file "testImages/test-file.xml" to "service_image" with relative path
        And I press "submit"
        Then I should see "please enter correct image type"