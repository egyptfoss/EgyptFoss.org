Feature: User view list of all news in the system
  In order to list all news in the system
  As an User
  I need to be able to navigate to news list page and load more news

    @Done
    Scenario: A not logged-in user navigating to News list
        Given I am on "/en/"
        When I follow "News"
        Then I should be on "/en/news/"
        And I should see "News"

    @javascript @Done  @set-empty-news-list  @return-list-news
    Scenario: A not logged-in user seeing an empty news list
        Given I am on "/en/news"
        Then I should see "There are no News yet, Suggest News"
        When I resize window with height 800 and width 1024 in px
        And I follow "Suggest News"
        And I wait to be redirected
        Then I should be on "/en/login/"
        And I should see "Please log in to suggest a new news"

    @javascript @Done @set-empty-news-list  @return-list-news
    Scenario: A logged-in user seeing an empty news list
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/news/"
        Then I should see "There are no News yet, Suggest News"
        When I resize window with height 800 and width 1024 in px
        And I follow "Suggest News"
        And I wait to be redirected
        Then I should be on "/en/news/add/"
        And I should see "Suggest News"
        And I close the browser

    @Done @add-news
    Scenario: A not logged-in user viewing 9 news per page
        Given I am on "/en/news"
        Then I should see 9 ".card-inner" elements
        And I should see "Show more"

    @javascript @Done
    Scenario: A not logged-in user loading more news
        Given I am on "/en/news/"
        When I follow "Show more"
        And I wait for 5 seconds
        Then I should see more "9" or more ".card-inner" elements

    @Done 
    Scenario: A not logged-in user viewing News title, image and date in the News card
        Given I am on "/en/news/"
        And I should see an ".date" element
        And I should see an ".news-thumbnail" element
        And I should see an ".egy-news-title" element
        And I should see an ".egy-news-content" element

    @Done 
    Scenario: A logged-in user navigating to News list
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/"
        When I follow "News"
        Then I should be on "/en/news/"
        And I should see "News"

    @Done
    Scenario: A logg1ed-in user viewing 9 news per page
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/news"
        Then I should see 9 ".card-inner" elements
        And I should see "Show more"

    @javascript @Done
    Scenario: A logged-in user loading more news
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/news/"
        When I follow "Show more"
        And I wait for 5 seconds
        Then I should see more "9" or more ".card-inner" elements

    @Done 
    Scenario: A logged-in user viewing News title, image and date in the News card
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/news"
        And I should see an ".date" element
        And I should see an ".news-thumbnail" element
        And I should see an ".egy-news-title" element
        And I should see an ".egy-news-content" element

    @Done 
    Scenario: A logged-in user viewing News according to the language of the interface
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/news/"
        Then I should not see "new-test-news-title-EGYPT-FOSS-Ar"