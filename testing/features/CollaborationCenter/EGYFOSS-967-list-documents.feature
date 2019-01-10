Feature: list my documents in a space
  In order list my documents in a space
  As a logged-in user
  I need to be able to click on a space to see it's documents

    @not_implemented
    Scenario: Not logged-in user can see "Space's name" in the documents list

    @not_implemented
    Scenario: Not logged-in user can list all published documents inside the space

    @not_implemented
    Scenario: Not logged-in user can't add a document (New Document) button should redirect to logging page

    @Done
    Scenario: Not logged in user can't edit in document, should be redirect to logging page
      Given I am on "/en/collaboration-center/spaces/2/document/3/edit/"
          Then I should be on "en/login/"
          And I should see "Please log in to access this page"
    
    @not_implemented      
    Scenario: Not logged in user can see document's creation date and creator name.

    @not_implemented
    Scenario: Not logged in user can open a document from “documents List” by clicking on the title

    @Done
    Scenario: Logged-in user can see "Space's name" in the documents list
      Given I am a logged in user with "bougy.tamtam" and "123456789"
          And I am on "en/collaboration-center/spaces"
          Then I click on the element with css selector "a:contains('my space #1')"
          And I wait to be redirected
          Then I should see "my space #1"

    @not_implemented      
    Scenario: Logged in user sees all published documents in other's spaces
    
    @not_implemented
    Scenario: Logged in user can't add “New document” in other's space

    @Done
    Scenario: Logged in user can't edit in document isn't shared with him
       Given I am a logged in user with "bougy.tamtam" and "123456789"
       And I am on "en/collaboration-center/shared/"
       And I click on the element with css selector "a:contains('user space interest #3 interest1')"
       And I click on the element with css selector "a:contains('user document tax and subtype #5')"
       Then I should see "You are not authorized to perform this action."

    @Done
    Scenario: Logged in user can see document's creation date and creator name.
       Given I am a logged in user with "bougy.tamtam" and "123456789"
       And I am on "en/collaboration-center/shared/spaces/11"
       Then I should find element with css selector ".modified-date"
       And I should find element with css selector "div.document span.documentOwner"

    @Done   
    Scenario: Logged in user can open a document from “documents List” by clicking on the title
      Given I am a logged in user with "bougy.tamtam" and "123456789"
         And I am on "en/collaboration-center/spaces/"
         And I click on the element with css selector "a:contains('my space #1')"
         And I click on the element with css selector "a:contains('my document #1')"
         Then I should be on "en/collaboration-center/spaces/2/document/3/edit/"

    @Done     
    Scenario: Logged in User sees empty state for empty space
      Given I am a logged in user with "bougy.tamtam" and "123456789"
         And I am on "en/collaboration-center/spaces/"
         And I click on the element with css selector "a:contains('my empty space #1')"
         Then I should see "No documents!"

    
    @not_implemented       
    Scenario: Logged in user sees all his documents in his space regardless their status
      
    @Done @javascript
    Scenario: Logged in user can add “New document” in his space
      Given I am a logged in user with "bougy.tamtam" and "123456789"
        When I resize window with height 800 and width 2048 in px
        And I am on "en/collaboration-center/spaces/"
        Then I click on the element with css selector "a:contains('my space #1')"
        And I wait to be redirected
        And I click on the element with css selector "a:contains('New Document')"
        And I wait to be redirected
        And I fill in "document_title" with "new document from listing test 1"
        And I fill in "document_content" with ""
        And I press "Save"
        And I wait to be redirected
        Then I should see "Document added successfully"
        And I should see "new document from listing test 1"

    @Done    
    Scenario: Logged in user can edit his documents
       Given I am a logged in user with "bougy.tamtam" and "123456789"
         And I am on "en/collaboration-center/spaces/"
         And I click on the element with css selector "a:contains('my space #1')"
         And I click on the element with css selector "a:contains('my document #1')"
         Then I should be on "en/collaboration-center/spaces/2/document/3/edit/"
    
    @Done
    Scenario: Logged in user can see document's creation date but NOT creator name.
       Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "en/collaboration-center/spaces/2/"
        Then I should find element with css selector ".modified-date"

    @not_implemented     
    Scenario: Logged in user can see “Rename” document in his spaces

    @Done
    Scenario: Logged in user can see “share” his documents with others
       Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "en/collaboration-center/spaces/2/"
        Then I should find element with css selector ".invite-space-document"

    @not_implemented
    Scenario: Logged in user can see “Delete” document in his spaces