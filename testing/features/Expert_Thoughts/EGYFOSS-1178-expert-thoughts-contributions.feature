Feature: User view list of all contributors on a thought in the system
  In order to list all contributors on a thought in the system
  As an User
  I need to be able to view list of my contributors

    @Done @add-expert-thoughts
    Scenario: A logged-in user navigating to Expert thought Contributions list
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/contributions/"
        When I follow "expert-thoughts"
        And I wait to be redirected
        Then I should be on "/en/members/foss/contributions/expert-thoughts/"

    @Done
    Scenario: A logged-in user navigating to empty Expert thoughts Contributions list
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/contributions/expert-thoughts/"
        Then I should see "There are no expert thoughts added by espace"

    @Done
    Scenario: A logged-in user viewing Expert thought title and date in the Expert thought card
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/contributions/expert-thoughts/"
        Then "card-thumb" should be visible
        And "product-info" should be visible
        And "fa-clock-o" should be visible

    @Done
    Scenario: A logged-in user viewing Pending Expert thoughts
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/contributions/expert-thoughts/"
        Then "card-thumb" should be visible
        And "product-info" should be visible
        And "fa-clock-o" should be visible
        And "pending-approval" should be visible

    @Done
    Scenario: A logged-in user viewing 20 Expert thoughts contributions per page
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/contributions/expert-thoughts/"
        Then I should see 20 ".profile-card" elements
        And I should see "Show more"

    @javascript
    Scenario: A logged-in user loading more Expert thoughts contribution in my profile
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/members/foss/contributions/expert-thoughts/"
        When I follow "Show more"
        And I wait to be redirected
        Then I should see more "20" or more ".profile-card" elements
        And I should not see "Show more"

    @Done
    Scenario: A Not logged-in user should not view Pending Expert thoughts
        Given I am on "/en/members/foss/contributions/expert-thoughts/"
        Then "card-thumb" should be visible
        And "product-info" should be visible
        And "fa-clock-o" should be visible
        And I should not see "pending-approval"

    @Done
    Scenario: A logged-in user viewing other profile contributions
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/foss/contributions/expert-thoughts/"
        Then "card-thumb" should be visible
        And "product-info" should be visible
        And "fa-clock-o" should be visible
        And I should not see "pending-approval"