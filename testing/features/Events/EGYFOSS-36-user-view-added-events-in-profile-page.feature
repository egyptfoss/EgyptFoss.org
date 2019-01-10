Feature: User view list of all events added by him/other user in his/other profile page
  In order to list all events added by him/other user in his/other profile page
  As an User
  I need to be able to navigate to added events page in his/other profile


    Scenario: A not logged-in user Navigating to added events list in profile page
        Given I am on "/members/foss/"
        When I follow "Events" in certain place ".no-ajax"
        Then I should be on "/members/foss/events/"

    
    Scenario: A not logged-in user seeing an empty added events list in profile page
        Given I am on "/members/buggy/events/"
        Then I should see "There are no events added by buggy"

    
    Scenario: A not logged-in user viewing 20 added events per page
        Given I am on "/members/foss/events/"
        Then I should see 20 ".user-event-card" elements
        And I should see "Show more"

    @javascript
    Scenario: A not logged-in user loading more added events
        Given I am on "/members/foss/events/"
        When I follow "Show more"
        And I wait to be redirected
        Then I should see more "20" or more ".user-event-card" elements
        And I should not see "Show more"

    
    Scenario: A logged-in user viewing event-title, venue-name, start-date and end-date for each event
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/members/foss/events/"
        Then I should see "Event-test2"
        And I should see "venue title" in the ".user-event-card" element