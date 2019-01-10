Feature: User view list of all success stories in the system
  In order to list all success stories in the system
  As an User
  I need to be able to navigate to success stories list page and load more success stories

    @Done
    Scenario: A not logged-in user navigating to Success Stories list
        Given I am on "/en/"
        When I follow "Stories"
        Then I should be on "/en/success-stories/"
        And I should see "Success Stories"

    @Done @set-empty-list-success-story @return-list-success-story
    Scenario: A not logged-in user seeing an empty success stories list
        Given I am on "/en/success-stories/"
        Then I should see "There are no Success Stories yet, Suggest Success Story"
        When I follow "Suggest Success Story"
        Then I should be on "/en/login/"
        And I should see "Please log in to suggest a new success story"

    @Done @set-empty-list-success-story @return-list-success-story
    Scenario: A logged-in user seeing an empty success stories list
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/success-stories/"
        Then I should see "There are no Success Stories yet, Suggest Success Story"
        When I follow "Suggest Success Story"
        Then I should be on "/en/success-stories/add/"
        And I should see "Suggest Success Story"
        And I close the browser

    @Done @add-success-stories
    Scenario: A not logged-in user viewing 10 success stories per page
        Given I am on "/en/success-stories/"
        Then I should see 10 ".story" elements
        And I should see "Show more"

    @javascript @Done 
    Scenario: A not logged-in user loading more success stories
        Given I am on "/en/success-stories/"
        When I follow "Show more"
        And I wait for 7 seconds
        Then I should see more "10" or more ".story" elements
        And I should not see "Show more"

    @Done
    Scenario: A not logged-in user viewing Success Story title, image and date in the Success Story card
        Given I am on "/en/success-stories/"
        Then "story-category" should be visible
        And "date" should be visible
        And "author" should be visible
        And "story-content" Should be visible

    @Done
    Scenario: A logged-in user navigating to Success Story list
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/"
        When I follow "Stories"
        Then I should be on "/en/success-stories/"
        And I should see "Success Stories"

    @Done
    Scenario: A logged-in user viewing 10 success stories per page
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/success-stories/"
        Then I should see 10 ".story" elements
        And I should see "Show more"

    @javascript @Done
    Scenario: A logged-in user loading more success stories
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/success-stories/"
        When I follow "Show more"
        And I wait for 5 seconds
        Then I should see more "10" or more ".story" elements
        And I should not see "Show more"

    @Done
    Scenario: A logged-in user viewing Success Story title, image and date in the Success Stories card
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/success-stories/"
        Then "story-category" should be visible
        And "date" should be visible
        And "author" should be visible
        And "story-content" Should be visible

    @Done
    Scenario: A logged-in user viewing Success Stories categories
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/success-stories/"
        Then "categories-list" should be visible
        And I should see "Testing Category"
     