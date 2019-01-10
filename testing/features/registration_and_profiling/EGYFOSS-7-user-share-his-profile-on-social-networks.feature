Feature: User share his profile on Facebook, Twitter, G+ and Linkedin
  In order to share his profile on Facebook, Twitter, G+ and Linkedin
  As a user
  I need to navigate to my profile page and share it on Facebook, Twitter, G+ and Linkedin

    Background:
    Given I am on "/en/"
    And I am a logged in user with "mohamed.said" and "123456789"

    @javascript @not_implemented @must
    Scenario: A user sharing his profile on Facebook
        Given I am on "/members/mohamed-said/profile/"
        # hover over the sharing menu
        When I hover ".share-profile"
        And I follow "Facebook" in certain place "heateor_sss_sharing_container heateor_sss_horizontal_sharing"
        And I switch to the new window
        And I fill in "email" with "bougy.tamtam13@gmail.com"
        And I fill in "pass" with "19021988"
        And I follow "u_0_2"
        And I wait to be redirected
        And I fill in "u_0_z" with "My Profile on EGYPTFOSS!"
        And I press "u_0_9"
        Then I should see "Your profile has been shared successfully"

    @javascript @not_implemented @must
    Scenario: A user sharing his profile on Twitter
        Given I am on "/members/mohamed-said/profile/"
        # hover over the sharing menu
        When I hover ".share-profile"
        And I follow "Twitter" in certain place "heateor_sss_sharing_container heateor_sss_horizontal_sharing"
        And I switch to the new window
        And I fill in "status" with "My Profile on EGYPTFOSS!"
        And I fill in "username_or_email" with "bougy.tamtam13@gmail.com"
        And I fill in "password" with "19021985"
        And I follow "Log in and Tweet"
        And I wait to be redirected
        Then I should see "Your profile has been shared successfully"

    @javascript @not_implemented @must
    Scenario: A user sharing his profile on G+
        Given I am on "/members/mohamed-said/profile/"
        # hover over the sharing menu
        When I hover ".share-profile"
        And I follow "Google plus" in certain place "heateor_sss_sharing_container heateor_sss_horizontal_sharing"
        And I switch to the new window
        And I fill in "Email" with "bougy.tamtam10@gmail.com"
        And I follow "next"        
        And I fill in "Passwd" with "Heba123456"
        And I follow "signIn"
        And I wait to be redirected
        And I fill in ":0.f" with "My Profile on EGYPTFOSS!"
        And I follow "sharebutton"        
        Then I should see "Your profile has been shared successfully"

    @javascript @not_implemented @must
    Scenario: A user sharing his profile on Linkedin
        Given I am on "/members/mohamed-said/profile/"
        # hover over the sharing menu
        When I hover ".share-profile"
        And I follow "Linkedin" in certain place "heateor_sss_sharing_container heateor_sss_horizontal_sharing"
        And I switch to the new window
        And I fill in "Email" with "bougy.tamtam10@gmail.com"        
        And I fill in "Passwd" with "Heba123456"
        And I follow "signIn"
        And I wait to be redirected
        And I fill in "share_text" with "My Profile on EGYPTFOSS!"
        And I follow "sharebutton"        
        Then I should see "Your profile has been shared successfully" 