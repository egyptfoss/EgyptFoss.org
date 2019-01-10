Feature: user can view wiki topic
  In order to view a wiki topic
  As a user
  I need to be able to navigate to wiki and view different topics

  Background:
    Given I am on "/"
    And there are following users:
            | username | email | plain_password | enabled |
            | bougy.tamtam | bougy.tamtam10@gmail.com | 123456789 | yes |

    @javascript @not_implemented
    Scenario: A not logged-in user navigating to wiki home page
        When I go to "/wiki"
        And I wait to be redirected
        Then I should see "Welcome to FOSSPedia"
        And I should see "Featured Topics"
        And I should see "Latest Topics"

    @javascript @not_implemented
    Scenario: A not logged-in user viewing a topic in the wiki
        Given I am on "/wiki"
        When I follow "EgyptFOSS"
        And I wait to be redirected
        Then I should be on "/wiki/index.php?title=Egypt_FOSS"
        And I should see "EgyptFOSS"
        And I should see "Free and Open Source Software"

    @javascript @not_implemented
    Scenario: A not logged-in user viewing a topic from inside another topic page
        Given I am on "/wiki/index.php?title=Main_Page"
        When I follow "EgyptFOSS"
        And I wait to be redirected
        Then I should be on "/wiki/index.php?title=Egypt_FOSS"
        And I should see "EgyptFOSS"

    @javascript @not_implemented
    Scenario: A logged-in user navigating to wiki home page
        Given I am a logged in user with "foss" and "F0$$"
        When I go to "/wiki"
        And I wait to be redirected
        Then I should see "Welcome to FOSSPedia"
        And I should see "Featured Topics"
        And I should see "Latest Topics"

    @javascript  @not_implemented
    Scenario: A logged-in user viewing a topic in the wiki
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/wiki"
        When I follow "EgyptFOSS"
        And I wait to be redirected
        Then I should be on "/wiki/index.php?title=Egypt_FOSS"
        And I should see "EgyptFOSS"
        And I should see "Free and Open Source Software"

    @javascript  @not_implemented
    Scenario: A logged-in user viewing a topic from inside another topic page
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/wiki/index.php?title=Main_Page"
        When I follow "EgyptFOSS"
        And I wait to be redirected
        Then I should be on "/wiki/index.php?title=Egypt_FOSS"
        And I should see "EgyptFOSS"
        And I should see "Free and Open Source Software"
