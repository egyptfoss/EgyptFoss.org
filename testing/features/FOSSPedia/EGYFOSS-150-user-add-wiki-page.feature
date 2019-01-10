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
Scenario: A logged-in user creating a topic in wiki from URL
    Given I am on "/wiki/"
    When I am on "/wiki/index.php?title=Maii"
    And I wait to be redirected
    Then I should be on "/wiki/index.php?title=Maii"
    And I should see "you do not have permission to create this page"

@not_implemented
Scenario: A logged-in user creating a topic in wiki from URL
    Given I am a logged in user with "foss" and "F0$$"
    And I am on "/wiki/"
    When I go to "/wiki/index.php?title=Maii2"
    And I wait to be redirected
    And I follow "edit this page"
    Then I should be on "/wiki/index.php?title=Maii2&action=edit"
    And I should see "Creating Maii2"

@not_implemented
Scenario: A logged-in user submiting a topic in wiki from URL
    Given I am a logged in user with "foss" and "F0$$"
    And I am on "/wiki/index.php?title=Maii3&action=edit"
    When I fill in "wpTextbox1" with "Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text"
    And I press "wpSave"
    Then I should be on "/wiki/index.php?title=Maii3"
    And I should see "Maii"
    And I should see "Dummy text Dummy text"

@not_implemented
Scenario: A logged-in user creating a topic in wiki from inside another wiki page
    Given I am a logged in user with "foss" and "F0$$"
    And I am on "/wiki/index.php?title=EGYFOSS&action=edit"
    When I fill in "wpTextbox1" with "[[eSpace]] Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text Dummy text"
#        And I follow "Link"
#        And I fill in "wikieditor-toolbar-link-int-target" with "eSpace"
#        And I select "To a wiki page"
#        And I press "Insert link"
    And I press "wpSave"
    Then I should be on "/wiki/index.php?title=EGYFOSS"
    And I should see "eSpace"
