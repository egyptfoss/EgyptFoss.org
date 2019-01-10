Feature: list and search for users in the system from admin dashboard
  In order to list and search for users in the system from admin dashboard
  As an Admin
  I need to navigate to user list and search for specific users in the system

  Background:
    Given I am on "/login"
    And there are following users:
        | username | email                      | password | enabled |
        | foss     | admin@example.com | F0$$   | yes     |
        | mohamed.said | maii.elnagar+mohamed.said@espace.com.eg | 123456789   | yes |
    When I fill in the following:
        | user_login | foss |
        | user_pass | F0$$ |
    And I press "wp-submit"

    @Done
    Scenario: Listing all users in the system
        Given I am on "/wp-admin/"
        When I follow "Users" in certain place "#adminmenu"
        And I wait to be redirected
        Then I should be on "/wp-admin/users.php"
        And I should see "foss"
        And I should see "Email"
        And I should see "Role"
        And I should see "Type"

    @Done
    Scenario: Searching for users in the system using the username
        Given I am on "/wp-admin/users.php"
        When I fill in "user-search-input" with "foss"
        And I press "Search Users"
        And I wait to be redirected
        Then I should see "foss"
    
    @Done
    Scenario: Searching for users in the system using the part of the username
        Given I am on "/wp-admin/users.php"
        When I fill in "user-search-input" with "sai"
        And I press "Search Users"
        And I wait to be redirected
        Then I should see "mohamed.said"

    @Done
    Scenario: Searching for users in the system using the part of the email
        Given I am on "/wp-admin/users.php"
        When I fill in "user-search-input" with "espace.com.eg"
        And I press "Search Users"
        And I wait to be redirected
        Then I should see "foss"
        And I should see "mohamed.said"

    @Done
    Scenario: Searching for users in the system the email
        Given I am on "/wp-admin/users.php"
        When I fill in "user-search-input" with "yomna.fahmy@espace.com.eg"
        And I press "Search Users"
        And I wait to be redirected
        Then I should see "foss"

    @Done
    Scenario: Searching for users in the system using the part of the email
        Given I am on "/wp-admin/users.php"
        When I fill in "user-search-input" with "espace.com.eg"
        And I press "Search Users"
        And I wait to be redirected
        Then I should see "foss"
        And I should see "mohamed.said"
