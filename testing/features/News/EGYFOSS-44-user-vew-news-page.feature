Feature: User view a news page in the system
  In order to view a news page in the system
  As a user
  I need to navigate to news page and view all news's details

    @Done @add-news @javascript
    Scenario: A user navigating to view news page from news list page
        Given I am on "/en/"
        When I resize window with height 800 and width 2048 in px
        And I follow "News"
        And I wait for 5 seconds
        And I follow "New News 10"
        Then I should be on "/en/news/new-news-10/"
    @Done 
    Scenario: A user viewing a news with all its details inserted
        Given I am on "/en/news/new-test-news-title-egypt-foss/"
        Then I should see "new-test-news-title-EGYPT-FOSS"
        And I should see "Subtitle new-test-news-title-EGYPT-FOSS"
        And I should see "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book."
        And I should see an ".post-date" element
        And I should see an ".news-author" element
        #And I should see an ".news-img" element

    @Done  
    Scenario: A user viewing a news with only required data
        Given I am on "/en/news/new-test-news-title-egypt-foss-55/"
        Then I should see "new-test-news-title-EGYPT-FOSS-55"
        And I should see "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book."
        And I should see an ".post-date" element
        And I should see an ".news-author" element

    @Done
    Scenario: A user viewing other news in the news page sidebar
        Given I am on "/en/news/new-test-news-title-egypt-foss-55/"
        Then I should see 5 ".news-list-item" elements