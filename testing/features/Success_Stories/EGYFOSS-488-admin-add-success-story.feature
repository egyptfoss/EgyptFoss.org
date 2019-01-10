Feature: Manage success stories by adding, editing and deleting them
  In order to manage the success stories in the system
  As an Admin
  I need to be able to list, add, edit and delete them

  Background:
        Given I am on "/login"
        And there are following users:
            | username | email                      | password | enabled |
            | foss     | admin@example.com | F0$$   | yes     |
        When I fill in the following:
            | user_login | foss |
            | user_pass | F0$$ |
        And I press "wp-submit"
        And I am on "wp-admin"

    @javascript @not_working
    Scenario: Adding new success story from the list page with valid inputs
        Given I am on "/wp-admin/edit.php?post_type=success_story"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Add new success story with "Success Story Title1", "Success Story Description"
        And I select "java" from "acf-field-interest"
        And I select "Testing Category" from "acf-field-success_story_category"
        And I press "publish"
        Then I should see "success story published."
       
    @javascript @not_working
    Scenario: Adding new success story from the list page with only required inputs
        Given I am on "/wp-admin/edit.php?post_type=success_story"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Add new success story with "Success Story Title1", "Success Story Description"
        And I select "Testing Category" from "acf-field-success_story_category"
        And I press "publish"
        Then I should see "success story published."

    @javascript @Done
    Scenario: Adding new success from the list page with Title exceeds the max length
        Given I am on "/wp-admin/edit.php?post_type=success_story"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Add new success story with "SUccess Story title SUccess Story title SUccess Story title SUccess Story title success Story title SUccess Story title SUccess Story title SUccess Story title", "Success Story Description"
        And I press "publish"
        And I wait to be redirected
        Then I should see "title should not be more than 100 characters"

    @javascript @Done
    Scenario: Adding new success story from the list page with Title less than the min length
        Given I am on "/wp-admin/edit.php?post_type=success_story"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Add new success story with "title1", "success story Description"
        And I press "publish"
        And I wait for 1 seconds
        Then I should see "title should be at least 10 characters"

    @javascript @Done
    Scenario: Adding new success story from the sidebar with valid inputs
        Given I am on "/wp-admin/"
        And I resize window with height 800 and width 1024 in px
        And I follow "Success Stories"
        And I wait to be redirected
        And I follow "Add Success Story"
        And I wait to be redirected
        Then I should be on "/wp-admin/post-new.php?post_type=success_story"
        And I should see "Add New Success Story"

    @javascript @Done
    Scenario: Adding new success story with null inputs
        Given I am on "/wp-admin/edit.php?post_type=success_story"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Add new success story with "", ""
        And I press "publish"
        Then I should see "Required"

    @javascript @Done
    Scenario: Adding new success story with numbers only in the textfields
        Given I am on "/wp-admin/edit.php?post_type=success_story"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Add new success story with "1223", "1324"
        And I press "publish"
        Then I should see "must at least contain one letter"

    @javascript @Done
    Scenario: Adding new success story with special characters only in the textfields
        Given I am on "/wp-admin/edit.php?post_type=success_story"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Add new success story with ",,,,,,", "&&&&"
        And I press "publish"
        Then I should see "must at least contain one letter"