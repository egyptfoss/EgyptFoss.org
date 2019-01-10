Feature: User view a request page in the system
  In order to view a request page in the system
  As a user
  I need to navigate to request page and view all request's details

    @not_implemented @add-requests
    Scenario: A user navigating to view requqest page from requqests list page
        Given I am on "/en/"
        When I follow "Success Stories"
        And I follow "new-test-success-title-EGYPT-FOSS"
        Then I should be on "/en/success-stories/new-test-success-title-egypt-foss/"

    @Done @add-requests
    Scenario: A user viewing a request with all its details inserted
        Given I am on "/en/request-center/request-1/"
        Then I should see "request-1"
        And I should see "Description"
        And I should see "request description"
        And I should see "Requirements"
        And I should see "Constraints"
        And I should see "Dataset Request"
        And I should see an ".request-date" element
        And I should see an ".created-by-name" element
        And I should see "Prince"
        And I should see "python"
        And I should see "interest1"
        And I should see "Joint Venture Agreement"
        And I should see "Respond to request"

    @Done
    Scenario: A user viewing a request with only required data
        Given I am on "/en/request-center/request-3/"
        Then I should see "request-3"
        And I should see "Description"
        And I should see "request description"
        And I should see "Joint Venture Agreement "
        And I should see "Respond to request"
        And I should see "Joint Venture Agreement"
        And I should see "Dataset Request"
        And I should not see "interest1"
        And I should not see "python"
        And I should not see "Prince"
        And I should not see "Requirements"
        And I should not see "Constraints"

    @Done
    Scenario: A Not logged-in user viewing a request with only required data and shouldnot see Edit
        Given I am on "/en/request-center/request-3/"
        Then I should see "request-3"
        And I should see "Description"
        And I should see "request description"
        And I should see "Joint Venture Agreement "
        And I should see "Respond to request"
        And I should see "Joint Venture Agreement"
        And I should see "Dataset Request"
        And I should not see "Edit"
        And I should not see "interest1"
        And I should not see "python"
        And I should not see "Prince"
        And I should not see "Requirements"
        And I should not see "Constraints"

    @Done
    Scenario: A logged-in user viewing his pending request
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/request-center/request-4/"
        Then I should see "request-4"
        And I should see "Description"
        And I should see "request description"
        And I should see "Joint Venture Agreement "
        And I should not see "Respond to request"
        And I should see "Joint Venture Agreement"
        And I should see "Dataset Request"
        And I should see "Edit"
        And I should see "interest1"
        And I should see "python"
        And I should see "Prince"
        And I should see "Requirements"
        And I should see "Constraints"