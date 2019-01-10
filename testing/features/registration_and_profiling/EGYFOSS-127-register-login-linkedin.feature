Feature: login to the system through (LinkedIn) social network
  In order to login to the system
  As a user
  I need to be able to login to with my (LinkedIn) social accounts,and ability to login directly if permissions granted previously

    @javascript @linkedin @Done
    Scenario: New user logging in to the system with linkedin and invoked the permissions
        Given I am on "/en/login/"
        And I follow "Connect with LinkedIn"
        And I wait to be redirected
        And I follow "Cancel"
        And I wait to be redirected
        And I wait for 1 seconds
        Then I should see "Authentication failed"
 
    @javascript @linkedin @Done @revoke-linkedin
    Scenario: New user logging in to the system with linkedin and granted the permissions
        Given I am on "/en/login/"
        And I follow "Connect with LinkedIn"
        And I wait to be redirected
        And I fill in "session_key-oauthAuthorizeForm" with "buggy.tamtam1@gmail.com"
        And I fill in "session_password-oauthAuthorizeForm" with "buggy123"
        And I press "Allow access"
        And I wait to be redirected
        And I wait for 5 seconds
        Then I should be on "/en/"
        And I close the browser

    @javascript @linkedin @revoke-linkedin @Done
    Scenario: User logs in with linkedin after previously logging in via a non-linkedin flow with the same email address
        Given I am on "/en/login/"
        And I follow "Connect with LinkedIn"
        And I wait to be redirected
        And I fill in "session_key-oauthAuthorizeForm" with "buggy.tamtam1@gmail.com"
        And I fill in "session_password-oauthAuthorizeForm" with "buggy123"
        And I wait for 3 seconds
        And I press "Allow access"
        And I wait to be redirected
        And I wait for 5 seconds
        Then I should be on "/en/"