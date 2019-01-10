Feature: edit news by admin from backend
  In order to edit news by admin from backend
  As an Admin
  I need to navigate to edit page and edit the news contents

  Background:
        Given I am on "/login"
        And there are following users:
            | username | email                      | password | enabled |
            | foss     | admin@example.com | F0$$   | yes     |
        When I fill in the following:
            | user_login | foss |
            | user_pass | F0$$ |
        And I press "wp-submit"

    @javascript @Done
    Scenario: editing a news in the system
        Given I am on "/wp-admin/edit.php?post_type=news"
        #And I follow "Edit" on the row containing "News Title1"
        When I follow "New News 11"
        And I wait to be redirected
        And I Add new news with "News Title for testing", "News Subtitle", "News Description"
        And I press "Update"
        Then I should see "news updated."