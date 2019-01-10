Feature: User view list of all fosspedia contributions in the system
  In order to list all fosspedia contributions in the system
  As an User
  I need to be able to navigate to fosspedia contributions list page and load more contributions

    @Done @add-wiki-pages
    Scenario: A logged-in user navigating to fosspedia Contributions list
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/contributions/"
        When I follow "foss-pedia"
        And I wait to be redirected
        Then I should be on "/en/members/foss/contributions/wiki/"

    @javascript @not_implemented @need_redo_after_new_design
    Scenario: A logged-in user navigating to empty open datasets Contributions list
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/contributions/open-datasets/"
        Then I should see "There are no success stories added by foss"

    @Done
    Scenario: A logged-in user viewing fosspedia title and date in the fosspedia card
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/contributions/wiki/"
        And "product-info" should be visible
        And "fa-clock-o" should be visible

    @Done
    Scenario: A logged-in user viewing 20 fosspedia contributions per page
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/contributions/wiki/"
        Then I should see more "20" or more ".profile-card" elements
        And I should see "Show more"

    @javascript @Done
    Scenario: A logged-in user loading more fosspedia contribution in my profile
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/contributions/wiki/"
        When I follow "Show more"
        And I wait to be redirected
        Then I should see more "20" or more ".profile-card" elements
        And I should not see "Show more"

    @Done
    Scenario: A Not logged-in user should view fosspedia
        Given I am on "/en/members/foss/contributions/wiki/"
        Then "product-info" should be visible
        And "fa-clock-o" should be visible

    @Done
    Scenario: A logged-in user viewing other profile contributions
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/foss/contributions/wiki/"
        Then "product-info" should be visible
        And "fa-clock-o" should be visible

    @javascript @Done
    Scenario: A logged-in user viewing  profile fosspedi edit contributions
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/contributions/wiki/"
        And I click on ".chng-email"
        Then I should see "Foss-22"
        And I should not see "Show more"