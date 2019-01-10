Feature: User receive the "Expert" badge once set by admin so that he can submit Expert Thoughts for review and get his skills promoted in his profile
  In order to receive the "Expert" badge once set by admin
  As a user
  I need to be marked as an expert by admin

    @Done
    Scenario: An admin mark a user as an expert
      Given I am a logged in user with "foss" and "F0$$"
      And I am on "/wp-admin/users.php"
      When I follow "nour.tarek"
      And I wait to be redirected
      And I check "is_expert"
      And I press "submit"
      And I wait to be redirected
      #Then the "is_expert" checkbox should be checked

    @Done @javascript
    Scenario: A user gets notified with his earned badge 1st time he access the system and should not see it again
      Given I am on "/en/login/"
      When I fill in "user_login" with "nour.tarek"
      And I fill in "user_pass" with "123456789"
      And I press "wp-submit"
      And I wait for 7 seconds
      Then I should see "Congratulations!"
      And I click on the element with css selector "#achievement-modal"
      And I wait for 4 seconds
      And I click on the element with css selector "button#user-nav2"
      And I click on the element with css selector "a:contains(Log out)"
      And I am on "/en/news/"
      And I am a logged in user with "nour.tarek" and "123456789"
      Then I should not see "Congratulations!"

    @Done
    Scenario: A user gets "Expert" badge and a notification email once his marked as an expert by admin and can add a thought to be reviewd by admin
      Given I am a logged in user with "nour.tarek" and "123456789"
      And I am on "/en/members/nour-tarek/about/"
      Then I should receive an email with subject "You have earned the Expert badge."
      And I should find element with css selector "img[alt='Expert']"
      When I am on "/en/expert-thoughts/add/"
      Then the response should contain "You can suggest a thought that inspires and motivates EgyptFOSS members."

    @Done @javascript
    Scenario: An expert adds a pending thought to be reviewed by Admin in the system
      Given I am a logged in user with "nour.tarek" and "123456789"
      And I am on "/en/expert-thoughts/add"
      When I Add new frontend expert thought with "New expert thought added from front end by expert", "New expert thought added from front end by expert description"
      And I attach the file "testImages/logo.png" to "expert_thought_image" with relative path
      And I add "new interest" to a auto-select "interest"
      And I press "submit"
      Then I should see "added successfully"

    @javascript @Done
    Scenario: Admin Accepts new Expert thought in the system with valid inputs
      Given I am a logged in user with "foss" and "F0$$"
      And I resize window with height 800 and width 2048 in px
      And I am on "/wp-admin/edit.php?post_type=expert_thought"
      And I click on the element with css selector "button#show-settings-link"
      And I fill in "edit_expert_thought_per_page" with "100"
      And I click on the element with css selector "#screen-options-apply"
      And I wait to be redirected
      When I follow "New expert thought added from front end by expert"
      And I wait to be redirected
      And I press "publish"
      #Then I should see "expert thought published."

    @Done
    Scenario: Admin unmark a user as an exper
      Given I am a logged in user with "foss" and "F0$$"
      And I am on "/wp-admin/users.php"
      When I follow "nour.tarek"
      And I wait to be redirected
      And I uncheck "is_expert"
      And I press "submit"
      And I wait to be redirected
     
    @Done @javascript
    Scenario: A user can't see "Expert" badge in his profile after admin unmark him as an expert
      Given I am a logged in user with "nour.tarek" and "123456789"
      And I am on "/en/members/nour-tarek/about/"
      And I should not find element with css selector "img[alt='Expert']"
      When I am on "/en/expert-thoughts/add/"
      Then I should see "Oops! That page can’t be found."