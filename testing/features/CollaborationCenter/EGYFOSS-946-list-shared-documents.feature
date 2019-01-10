Feature: list shared documents by user
  In order list shared documents
  As a logged-in user
  I need to be able to click on "Shared With me" spaces

    @done
    Scenario: Logged-in user can see "Space's name" in the documents list
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "en/collaboration-center/shared"
        Then I should see "user space tech #2 php"
    
    @done
    Scenario: Logged in user sees ONLY documents shared with him.
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "en/collaboration-center/shared"
        Then I should see "user document #3"
        And I should not see "user space #4"
     
    @done @javascript       
    Scenario: Logged in user can't add “New document” in "Shared with me" spaces.
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "en/collaboration-center/shared"
        Then I click on "user space #3"
        And I should not see "new docment"

    @done      
    Scenario: Logged in user can see document's last update date and creator name.
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "en/collaboration-center/shared"
        Then I should find element with css selector "div.document span.documentDate"
        Then I should find element with css selector "div.document span.documentOwner"

    @not_implemented     
    Scenario: Logged in user can open a document from “documents List” by clicking on the title

    @not_implemented      
    Scenario: Logged in user can edit documents shared with him
      
    @not_implemented      
    Scenario: Logged in user can see “Rename” document shared with him if allowed (according to the his permission)

    @not_implemented      
    Scenario: Logged in user can see “share” document shared with him if allowed (according to the his permission)
