Feature: add comment to a profile update
  In order to add comment to a profile update
  As a logged-in user
  I need to be able to navigate to my activity/ others activity page and add a comment to an update

    @javascript @Done
    Scenario: A not logged-in user view comments on profile updates but can't add a comment
        Given I am on "/en/"
        When I go to "/members/espace/"
        And I wait to be redirected
        Then I should be on "/en/members/espace/"
        And I should see "replied"
        # shouldn't see comment textbox
        And I should not see an ".button acomment-reply bp-primary-action" element

    @javascript @Done
    Scenario: A logged-in user adding a comment to his update
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/members/espace/"
        When I comment on "Good Morning great team from Saudi Arabia!" with "Good Morning buddy."
        Then I should see "espace replied"
        And I should see "Good Morning buddy."
        And comments counter should add more one on "Good Morning great team from Saudi Arabia!"

    @javascript @not_working
    Scenario: A logged-in user adding an empty comment to his update
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/members/espace/"
        When I comment on "Good Morning great team from Saudi Arabia!" with ""
        Then I should see "Please do not leave the comment area blank."

    @javascript @Done
    Scenario: A logged-in user adding a comment to other user update
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/members/foss/"
        When I comment on "Good Morning team!" with "Have a good day foss."
        Then I should see "espace replied"
        And I should see "Have a good day foss."
        And comments counter should add more one on "Good Morning team!"

    @javascript @Done
    Scenario: A logged-in user adding an empty comment to others update
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/members/foss/"
        When I comment on "Good Morning foss" with ""
        Then the response should contain "Please do not leave the comment area blank."
