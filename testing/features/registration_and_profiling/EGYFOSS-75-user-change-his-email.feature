Feature: change email in the system while being logged in
  In order to change my email in the system
  As a logged-in user
  I need to be able navigate to the change email page and provide my 

    @Done
    Scenario: A not logged-in user can't access change email page
        Given I am on "/members/foss/settings/"
        Then I should be on "en/login/?redirect_to=http%3A%2F%2Ffoss.espace.ws%2Fmembers%2foss%2Fsettings%2F&action=bpnoaccess"
        And I should see "You must log in to access the page you requested."

    @Done
    Scenario: A logged-in user navigating to change his email page in the system
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/members/espace/settings/"
        Then I should see "Change Your Email"

    @javascript @Done
    Scenario: A logged-in user changing his email successfully with valid email
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/members/espace/settings/"
        And I wait to be redirected
        And I choose change email tab
        And I fill in "pwd" with "123456789"
        And I fill in "email" with "ahalaa@espace.com"
        And take screenshot
        And I press "submit"
        And I wait to be redirected
        Then I should see "Your settings have been saved."

    @Done
    Scenario: A logged-in user changing his email with invalid email
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/members/espace/settings/"
        And I wait to be redirected
        And I fill in "pwd" with "123456789"
        And I fill in "email" with "assa.aaa"
        And I press "submit"
        And I wait to be redirected
        Then I should see "That email address is invalid. Check the formatting and try again."

    @javascript @Done
    Scenario: A logged-in user changing his email with null email
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/members/espace/settings/"
        And I wait to be redirected
        And I choose change email tab
        And I fill in "pwd" with "123456789"
        And I fill in "email" with ""
        And I press "submit"
        Then I should see "Please enter a valid email address"

    @javascript @Done
    Scenario: A logged-in user changing his email with wrong current password
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/members/espace/settings/"
        And I wait to be redirected
        And I choose change email tab
        And I fill in "pwd" with "490"
        And I fill in "email" with "bbb@bbb.ddd"
        Then I should see " Current password is incorrect"

    @javascript @Done
    Scenario: A logged-in user changing his email with empty current password
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/members/espace/settings/"
        And I wait to be redirected
        And I choose change email tab
        And I fill in "pwd" with ""
        And I fill in "email" with "sss@eee.aaa"
        And I press "submit"
        Then I should see "Current password is incorrect"

    @javascript @not_working
    Scenario: A user confirm his new email from the confirmation email sent
        Given I am a logged in user with "espace" and "123456789"
        And I should receive change-email email
        Then I confirm my new email
        And I wait to be redirected
        Then I should see "You have successfully verified your new email address."