Feature: User view marketplace home so that he can find best ones
  In order to view marketplace home
  As a User
  I need to be able to navigate to marketplace homepage and list top services and top providers and check all categores in the system

    @add-top-services @wip
    Scenario: A not logged-in user navigating to marketplace homepage
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/marketplace/services/service-4"
        And I should not see an "a.archive-request-button" element
        And I should see an ".btn-light" element

    Scenario: A not logged-in user viewing top services slider

    Scenario: A not logged-in user can not see top services slider if they are less than 5 services

    Scenario: A not logged-in user sees latest services if there are no top services

