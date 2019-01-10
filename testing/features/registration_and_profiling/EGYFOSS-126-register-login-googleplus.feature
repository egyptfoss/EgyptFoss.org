Feature: login to the system through (Google+) social network
  In order to login to the system
  As a user
  I need to be able to login to with my (Google+) social accounts,and ability to login directly if permissions granted previously

  
    @javascript @google+ @Done
    Scenario: New user logging in to the system with google+ and invoked the permissions
        Given I am on "/en/login/"
        And I follow "Connect with Google"
        And I wait to be redirected
        When I fill in "Email" with "buggy.tamtam1@gmail.com"
        And I press "next"
        And I wait for 3 seconds
        And I fill in "Passwd" with "buggy123"
        And I press "Sign in"
        And I wait to be redirected
        And I wait for 3 seconds
        And I press "submit_deny_access"
        And I wait for 1 seconds
        Then I should see "Authentication failed"
        And I close the browser
    
    @javascript @google+ @Done
    Scenario: New user logging in to the system with google+ and granted the permissions
        Given I am on "/en/login/"
        And I follow "Connect with Google"
        And I wait to be redirected
        And I wait for 3 seconds
        When I fill in "Email" with "buggy.tamtam1@gmail.com"
        And I press "next"
        And I wait for 3 seconds
        And I fill in "Passwd" with "buggy123"
        And I press "Sign in"
        And I wait to be redirected
        And I wait for 3 seconds
        And I press "submit_approve_access"
        And I wait to be redirected
        And I wait for 10 seconds
        Then I should be on "/en/"
        And I close the browser

    @javascript @google+ @Done @revoke-googleplus
    Scenario: User logs in with google+ after previously logging in via a non-google+ flow with the same email address
        Given I am on "/en/login/"
        When I follow "Connect with Google"
        When I fill in "Email" with "buggy.tamtam1@gmail.com"
        And I press "next"
        And I wait for 3 seconds
        And I fill in "Passwd" with "buggy123"
        And I press "Sign in"
        And I wait to be redirected
        Then I should be on "/en/"