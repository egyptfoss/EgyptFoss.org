Feature: Add new user from admin dashboard
  In order to add new user from admin dashboard
  As an Admin
  I need to navigate to user list and add new user from side bar and from inside user list page

  Background:
    Given I am on "/login"
    And there are following users:
        | username | email                      | password | enabled |
        | foss     | admin@example.com | F0$$   | yes     |
    When I fill in the following:
        | user_login | foss |
        | user_pass | F0$$ |
    And I press "wp-submit"

    @javascript @not_implemented
    Scenario Outline: Adding new user from the list page with valid inputs
        And I am on "wp-admin/users.php"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        Then I should be on "wp-admin/user-new.php"
		And I Add new user with "<username>", "<email>", "<password>", "<role>"
        And I press "Add New User"
        Then I should see "New user created."
        And User should get an email on "<email>" with:
           """
           To set your password, visit the following address:
           """
        
        Examples:
        | username | email | password | role |
        | yomna.fahmy | maii.elnagar+yomna.fahmy@espace.com.eg | 123456789 | Subscriber |
        | ashraf.kotb | maii.elnagar+ashraf.kotb@espace.com.eg | 123456789 | Contributor |
        | eslam.diaa | maii.elnagar+eslam.diaa@espace.com.eg | 123456789 | Author |

    @javascript @not_implemented
    Scenario: Adding new user from the sidebar with valid inputs
        Given I am on "/wp-admin/"
        When I follow "Users" in certain place "#adminmenu"
        And I follow "Add New" in certain place "li#menu-users"
        And I wait to be redirected
        Then I should be on "/wp-admin/user-new.php"
        And I should see "Add New User"

    @javascript @not_implemented
    Scenario: Adding new user from the list page with already exist username
        Given I am on "wp-admin/users.php"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Add new user with "yomna.fahmy", "maii.elnagar+yomna.fahmy12@espace.com.eg", "123456789", "Contributor"
        And I press "Add New User"
        Then I should see "This username is already registered. Please choose another one."

    @javascript @not_implemented
    Scenario: Adding new user from the list page with already exist email
        Given I am on "wp-admin/users.php"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Add new user with "yoya.fahmy", "maii.elnagar+yomna.fahmy@espace.com.eg", "123456789", "Author"
        And I press "Add New User"
        Then I should see "This email is already registered, please choose another one."

    @javascript @not_implemented
    Scenario: Adding new user with invalid username
        Given I am on "wp-admin/users.php"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Add new user with "121@1", "maii.elnagar+test@espace.com.eg", "123456789", "Author"
        And I press "Add New User"
        And I wait to be redirected
        Then I should see "This username is invalid because it uses illegal characters. Please enter a valid username."

    @not_implemented @javascript
    Scenario: Adding new user with invalid email
        Given I am on "wp-admin/users.php"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Add new user with "aaa.bbb", "1254$2", "123456789", "Author"
        And I press "Add New User"
        Then I should see "The email address isn’t correct."

    @Done @javascript
    Scenario: Adding new user with username exceeds the max length
        Given I am on "wp-admin/users.php"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Add new user with "sjdfvhgfsdhagfjghasasdsdashsgdge.hg", "maii.elnagar+test@espace.com.eg", "123456789_asksass$!", "Author"
        And I press "Add New User"
        And I wait to be redirected
        Then I should see "Username should contain at least one letter and be at least 4 characters & no more than 20 characters"

    @javascript @Done
    Scenario: Adding new user with username less than the min length
        Given I am on "wp-admin/users.php"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Add new user with "hg", "maii.elnagar+test@espace.com.eg", "123456789_asksass$!", "Author"
        And I press "Add New User"
        And I wait to be redirected
        Then I should see "Username should contain at least one letter and be at least 4 characters & no more than 20 characters"

    @javascript @Done
    Scenario: Adding new user with password less than the min length
        Given I am on "wp-admin/users.php"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Add new user with "aaa.aaaaa", "maii.elnagar+test@espace.com.eg", "123", "Author"
        And I press "Add New User"
        Then I should see "Very weak"
