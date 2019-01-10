Feature: User adds open dataset to the system
  In order to add an open dataset in the system
  As an User
  I need to provide all attributes needed to add an open dataset in the system

    @Done
    Scenario: A not logged-in user Adding new open dataset to the system
        Given I am on "/en/open-datasets/add"
        And I wait to be redirected
        Then I should be on "/en/login/?redirected=addopendataset&redirect_to=http%3A%2F%2Ffoss.espace.ws%2Fen%2Fopen-datasets%2Fadd%2F"
        And I should see "Please log in to suggest a new open dataset"

    @javascript @working_chrome_firefox @not_working_phantomjs @not_working
    Scenario: A logged-in user Adding new open dataset to the system with valid inputs
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/open-datasets/add"
        When I Add new frontend open dataset with "Monetizing Mobile Gaming", "The mobile gaming industry made $29 billion in 2015 — and it is only set to continue growing (with estimates as high as $49 billion by 2018).","Publisher","Referneces","http://www.google.com","","theme1","Nation","dataset license1"
        #And I attach "logo.png" to "open_dataset_resources"
        And I attach the file "testImages/logo.png" to "open_dataset_resources[]" with relative path
        And I add "Java" to a auto-select "interest"
        And I add "PHP" to a auto-select "interest"
        And I add "Python" to a auto-select "interest"
        And I press "submit"
        And I wait to be redirected
        Then I should see "Monetizing Mobile Gaming"
       
    @javascript @working_chrome_firefox @not_working_phantomjs @not_working
    Scenario: A logged-in user Adding new open dataset with only required inputs
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/open-datasets/add"
        When I Add new frontend open dataset with "Monetizing Mobile Gaming_1", "The mobile gaming industry made $29 billion in 2015 — and it is only set to continue growing (with estimates as high as $49 billion by 2018).","Publisher","Referneces","http://www.google.com","","theme1","Nation","dataset license1"
        And I attach the file "testImages/logo.png" to "open_dataset_resources" with relative path
        And I press "submit"
        And I wait to be redirected
        Then I should see "Monetizing Mobile Gaming_1"

    @javascript @Done
    Scenario: A logged-in user Adding new open dataset with null inputs
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/open-datasets/add"
        When I Add new frontend open dataset with "", "", "", ""
        And I press "submit"
        Then I should see "Title required"
        And I should see "Description required"
        And I should see "Publisher required"
        And I should see "References required"

    @javascript @Done
    Scenario: A logged-in user Adding new open dataset with numbers only in the textfields
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/open-datasets/add"
        When I Add new frontend open dataset with "121323415", "1253253", "1253253", "1253253"
        And I press "submit"
        Then I should see "Title must include at least one letter"
        And I should see "Description must include at least one letter"
        And I should see "Publisher must include at least one letter"
        And I should see "References must include at least one letter"

    @javascript @Done
    Scenario: A logged-in user Adding new open dataset with special characters only in the textfields
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/open-datasets/add"
        When I Add new frontend open dataset with ";;;;;", ";;;;;", ";;;;;", ";;;;;"
        And I press "submit"
        Then I should see "Title must include at least one letter"
        And I should see "Description must include at least one letter"
        And I should see "Publisher must include at least one letter"
        And I should see "References must include at least one letter"

    @javascript @Done
    Scenario: A logged-in user Adding new open dataset with title exceeds the max length
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/open-datasets/add"
        When I Add new frontend open dataset with "More than 100 characters title More than 100 characters title More than 100 characters title More than 100 characters title More than 100 characters title More than 100 characters title More than 100 characters title", "Description", "Publisher", "Refe"
        And I press "submit"
        Then I should see "Title should not be more than 100 characters"

    @javascript @Done
    Scenario: Adding new success story from the list page with Title less than the min length
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/open-datasets/add"
        When I Add new frontend open dataset with "title", "Description", "Publisher", "References"
        And I press "submit"
        Then I should see "Title should be at least 10 characters"
