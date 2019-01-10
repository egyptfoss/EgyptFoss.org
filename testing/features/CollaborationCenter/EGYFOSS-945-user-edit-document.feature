Feature: User can edit a document
  In order edit a document
  As a logged-in user
  I need to be able to edit document title,content and status

    @javascript @Done
    Scenario: Logged-in user with permission to edit document should not add empty document title
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "/en/collaboration-center/shared/"
        And I follow "user space #3"
        When I follow "user document #4"
        And I fill in "document_title" with ""
        And I press "Save"
        Then I should see "Title required"

    @javascript @Done
    Scenario: Logged-in user with permission to edit document should not add special characters only document title
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "/en/collaboration-center/shared/"
        When I follow "user space #3"
        And I follow "user document #4"
        And I fill in "document_title" with "$$$"
        And I press "Save"
        Then the response should contain "Title must include at least one letter"

    @javascript @Done
    Scenario: Logged-in user with permission to edit document should not add numbers only document title
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "/en/collaboration-center/shared/"
        When I follow "user space #3"
        And I follow "user document #4"
        And I fill in "document_title" with "123"
        And I press "Save"
        Then the response should contain "Title must include at least one letter"

    @javascript @Done
    Scenario: Logged-in user with permission to edit document should not add empty content
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "/en/collaboration-center/shared/"
        When I follow "user space #3"
        And I follow "user document #4"
        And I wait for ajax return
        And I fill in "document_content" with ""
        And I press "Save"
        Then I should see "Content required"

    @javascript @Done
    Scenario: Logged-in user with permission to edit document should not add special characters only content
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "/en/collaboration-center/shared/"
        When I follow "user space #3"
        And I follow "user document #4"
        And I wait for ajax return
        And I fill in "document_content" with "$$$"
        And I press "Save"
        Then I should see "Content must include at least one letter"

    @javascript @Done
    Scenario: Logged-in user with permission to edit document should not add numbers only content
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "/en/collaboration-center/shared/"
        When I follow "user space #3"
        And I follow "user document #4"
        And I wait for ajax return
        And I fill in "document_content" with "123"
        And I press "Save"
        Then I should see "Content must include at least one letter"

    @javascript @Done
    Scenario: Logged-in user with edit permission should be able to edit a document and not view any status
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "/en/collaboration-center/shared/"
        When I follow "user space #3"
        And I follow "user document #4"
        Then I should not see "status"

    @javascript @Done
    Scenario: Logged-in user with review permission should be able to edit a document and view status draft and reviewed
        Given I am a logged in user with "maii.test" and "123456789"
        And I am on "/en/collaboration-center/shared/"
        When I follow "user document #4"
        Then I should see "status"
        And I should see "draft"
        And I should see "reviewed"
        And I should not see "published"

    @javascript @Done
    Scenario: Logged-in user with publisher permission should be able to edit a document and view status draft and reviewed and published
        Given I am a logged in user with "leen.tarek" and "123456789"
        And I am on "/en/collaboration-center/shared/"
        When I follow "user document #4"
        Then I should see "status"
        And I should see "draft"
        And I should see "reviewed"
        And I should see "published"

    @javascript @Done
    Scenario: Logged-in user with owner permission should be able to edit a document and view status draft and reviewed and published
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/collaboration-center/spaces/8/"
        When I follow "user document #4"
        Then I should see "status"
        And I should see "draft"
        And I should see "reviewed"
        And I should see "published"

    @javascript @Done
    Scenario: Logged-in user with edit permission should be able to edit a document
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "/en/collaboration-center/shared/"
        When I follow "user space #3"
        And I follow "user document #4"
        And I wait to be redirected
        And I fill in "document_title" with "Hello Editor"
        And I press "Save"
        And I wait to be redirected
        Then I should see "Document Title"
        And the response should contain "Hello Editor"

    @javascript @Done
    Scenario: Logged-in user with review permission should be able to edit a document
        Given I am a logged in user with "maii.test" and "123456789"
        And I am on "/en/collaboration-center/shared/"
        When I follow "user document #4"
        And I wait to be redirected
        And I fill in "document_title" with "Hello Reviewer"
        And I press "Save"
        And I wait to be redirected
        Then I should see "Document Title"
        And the response should contain "Hello Reviewer"

    @javascript @Done
    Scenario: Logged-in user with publisher permission should be able to edit a document
        Given I am a logged in user with "leen.tarek" and "123456789"
        And I am on "/en/collaboration-center/shared/"
        When I follow "Hello Reviewer"
        And I wait to be redirected
        And I fill in "document_title" with "Hello Publisher"
        And I press "Save"
        And I wait to be redirected
        Then I should see "Document Title"
        And the response should contain "Hello Publisher"

    @javascript @Done
    Scenario: Logged-in user with owner permission should be able to edit a document
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/collaboration-center/spaces/"
        When I follow "user space #2"
        And I follow "Hello Publisher"
        And I wait to be redirected
        And I fill in "document_title" with "user document #4"
        And I select "published" from "status"
        And I press "Save"
        And I wait for 10 seconds
        And I press popup save button
        And I wait to be redirected
        Then I should see "Document Title"
        And the response should contain "user document #4"  

    @javascript @Done
    Scenario: Logged-in user with owner permission should be able to edit a document inside a space
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/collaboration-center/spaces/"
        When I follow "user space #3"
        And I follow "Hello Editor"
        And I wait to be redirected
        And I fill in "document_title" with "user document #4"
        And I select "published" from "status"
        And I press "Save"
        And I wait for 10 seconds
        And I press popup save button
        And I wait to be redirected
        Then I should see "Document Title"
        And the response should contain "user document #4"    

    @javascript @Done
    Scenario: Logged-in user with edit permission should not be able to edit published or reviewed document
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "/en/collaboration-center/shared/"
        When I follow "user space #3"
        And I follow "user document #4"
        Then I should not see "Save"

    @javascript @Done
    Scenario: Logged-in user with review permission should not be able to edit published
        Given I am a logged in user with "maii.test" and "123456789"
        And I am on "/en/collaboration-center/shared/"
        When I follow "user document #4"
        Then I should not see "Save"

    @javascript @Done
    Scenario: Logged-in user with publisher permission should be able to edit a  published or reviewed document
        Given I am a logged in user with "leen.tarek" and "123456789"
        And I am on "/en/collaboration-center/shared/"
        When I follow "user document #4"
        Then the response should contain "Save"

    @javascript @Done
    Scenario: Logged-in user with owner permission should be able to edit a  published or reviewed document
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/collaboration-center/spaces/"
        When I follow "user space #3"
        And I follow "user document #4"
        Then the response should contain "Save"