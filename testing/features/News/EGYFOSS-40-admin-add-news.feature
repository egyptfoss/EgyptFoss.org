Feature: Manage news by adding, editing and deleting them
  In order to manage the news in the system
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
    Scenario: Adding new news from the list page with valid inputs
        Given I am on "/wp-admin/edit.php?post_type=news"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Add new news with "News Title1", "News Subtitle", "News Description"
        And I press "publish"
        Then I should see "news published."
       
    @javascript @Done
    Scenario: Adding new news from the list page with only required inputs
        Given I am on "/wp-admin/edit.php?post_type=news"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Add new news with "News Title2", "", "News Description"
        And I press "publish"
        And take screenshot
        Then I should see "news published."


    @javascript @Done
    Scenario: Adding new news from the list page with Title exceeds the max length
        Given I am on "/wp-admin/edit.php?post_type=news"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Add new news with "News title News title News title News title News title News title News title News title News title News title", "", "News Description"
        And I press "publish"
        And I wait to be redirected
        Then I should see "title should not be more than 100 characters"

    @javascript @Done
    Scenario: Adding new news from the list page with Title less than the min length
        Given I am on "/wp-admin/edit.php?post_type=news"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Add new news with "title1", "", "News Description"
        And I press "publish"
        And I wait for 1 seconds
        Then I should see "title should be at least 10 characters"

    @javascript @Done
    Scenario: Adding new news from the sidebar with valid inputs
        Given I am on "/wp-admin/"
        And I resize window with height 800 and width 2048 in px
        And I follow "News"
        And I follow "Add New" in certain place "li#menu-posts-news"
        And I wait to be redirected
        Then I should be on "/wp-admin/post-new.php?post_type=news"
        And I should see "Add New News"

    @javascript  @Done
    Scenario: Adding new news with null inputs
        Given I am on "/wp-admin/edit.php?post_type=news"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Add new news with "", "", ""
        And I press "publish"
        Then I should see "Required"

    @javascript @Done
    Scenario: Adding new news with numbers only in the textfields
        Given I am on "/wp-admin/edit.php?post_type=news"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Add new news with "1223", "12412", "1324"
        And I press "publish"
        Then I should see "must at least contain one letter"

    @javascript  @Done
    Scenario: Adding new news with special characters only in the textfields
        Given I am on "/wp-admin/edit.php?post_type=news"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Add new news with ",,,,,,", ";;;;;", "&&&&"
        And I press "publish"
        Then I should see "must at least contain one letter"