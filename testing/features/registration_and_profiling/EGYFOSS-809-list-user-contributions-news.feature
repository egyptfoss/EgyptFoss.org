Feature: User view list of all news contributions in the system
  In order to list all news contributions in the system
  As an User
  I need to be able to navigate to news contributions list page and load more contributions

    @Done @add-news
    Scenario: A logged-in user navigating to News Contributions list
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/contributions/"
        When I follow "news"
        And I wait to be redirected
        Then I should be on "/en/members/foss/contributions/news/"

    @javascript @not_implemented @need_redo_after_new_design
    Scenario: A logged-in user navigating to Empty News Contributions list
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/contributions/news/"
        Then I should see "There are no news added by foss"

    @Done
    Scenario: A logged-in user viewing News title, image and date in the News card
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/contributions/news/"
        Then "card-thumb" should be visible
        And "product-info" should be visible
        And "fa-clock-o" should be visible

    @Done
    Scenario: A logged-in user viewing Pending News
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/contributions/news/"
        Then "card-thumb" should be visible
        And "product-info" should be visible
        And "fa-clock-o" should be visible
        And "pending-approval" should be visible

    @Done
    Scenario: A logged-in user viewing 20 news contributions per page
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/contributions/news/"
        Then I should see 20 ".profile-card" elements
        And I should see "Show more"

    @javascript @Done
    Scenario: A logged-in user loading more news contribution in my profile
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/contributions/news/"
        When I follow "Show more"
        And I wait to be redirected
        Then I should see more "20" or more ".profile-card" elements
        And I should not see "Show more"

    @Done
    Scenario: A Not logged-in user should not view Pending News
        Given I am on "/en/members/foss/contributions/news/"
        Then "card-thumb" should be visible
        And "product-info" should be visible
        And "fa-clock-o" should be visible
        And I should not see "pending-approval"

    @Done
    Scenario: A logged-in user viewing other profile contributions
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/foss/contributions/news/"
        Then "card-thumb" should be visible
        And "product-info" should be visible
        And "fa-clock-o" should be visible
        And I should not see "pending-approval"