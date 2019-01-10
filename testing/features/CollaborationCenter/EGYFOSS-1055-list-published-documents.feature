Feature: As a user I want to list published documents
  In order list of published documents
  As a user
  I need to be able to list/view published documents

    @remove-published-document @Done
    Scenario: Not Logged-in user can view empty statement of no published documents
        Given I am on "en/collaboration-center/"
        Then I should see "No published documents!"

    @Done
    Scenario: Logged-in user can view empty statement of no published documents
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "en/collaboration-center/"
        Then I should see "No published documents!"

    @javascript @Done
    Scenario: Not Logged-in user can view empty statement of no published documents by filter
        Given I am on "en/collaboration-center/"
        When I click on the element with css selector ".technologies"
        Then I should see "No published documents!"

    @javascript @Done
    Scenario: Logged-in user can view empty statement of no published documents by filter
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "en/collaboration-center/"
        When I click on the element with css selector ".technologies"
        Then I should see "No published documents!"
    
    @Done
    Scenario: Logged-in user can set his own document to published
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "/en/collaboration-center/spaces/2/document/4/edit/"
        When I select "published" from "status"
        And I press "Save"

    @Done
    Scenario: Logged-in user can set his own document to published
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/collaboration-center/spaces/17/document/18/edit/"
        When I select "published" from "status"
        And I press "Save"

    @Done
    Scenario: Not Logged-in user can view published documents
        Given I am on "en/collaboration-center/"
        Then I should see "my document #2"
        And I should see "user document subtype #2 developer"

    @Done
    Scenario: Logged-in user can view published documents
        Given I am on "en/collaboration-center/"
        Then I should see "my document #2"
        And I should see "user document subtype #2 developer"

    @Done
    Scenario: Not Logged-in user can view published documents title, date ,and creator
        Given I am on "en/collaboration-center/"
        Then I should see "my document #2"
        And I should see "user document subtype #2 developer"
        And the response should contain "modified-date"
        And the response should contain "space_title"
        And the response should contain "documentOwner"

    @Done
    Scenario: Logged-in user can view published documents title, date ,and creator
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "en/collaboration-center/"
        Then I should see "my document #2"
        And I should see "user document subtype #2 developer"
        And the response should contain "modified-date"
        And the response should contain "space_title"
        And the response should contain "documentOwner"

    @Done
    Scenario: Not Logged-in user can view single publish document
        Given I am on "en/collaboration-center/"
        And I should see "my document #2"
        When I follow "my document #2"
        Then I should be on "/en/collaboration-center/published/4/"
        And I should see "my document #2"
        And the response should contain "content-area"

    @Done
    Scenario: Logged-in user can view single publish document
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "en/collaboration-center/"
        And I should see "my document #2"
        When I follow "my document #2"
        Then I should be on "/en/collaboration-center/published/4/"
        And I should see "my document #2"
        And the response should contain "content-area"

    @Done
    Scenario: Logged-in user can change his own document to published and change content
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "/en/collaboration-center/spaces/2/document/3/edit/"
        When I select "published" from "status"
        And I select "event" from "section"
        And I fill in "document_content" with "Weclome Egyptfoss Team"
        And I press "Save"

    @Done
    Scenario: Not Logged-in user can view single publish document and its latest published content
        Given I am on "/en/collaboration-center/"
        When I follow "my document #1"
        Then I should not see "test"
        And I should see "Weclome Egyptfoss Team"

    @Done
    Scenario: Logged-in user can view single publish document and its latest published content
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "en/collaboration-center/"
        When I follow "my document #1"
        Then I should not see "test"
        And I should see "Weclome Egyptfoss Team"

    @not_implemented
    Scenario: Not Logged-in user can view single publish document and its latest published content and not able to view any previous content

    @javascript @Done
    Scenario: Not Logged-in user can view published documents using filter
        Given I am on "en/collaboration-center/"
        When I click on the element with css selector ".technologies"
        Then I should see "my document #2"

    @javascript @Done
    Scenario: Logged-in user can view published documents using filter
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "en/collaboration-center/"
        When I click on the element with css selector ".technologies"
        Then I should see "my document #2"

    @Done
    Scenario: Logged-in user can change his own document to draft and change content
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "/en/collaboration-center/spaces/29/document/30/edit/"
        When I select "draft" from "status"
        And I select "event" from "section"
        And I fill in "document_content" with "test"
        And I press "Save"

    @Done
    Scenario: Not Logged-in user can view single publish document and its latest published content of document changed back to draft
        Given I am on "en/collaboration-center/"
        When I follow "my document #1"
        Then I should not see "test"
        And I should see "Weclome Egyptfoss Team"

    @Done
    Scenario: Logged-in user can view single publish document and its latest published content of document changed back to draft
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "en/collaboration-center/"
        When I follow "my document #1"
        Then I should not see "test"
        And I should see "Weclome Egyptfoss Team"