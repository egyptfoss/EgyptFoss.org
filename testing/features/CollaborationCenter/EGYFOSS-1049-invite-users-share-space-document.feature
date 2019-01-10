Feature: Invite users to contribute on a space or document
  In order list of users
  As a logged-in user
  I need to be able to invite users to my space or my document

    @not_implemented
    Scenario: Logged-in user should see empty statement in invite users

    @javascript @Done
    Scenario: Logged-in user can invite user to his space/document
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "/en/collaboration-center/spaces/"
        When I invite new user with id "4" and name "maii.test"
        Then I should see "Editor"
        And I should see "maii.test"

    @javascript @Done
    Scenario: Logged-in user should be able to cancel
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "en/collaboration-center/spaces/"
        When I invite new user with id "4" and name "maii.test"
        Then I press "cancel"

    @javascript @Done
    Scenario: Logged-in user should not be allowed to invite empty user
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "en/collaboration-center/spaces/"
        When I invite new user with id "4" and name "maii.test"
        And I invite new user with id "4" and name "maii.test" with opened popup
        Then I should see "User required"

    @javascript @Done
    Scenario: Logged-in user should be able to save the list of users he added, and send emails to invited users
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "en/collaboration-center/spaces/"
        When I invite new user with id "4" and name "maii.test"
        And I press "save_invited"
        And I wait for 10 seconds
        Then I should see "Sharing Settings saved successfully. Email is sent to all inviters with their sharing permission"
        And I should receive an email with subject "invited you to collaborate on"

    @javascript @Done
    Scenario: Logged-in user should not be allowed to invite existing user
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "en/collaboration-center/spaces/"
        When I invite new user with id "4" and name "maii.test"
        And I wait for 10 seconds
        Then I should see "User already added"

    @javascript @Done
    Scenario: Logged-in user should be able to delete added users
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "en/collaboration-center/spaces/"
        When I invite new user with id "4" and name "maii.test"
        Then I click on the element with css selector ".remove-icon"
        #And I should see empty list

    @javascript @Done
    Scenario: Logged-in user should be able to find the shared document in his shared documents
        Given I am a logged in user with "maii.test" and "123456789"
        And I am on "/en/collaboration-center/shared/"
        And I wait for 10 seconds
        Then I should see "my space #1"
