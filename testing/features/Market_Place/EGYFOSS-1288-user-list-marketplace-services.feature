Feature: User list marketplace services in the system
  In order to list marketplace services in the system
  As a User
  I need to be able to navigate to services list page and load more services

    @not_implemented
    Scenario: A not logged-in user navigating to marketplace services list
        Given I am on "/en/"
        When I follow "Marketplace Services"
        Then I should be on "/en/marketplace/services"
        And I should see "Market Place"

    @Don @set-empty-list-market-place @return-list-market-place
    Scenario: A not logged-in user seeing an empty services list
        Given I am on "/en/marketplace/services/"
        Then the response should contain "There are no services yet,"

    @Done @add-services
    Scenario: A not logged-in user viewing 9 services per page
        Given I am on "/en/marketplace/services"
        Then I should see 9 ".service-card" elements
        And I should see "Show more"

    @javascript @Done
    Scenario: A not logged-in user loading more services
        Given I am on "/en/marketplace/services"
        When I follow "Show more"
        And I wait for 5 seconds
        Then I should see more "9" or more ".service-card" elements
        Then I follow "Show more"
        And I wait for 10 seconds
        And I should not see "Show more"

    @Done
    Scenario: A not logged-in user viewing service title, image and category and rating in the service card
        Given I am on "/en/marketplace/services"
        Then "service-cover" should be visible
        And "card-content" should be visible
        And "card-footer" should be visible
        And "provider-type-label" Should be visible

    @not_implemented
    Scenario: A logged-in user navigating to services list
        Given I am a logged in user with "maii.test" and "123456789"
        And I am on "/en/"
        When I follow "Marketplace Services"
        Then I should be on "/en/marketplace/"
        And I should see "Market Place"

    @Done
    Scenario: A logged-in user viewing 9 services per page
        Given I am a logged in user with "maii.test" and "123456789"
        And I am on "/en/marketplace/services"
        Then I should see 9 ".service-card" elements
        And I should see "Show more"

    @javascript @Done
    Scenario: A logged-in user loading more services
        Given I am a logged in user with "maii.test" and "123456789"
        And I am on "/en/marketplace/services"
        When I follow "Show more"
        And I wait for 5 seconds
        Then I should see more "9" or more ".service-card" elements
        Then I follow "Show more"
        And I wait for 10 seconds
        And I should not see "Show more"

    @Done
    Scenario: A logged-in user viewing service title, image and category and rating in the service card
        Given I am a logged in user with "maii.test" and "123456789"
        And I am on "/en/marketplace/services"
        Then "service-cover" should be visible
        And "card-content" should be visible
        And "card-footer" should be visible
        And "provider-type-label" Should be visible

    @Done
    Scenario: A logged-in user viewing services categories
        Given I am a logged in user with "maii.test" and "123456789"
        And I am on "/en/marketplace/services"
        Then "categories-list" should be visible
        And I should see "mobile"
