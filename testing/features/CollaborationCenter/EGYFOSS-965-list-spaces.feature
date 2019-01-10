Feature: list shared documents by user
  In order list shared documents
  As a logged-in user
  I need to be able to click on "Shared With me" spaces

    @not_valid
    Scenario: Not logged-in user sees empty state for collaboration center
        Given I am on "en/collaboration-center/spaces"
        Then I should be on "en/login/"
        And I should see "Please log in to access this page"
    
    @not_implemented    
    Scenario: Not logged-in user can list all published spaces (that contains at least a publish document)'

    @not_implemented
    Scenario: Not logged-in user can't add a space (New space) button should redirect to logging page
    
    @not_implemented
    Scenario: Not logged in user can see space creation date and no. of documents inside it

    @not_implemented
    Scenario: Not logged in user can open a space from “Spaces List” by clicking on the title

    @not_implemented
    Scenario: Logged in User sees empty state for collaboration center

    @not_implemented
    Scenario: Logged in User sees all Published spaces

    @Done
    Scenario: Logged In user sees “New Space” button
      Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "en/collaboration-center/spaces/"
        Then I should see "New Space"
       
    @Done
    Scenario: Logged in user sees his spaces under “My space” menu
      Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "en/collaboration-center/"
        And I click on the element with css selector "a:contains(My Spaces)"
        Then I should be on "en/collaboration-center/spaces/"
        And I should see "my space #1"

    @Done
    Scenario: Logged in user can see “Rename” space beside his spaces
      Given I am a logged in user with "bougy.tamtam" and "123456789"
          And I am on "en/collaboration-center/"
          And I click on the element with css selector "a:contains(My Spaces)"
          Then I should be on "en/collaboration-center/spaces/"
          And I should find element with css selector ".rename_space"
          
    @Done  
    Scenario: Logged in user can see “share” his spaces
      Given I am a logged in user with "bougy.tamtam" and "123456789"
          And I am on "en/collaboration-center/"
          And I click on the element with css selector "a:contains(My Spaces)"
          Then I should be on "en/collaboration-center/spaces/"
          And I should find element with css selector ".rename_space"

    @not_implemented      
    Scenario: Logged in user can see “Delete” space beside his spaces

    @Done
    Scenario: Logged in user can see space creation date and no. of documents inside it
    Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "en/collaboration-center/spaces"
        Then I should find element with css selector "div.space .space_title"
        And I should see "files"
        And I should find element with css selector "div.space .modified-date"
        
    @Done  
    Scenario: Logged in user can open a space from “Spaces List” by clicking on the title
      Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "en/collaboration-center/"
        And I click on the element with css selector "a:contains(My Spaces)"
        Then I should be on "en/collaboration-center/spaces/"
        And I should see "my space #1"
        And I click on the element with css selector "div#space_2 a:contains(my space)"
        Then I should be on "en/collaboration-center/spaces/2/"
        And I should see "my document #1"