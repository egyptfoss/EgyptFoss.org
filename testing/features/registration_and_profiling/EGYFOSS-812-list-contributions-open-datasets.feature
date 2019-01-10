Feature: User view list of all open datasets contributions in the system
  In order to list all open datasets contributions in the system
  As an User
  I need to be able to navigate to open datasets contributions list page and load more contributions

    @Done @add-open-datasets
    Scenario: A logged-in user navigating to open datasets Contributions list
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/contributions/"
        When I follow "open-datasets"
        And I wait to be redirected
        Then I should be on "/en/members/foss/contributions/open-datasets/"

    @javascript @not_implemented @need_redo_after_new_design
    Scenario: A logged-in user navigating to empty open datasets Contributions list
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/contributions/open-datasets/"
        Then I should see "There are no success stories added by foss"

    @Done
    Scenario: A logged-in user viewing open dataset title, image and date in the open datasets card
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/contributions/open-datasets/"
        And "product-info" should be visible
        And "fa-clock-o" should be visible

    @Done
    Scenario: A logged-in user viewing Pending open datasets
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/contributions/open-datasets/"
        And "product-info" should be visible
        And "fa-clock-o" should be visible
        And "pending-approval" should be visible

    @Done
    Scenario: A logged-in user viewing 20 open datasets contributions per page
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/contributions/open-datasets/"
        Then I should see 20 ".profile-card" elements
        And I should see "Show more"

    @javascript @Done
    Scenario: A logged-in user loading more open datasets contribution in my profile
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/contributions/open-datasets/"
        When I follow "Show more"
        And I wait to be redirected
        Then I should see more "20" or more ".profile-card" elements
        And I should not see "Show more"

    @Done
    Scenario: A Not logged-in user should not view Pending open datasets
        Given I am on "/en/members/foss/contributions/open-datasets/"
        Then "product-info" should be visible
        And "fa-clock-o" should be visible
        And I should not see "pending-approval"

    @Done
    Scenario: A logged-in user viewing other profile contributions
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/foss/contributions/open-datasets/"
        Then "product-info" should be visible
        And "fa-clock-o" should be visible
        And I should not see "pending-approval"

    @Done
    Scenario: A logged-in user navigating to open datasets edits Contributions list
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/contributions/"
        When I follow "open-datasets"
        And I wait to be redirected
        Then I should be on "/en/members/espace/contributions/open-datasets/"
        And I should see "Edits"

    @javascript @Done
    Scenario: A logged-in user navigating to open datasets edits Contributions list and should see list of his contributions
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/contributions/"
        When I follow "open-datasets"
        And I wait to be redirected
        Then I should be on "/en/members/espace/contributions/open-datasets/"
        And I should see "Edits"
        And I click on "Edits"
        Then I should see "new-test-dataset-title-ninteen"

    @javascript @Done
    Scenario: A logged-in user navigating to open datasets edits Contributions list and shouln't see show more
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/contributions/"
        When I follow "open-datasets"
        And I wait to be redirected
        Then I should be on "/en/members/espace/contributions/open-datasets/"
        And I should see "Edits"
        And I click on "Edits"
        Then I should see "new-test-dataset-title-ninteen"
        And I should not see "Show more"

    @javascript @Done
    Scenario: A logged-in user navigating to open datasets edits Contributions of another user and should see published resources only
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/espace/contributions/"
        When I follow "open-datasets"
        And I wait to be redirected
        Then I should be on "/en/members/espace/contributions/open-datasets/"
        And I should see "Edits"
        And I click on "Edits"
        Then I should not see "new-test-dataset-title-ninteen"
        And I should not see "Show more"
        And I should see "new-test-dataset-title-six"