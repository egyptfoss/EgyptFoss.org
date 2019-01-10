Feature: User view list of Expert Thoughts in the system
  In order to list all expert thoughts in the system
  As a User
  I need to be able to navigate to expert thoughts list page and load more expert thoughts

Scenario: A not logged-in user navigating to Expert Thoughts list
        Given  I am on "/en/expert-thoughts/"
        Then I should see "Expert Thought"

Scenario: A not logged-in user seeing an empty expert thoughts list
        Given I am on "/en/expert-thoughts/"
        Then I should see "There are no Expert Thoughts yet"

@add-expert-thoughts
Scenario: A logged-in user seeing expert thoughts list
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/expert-thoughts/"
        Then I should see "Expert Thoughts"
        And I should see ".expert-identity"
        And I should see ".expert-avatar"
        And I should see ".user-type"
        And I should see ".post-date"
        And I should see ".xprofile bp-user my-profile about public buddypress page page-id-0 page-parent page-template-default logged-in js"
        #And I should see ".share-profile rfloat"

@javascript
Scenario: A not logged-in user viewing 10 expert thoughts per page
        Given I am on "/en/success-stories/"
        Then I should see 10 ".thought-card" elements
        And I should see "Show more"

@javascript
Scenario: A not logged-in user loading more expert thoughts
        Given I am on "/en/expert-thoughts/"
        When I follow "Show more"
        And I wait for 7 seconds
        Then I should see more "10" or more ".thought-card" elements
        And I should not see "Show more"