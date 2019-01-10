Feature: User view list of all open datasets in the system
  In order to list all open datasets in the system
  As an User
  I need to be able to navigate to open datasets list page and load more open datasets

    @Done
    Scenario: A not logged-in user navigating to open datasets list
        Given I am on "/en/"
        When I follow "Data"
        Then I should be on "/en/open-datasets/"
        And I should see "Open Datasets"

    @Done @set-empty-list-open-dataset @return-list-open-dataset
    Scenario: A not logged-in user seeing an empty open datasets list
        Given I am on "/en/open-datasets/"
        Then I should see "There are no Open Datasets yet, Suggest Open Dataset"
        When I follow "Suggest Open Dataset"
        Then I should be on "/en/login/"
        And I should see "Please log in to suggest a new open dataset"

    @Done @set-empty-list-open-dataset @return-list-open-dataset
    Scenario: A logged-in user seeing an empty open datasets list
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/open-datasets/"
        Then I should see "There are no Open Datasets yet, Suggest Open Dataset"
        When I follow "Suggest Open Dataset"
        Then I should be on "/en/open-datasets/add/"
        And I should see "Suggest Open Dataset"
        And I close the browser

    @Done @add-open-datasets
    Scenario: A not logged-in user viewing 10 open datasets per page
        Given I am on "/en/open-datasets/"
        Then I should see 10 ".dataset-card" elements
        And I should see "Show more"

    @javascript @Done
    Scenario: A not logged-in user loading more open datasets
        Given I am on "/en/open-datasets/"
        When I follow "Show more"
        And I wait for 5 seconds
        Then I should see more "10" or more ".dataset-card" elements
        And I should not see "Show more"

    @Done
    Scenario: A not logged-in user viewing open dataset fields
        Given I am on "/en/open-datasets/"
        Then "card-title" should be visible
        And "card-summary" should be visible
        And "card-meta" should be visible

    @Done
    Scenario: A logged-in user navigating to open dataset list
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/"
        When I follow "Data"
        Then I should be on "/en/open-datasets/"
        And I should see "Open Datasets"

    @Done
    Scenario: A logged-in user viewing 10 open datasets per page
        Given I am a logged in user with "espace" and "123456789"
        Given I am on "/en/open-datasets/"
        Then I should see 10 ".dataset-card" elements
        And I should see "Show more"

    @javascript @Done
    Scenario: A logged-in user loading more open datasets
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/open-datasets/"
        When I follow "Show more"
        And I wait for 5 seconds
        Then I should see more "10" or more ".dataset-card" elements
        And I should not see "Show more"

    @Done
    Scenario: A logged-in user viewing open dataset fields
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/open-datasets/"
        Then "card-title" should be visible
        And "card-summary" should be visible
        And "card-meta" should be visible

    @Done
    Scenario: A logged-in user viewing open dataset themes
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/open-datasets/"
        Then "categories-list" should be visible
        And I should see "theme1"

    @javascript @Done
    Scenario: A user seeing empty list after filtering
        Given I resize window with height 800 and width 2048 in px
        And I am a logged in user with "foss" and "F0$$"
        And I am on "/en/open-datasets/?theme=hii"
        And I wait for 1 seconds
        Then I should see "There are no Open Datasets yet, Suggest Open Dataset"
        And I follow "Suggest Open Dataset"
        And I wait to be redirected
        Then I should be on "/en/open-datasets/add/"
        And I should see "Suggest Open Dataset"
        And I close the browser

    @javascript @Done
    Scenario: A user filtering the open dataset list with theme
        Given I resize window with height 800 and width 2048 in px
        And I am on "/en/open-datasets/"
        And I follow "prince"
        And I wait for 1 seconds
        Then I should see 10 ".dataset-card" elements
        And I should be on "/en/open-datasets/?theme=prince"

    @javascript @Done
    Scenario: A user filtering the opend ataset list with type
        Given I resize window with height 800 and width 2048 in px
        And I am on "/en/open-datasets/"
        And I follow "prince"
        And I click on the element with css selector "div.type-filter"
        And I click on the element with css selector "option:contains('dataset type one')"
        Then I should see 10 ".dataset-card" elements
        And I should be on "/en/open-datasets/?theme=prince&type=dataset type one"
     
    @javascript @Done
    Scenario: A user filtering the opend dataset list with license
        Given I resize window with height 800 and width 2048 in px
        And I am on "/en/open-datasets/"
        And I follow "prince"
        And I click on the element with css selector "div.type-filter"
        And I click on the element with css selector "option:contains('dataset license1')"
        Then I should see 10 ".dataset-card" elements
        And I should be on "/en/open-datasets/?theme=prince&license=dataset license1"

    @javascript @Done
    Scenario: A user resetting the filteration in open dataset list
        Given I resize window with height 800 and width 2048 in px
        And I am on "/en/open-datasets/?theme=all&type=dataset type one&license=dataset license1"
        And I wait for 1 seconds
        When I follow "prince"
        When I click on the element with css selector "button.reset-filters"
        Then I should be on "/en/open-datasets/?theme=prince"


