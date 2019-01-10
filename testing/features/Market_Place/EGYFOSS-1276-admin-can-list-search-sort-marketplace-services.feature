Feature: As an admin I want to list/search/sort marketplace services
  As an admin
  I need to be able to navigate/list/search/sort marketplace services by provider, status, category, number of responses

    @Done @add-services
    Scenario: Admin lists all services submitted to the system
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "wp-admin/edit.php?post_type=service"
        Then I should see "service-8"
        And I should see "mobile"
        And I should see "Last Modified"
        And I should see "Published"
        And I should see "# of responses"

    @Done @add-services @javascript
    Scenario: Admin searches for specific service with title
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "wp-admin/edit.php?post_type=service"
        When I resize window with height 800 and width 2048 in px
        When I fill in "s" with "service-8"
        And I click on the element with css selector "#search-submit"
        Then I should see "Search results for"
        And I should see "service-8"

    @not_working @add-services @javascript
    Scenario: Admin searches for specific service with description
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "wp-admin/edit.php?post_type=service"
        When I resize window with height 800 and width 2048 in px
        When I fill in "s" with "Lorem Ipsum"
        And I click on the element with css selector "#search-submit"
        And I wait for 8 seconds
        Then I should see "Search results for"
        And I should see "service-8"
        And I should see "service-3"
        And I should see "service-5"
