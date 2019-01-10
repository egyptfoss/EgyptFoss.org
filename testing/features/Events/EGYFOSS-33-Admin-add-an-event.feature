Feature: Manage events by adding, editing and deleting them
  In order to manage the events in the system
  As an Admin
  I need to be able to list, add, edit and delete them

  Background:
        Given I am on "/login"
        And there are following users:
            | username | email                      | password | enabled |
            | foss     | admin@example.com | F0$$   | yes     |
        When I fill in the following:
            | user_login | foss |
            | user_pass | F0$$ |
        And I press "wp-submit"

    Scenario: Adding new event from the list page with valid inputs
        Given I am on "/wp-admin/edit.php?post_type=tribe_events"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Admin Add new event with "Event1" and "this is the event1 description" and "2016-02-16" and "2016-02-17" and "venue1" and "15 gamal abdelnasser street and louran" and "Alexandria" and "Egypt" and "Alexandria" and "03-322677282" and "www.test.com" and "Organizer1" and "03-43626375447" and "www.test2.com" and "www.test3.com" and "$" and "2000" and "Audience text" and "Objectives text" and "Prerequisites text" and "Functionality text" and "16" and "Competitions" and "Assembly Language" and "BlackBerry OS"
        And I press "publish"
        Then I should see "Event published."
       
    Scenario: Adding new event from the list page with only required inputs
        Given I am on "/wp-admin/edit.php?post_type=tribe_events"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Admin Add new event with "Event2" and "" and "2016-02-16" and "2016-02-17" and "venue2" and "15 gamal abdelnasser street and louran" and "Alexandria" and "Egypt" and "Alexandria" and "" and "" and "Organizer2" and "03-43626375447" and "" and "" and "$" and "2000" and "" and "" and "" and "" and "" and "" and "" and ""
        And I press "publish"
        Then I should see "Event published."
       

    @javascript
    Scenario: Adding new event from the list page with already exist title
        Given I am on "/wp-admin/edit.php?post_type=tribe_events"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Admin Add new event with "Event2" and "" and "2016-02-16" and "2016-02-17" and "venue2" and "15 gamal abdelnasser street and louran" and "Alexandria" and "Egypt" and "Alexandria" and "" and "" and "Organizer2" and "03-43626375447" and "" and "" and "$" and "2000" and "" and "" and "" and "" and "" and "" and "" and ""
        And I press "publish"
        Then I should see "already exist"

    Scenario: Adding new event from the sidebar with valid inputs
        Given I am on "/wp-admin/"
        When I follow "Events"
        And I follow "Add New" in certain place "li#menu-posts-tribe_events"
        And I wait to be redirected
        Then I should be on "/wp-admin/post-new.php?post_type=tribe_events"
        And I should see "Add New Event"

    @javascript
    Scenario: Adding new event with null inputs
        Given I am on "/wp-admin/edit.php?post_type=tribe_events"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Admin Add new event with "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", ""
        And I press "publish"
        Then I should see "Required"

    @javascript 
    Scenario: Adding new event with numbers only in the textfields
        Given I am on "/wp-admin/edit.php?post_type=tribe_events"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Admin Add new event with "1234", "555", "555", "454", "45", "335", "345", "546", "546", "546", "456", "546", "546", "4325", "4352", "43523", "43524", "3454", "34444", "4325", "3425", "435", "454", "345", "35"
        And I press "publish"
        Then I should see "must contain at least one letter"

    @javascript 
    Scenario: Adding new event with special characters only in the textfields
        Given I am on "/wp-admin/edit.php?post_type=tribe_events"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Admin Add new event with ",,,", ",,,", ",,,", "45,,4", "4,,5", ",,,,", ";;;", ";;;", ";;;", ",,,", ",,,", ",,,", "**", ";;;5", ";;", ";;;", ";;;", "3454", ";;", ";;;", ",,,", ",,,", "///", "///", "///"
        And I press "publish"
        Then I should see "must contain at least one letter"