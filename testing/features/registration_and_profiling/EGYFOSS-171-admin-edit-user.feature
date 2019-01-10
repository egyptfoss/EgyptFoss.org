Feature: Edit a user in the system from admin dashboard
  In order to edit a user in the system from admin dashboard
  As an admin
  I need to navigate to user list and edit a user in the system

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
    Scenario: Editing a user in the system with valid inputs
        Given I am on "/wp-admin/users.php"
        And I follow "Edit" on the row containing "yomna.fahmy"
        And I wait to be redirected
        When I Edit the user with "maii.elnagar+nomna.fahmy@espace.com.eg", "123698745", "Administrator"
        And I press "Update User"
        Then I should see "User updated."

    @javascript @not_implemented
    Scenario: Editing a user in the system with invalid inputs
        Given I am on "/wp-admin/users.php"
        And I follow "Edit" on the row containing "yomna.fahmy"
        And I wait to be redirected
        When I Edit the user with "1234", "as", ""
        And I press "Update User"
        And I should see "The email address isn’t correct."
        And I should see "Please enter Password at least 8 characters"

    @javascript @not_implemented
    Scenario: Editing a user in the system with already exist email
        Given I am on "/wp-admin/users.php"
        And I follow "Edit" on the row containing "yomna.fahmy"
        And I wait to be redirected
        When I Edit the user with "maii.elnagar+ashraf.kotb@espace.com.eg", "123456789", "Contributor"
        And I press "Update User"
        Then I should see "This email is already registered, please choose another one."

    @javascript
    Scenario: Editing a user in the system with null inputs
        Given I am on "/wp-admin/users.php"
        And I follow "Edit" on the row containing "yomna.fahmy"
        And I wait to be redirected
        When I Edit the user with "", "", ""
        And I press "Update User"
        And I should see "Please enter an e-mail address."