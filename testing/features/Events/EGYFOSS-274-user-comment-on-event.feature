Feature: add comment on an event
  In order to add comment on an event
  As a logged-in user
  I need to be able to navigate to event page and add a comment to this event

    @Done @add-events
    Scenario: A not logged-in user view comments on event but can't add a comment
        Given I am on "/en/"
        When I go to "/event/new-test-event-title-egypt-foss/"
        And I wait to be redirected
        #And I should not see an ".form-control" element
        And I should not see an ".submit" element

    @Done
    Scenario: A logged-in user adding a comment to an event
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/event/new-test-event-title-egypt-foss/"
        When I comment on the event with "A very useful article"
        Then I should see "espace"
        And I should see "A very useful article"
        And event comments counter should add more one

    @javascript @Done
    Scenario: A logged-in user adding an empty comment to an event
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/event/new-test-event-title-egypt-foss/"
        When I comment on the event with ""
        And I wait for 2 seconds
        Then I should see "Comment can not be empty"

    @javascript @Done
    Scenario: A logged-in user adding a reply to a comment on an event
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/event/new-test-event-title-egypt-foss/"
        When I reply on "A very useful article" with "More than perfect"
        And I go to "/event/new-test-event-title-egypt-foss/"
        Then I should see "espace"
        And the response should contain "More than perfect"
        And event comments counter should add more one

    @javascript @Done
    Scenario: A logged-in user adding a reply to 1st reply on an event
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/event/new-test-event-title-egypt-foss/"
        When I reply on reply "More than perfect" with "yes, its very nice article and to the point"
        And I go to "/event/new-test-event-title-egypt-foss/"
        Then I should see "espace"
        And I should see "yes, its very nice article and to the point"
        And event comments counter should add more one