Feature: User update his service, regardless status
  In order to update his service, regardless status
  As an User
  I need to provide all attributes needed to edit my service in the system
    
    @Done @javascript @add-services
    Scenario: A user navigating to edit page of his pending service in the system
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/marketplace/services/service-4/"
        When I follow "Edit"
        Then I should see "Edit Service"
        And the response should contain "service-4"

    @Done @javascript
    Scenario: A user editing his pending approval service to the system with valid inputs
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/marketplace/services/service-4/"
        When I follow "Edit"
        And I wait for 3 seconds
        And I User Add new service with "Edited Service-4" and "web" and "hii" and "new service description" and "new service constraints" and "new service conditions"
        And I add "edit techno" to a auto-select "technology"
        And I add "edit interest" to a auto-select "interest"
        And I attach the file "testImages/logo.png" to "service_image" with relative path
        And I press "submit"
        Then I should see "Service edited successfully"
        And the response should contain "Edited Service-4"

    @Done @javascript
    Scenario: A user editing his published service to the system with valid inputs
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/marketplace/services/service-1/"
        When I follow "Edit"
        And I wait for 3 seconds
        And I User Add new service with "Edited Service-1" and "web" and "hii" and "new service description" and "new service constraints" and "new service conditions"
        And I add "edit techno" to a auto-select "technology"
        And I add "edit interest" to a auto-select "interest"
        And I attach the file "testImages/logo.png" to "service_image" with relative path
        And I press "submit"
        Then I should see "Service edited successfully"
        And I should be on "/en/marketplace/services/service-1/"
        And the response should contain "Edited Service-1"

    @Done
    Scenario: A user getting 404 error when he edit other published service
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/marketplace/services/service-1/"
        When I visit post name "service-1" with post type "service" in "marketplace"
        Then I should see "Oops! That page can’t be found."

    @Done @javascript
    Scenario: A user editing his service with only required inputs
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/marketplace/services/service-1/"
        When I follow "Edit"
        And I wait for 3 seconds
        And I User Add new service with "Edited service with required" and "mobile" and "" and "Edited service description with required" and "" and ""
        And I attach the file "testImages/logo.png" to "service_image" with relative path
        And I press "submit"
        Then I should see "Service edited successfully"
        And the response should contain "Edited service with required"
        And I should not see "hii"
        And I should not see "new service constraints"
        And I should not see "new service conditions"


    @javascript @Done
    Scenario: A user editing his service with null inputs
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/marketplace/services/service-1/"
        When I follow "Edit"
        And I wait for 3 seconds
        And I User Add new service with "" and "" and "" and "" and "" and ""
        And I press "submit"
        Then I should see "Title required"
        And I should see "Description required"
        And I should see "Category required"

    @javascript @Done
    Scenario: A user editing his service with numbers only in the textfields
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/marketplace/services/service-1/"
        When I follow "Edit"
        And I wait for 3 seconds
        And I User Add new service with "1234567891" and "" and "" and "1234567891" and "1234567891" and "1234567891"
        And I press "submit"
        Then I should see "Title must include at least one letter"
        And I should see "Description must include at least one letter"
        And I should see "Constraints must include at least one letter"
        And I should see "Conditions must include at least one letter"

    @javascript @Done
    Scenario: A user editing his service with special characters only in the textfields
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/marketplace/services/service-1/"
        When I follow "Edit"
        And I wait for 3 seconds
        And I User Add new service with ";;;;;;;;;;;" and "" and "" and ";;;;;;;;;;;" and ";;;;;;;;;;;" and ";;;;;;;;;;;"
        And I press "submit"
        Then I should see "Title must include at least one letter"
        And I should see "Description must include at least one letter"
        And I should see "Constraints must include at least one letter"
        And I should see "Conditions must include at least one letter"

    @javascript @Done
    Scenario: A user editing his service with invalid image
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/marketplace/services/service-1/"
        When I follow "Edit"
        And I wait for 3 seconds
        And I User Add new service with "new service title" and "mobile" and "prince" and "new service description" and "new service constraints" and "new service conditions"
        And I attach the file "testImages/test-file.xml" to "service_image" with relative path
        And I press "submit"
        Then I should see "please enter correct image type"

