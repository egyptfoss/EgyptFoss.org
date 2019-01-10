Feature: User view list of all success stories contributions in the system
  In order to list all success stories contributions in the system
  As an User
  I need to be able to navigate to success stories contributions list page and load more contributions

    @Done @add-success-stories
    Scenario: A logged-in user navigating to Success Stories Contributions list
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/contributions/"
        When I follow "success-stories"
        And I wait to be redirected
        Then I should be on "/en/members/foss/contributions/success-stories/"

    @javascript @not_implemented @need_redo_after_new_design
    Scenario: A logged-in user navigating to empty Success Stories Contributions list
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/contributions/success-stories/"
        Then I should see "There are no success stories added by foss"

    @Done
    Scenario: A logged-in user viewing Success Story title, image and date in the Success Story card
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/contributions/success-stories/"
        Then "card-thumb" should be visible
        And "product-info" should be visible
        And "fa-clock-o" should be visible

    @Done
    Scenario: A logged-in user viewing Pending Success Stories
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/contributions/success-stories/"
        Then "card-thumb" should be visible
        And "product-info" should be visible
        And "fa-clock-o" should be visible
        And "pending-approval" should be visible

    @Done
    Scenario: A logged-in user viewing 20 Success Stories contributions per page
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/contributions/success-stories/"
        Then I should see 20 ".profile-card" elements
        And I should see "Show more"

    @javascript @Done
    Scenario: A logged-in user loading more Success Stories contribution in my profile
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/contributions/success-stories/"
        When I follow "Show more"
        And I wait to be redirected
        Then I should see more "20" or more ".profile-card" elements
        And I should not see "Show more"

    @Done
    Scenario: A Not logged-in user should not view Pending Success Stories
        Given I am on "/en/members/foss/contributions/success-stories/"
        Then "card-thumb" should be visible
        And "product-info" should be visible
        And "fa-clock-o" should be visible
        And I should not see "pending-approval"

    @Done
    Scenario: A logged-in user viewing other profile contributions
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/foss/contributions/success-stories/"
        Then "card-thumb" should be visible
        And "product-info" should be visible
        And "fa-clock-o" should be visible
        And I should not see "pending-approval"