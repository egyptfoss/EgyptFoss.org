Feature: User share a published news on Facebook, Twitter, G+ and Linkedin
  In order to share a published news on Facebook, Twitter, G+ and Linkedin
  As a user
  I need to navigate to news page and share it on Facebook, Twitter, G+ and Linkedin

    @javascript @works_firefox_chrome @not_working_phantomjs
    Scenario: A user sharing a news on Facebook
        Given I am on "/en/news/new-test-news-title-egypt-foss/"
        When I follow "Facebook" share
        And I switch to the new window
        And I wait to be redirected
        And I fill in "email" with "buggy.tamtam1@gmail.com"
        And I fill in "pass" with "buggy123"
        And I wait for 2 seconds 
        And I press "u_0_2"
        And I wait to be redirected
        #And I fill in "u_0_z" with "A very nice article!"
        And I press "u_0_d"
        And I close the browser

    @javascript @Done  @add-news
    Scenario: A user sharing a news on Twitter
        Given I am on "/en/news/new-test-news-title-egypt-foss/"
        When I follow "Twitter" share
        And I switch to the new window
        And I wait to be redirected
        And I wait for 3 seconds
        #And I fill in "status" with "A very nice article!"
        And I fill in "username_or_email" with "buggy.tamtam1@gmail.com"
        And I fill in "password" with "buggy123"
        And I press "Log in and Tweet"
        #And I wait to be redirected
        #Then I press "Tweet"
        #And I wait to be redirected
        #And I should see "All done."
        Then I close the browser

    @javascript @Done
    Scenario: A user sharing a news on G+
        Given I am on "/en/news/new-test-news-title-egypt-foss/"
        When I follow "Google+" share
        And I switch to the new window
        And I fill in "Email" with "buggy.tamtam1@gmail.com"
        And I press "next"        
        And I fill in "Passwd" with "buggy123"
        And I press "Sign in"
        And I wait to be redirected
        #And I fill in ":0.f" with "A very nice article!"
        And I share the news on "Google+"        
        #Then I should see "News has been shared successfully"
        And I close the browser

    @javascript @Done
    Scenario: A user sharing a news on Linkedin
        Given I am on "/en/news/new-test-news-title-egypt-foss/"
        When I follow "LinkedIn" share
        And I switch to the new window
        And I wait to be redirected
        And I wait for 5 seconds
        And I fill in "session_key" with "buggy.tamtam1@gmail.com"        
        And I fill in "session_password" with "buggy123"
        And I press "Sign In"
        And I wait to be redirected
        #And I fill in "share-text" with "A very nice article!"
        And I share the news on "LinkedIn"        
        #And I wait to be redirected
        #Then the response should contain "Great! You have successfully shared this update." 