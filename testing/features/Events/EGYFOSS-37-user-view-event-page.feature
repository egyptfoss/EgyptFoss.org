Feature: User view an event page in the system
  In order to view an event page in the system
  As a user
  I need to navigate to event page and view all event's details

    @javascript @Done
    Scenario: A user navigating to view event page from events list page
        Given I am on "/events/"
        And I resize window with height 800 and width 1024 in px
        And I follow "new-test-event-title-egypt-foss" in certain place ".tribe-events-month-event-title"
        Then I should be on "/event/new-test-event-title-egypt-foss/"

    @Done
    Scenario: A user viewing an event with all its details inserted
        Given I am on "/event/new-test-event-title-egypt-foss/"
        Then I should see "new-test-event-title-egypt-foss"
        And I should see "description 1"
        And I should see "Competitions"
        And I should see an ".tribe-event-date-start" element
        And I should see an ".tribe-events-single-section-title" element
        And I should see an ".tribe-organizer-tel" element
        And I should see an ".tribe-locality" element

    @Done
    Scenario: A user viewing an event with only required data
        Given I am on "/event/new-test-event-title-egypt-foss/"
        Then I should see "new-test-event-title-egypt-foss"
        And I should see "description 1"
        And I should see "Competitions"
        And I should see an ".tribe-event-date-start" element
        And I should see an ".tribe-events-single-section-title" element