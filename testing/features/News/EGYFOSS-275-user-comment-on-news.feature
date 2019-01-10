Feature: add comment on a news
  In order to add comment on a news
  As a logged-in user
  I need to be able to navigate to news page and add a comment to this news

    @Done @add-news
    Scenario: A not logged-in user view comments on news but can't add a comment
        Given I am on "/en/"
        When I go to "/en/news/new-test-news-title-egypt-foss/"
        And I wait to be redirected
        # Then I should see an ".form-control" element
        # shouldn't see comment textbox nor submit button
        And I should not see an ".form-control" element
        And I should not see an ".submit" element

    @Done  
    Scenario: A logged-in user adding a comment to a news
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/news/new-test-news-title-egypt-foss/"
        When I comment on the news with "A very useful article"
        Then I should see "espace"
        And I should see "A very useful article"
        And news comments counter should add more one

    @javascript @Done
    Scenario: A logged-in user adding an empty comment to a news
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/news/new-test-news-title-egypt-foss/"
        When I comment on the news with ""
        And I wait for 2 seconds
        Then I should see "Comment can not be empty"

    @javascript @Done 
    Scenario: A logged-in user adding a reply to a comment on a news
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/news/new-test-news-title-egypt-foss/"
        When I reply on "A very useful article" with "More than perfect"
        And I go to "/en/news/new-test-news-title-egypt-foss/"
        Then I should see "espace"
        And the response should contain "More than perfect"
        And news comments counter should add more one
