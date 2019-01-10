Feature: User view an open dataset page in the system
  In order to view an open dataset page in the system
  As a user
  I need to navigate to open dataset page and view all open dataset's details

    @Done @add-open-datasets
    Scenario: A user navigating to view open dataset page from open datasets list page
        Given I am on "/en/"
        When I follow "Data"
        And I follow "new-test-dataset-title-ninteen"
        Then I should be on "/en/open-datasets/new-test-dataset-title-ninteen/"

    @Done @add-open-datasets
    Scenario: A user viewing an open dataset with all its details inserted
        Given I am on "/en/open-dataset/new-test-dataset-title-egypt-foss/"
        Then I should see "new-test-dataset-title-EGYPT-FOSS"
        And I should see "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book."
        And I should see "mark zuckerberg"
        And I should see "dataset type one"
        And I should see "prince"
        And I should see "dataset license1"
        And I should see "It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged."
        And I should see "Sand cats live in the deserts of North Africa and Southwest and Central Asia."
        And I should see "http://example.com"
        And I should see "python"

    @Done @add-open-datasets
    Scenario: A user viewing an open dataset with only required data
        Given I am on "/en/open-dataset/new-test-dataset-title-required-data-only/"
        Then I should see "new-test-dataset-title-required-data-only"
        And I should see "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book."
        And I should see "mark zuckerberg"
        And I should see "dataset type one"
        And I should see "prince"
        And I should see "dataset license1"
        And I should see "Sand cats live in the deserts of North Africa and Southwest and Central Asia."
        And I should see "http://example.com"