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
    Scenario: A logged-in user editing a page in wiki that created from inside another wiki page
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/wiki/index.php?title=EGYFOSS"
        When I follow "ESpace (page does not exist)"
        Then I should be on "/wiki/index.php?title=ESpace&action=edit"
        And I should see "Creating ESpace"
