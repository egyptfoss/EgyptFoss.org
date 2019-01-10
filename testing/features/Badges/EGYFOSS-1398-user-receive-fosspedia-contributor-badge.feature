Feature: User receive the "FossPedia Contributor" badges once user add/edit X post in fosspedia module
  In order to receive the "FossPedia Contributor" badges once user add/edit X posts 
  As a user
  I need to add post and published from admin

    @Done @javascript
    Scenario: An admin set (X) as fosspedia contributor threshold
      Given I resize window with height 800 and width 2048 in px
      And I am a logged in user with "foss" and "F0$$"
      And I am on "/wp-admin/admin.php?page=efb_badges"
      When I click on the element with css selector "button#show-settings-link"
      And I fill in "efb_badges_per_page" with "100"
      And I click on the element with css selector "#screen-options-apply"
      And I follow "Fosspedia Contributor"
      And I fill in "efb-min-threshold" with "2"
      And I fill in "efb-desc" with "Lorem Ipsum is simply dummy text of the printing and typesetting industry."
      And I fill in "efb-desc-ar" with "لوريم إيبسوم(Lorem Ipsum) هو ببساطة نص شكلي (بمعنى أن الغاية هي الشكل وليس المحتوى) ويُستخدم في صناعات المطابع ودور النشر."
      And I press "submit"
      Then I should see "Badge has been updated successfully."

    @javascript @Useless
    Scenario: A user with contributor role shouldn't add/edit fosspedia
      Given I am a logged in user with "pedia.contributor" and "123456789"
      And I am on "/en/wiki/FOSSPedia"
      And I wait for 5 seconds
      Then I should not see "Edit"
      And I am on "/en/wiki/FOSSPedia3.1"
      And I wait for 5 seconds
      Then I should see "you do not have permission to create this page."

    @Done
    Scenario: A user gets a "Suggester" badge once he suggests a news
      Given I am a logged in user with "pedia.contributor" and "123456789"
      And I am on "/en/news/add"
      When I Add new frontend news with the folllowing "Monetizing Android Gaming", "Increased focus on mobile branding — maximizing each user", "News-Category", "The mobile gaming industry made $29 billion in 2015 — and it is only set to continue growing (with estimates as high as $49 billion by 2018)."
      And I attach the file "testImages/logo.png" to "news_image" with relative path
      And I press "submit"
      Then I should see "News Monetizing Android Gaming added successfully, it is now under review"

    @Done
    Scenario: Admin Accepts new news in the system with valid inputs
      Given I am a logged in user with "foss" and "F0$$"
      And I am on "/wp-admin/edit.php?post_type=news"
      When I follow "Monetizing Android Gaming"
      And I wait to be redirected
      And I press "publish"
      Then I should see "news published."

    @Done   
    Scenario: A user achieved author role should add/edit fosspedia
      Given I am a logged in user with "pedia.contributor" and "123456789"
      And I am on "/en/wiki/FOSSPedia"
      And I wait to be redirected
      Then I should see "Edit"
      And I am on "/en/wiki/FOSSPedia3.1"
      And I wait to be redirected
      Then I should see "Create"

    @Done  
    Scenario: A user post his first fosspedia page and should get fosspedia contributor badge
      Given I am a logged in user with "pedia.contributor" and "123456789"
      And I am on "/en/wiki/FOSSPedia3.1"
      And I wait to be redirected
      When I follow "Create"
      And I wait to be redirected
      And I fill in "wpTextbox1" with "Hello Fosspedia3"
      Then I press "wpSave"
      And I wait to be redirected
      Then I should see "Congratulations!"

    @javascript @not_implemented   
    Scenario: A user edit his fosspedia page and should get fosspedia contributor badge
      Given I am a logged in user with "pedia.contributor" and "123456789"
      And I am on "/en/wiki/FOSSPedia3.1"
      When I follow "Edit"
      And I wait to be redirected
      And I fill in "wpTextbox1" with "Hello Fosspedia3-1"
      Then I press "wpSave"
      And I wait for 7 seconds
      Then I should see "Congratulations!"

    @not_implemented   
    Scenario: A user who receives fosspedia contributor badge, shouldn't get any point
      Given I am a logged in user with "pedia.contributor" and "123456789"
      And I am on "/en/members/pedia-contributor/"
      And I wait to be redirected
      Then I should see "10 Points"
      And I should find element with css selector "img[alt='Fosspedia Contributor']"
