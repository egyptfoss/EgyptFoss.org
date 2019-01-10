Feature: User adds event to the system
  In order to add an event in the system
  As an User
  I need to provide all attributes needed to add an event

    @done
    Scenario: A not logged-in user Adding new event to the system
        Given I am on "/add-event"
        Then I should be on "/en/login/?redirected=addevent"
        And I should see "Please log in to suggest a new event"

    @not_implemented
    Scenario: A logged-in user Adding new event to the system with new venue and new organizer
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/add-event"
        When I Add new event with "Event-test" and "this is the event-test description" and "2016-05-20 10:00:00" and "2016-05-20 18:00:00" and "venue1" and "15 gamal abdelnasser street and louran" and "Alexandria" and "Egypt" and "03-322677282" and "www.test.com" and "Organizer1" and "03-43626375447" and "www.test2.com" and "www.test3.com" and "USD" and "2000" and "Audience text" and "Objectives text" and "Prerequisites text" and "Functionality text" and "Software Engineering" and "Competitions" and "here" and "plat2"
        And I press "submit"
        Then I should see "Event Event-test Added successfully"

    @not_implemented
    Scenario: A logged-in user Adding new event to the system with already exist venue and organizer
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/add-event"
        When I Add new event with "Event-test" and "this is the event-test description" and "2016-05-20 10:00:00" and "2016-05-20 18:00:00" and "espace" and "maii elnagar" and "www.test3.com" and "USD" and "2000" and "Audience text" and "Objectives text" and "Prerequisites text" and "Functionality text" and "Software Engineering" and "Competitions" and "Assembly Language" and "BlackBerry OS"
        And I press "submit"
        Then I should see "Event Event-test Added successfully"
       
    @not_implemented
    Scenario: A logged-in user Adding new event with only required inputs [Name, Desc, organizer, Start/End date, Cost, venue(name, address, city, country)]
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/add-event"
        When I Add new event with "Event-test2" and "this is the event-test2 description" and "2016-05-20 10:00:00" and "2016-05-20 18:00:00" and "venue1" and "15 gamal abdelnasser street and louran" and "Alexandria" and "Egypt" and "" and "" and "Organizer-test12" and "" and "" and "" and "USD" and "2000" and "" and "" and "" and "" and "" and "" and "" and ""
        And I press "submit"
        Then I should see "Event Event-test2 Added successfully"

    @not_implemented
    Scenario: A logged-in user Adding new event with null inputs
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/add-event"
        When I Add new event with "" and "" and "" and "" and "" and "" and "" and "" and "" and "" and "" and "" and "" and "" and "" and "" and "" and "" and "" and "" and "" and "" and "" and ""
        And I press "submit"
        Then I should see "Event title required"

    @not_implemented
    Scenario: A logged-in user Adding new event with numbers only in the textfields
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/add-event"
        When I Add new event with "13243" and "13243" and "2016-05-20 10:00:00" and "2016-05-20 18:00:00" and "13243" and "13243" and "13243" and "Egypt" and "03-322677282" and "13243" and "13243" and "03-43626375447" and "www.test2.com" and "www.test3.com" and "USD" and "2000" and "13243" and "13243" and "13243" and "13243" and "Software Engineering" and "" and "13243" and "13243"
        And I press "submit"
        Then I should see "Event title must at least contain one letter"

    @not_implemented
    Scenario: A logged-in user Adding new event with special characters only in the textfields
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/add-event"
        When I Add new event with "^^^^" and "^^^^" and "2016-05-20 10:00:00" and "2016-05-20 18:00:00" and "^^^^" and "^^^^" and "^^^^" and "Egypt" and "03-322677282" and "^^^^" and "^^^^" and "03-43626375447" and "www.test2.com" and "www.test3.com" and "USD" and "2000" and "^^^^" and "^^^^" and "^^^^" and "^^^^" and "Software Engineering" and "" and "here" and "plat1"
        And I press "submit"
        Then I should see "Event title must at least contain one letter"
