Feature: login to the system through (Facebook) social network
  In order to login to the system
  As a user
  I need to be able to login to with my (Facebook) social accounts,and ability to login directly if permissions granted previously


    @javascript @AfterStep @not_implemented @hard
    Scenario: New user logging in to the system with Facebook and invoked the permissions
        Given I am on "/en/login/"
        And I follow "Connect with Facebook"
        #And I follow the redirection
        When I fill in "email" with "buggy.tamtam1@gmail.com"
        And I fill in "pass" with "buggy123"
        And I press "Log In"
        And I wait to be redirected
        And take screenshot
        And I press "__CANCEL__"
        And I wait for 3 seconds
        Then I should see "Authentication failed"
        Then I close the browser

    @javascript @fb @not_implemented
    Scenario: New user logging in to the system with Facebook and granted the permissions
        Given I am on "/en/login/"
        And I follow "Connect with Facebook"
        When I fill in "email" with "buggy.tamtam1@gmail.com"
        And I fill in "pass" with "buggy123"
        And I press "Log In"
        And I wait to be redirected
        And I press "__CONFIRM__"
        And I wait to be redirected
        And I wait for 10 seconds
        Then I should be on "/en/"
        And I close the browser

    @javascript @fb @not_implemented @revoke-facebook 
    Scenario: User logs in with Facebook after previously logging in via a non-Facebook flow with the same email address
        Given I am on "/en/login/"
        When I follow "Connect with Facebook"
        When I fill in "email" with "buggy.tamtam1@gmail.com"
        And I fill in "pass" with "buggy123"
        And I press "Log In"
        And I wait to be redirected
        Then I should be on "/en/"
        And I should see "Buggy Tamtam"