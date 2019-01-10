Feature: change password in the system while being logged in
  In order to change my password in the system
  As a logged-in user
  I need to be able navigate to the change password page and provide my current password

  Background:
    Given I am on "/"
    And there are following users:
            | username | email | password | enabled |
            | mohamed.said | maii.elnagar+mohamed.said@espace.com.eg | 123456789 | yes |

    @Done @javascript
    Scenario: A logged-in user navigating to change his password page in the system
        Given I am a logged in user with "mohamed.said" and "123456789"
        And I am on "/en/members/mohamed.said/settings/"
        Then I should see "Change Your Password"

    @not_working
    Scenario: A logged-in user changing his password successfully with valid password
        Given I am a logged in user with "mohamed.said" and "123456789"
        And I am on "/en/members/mohamed.said/settings/"
        And I fill in "pwd" with "123456789"
        And I fill in "pass1" with "123698745"
        And I fill in "pass2" with "123698745"
        And I press "submit"
        And I wait to be redirected
        Then I should see "Your settings have been saved."

    @javascript @Done
    Scenario: A logged-in user changing his password with password less than the min length
        Given I am a logged in user with "mohamed.said" and "123698745"
        And I am on "/members/mohamed-said/settings/"
        And I wait to be redirected
        And I fill in "pwd" with "123456789"
        And I fill in "pass1" with "1236"
        And I fill in "pass2" with "1236"
        And I press "submit"
        Then I should see "Password must be at least 8 characters"

    @not_working
    Scenario: A logged-in user changing his password with null password confirmation
        Given I am a logged in user with "mohamed.said" and "123698745"
        And I am on "/members/mohamed-said/settings/"
        And I wait to be redirected
        And I fill in "pwd" with "123698745"
        And I fill in "pass1" with "123456789"
        And I fill in "pass2" with ""
        And I press "submit"
        And I wait to be redirected
        Then I should see "One of the password fields was empty."
        
    @not_working
    Scenario: A logged-in user changing his password with null new password
        Given I am a logged in user with "mohamed.said" and "123698745"
        And I am on "/members/mohamed-said/settings/"
        And I wait to be redirected
        And I fill in "pwd" with "123698745"
        And I fill in "pass1" with ""
        And I fill in "pass2" with "123456789"
        And I press "submit"
        And I wait to be redirected
        Then I should see "One of the password fields was empty."

    @not_working
    Scenario: A logged-in user changing his password with wrong current password
        Given I am a logged in user with "mohamed.said" and "123698745"
        And I am on "/members/mohamed-said/settings/"
        And I wait to be redirected
        And I fill in "pwd" with "1324358490"
        And I fill in "pass1" with "123456789"
        And I fill in "pass2" with "123456789"
        And I press "submit"
        And I wait to be redirected
        Then I should see "Your current password is invalid."

    @javascript @Done
    Scenario: A logged-in user changing his password with empty current password
        Given I am a logged in user with "mohamed.said" and "123698745"
        And I am on "/members/mohamed-said/settings/"
        And I wait to be redirected
        And I fill in "pwd" with ""
        And I fill in "pass1" with "123456789"
        And I fill in "pass2" with "123456789"
        Then I should see " Current password is incorrect"