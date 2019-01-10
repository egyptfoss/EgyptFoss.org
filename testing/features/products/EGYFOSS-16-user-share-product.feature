Feature: User share a published product on Facebook, Twitter, G+ and Linkedin
  In order to share a published product on Facebook, Twitter, G+ and Linkedin
  As a user
  I need to navigate to product page and share it on Facebook, Twitter, G+ and Linkedin

    @javascript @not_implemented @must
    Scenario: A user sharing a product on Facebook
        Given I am on "/en/product/CDex"
        When I follow "Facebook" in certain place "heateor_sss_sharing_container heateor_sss_horizontal_sharing"
        And I switch to the new window
        And I fill in "email" with "bougy.tamtam13@gmail.com"
        And I fill in "pass" with "19021988"
        And I follow "u_0_2"
        And I wait to be redirected
        And I fill in "u_0_z" with "A very nice app to try!"
        And I press "u_0_9"
        Then I should see "Product has been shared successfully"

    @javascript @not_implemented @must
    Scenario: A user sharing a product on Twitter
        Given I am on "/en/products/CDex"
        When I follow "Twitter" in certain place "heateor_sss_sharing_container heateor_sss_horizontal_sharing"
        And I switch to the new window
        And I fill in "status" with "A very nice app to try!"
        And I fill in "username_or_email" with "bougy.tamtam13@gmail.com"
        And I fill in "password" with "19021985"
        And I follow "Log in and Tweet"
        And I wait to be redirected
        Then I should see "Product has been shared successfully"

    @javascript @not_implemented @must
    Scenario: A user sharing a product on G+
        Given I am on "/en/products/CDex"
        When I follow "Google plus" in certain place "heateor_sss_sharing_container heateor_sss_horizontal_sharing"
        And I switch to the new window
        And I fill in "Email" with "bougy.tamtam10@gmail.com"
        And I follow "next"        
        And I fill in "Passwd" with "Heba123456"
        And I follow "signIn"
        And I wait to be redirected
        And I fill in ":0.f" with "A very nice app to try!"
        And I follow "sharebutton"        
        Then I should see "Product has been shared successfully"

    @javascript @not_implemented @must
    Scenario: A user sharing a product on Linkedin
        Given I am on "/en/products/CDex"
        When I follow "Linkedin" in certain place "heateor_sss_sharing_container heateor_sss_horizontal_sharing"
        And I switch to the new window
        And I fill in "Email" with "bougy.tamtam10@gmail.com"        
        And I fill in "Passwd" with "Heba123456"
        And I follow "signIn"
        And I wait to be redirected
        And I fill in "share_text" with "A very nice app to try!"
        And I follow "sharebutton"        
        Then I should see "Product has been shared successfully" 