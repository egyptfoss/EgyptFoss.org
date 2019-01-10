Feature: share document by groups
  In order share document
  As a logged-in user
  I need to be able to share document in my spaces
   
    @Done @javascript    
    Scenario: User can't invite a "Group" to a document shared with him.
      Given I am a logged in user with "bougy.tamtam" and "123456789"
      And I am on "en/collaboration-center/shared/"
      Then I should not find element with css selector ".invite-space-document"
      
    @Done @javascript  
    Scenario: From documents list, user can navigate to "Group" invite tab
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "en/collaboration-center/spaces/29/"
        Then I click on the element with css selector "#document_35 div.options .invite-space-document"
        And I click on the element with css selector "a[href='#invite-groups']"
        Then I should see "type"
        And I should see "Account subtype"
        And I should see "Interests"
        And I should see "Technologies"
        And I should see "Theme"

    @Done @javascript    
    Scenario: User can invite a group with type "individual" and sub-type "developer" and theme "theme1" an interest "interest1" or "php" and technology "java" or "php" to his document
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "en/collaboration-center/spaces/29/"
        Then I click on the element with css selector "#document_30 div.options .invite-space-document"
        And I click on the element with css selector "a[href='#invite-groups']"
        And I fill in "share_type" with "Individual"
        And I fill in "sub_type" with "developer"
        And I add "java,php" to a auto-multi-select "share_technologies"
        And I add "interest1,php" to a auto-multi-select "share_interests"
        And I add "theme1," to a auto-multi-select "share_industry"
        And i wait 2 seconds
        And I click on the element with css selector "button#save_invited"
        And i wait 2 seconds
        Then  I should see "Sharing settings saved successfully"
        And I click on the element with css selector "button#user-nav2"
        And I click on the element with css selector "a:contains(Log out)"
        When I am a logged in user with "maii.test" and "123456789"
        And I am on "en/collaboration-center/shared/"
        Then I should see "grp document #1"
    
    @Done @javascript    
    Scenario: User can invite a group with type "Individual" and sub-type "developer" to his document
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "en/collaboration-center/spaces/29/"
        Then I click on the element with css selector "#document_31 div.options .invite-space-document"
        And I click on the element with css selector "a[href='#invite-groups']"
        And I fill in "share_type" with "Individual"
        And I fill in "sub_type" with "developer"
        And I click on the element with css selector "button#save_invited"
        And i wait 2 seconds
        Then  I should see "Sharing settings saved successfully"
        And I click on the element with css selector "button#user-nav2"
        And I click on the element with css selector "a:contains(Log out)"
        When I am a logged in user with "maii.test" and "123456789"
        And I am on "en/collaboration-center/shared/"
        Then I should see "grp document #2"
      
    @Done @javascript    
    Scenario: User can invite a group with interest "interest1" or "php" to his document
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "en/collaboration-center/spaces/29/"
        Then I click on the element with css selector "#document_32 div.options .invite-space-document"
        And I click on the element with css selector "a[href='#invite-groups']"
        When I add "interest1,php" to a auto-multi-select "share_interests"
        And i wait 2 seconds
        And I click on the element with css selector "button#save_invited"
        And i wait 2 seconds
        Then  I should see "Sharing settings saved successfully"
        And I click on the element with css selector "button#user-nav2"
        And I click on the element with css selector "a:contains(Log out)"
        When I am a logged in user with "maii.test" and "123456789"
        And I am on "en/collaboration-center/shared/"
        Then I should see "grp document #3"
              

    @Done @javascript
    Scenario: User can invite a group with technology "java" or "php" to his document
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "en/collaboration-center/spaces/29/"
        Then I click on the element with css selector "#document_33 div.options .invite-space-document"
        And I click on the element with css selector "a[href='#invite-groups']"
        When I add "java,php" to a auto-multi-select "share_technologies"
        And i wait 2 seconds
        And I click on the element with css selector "button#save_invited"
        And i wait 2 seconds
        Then  I should see "Sharing settings saved successfully"
        And I click on the element with css selector "button#user-nav2"
        And I click on the element with css selector "a:contains(Log out)"
        When I am a logged in user with "maii.test" and "123456789"
        And I am on "en/collaboration-center/shared/"
        Then I should see "grp document #4"

    @Done @javascript    
    Scenario: User can invite a group with theme "theme1" to his document
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "en/collaboration-center/spaces/29/"
        Then I click on the element with css selector "#document_34 div.options .invite-space-document"
        And I click on the element with css selector "a[href='#invite-groups']"
        And I add "theme1," to a auto-multi-select "share_industry"
        And i wait 2 seconds
        And I click on the element with css selector "button#save_invited"
        And i wait 2 seconds
        Then  I should see "Sharing settings saved successfully"
        And I click on the element with css selector "button#user-nav2"
        And I click on the element with css selector "a:contains(Log out)"
        When I am a logged in user with "maii.test" and "123456789"
        And I am on "en/collaboration-center/shared/"
        Then I should see "grp document #5"
    
    @Done @javascript
    Scenario: User can invite a group with type "Individual" to his document
        Given I am a logged in user with "bougy.tamtam" and "123456789"
        And I am on "en/collaboration-center/spaces/29/"
        Then I click on the element with css selector "#document_35 div.options .invite-space-document"
        And I click on the element with css selector "a[href='#invite-groups']"
        And I fill in "share_type" with "Individual"
        And I click on the element with css selector "button#save_invited"
        And i wait 2 seconds
        Then  I should see "Sharing settings saved successfully"
        And I click on the element with css selector "button#user-nav2"
        And I click on the element with css selector "a:contains(Log out)"
        When I am a logged in user with "maii.test" and "123456789"
        And I am on "en/collaboration-center/shared/"
        Then I should see "grp document #6"
    
    @not_implemented
    Scenario: Invitees should receive the invitation email
    
    @not_implemented
    Scenario: Invitees should see the space of shared documents in his "Shared with me" list

    @not_implemented
    Scenario: Invitees should see the shared documents in the right space in "Shared with me" list

        
    