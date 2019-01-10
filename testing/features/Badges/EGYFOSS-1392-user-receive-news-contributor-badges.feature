Feature: User receive the "News Beginner" badges once my X post is reviewed and published by admin so that I can publish future posts without admin approval
  In order to receive the "News Specialist" badges once my X post is reviewed and published by admin
  As a user
  I need to add news and publish them without admin approval

    @Done @javascript
    Scenario: An admin set (X) as news specialist threshold
      Given I resize window with height 800 and width 2048 in px
      And I am a logged in user with "foss" and "F0$$"
      And I am on "/wp-admin/admin.php?page=efb_badges"
      When I click on the element with css selector "button#show-settings-link"
      And I fill in "efb_badges_per_page" with "100"
      And I click on the element with css selector "#screen-options-apply"
      And I follow "News Specialist"
      And I fill in "efb-min-threshold" with "10"
      And I fill in "efb-desc" with "Lorem Ipsum is simply dummy text of the printing and typesetting industry."
      And I fill in "efb-desc-ar" with "لوريم إيبسوم(Lorem Ipsum) هو ببساطة نص شكلي (بمعنى أن الغاية هي الشكل وليس المحتوى) ويُستخدم في صناعات المطابع ودور النشر."
      And I press "submit"
      Then I should see "Badge has been updated successfully."

    @Done
    Scenario: A user gets a "Suggester" badge once he suggests a news
      Given I am a logged in user with "leen.tarek" and "123456789"
      And I am on "/en/news/add"
      When I Add new frontend news with the folllowing "Monetizing Mobile Gaming", "Increased focus on mobile branding — maximizing each user", "News-Category", "The mobile gaming industry made $29 billion in 2015 — and it is only set to continue growing (with estimates as high as $49 billion by 2018)."
      And I attach the file "testImages/logo.png" to "news_image" with relative path
      And I press "submit"
      Then I should see "News Monetizing Mobile Gaming added successfully, it is now under review"
      And I should see "Congratulations!"

    @Done
    Scenario: Admin Accepts new news in the system with valid inputs
      Given I am a logged in user with "foss" and "F0$$"
      And I am on "/wp-admin/edit.php?post_type=news"
      When I follow "Monetizing Mobile Gaming"
      And I wait to be redirected
      And I press "publish"
      Then I should see "news published."

    @Done @javascript
    Scenario: A user gets notified with his earned badge 1st time he access the system and should not see it again
      Given I am on "/en/login/"
      When I fill in "user_login" with "leen.tarek"
      And I fill in "user_pass" with "123456789"
      And I press "wp-submit"
      And I wait for 7 seconds
      Then I should see "Congratulations!"
      And I click on the element with css selector "#achievement-modal"
      And I wait for 4 seconds
      And I click on the element with css selector "button#user-nav2"
      And I click on the element with css selector "a:contains(Log out)"
      And I am on "/en/news/"
      And I am a logged in user with "leen.tarek" and "123456789"
      Then I should not see "Congratulations!"

    @Done
    Scenario: A user gets 10 points, a "News Beginner" badge and a notification email once his 1st news reviewed and published by admin
      Given I am a logged in user with "leen.tarek" and "123456789"
      And I am on "/en/members/leen-tarek/about/"
      Then I should receive an email with subject "You have earned the News Beginner badge."
      And I should see "10 Points"
      And I should find element with css selector "img[alt='News Beginner']"

    #@Done @javascript
    #Scenario: A 1st user has (X) published news in the system and gets notified with his earned badge 1st time he access the system and should not see it again
      #Given I am on "/en/login/"
      #When I fill in "user_login" with "leen.tarek"
      #And I fill in "user_pass" with "123456789"
      #And I press "wp-submit"
      #And I wait for 4 seconds
      #And take screenshot
      #Then I should see "Congratulations!"
      #And I click on the element with css selector "#achievement-modal"
      #And I wait for 4 seconds
      #And I click on the element with css selector "button#user-nav2"
      #And I click on the element with css selector "a:contains(Log out)"
      #And I am on "/en/news/"
      #And I am a logged in user with "leen.tarek" and "123456789"
      #Then I should not see "Congratulations!"

    @Done @add-news-badges-first-user
    Scenario: A user adds news no X to get News Specialist
      Given I am a logged in user with "leen.tarek" and "123456789"
      And I am on "/en/news/add"
      When I Add new frontend news with the folllowing "#X news for level 2 badge", "Increased focus on mobile branding — maximizing each user", "News-Category", "The mobile gaming industry made $29 billion in 2015 — and it is only set to continue growing (with estimates as high as $49 billion by 2018)."
      And I attach the file "testImages/logo.png" to "news_image" with relative path
      And I press "submit"
      Then I should see "News #X news for level 2 badge added successfully, it is now under review"

    @Done
    Scenario: Admin Accepts new news in the system with valid inputs
      Given I am a logged in user with "foss" and "F0$$"
      And I am on "/wp-admin/edit.php?post_type=news"
      When I follow "#X news for level 2 badge"
      And I wait to be redirected
      And I press "publish"
      Then I should see "news published."
     
    @Done @javascript
    Scenario: A user gets 10 points for each news published, a "News Specialist" badge and a notification email once he meets the badge threshold
      Given I am a logged in user with "leen.tarek" and "123456789"
      And I wait for 5 seconds
      Then I should see "Congratulations!"
      And I click on the element with css selector "#achievement-modal"
      And I wait for 4 seconds
      And I am on "/en/members/leen-tarek/about/"
      Then I should receive an email with subject "You have earned the News Specialist badge."
      And I should see "100"
      And I should find element with css selector "img[alt='News Specialist']"
  
    @Done
    Scenario: A 1st user publish news without admin permission after getting level 2 badge and should see pop up
      Given I am a logged in user with "leen.tarek" and "123456789"
      And I am on "/en/news/add"
      When I Add new frontend news with the folllowing "Test New news published", "without admin permission", "News-Category", "Test New news published without admin permission"
      And I attach the file "testImages/logo.png" to "news_image" with relative path
      And I press "submit"
      Then I should see "Test New news published added successfully"
      When I am on "/en/news/"
      And I should see "Test New news published"
  
    @Done @javascript
    Scenario: An admin increase threshold value in news Specialist to (X + 5)
      Given I resize window with height 800 and width 2048 in px
      And I am a logged in user with "foss" and "F0$$"
      And I am on "/wp-admin/admin.php?page=efb_badges"
      When I click on the element with css selector "button#show-settings-link"
      And I fill in "efb_badges_per_page" with "100"
      And I click on the element with css selector "#screen-options-apply"
      And I follow "News Specialist"
      And I fill in "efb-min-threshold" with "15"
      And I fill in "efb-desc" with "Lorem Ipsum is simply dummy text of the printing and typesetting industry."
      And I fill in "efb-desc-ar" with "لوريم إيبسوم(Lorem Ipsum) هو ببساطة نص شكلي (بمعنى أن الغاية هي الشكل وليس المحتوى) ويُستخدم في صناعات المطابع ودور النشر."
      And I press "submit"
      Then I should see "Badge has been updated successfully."

   	@Done @add-news-badges-second-user
    Scenario: A 2nd user has (X) published news in the system
      Given I am a logged in user with "nour.tarek" and "123456789"
      And I am on "/en/news/add"
      When I Add new frontend news with the folllowing "2nd News Test- Monetizing Mobile Gaming", "Increased focus on mobile branding — maximizing each user", "News-Category", "The mobile gaming industry made $29 billion in 2015 — and it is only set to continue growing (with estimates as high as $49 billion by 2018)."
      And I attach the file "testImages/logo.png" to "news_image" with relative path
      And I press "submit"
      Then I should see "2nd News Test- Monetizing Mobile Gaming added successfully, it is now under review"
      When I am on "/en/news/"
      And I should not see "2nd News Test- Monetizing Mobile Gaming"

    @Done
    Scenario: A 2nd user can't see the badge in his profile and can't publish news without admin permission
      Given I am a logged in user with "nour.tarek" and "123456789"
      And I am on "/en/news/add"
      When I Add new frontend news with the folllowing "2nd user add News Test", "2nd user add News Test", "News-Category", "2nd user add News Test"
      And I attach the file "testImages/logo.png" to "news_image" with relative path
      And I press "submit"
      Then I should see "2nd user add News Test added successfully, it is now under review"
      When I am on "/en/news/"
      And I should not see "2nd user add News Test"
      When I am on "/en/members/nour-tarek/about/"
      Then I should see "100"
      And I should not find element with css selector "img[alt='News Specialist']"

    @javascript @Done
    Scenario: A 1st user still can publish news without admin permission after changing the badge threshold
      Given I am a logged in user with "leen.tarek" and "123456789"
      And I am on "/en/news/add"
      When I Add new frontend news with the folllowing "Changed threshold Test New news published", "without admin permission", "News-Category", "Test New news published without admin permission"
      And I attach the file "testImages/logo.png" to "news_image" with relative path
      And I press "submit"
      Then I should see "Changed threshold Test New news published added successfully"
      When I am on "/en/news/"
      And I wait to be redirected
      And I should see "Changed threshold Test New news published"

 