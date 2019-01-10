Feature: User receive the "Collaboration Contributor" badges once user add/edit X post in collaboration center
  In order to receive the "Collaboration Contributor" badges once user add/edit X posts 
  As a user
  I need to add post and published from admin

    @Done @javascript
    Scenario: An admin set (X) as collaboration contributor threshold
      Given I resize window with height 800 and width 2048 in px
      And I am a logged in user with "foss" and "F0$$"
      And I am on "/wp-admin/admin.php?page=efb_badges"
      When I click on the element with css selector "button#show-settings-link"
      And I fill in "efb_badges_per_page" with "100"
      And I click on the element with css selector "#screen-options-apply"
      And I follow "Collaboration Contributor"
      And I fill in "efb-min-threshold" with "2"
      And I fill in "efb-desc" with "Lorem Ipsum is simply dummy text of the printing and typesetting industry."
      And I fill in "efb-desc-ar" with "لوريم إيبسوم(Lorem Ipsum) هو ببساطة نص شكلي (بمعنى أن الغاية هي الشكل وليس المحتوى) ويُستخدم في صناعات المطابع ودور النشر."
      And I press "submit"
      Then I should see "Badge has been updated successfully."

    @Done
    Scenario: A user gets a "Suggester" badge once he suggests a news
      Given I am a logged in user with "collaboration.contributor" and "123456789"
      And I am on "/en/news/add"
      When I Add new frontend news with the folllowing "Monetizing Android Gaming", "Increased focus on mobile branding — maximizing each user", "News-Category", "The mobile gaming industry made $29 billion in 2015 — and it is only set to continue growing (with estimates as high as $49 billion by 2018)."
      And I attach the file "testImages/logo.png" to "news_image" with relative path
      And I press "submit"
      Then I should see "News Monetizing Android Gaming added successfully, it is now under review"

    @Done @javascript
    Scenario: Logged in user can create a space
        Given I am a logged in user with "collaboration.contributor" and "123456789"
        When I resize window with height 800 and width 2048 in px
        And I am on "en/collaboration-center/spaces/"
        And I click on the element with css selector "a#new_space"
        And I fill in "new_space_title" with "inserted space test"
        And I click on the element with css selector "button[data-action='addNewCollaborativeSpace']"
        Then I should be on "en/collaboration-center/spaces/"
        And I should see "inserted space test"

    @Done   
    Scenario: A user post his first contribution page and shouldn't get any badge
      Given I am a logged in user with "collaboration.contributor" and "123456789"
      And I am on "en/collaboration-center/spaces/"
      Then I click on the element with css selector "a:contains('inserted space test')"
      And I wait to be redirected
      And I click on the element with css selector "a:contains('New Document')"
      And I wait to be redirected
      And I fill in "document_title" with "new document testing"
      And I fill in "document_content" with "new document testing"
      And I press "Save"
      And I wait to be redirected
      Then I should see "Document added successfully"
      And I should see "new document testing"
      And I should not see "Congratulations!"

    @javascript @Done   
    Scenario: A user with contributor role shouldn't be able to  to publish a document
      Given I am a logged in user with "collaboration.contributor" and "123456789"
        And I am on "/en/collaboration-center/spaces/"
        When I follow "inserted space test"
        And I follow "new document testing"
        And I wait to be redirected
        Then I should not see "published"

    @Done
    Scenario: Admin Accepts new news in the system with valid inputs
      Given I am a logged in user with "foss" and "F0$$"
      And I am on "/wp-admin/edit.php?post_type=news"
      When I follow "Monetizing Android Gaming"
      And I wait to be redirected
      And I press "publish"
      Then I should see "news published."


    @javascript @Done   
    Scenario: A user achieved author role should be able to publish a document
      Given I am a logged in user with "collaboration.contributor" and "123456789"
        And I am on "/en/collaboration-center/spaces/"
        When I follow "inserted space test"
        And I follow "new document testing"
        And I wait to be redirected
        Then I should see "published"

    @javascript @Done   
    Scenario: A user edit his fosspedia page and should get collaboration contributor badge
        Given I am a logged in user with "collaboration.contributor" and "123456789"
        And I am on "/en/collaboration-center/spaces/"
        When I follow "inserted space test"
        And I follow "new document testing"
        And I wait to be redirected
        And I fill in "document_title" with "Hello Editor"
        And I press "Save"
        And I wait to be redirected
        Then I should see "Document Title"
        And I wait for 4 seconds
        And the response should contain "Hello Editor"
        And I should see "Congratulations!"

    @Done   
    Scenario: A user who receives collaboration contributor badge, shouldn't get any point
      Given I am a logged in user with "collaboration.contributor" and "123456789"
      And I am on "/en/members/collaboration-contributor/"
      And I wait to be redirected
      Then I should see "10 Points"
      And I should find element with css selector "img[alt='Collaboration Contributor']"

