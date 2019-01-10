Feature: create space
  In order to create new space
  As a logged-in user
  I need to be able to create space

    @Done @javascript
    Scenario: Logged in user can create a space
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        When I resize window with height 800 and width 2048 in px
        And I am on "en/collaboration-center/spaces/"
        And I click on the element with css selector "a#new_space"
        And I fill in "new_space_title" with "inserted space test"
        And I click on the element with css selector "button[data-action='addNewCollaborativeSpace']"
        Then I should be on "en/collaboration-center/spaces/"
        And I should see "inserted space test"

    @Done @javascript        
    Scenario: Space title can't be empty
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        When I resize window with height 800 and width 2048 in px
        And I am on "en/collaboration-center/spaces/"
        And I click on the element with css selector "a#new_space"
        And I click on the element with css selector "button[data-action='addNewCollaborativeSpace']"
        Then I should be on "en/collaboration-center/spaces/"
        And I should see "title is required"

    @Done @javascript        
    Scenario: Space title should at least contain one character
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        When I resize window with height 800 and width 2048 in px
        And I am on "en/collaboration-center/spaces/"
        And I click on the element with css selector "a#new_space"
        And I fill in "new_space_title" with "111111"
        And I click on the element with css selector "button[data-action='addNewCollaborativeSpace']"
        Then I should be on "en/collaboration-center/spaces/"
        Then I should see "Title must at least contain one letter"

    @working @javascript        
    Scenario: Space title should be unique for this user
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        When I resize window with height 800 and width 2048 in px
        And I am on "en/collaboration-center/spaces/"
        And I click on the element with css selector "a#new_space"
        And I fill in "new_space_title" with "my space #1"
        And I click on the element with css selector "button[data-action='addNewCollaborativeSpace']"
        Then I should be on "en/collaboration-center/spaces/"
        Then I should see "Title already exists"    
        
        
        
        