Feature: force account activation from admin dashboard
  In order to force account activation from admin dashboard
  As an Admin
  I need to navigate to pending users list and force specific account or bulk of accounts activation

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
    Scenario: navigating to pending users list
        Given I am on "/wp-admin/users.php"
        When I follow "Pending"
        Then I should be on "/wp-admin/users.php?page=bp-signups"

    @javascript @not_implemented
    Scenario: forcing activation for certain user
        Given I am on "/wp-admin/users.php?page=bp-signups"
        And I follow "Activate" on the row containing "profit"
        And I wait to be redirected
        When I follow "Confirm"
        Then I should see "account successfully activated!"

    @javascript @not_implemented
    Scenario: Cancelling forcing activation for certain user
        Given I am on "/wp-admin/users.php?page=bp-signups"
        And I follow "Activate" on the row containing "profit"
        And I wait to be redirected
        When I follow "Cancel"
        Then I should be on "/wp-admin/users.php?page=bp-signups"

    @javascript @not_implemented @must
    Scenario: forcing bulk of account activation
        Given I am on "/wp-admin/users.php?page=bp-signups"
        And I check "signup_7"
        And I check "signup_8"
        And I check "signup_9"
        And I select "Activate" from "bulk-action-selector-top"
        And I press "Apply"
        And I press "Confirm"
        Then I should see "accounts successfully activated!"

    @javascript @not_implemented @must
    Scenario: forcing bulk of account activation
        Given I am on "/wp-admin/users.php?page=bp-signups"
        And I check "signup_7"
        And I check "signup_6"
        And I check "signup_1"
        And I select "Activate" from "bulk-action-selector-top"
        And I follow "Apply" in certain place ".bulkactions"
        And I press "Cancel"
        Then I should be on "/wp-admin/users.php?page=bp-signups"