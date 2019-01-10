Feature: login to the system using my username and password
  In order to login to the system
  As a user
  I need to be able to login to the system with my username and password

    @Done
    Scenario: A verified user logging in to the system with valid credentials successfully
        Given I am on "/en/login/"
        And I login to the system with "foss" and "F0$$"
        And I wait to be redirected
        Then I should be on "/en/"
        And I should see "foss"

    @Done    
    Scenario: A user logging in to the system with invalid credentials
        Given I am on "/en/login/"
        When I login to the system with "ay.7aga" and "ay.7aga"
        And I wait to be redirected
        Then I should be on "/en/login/"
        And I should see "Wrong username or password"
        
    @javascript @Done 
    Scenario: A user logging in to the system with valid username and empty password
        Given I am on "/en/login/"
        When I login to the system with "bougy.tamtam" and ""
        Then I should be on "/en/login/"
        And I should see "Password can not be empty"
    
    @javascript @Done
    Scenario: A user logging in to the system with empty username and valid password
        Given I am on "/en/login/"
        When I login to the system with "" and "123456789"
        Then I should be on "/en/login/"
        And I should see "Username can not be empty"
    
    @javascript @Done
    Scenario: A user logging in to the system with empty username and empty password
        Given I am on "/en/login/"
        When I login to the system with "" and ""
        And I wait to be redirected
        Then I should be on "/en/login/"
        And I should see "Username can not be empty"
        And I should see "Password can not be empty"

    @Done
    Scenario: User seeing the password is masked on the screen
        Given I am on "/en/login/"
        When I fill in "user_pass" with "123456789"
        Then I should not see "123456789"

    @Done
    Scenario: A user logging in to the system with wrong case sensitive password
        Given I am on "/en/login/"
        When I login to the system with "foss" and "f0$$"
        And I wait to be redirected
        Then I should be on "/en/login/"
        And I should see "Wrong username or password"

    @Done
    Scenario: A user checking Remember me option in login page
        Given I am on "/en/login/"
        When  I fill in "user_login" with "foss"
        And   I fill in "user_pass" with "F0$$"
        And   I check "rememberme"
        And   I press "wp-submit"
        Then I should be on "/en/"
        And I should see "foss"
        When  I close the browser
        And I am on "/en/"
        Then I should see "foss"

    @Done
    Scenario: A user not checking Remember me option in login page
        Given I am on "/en/login/"
        When I login to the system with "foss" and "F0$$"
        Then I should be on "/en/"
        And I should see "foss"
        When  I close the browser
        And I am on "/en/"
        Then I should see "Log in"


