Feature: create document in space
  In order create document in space
  As a logged-in user
  I need to be able to create document in spaces

    @Done @javascript
    Scenario: Logged in user can add a document in one of "My Spaces"
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        When I resize window with height 800 and width 2048 in px
        And I am on "en/collaboration-center/spaces/"
        Then I click on the element with css selector "a:contains('my space #1')"
        And I wait to be redirected
        And I click on the element with css selector "a:contains('New Document')"
        And I wait to be redirected
        And I fill in "document_title" with "new document testing"
        And I fill in "document_content" with "new document testing"
        And I press "Save"
        And I wait to be redirected
        Then I should see "Document added successfully"
        And I should see "new document testing"
        
    
    @Done @javascript
    Scenario: Logged in user can't add a document in one of "Others Spaces"
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        When I am on "en/collaboration-center/spaces/5/documents/add/"
        Then I should be on "?status=403"
        
     
    @Done @javascript       
    Scenario: Logged in user can't add a document in one of "Shared With Me" spaces
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        When I am on "en/collaboration-center/shared/"
        And I click on "user space #3"
        Then I should not see "New Document"

    @Done @javascript        
    Scenario: Document title can't be empty
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "en/collaboration-center/spaces/2/documents/add/"
        And I press "Save"
        Then I should see "title required"

    @Done @javascript        
    Scenario: Document title should at least contain one character
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "en/collaboration-center/spaces/2/documents/add/"
        And I fill in "document_title" with "11111"
        And I press "Save"
        Then I should see "Title must include at least one letter"
        
    @Done @javascript
    Scenario: Document content can be empty
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        When I resize window with height 800 and width 2048 in px
        And I am on "en/collaboration-center/spaces/"
        Then I click on the element with css selector "a:contains('my space #1')"
        And I wait to be redirected
        And I click on the element with css selector "a:contains('New Document')"
        And I wait to be redirected
        And I fill in "document_title" with "new document testing without content"
        And I fill in "document_content" with ""
        And I press "Save"
        And I wait to be redirected
        Then I should see "Document added successfully"
        And I should see "new document testing without content"

    @Done @javascript        
    Scenario: Document content if, not empty, should contain at least one character.
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "en/collaboration-center/spaces/2/documents/add/"
        And I fill in "document_title" with "document test with only numbers content"
        And I fill in "document_content" with "11111111"
        And I press "Save"
        Then I should see "Content must include at least one letter"

    @not_implemented        
    Scenario: Newly created document should initially has state Draft
        
        
        