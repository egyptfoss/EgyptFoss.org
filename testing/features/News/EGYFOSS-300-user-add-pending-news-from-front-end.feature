Feature: User adds news to the system
  In order to add a news in the system
  As an User
  I need to provide all attributes needed to add a news in the system

    @Done
    Scenario: A not logged-in user Adding new news to the system
        Given I am on "/en/news/add"
        And I wait to be redirected
        Then I should be on "/en/login/?redirected=addnews&redirect_to=http%3A%2F%2Ffoss.espace.ws%2Fen%2Fadd-news%2F"
        And I should see "Please log in to suggest a new news"

    @javascript @Done 
    Scenario: A logged-in user Adding new news to the system with valid inputs
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/news/add"
        When I Add new frontend news with the folllowing "Monetizing Mobile Gaming", "Increased focus on mobile branding — maximizing each user", "News-Category", "The mobile gaming industry made $29 billion in 2015 — and it is only set to continue growing (with estimates as high as $49 billion by 2018)."
        And I attach the file "testImages/logo.png" to "news_image" with relative path
        And I press "submit"
        Then I should see "News Monetizing Mobile Gaming added successfully, it is now under review"
       
    @javascript @Done 
    Scenario: A logged-in user Adding new news with only required inputs
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/news/add"
        When I Add new frontend news with the folllowing "Tablets Are Dead", "", "News-Category", " 2010, tablets were supposed to be the new hot thing. Apple released the first iPad, Samsung was working on the Galaxy Tab and countless others were about to flood the market with Android tablets. Six years later, there weren’t any tablets at Mobile World Congress in Barcelona. Companies and consumers have moved on. Tablets are dead."
        And I attach the file "testImages/logo.png" to "news_image" with relative path
        And I press "submit"
        Then I should see "News Tablets Are Dead added successfully, it is now under review"

    @javascript  @Done 
    Scenario: A logged-in user Adding new news with null inputs
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/news/add"
        When I Add new frontend news with the folllowing "", "", "", ""
        And I press "submit"
        Then I should see "Title required"
        And I should see "Category required"
        And I should see "Image required"
        And I should see "Description required"

    @javascript  @Done 
    Scenario: A logged-in user Adding new news with numbers only in the textfields
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/news/add"
        When I Add new frontend news with "121323415", "32153", "1253253"
        And I press "submit"
        Then I should see "Title must include at least one letter"
        And I should see "Subtitle must include at least one letter"
        And I should see "Description must include at least one letter"

    @javascript  @Done
    Scenario: A logged-in user Adding new news with special characters only in the textfields
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/news/add"
        When I Add new frontend news with ";;;;;", ";;;;;", ";;;;;"
        And I press "submit"
        Then I should see "Title must include at least one letter"
        And I should see "Subtitle must include at least one letter"
        And I should see "Description must include at least one letter"

    @javascript  @Done 
    Scenario: A logged-in user Adding new news with title exceeds the max length
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/news/add"
        When I Add new frontend news with "News title News title News title News title News title News title News title News title News title News title", "News subtitle", "News description"
        And I press "submit"
        Then I should see "Title should not be more than 100 characters"

    @javascript  @Done
    Scenario: Adding new news from the list page with Title less than the min length
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/news/add"
        When I Add new frontend news with "title1", "News subtitle", "News description"
        And I press "submit"
        Then I should see "Title should be at least 10 characters"
