Feature: user can view wiki topic
  In order to view a wiki topic
  As a user
  I need to be able to navigate to wiki and view different topics

  Background:
    Given I am on "/"
    And there are following users:
            | username | email | plain_password | enabled |
            | bougy.tamtam | bougy.tamtam10@gmail.com | 123456789 | yes |

    @not_implemented
    Scenario: A logged-in user viewing the history of wiki page
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/wiki/index.php?title=Maii"
        When I follow "Edit"
        And I fill in "wpTextbox1" with "Edited Dummy text"
        And I press "wpSave"
        And I follow "History"
        Then I should be on "/wiki/index.php?title=Maii&action=history"
        And I should see "Revision history of"
        And I should see "(cur | prev)"
        And I should see "undo"


    @not_implemented
    Scenario: A logged-in user comparing two versions in history of wiki page
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/wiki/index.php?title=Maii&action=history"
        #When I press button with css selector "#mw-content-text input.historysubmit"
        When I follow "prev"
        And I wait to be redirected
        And I wait to be redirected
        Then I should see "Revision As Of"
        And I should see "Difference between revisions of"