Feature: add comment on a expert thought
  In order to add comment on a expert thought
  As a logged-in user
  I need to be able to navigate to expert thought page and add a comment to this expert thought

    @Done @javascript @add-expert-thoughts
    Scenario: A not logged-in user view comments on expert thought but can't add a comment
        Given I am on "/en/"
        When I go to "/en/expert-thoughts/new-success-2/"
        And I wait to be redirected
        And I should see an ".must-log-in" element
        And I should not see an ".submit" element

    @Done
    Scenario: A logged-in user adding a comment to a expert thought
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/expert-thoughts/new-success-2/"
        When I comment on the news with "A very useful article"
        And I go to "/en/expert-thoughts/new-success-2/"
        And I wait to be redirected
        Then I should see "espace"
        And I should see "A very useful article"
        And news comments counter should add more one

    @javascript @Done
    Scenario: A logged-in user adding an empty comment to a expert thought
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/expert-thoughts/new-success-2/"
        When I comment on the news with ""
        And I wait for 2 seconds
        Then I should see "Comment can not be empty"

    @javascript @not-Done
    Scenario: A logged-in user adding a reply to a comment on a expert thought
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/expert-thoughts/new-success-2/"
        When I reply on "A very useful article" with "More than perfect"
        And I go to "/en/expert-thoughts/new-success-2/"
        And I wait for 7 seconds
        Then the response should contain "More than perfect"
        And news comments counter should add more one
