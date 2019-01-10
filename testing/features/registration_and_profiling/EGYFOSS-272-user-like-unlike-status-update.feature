Feature: Like/unlike a status update
  In order to like/unlike a status update
  As a logged-in user
  I need to be able to view a status update and like/unlike it

    @Done
    Scenario: A not logged-in user can't like/unlike a status update
        Given I am on "/members/foss/"
        # shouldn't see profile update textbox
        Then I should not see an ".button fav bp-secondary-action btn btn-light" element

    @javascript @Done
    Scenario: A logged-in user like his status update
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/members/espace/"
        When I follow "button fav bp-secondary-action btn btn-light" in certain comment "Good Morning great team from Saudi Arabia!"
        # When I do "like" "Good Morning great team from Saudi Arabia!"
        Then the response should contain "Like"
        And likes counter should add more one on "Good Morning great team from Saudi Arabia!"

    @javascript @Done
    Scenario: A logged-in user unlike his status update
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/members/espace/"
        When I follow "button bp-secondary-action btn btn-light unfav" in certain comment "Good Morning great team from Saudi Arabia!"
        Then the response should contain "Like"
        And likes counter should subtract one on "Good Morning great team from Saudi Arabia!"

    @javascript @Done
    Scenario: A logged-in user like other status update
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/members/foss/"
        When I follow "button fav bp-secondary-action btn btn-light" in certain comment "A very rich profile, keep it up"
        Then the response should contain "Like"
        And likes counter should add more one on "A very rich profile, keep it up"

    @javascript @Done
    Scenario: A logged-in user unlike other status update
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/members/foss/"
        When I follow "button bp-secondary-action btn btn-light unfav" in certain comment "A very rich profile, keep it up"
        Then the response should contain "Like"
        And likes counter should subtract one on "A very rich profile, keep it up"