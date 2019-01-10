Feature: delete a user or bulk of users in the system from admin dashboard
  In order to delete a user or bulk of users in the system from admin dashboard
  As an Admin
  I need to navigate to users list and delete a user or bulk of users

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

    @javascript @not_implemented @must
    Scenario: deleting a user from the list page
        Given I am on "/wp-admin/users.php"
        When I follow "Delete" on the row containing "mohamed.said"
        And I select "delete_option0"
        And I press "submit"
        Then I should see "User deleted."

    @javascript @not_implemented @must
    Scenario: deleting bulk of users
        Given I am on "/wp-admin/users.php"
        And I check "signup_7"
        And I check "signup_8"
        And I select "Delete" from "bulk-action-selector-top"
        And I press "Apply"
        And I select "delete_option0"
        And I press "submit"
        Then I should see "users deleted."