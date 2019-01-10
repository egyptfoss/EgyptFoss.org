Feature: Admin list/search badges in the system
  In order to list/search badges in the system
  As an admin
  I need to be able to navigate/list/search/sort badges in the system by Title, Ar title, threshold and badge's image

    @Done
    Scenario: Admin lists all badges in the system
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "wp-admin/admin.php?page=efb_badges"
        Then I should see "Events Specialist"
        And I should see "متخصص في الفعاليات"
        And I should see "10"
        And I should see "Image"

    @Done @javascript
    Scenario: Admin searches for specific badge with title
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "wp-admin/admin.php?page=efb_badges"
        When I resize window with height 800 and width 2048 in px
        When I fill in "s" with "Events Specialist"
        And I click on the element with css selector "#search-submit"
        Then I should see "Events Specialist"

    @Done @javascript
    Scenario: Admin searches for specific badge with Ar title
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "wp-admin/admin.php?page=efb_badges"
        When I resize window with height 800 and width 2048 in px
        When I fill in "s" with "متخصص في الفعاليات"
        And I click on the element with css selector "#search-submit"
        And I wait for 8 seconds
        And I should see "متخصص في الفعاليات"
