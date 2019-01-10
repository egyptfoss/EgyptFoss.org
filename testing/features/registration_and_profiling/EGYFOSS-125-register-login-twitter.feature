Feature: login to the system through (Twitter) social network
  In order to login to the system
  As a user
  I need to be able to login to with my (Twitter) social accounts,and ability to login directly if permissions granted previously

    @javascript @twitter @Done
    Scenario: New user logging in to the system with twitter and invoked the permissions
        Given I am on "/en/login/"
        And I follow "Connect with Twitter"
        And I wait to be redirected
        And I press "cancel"
        And I wait to be redirected
        And I follow "Return to EgyptFOSS"
        And I wait for 1 seconds
        Then I should see "Authentication failed"

    @javascript @twitter @not_implemented
    Scenario: New user logging in to the system with twitter and granted the permissions
        Given I login to twitter with "buggy.tamtam1@gmail.com" and "buggy123"
        And I wait to be redirected
        And I follow "Connect with Twitter"
        #And I wait to be redirected
        Then I press "Create a new account"
        And I wait to be redirected
        Then I should see "Please fill in your information in the form below. Once completed, you will be able to automatically sign into our website through your Twitter ID."
        And I fill in "user_email" with "buggy.tamtam4@gmail.com"
        And I wait for 8 seconds
        And I click on the element with css selector "form#info-form input[value='Continue']"
        And I wait to be redirected
        Then I should be on "/en/"
        And I close the browser

    @javascript @twitter @revoke-twitter @not_implemented
    Scenario: User logs in with twitter after previously logging in via a non-twitter flow with the same email address
        Given I login to twitter with "buggy.tamtam1@gmail.com" and "buggy123"
        And I am on "/en/login/"
        And I follow "Connect with Twitter"
        And I wait to be redirected
        And I wait for 5 seconds
        #And I fill in "username_or_email" with "buggy.tamtam1@gmail.com"
        #And I fill in "password" with "buggy123"
        #And I press "allow"
        Then I should be on "/en/"