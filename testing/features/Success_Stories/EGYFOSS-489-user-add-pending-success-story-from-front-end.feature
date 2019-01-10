Feature: User adds success story to the system
  In order to add a success story in the system
  As an User
  I need to provide all attributes needed to add a success story in the system

    @Done
    Scenario: A not logged-in user Adding new success story to the system
        Given I am on "/en/success-stories/add"
        And I wait to be redirected
        Then I should be on "/en/login/?redirected=addsuccessstory&redirect_to=http%3A%2F%2Ffoss.espace.ws%2Fen%2Fadd-success-story%2F"
        And I should see "Please log in to suggest a new success story"

    @javascript @not_working
    Scenario: A logged-in user Adding new success story to the system with valid inputs
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/success-stories/add"
        When I Add new frontend success story with "Monetizing Mobile Gaming", "The mobile gaming industry made $29 billion in 2015 — and it is only set to continue growing (with estimates as high as $49 billion by 2018)."
        And I attach the file "testImages/logo.png" to "success_story_image" with relative path
        And I add "java" to a auto-select "interest"
        And I add "php" to a auto-select "interest"
        And I add "python" to a auto-select "interest"
        And I select "Testing Category" from "post_category"
        And I press "submit"
        Then I should see "added successfully"
       
    @not_working
    Scenario: A logged-in user Adding new success story with only required inputs
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/success-stories/add"
        When I Add new frontend success story with "Tablets Are Dead", " 2010, tablets were supposed to be the new hot thing. Apple released the first iPad, Samsung was working on the Galaxy Tab and countless others were about to flood the market with Android tablets. Six years later, there weren’t any tablets at Mobile World Congress in Barcelona. Companies and consumers have moved on. Tablets are dead."
        And I attach the file "testImages/logo.png" to "success_story_image" with relative path
        And I select "Testing Category" from "post_category"
        And I press "submit"
        Then I should see "added successfully"

    @javascript @not_working
    Scenario: A logged-in user Adding new success story with null inputs
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/success-stories/add"
        When I Add new frontend success story with "", ""
        And I press "submit"
        Then I should see "Title required"
        And I should see "Content required"
        And I should see "Image required"
        And I should see "Category required"

    @javascript @not_working
    Scenario: A logged-in user Adding new success story with numbers only in the textfields
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/success-stories/add"
        When I Add new frontend success story with "121323415", "1253253"
        And I press "submit"
        Then I should see "Title Must includes at least one letter"
        And I should see "Content Must includes at least one letter"

    @javascript @not_working
    Scenario: A logged-in user Adding new success story with special characters only in the textfields
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/success-stories/add"
        When I Add new frontend success story with ";;;;;", ";;;;;"
        And I press "submit"
        Then I should see "Title Must includes at least one letter"
        And I should see "Content Must includes at least one letter"

    @javascript @Done
    Scenario: A logged-in user Adding new success story with title exceeds the max length
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/success-stories/add"
        When I Add new frontend success story with "SUccess Story title SUccess Story title SUccess Story title SUccess Story title success Story title SUccess Story title SUccess Story title SUccess Story title", "Success Story Description"
        And I press "submit"
        Then I should see "Title should not be more than 100 characters"

    @javascript @Done
    Scenario: Adding new success story from the list page with Title less than the min length
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/success-stories/add"
        When I Add new frontend success story with "title1", "success story description"
        And I press "submit"
        Then I should see "Title should be at least 10 characters"
