Feature: User view his profile updates and others' profile updates
  In order to view profile updates
  As an User
  I need to be able to navigate to profile updates page and load more

  Background:
    Given I am on "/"
    And there are following users:
            | username | email | password | enabled |
            | mohamed.said | eslam.diaa+mohamed.said@espace.com.eg | 123456789 | yes |

    @javascript @not_implemented
    Scenario: A user seeing an empty profile updates page with only automatic updates
        # newly registerd user profile
        Given I am on "/members/maiiiii/"
        Then I should see "became a registered member"

    @javascript @not_implemented
    Scenario: A not logged-in user seeing profile updates for another user
        Given I am on "/members/mohamed-said/"
        Then I should see "posted an update"
        And I should not see "What's new, mohamed-said?"

    @javascript @not_implemented
    Scenario: A not logged-in user seeing profile updates for another user
        Given I am a logged in user with "mohamed.said" and "123456789"
        And I am on "/members/mohamed-said/"
        Then I should see "posted an update"
        # response should contains will see all text in page
        And the response should contain "What's new, mohamed-said?"

    @javascript @not_implemented
    Scenario: A not logged-in user viewing 20 updates per page
        Given I am on "/members/foss/"
        Then I should see 20 ".activity-item" elements
        And I should see "Load More"