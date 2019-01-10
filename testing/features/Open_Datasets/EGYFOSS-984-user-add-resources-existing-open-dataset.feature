Feature: User add resources to existing open dataset to the system
  In order to add resources to existing open dataset in the system
  As an User
  I need to provide all attributes needed to add resources to open dataset in the system

    @Done @add-open-datasets
    Scenario: A not logged-in user Adding new resources to open dataset to the system
        Given I am on "/en/open-datasets/new-test-dataset-title-egypt-foss/"
        And I follow "Add More Resources"
        And I wait to be redirected
        Then I should be on "/en/login/?redirected=addresourcesopendataset&redirect_to=http://egyptfoss.com/en/open-dataset/add-resources/"
        And I should see "Please log in to suggest a new resource"

    @javascript @working_chrome_firefox @not_working_phantomjs @not_working
    Scenario: A logged-in user Adding new resources to open dataset
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/open-datasets/add"
        When I Add new frontend open dataset with "Monetizing Mobile Gaming_1", "The mobile gaming industry made $29 billion in 2015 — and it is only set to continue growing (with estimates as high as $49 billion by 2018).","Publisher","Referneces","http://www.google.com","","theme1","Nation","dataset license1"
        And I attach the file "testImages/logo.png" to "open_dataset_resources" with relative path
        And I press "submit"
        And I wait to be redirected
        Then I should see "Monetizing Mobile Gaming_1"

    @javascript @Done
    Scenario: A logged-in user Adding new resources open dataset with null inputs
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/open-datasets/new-test-dataset-title-egypt-foss/"
        And I follow "Add More Resources"
        When I fill in "open_dataset_description" with ""
        And I press "submit"
        Then I should see "Description required"

    @javascript @Done
    Scenario: A logged-in user Adding new open dataset with numbers only in the textfields
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/open-datasets/new-test-dataset-title-egypt-foss/"
        And I follow "Add More Resources"
        When I fill in "open_dataset_description" with "1222121"
        And I press "submit"
        Then I should see "Description must include at least one letter"

    @javascript @Done
    Scenario: A logged-in user Adding new open dataset with special characters only in the textfields
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/open-datasets/new-test-dataset-title-egypt-foss/"
        And I follow "Add More Resources"
        When I fill in "open_dataset_description" with ";;;"
        And I press "submit"
        Then I should see "Description must include at least one letter"