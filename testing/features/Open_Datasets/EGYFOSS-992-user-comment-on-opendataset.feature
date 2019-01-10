Feature: add comment on an open dataset
  In order to add comment on an open dataset
  As a logged-in user
  I need to be able to navigate to open datasets page and add a comment to this open dataset

    @Done @add-open-datasets
    Scenario: A not logged-in user view comments on open datasets but can't add a comment
        Given I am on "/en/"
        When I go to "/en/open-datasets/new-test-dataset-title-one/"
        And I wait to be redirected
        And I should not see an ".form-control" element
        And I should not see an ".submit" element

    @Done @add-open-datasets
    Scenario: A logged-in user adding a comment to an open dataset
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/open-datasets/new-test-dataset-title-one/"
        When I comment on the dataset with "A very useful article"
        Then I should see "espace"
        And I should see "A very useful article"
        And dataset comments counter should add more one

    @javascript @Done @add-open-datasets
    Scenario: A logged-in user adding an empty comment to an open dataset
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/open-datasets/new-test-dataset-title-one/"
        When I comment on the dataset with ""
        And I wait for 2 seconds
        Then I should see "Comment can not be empty"

    @javascript @Done @add-open-datasets
    Scenario: A logged-in user adding a reply to a comment on an open dataset
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/open-datasets/new-test-dataset-title-one/"
        When I reply on "A very useful article" with "More than perfect"
        And I go to "/en/open-datasets/new-test-dataset-title-one/"
        And I wait to be redirected
        Then the response should contain "More than perfect"
        And dataset comments counter should add more one
