Feature: Manage open datasets by adding, editing and deleting them
  In order to manage the open datasets in the system
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

    @javascript @Done
    Scenario: Adding new open dataset from the list page with valid inputs
        Given I am on "/wp-admin/edit.php?post_type=open_dataset"
        When I resize window with height 800 and width 2048 in px
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Add new open dataset with "Open Dataset Title", "Open Dataset Publisher", "Open Dataset Description", "dataset type one", "prince", "dataset license1", "hints", "References", "http://test.com"       
        And I select "java" from "acf-field-interest"
        And I press "publish"
        Then I should see "open dataset published."
       
    @javascript @Done
    Scenario: Adding new open dataset from the list page with only required inputs
        Given I am on "/wp-admin/edit.php?post_type=open_dataset"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Add new open dataset with "Open dataset only required inputs", "Open Dataset Publisher", "Open Dataset Description", "dataset type one", "prince", "", "", "References", "http://test.com"
        And I press "publish"
        And I wait to be redirected
        Then I should see "open dataset published."

    @javascript @Done
    Scenario: Adding new open dataset from the list page with Title exceeds the max length
        Given I am on "/wp-admin/edit.php?post_type=open_dataset"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Add new open dataset with "OpenDataset title OpenDataset title OpenDataset title OpenDataset title OpenDataset title OpenDataset title OpenDataset title OpenDataset title OpenDataset title OpenDataset title OpenDataset title OpenDataset title", "Open Dataset Publisher", "Open Dataset Description", "dataset type one", "prince", "", "", "References", "http://test.com"
        And I press "publish"
        And I wait to be redirected
        Then I should see "title should not be more than 100 characters"

    @javascript @Done
    Scenario: Adding new open dataset from the list page with Title less than the min length
        Given I am on "/wp-admin/edit.php?post_type=open_dataset"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Add new open dataset with "title1", "Open Dataset Publisher", "Open Dataset Description", "dataset type one", "prince", "", "", "References", "http://test.com"
        And I press "publish"
        And I wait to be redirected
        Then I should see "title should be at least 10 characters"

    @javascript @Done
    Scenario: Adding new open dataset from the sidebar with valid inputs
        Given I am on "/wp-admin/"
        And I resize window with height 800 and width 1024 in px
        And I follow "Open datasets"
        And I wait to be redirected
        And I follow "Add open dataset"
        And I wait to be redirected
        Then I should be on "/wp-admin/post-new.php?post_type=open_dataset"
        And I should see "Add New Open Dataset"

    @javascript @Done
    Scenario: Adding new open dataset with null inputs
        Given I am on "/wp-admin/edit.php?post_type=open_dataset"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Add new open dataset with "", "", "", "", "", "", "", "", ""
        And I press "publish"
        Then I should see "Open dataset title is required"

    @javascript @Done
    Scenario: Adding new open dataset with numbers only in the textfields
        Given I am on "/wp-admin/edit.php?post_type=open_dataset"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Add new open dataset with "1223", "1324", "", "", "", "", "", "", ""
        And I press "publish"
        Then I should see "must at least contain one letter"

    @javascript @Done
    Scenario: Adding new open dataset with special characters only in the textfields
        Given I am on "/wp-admin/edit.php?post_type=open_dataset"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Add new open dataset with ",,,,,,", "&&&&", "", "", "", "", "", "", ""
        And I press "publish"
        Then I should see "must at least contain one letter"