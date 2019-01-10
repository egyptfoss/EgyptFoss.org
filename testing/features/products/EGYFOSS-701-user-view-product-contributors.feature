Feature: User view list of all contributors on a product in the system
  In order to list all contributors on a product in the system
  As an User
  I need to be able to navigate to product view page

    @Done
    Scenario: A not logged-in user seeing a product contributors list on no update yet
        Given I am on "/en/products/40no_taxs_en/"
        Then I should see "1 Contributor"
        And I should see "foss"
        And I should see "1 update"

    @javascript @Done
    Scenario: A not logged-in user seeing a product contributors list with updates
        Given I am on "/en/products/producttest_10_en/"
        Then I should see "2 Contributors"
        And I should see "foss"
        And I should see "2 updates"
        And I should see "espace"
        And "foss" should precede "espace" in jquery

    @Done
    Scenario: A not logged-in user seeing a product contributors list with updates and able to redirect to contributor profile
        Given I am on "/en/products/producttest_10_en/"
        Then I should see "2 Contributors"
        And I should see "foss"
        And I should see "2 updates"
        And I should see "espace"
        When I follow "foss"
        And I wait to be redirected
        Then I should be on "/en/members/foss/"
        And I should see "foss"

    @Done
    Scenario: A logged-in user seeing a product contributors list on no update yet
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/products/40no_taxs_en/"
        Then I should see "1 Contributor"
        And I should see "foss"
        And I should see "1 update"

    @javascript @Done
    Scenario: A logged-in user seeing a product contributors list with updates
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/products/producttest_10_en/"
        Then I should see "2 Contributors"
        And I should see "foss"
        And I should see "2 updates"
        And I should see "espace"
        And "foss" should precede "espace" in jquery

    @Done
    Scenario: A logged-in user seeing a product contributors list with updates and able to redirect to contributor profile
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/products/producttest_10_en/"
        Then I should see "2 Contributors"
        And I should see "foss"
        And I should see "2 updates"
        And I should see "espace"
        When I follow "foss"
        And I wait to be redirected
        Then I should be on "/en/members/foss/"
        And I should see "foss"

    @javascript @Done
    Scenario: A logged-in user seeing a product contributors list with updates with last updated displayed first
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/products/producttest_10with_taxs_en/"
        Then I should see "2 Contributors"
        And I should see "foss"
        And I should see "espace"
        And I should see "1 update"
        And "espace" should precede "foss" in jquery