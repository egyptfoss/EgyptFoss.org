Feature: Reset the password in the system using my username or email
  In order to reset my password in the system
  As a user
  I need to be able navigate to the forgot password form and provide my username or email

    @javascript @Done
    Scenario: A user navigating to reset his password page in the system
        Given I am on "/en/login"
        When I follow "Forgot Password?"
        And I wait to be redirected
        Then I should be on "/en/login/?action=lostpassword"
        And I should see "E-mail or Username"

    @javascript @not_working
    Scenario: A user providing his email to reset the password
        Given I am on "/en/login/?action=lostpassword"
        When I resize window with height 800 and width 1024 in px
        When I fill in "user_login" with "mainagar"
        And I press "Get New Password"
        And I wait to be redirected
        Then I should be on "/en/login/?checkemail=confirm"
        And I should see "Check your e-mail for the confirmation link."

    @javascript @not_working 
    Scenario: A user providing his username to reset the password
        Given I am on "/en/login/?action=lostpassword"
        When I fill in "user_login" with "mainagar"
        And I press "Get New Password"
        And I wait to be redirected
        Then I should be on "/en/login/?checkemail=confirm"
        And I should see "Check your e-mail for the confirmation link."

    @javascript @not_working
    Scenario: A user changing his password from the forgot password email with password less than the min length
        Given I should receive reset password email
        Then I reset password to "1234" and "1234"
        Then I should see "Password must be at least 8 characters"

    @javascript @not_working
    Scenario: A user changing his password from the forgot password email with null password and null password confirmation
        Given I should receive reset password email
        Then I reset password to "" and ""
        Then I should see "Password must be at least 8 characters"

    @javascript @not_working
    Scenario: A user changing his password from the forgot password email with null password confirmation
        Given I should receive reset password email
        Then I reset password to "123456789" and ""
        Then I should see "Please Confirm your password"

    @not_working
    Scenario: A user changing his password from the forgot password email successfully
        Given I should receive reset password email
        Then I reset password to "F0$$123456" and "F0$$123456"
        And I should be on "/en/login/?action=resetsuccess"
        And I should see "Your password has been reset."

    @javascript @not_working
    Scenario: A user changing his password from an already used link in forgot password email
        Given I should receive reset password email
        Then I should be on "/en/"
        #And I should see "Your password reset link appears to be invalid. Please request a new link below."

    @javascript @not_working
    Scenario: A user providing a wrong email while resetting the password
        Given I am on "/en/login/?action=lostpassword"
        When I fill in "user_login" with "aaa"
        And I press "Get New Password"
        And I wait to be redirected
        Then I should be on "/en/login/?action=lostpassword"
        And I should see "Invalid username or e-mail."

    @javascript @not_working 
    Scenario: A user providing an empty email while resetting the password
        Given I am on "/en/login/?action=lostpassword"
        When I fill in "user_login" with ""
        And I press "Get New Password"
        And I wait to be redirected
        Then I should be on "/en/login/?action=lostpassword"
        And I should see "Enter a username or e-mail address."

    @javascript @not_working 
    Scenario: A user providing a not exist email while resetting the password
        Given I am on "/en/login/?action=lostpassword"
        When I fill in "user_login" with "maii.elnagar+not_exist@espace.com.eg"
        And I press "Get New Password"
        And I wait to be redirected
        Then I should be on "/en/login/?action=lostpassword"
        And I should see "Wrong username or password"