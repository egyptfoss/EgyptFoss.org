Feature: set/unset user as expert
  In order to set/unset user as expert
  As Admin
  I need to be able to set/unset user as expert

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

    @Done
    Scenario: Admin can set user as expert
        Given I am on "/wp-admin/users.php"
        And I follow "bougy.tamtam"
        When I check "is_expert"
        And I press "submit"
        And I wait to be redirected
        Then the "is_expert" checkbox should be checked
        
    @waiting_implementation
    Scenario: Admin can set user as expert and this user should see add expert button in listing

    @Done
    Scenario: Admin can unset user as expert
        Given I am on "/wp-admin/users.php"
        And I follow "bougy.tamtam"
        When I uncheck "is_expert"
        And I press "submit"
        And I wait to be redirected
        Then the "is_expert" checkbox should not be checked

    @waiting_implementation
    Scenario: Admin can set user as expert and this user should not see add expert button in listing

    @Done
    Scenario: Admin can set subscriber but a warning should appear
        Given I am on "/wp-admin/users.php"
        And I follow "bougy.tamtam"
        When I check "is_expert"
        Then I should see "Are you sure you want to set a subscriber as an expert ?"


