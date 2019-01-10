Feature: delete profile update
  In order to delete a profile update
  As a logged-in user
  I need to be able to delete update from my activity page

    @Done @javascript
    Scenario: A not logged-in user can't delete user update
        Given I am on "/en/members/espace/"
        Then I should not see "Delete"
        And I close the browser

    @Done @javascript
    Scenario: A logged-in user can't delete another user update
        Given I am a logged in user with "maii.test" and "123456789"
        And I am on "/en/members/foss/"
        Then I should not see "Delete"
        And I close the browser

    @not_working @javascript
    Scenario: A logged-in user can delete his user update
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/"
        When I follow "button btn-delete-confirm bp-secondary-action" in certain comment "Good Morning great team from Saudi Arabia-1!"
        And I press "Ok"
        Then I should not see "Good Morning great team from Saudi Arabia-1!"