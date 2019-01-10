Feature: add profile update
  In order to add a profile update
  As a logged-in user
  I need to be able to navigate to activity page and add new update and relate it to an interest

  Background:
    Given I am on "/"
    And there are following users:
            | username | email | password | enabled |
            | salem.mohamed | eslam.diaa+salem.mohamed@espace.com.eg | 123456789 | yes |

    @javascript @Done
    Scenario: A not logged-in user view user profile updates but can't add an update 
        Given I am on "/en/"
        When I go to "/members/espace/"
        And I wait to be redirected
        Then I should be on "/en/members/espace/"
        And I should see "espace"
        # shouldn't see profile update textbox
        And I should not see an ".bp-suggestions" element

    @javascript @not_implemented
    Scenario: A logged-in user navigate to his activity page from header menu
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/"
        # hover over the menu in the header
        When I hover ".login-register"
        And I follow "Activity" in certain place ".login-sub"
        Then I should be on "/en/members/espace/"
        And I should see "espace"
        # should see profile update textbox
        And I should see an ".bp-suggestions" element

    @Done
    Scenario: A logged-in user navigate to his activity page from profile side menu
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/"
        When I go to "/members/espace/profile"
        And I follow "user-activity"
        Then I should be on "/en/members/espace/"
        And I should see "espace"
        # should see profile update textbox
        And I should see an ".bp-suggestions" element

    @done @javascript
    Scenario: A logged-in user add a profile update and link it to an interest 
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/"
        When I fill in "whats-new" with "Good Morning great team from Saudi Arabia!"
        And I add "Swimming" to a auto-select "post_interest"
        And I press "aw-whats-new-submit"
        Then I should see "posted an update right now"
        And I should see "Good Morning great team from Saudi Arabia!"
        And I should see "Swimming"

    @done @javascript
    Scenario: A logged-in user add a profile update and link it to more than one interest 
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/members/espace/"
        When I fill in "whats-new" with "Good Morning great team from Saudi Arabia!"
        And I add "Internet Of Things" to a auto-select "post_interest"
        And I add "Driving" to a auto-select "post_interest"
        And I add "Java" to a auto-select "post_interest"
        And I press "aw-whats-new-submit"
        Then I should see "posted an update"
        And I should see "Good Morning great team from Saudi Arabia!"
        And I should see "Swimming"
        And I should see "Driving"
        And I should see "Java"

    @not_working
    Scenario: A logged-in user add a profile update without linking it to an interest 
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/members/espace/"
        When I fill in "whats-new" with "Good Morning great team from Saudi Arabia!"
        And I press "aw-whats-new-submit"
        Then I should see "espace posted an update right now"
        And I should see "Good Morning great team from Saudi Arabia!"

    @not_implemented
    Scenario: A logged-in user add a profile update that exceeds the maximum length 
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/"
        When I fill in "whats-new" with "This is a free online calculator which counts the number of characters or letters in a text, useful for your tweets on Twitter, as well as a multitude of other applications. Whether it is Twitter, Facebook, Yelp or just a post to co-workers or business of. This is a free online calculator which counts the number of characters or letters in a text, useful for your tweets on Twitter, as well as a multitude of other applications. Whether it is Twitter, Facebook, Yelp or just a post to co-workers or business This is a free online calculator which counts the number of characters or letters in a text, useful for your tweets on Twitter, as well as a multitude of other applications. Whether it is Twitter."
        And I press "aw-whats-new-submit"
        # Our maximum length is 700 charecters
        Then I should see "Can't exceed 700 charecters"

    @not_implemented
    Scenario: A logged-in user add a profile update less than the minimum length 
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/"
        When I fill in "whats-new" with "a"
        And I press "aw-whats-new-submit"
        # Our minimum length is 2 charecters
        Then I should see "Can't be less than 2 charecters"

    @not_implemented
    Scenario: A logged-in user add a profile update numbers only
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/"
        When I fill in "whats-new" with "12345675432"
        And I press "aw-whats-new-submit"
        Then I should see "Profile update must include one letter"

    @not_implemented
    Scenario: A logged-in user add a profile update special charecters only
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/"
        When I fill in "whats-new" with ",,,,,,,,,,,"
        And I press "aw-whats-new-submit"
        Then I should see "Profile update must include one letter"

    @Done
    Scenario: A logged-in user add an empty profile update
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/"
        When I fill in "whats-new" with ""
        And I press "aw-whats-new-submit"
        Then I should see "Please enter some content to post."