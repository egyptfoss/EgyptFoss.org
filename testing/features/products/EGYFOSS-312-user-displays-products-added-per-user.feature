Feature: User view list of all products added by him/other user in his/other profile page
  In order to list all products added by him/other user in his/other profile page
  As an User
  I need to be able to navigate to added products page in his/other profile

    @done
    Scenario: A not logged-in user Navigating to added products list in profile page
        Given I am on "/members/foss/"
        When I follow "Product Contributions" in certain place ".no-ajax"
        And I follow "Additions" in certain place ".all-products"
        Then I should be on "/members/foss/products/"

    @done
    Scenario: A not logged-in user seeing an empty added products list in profile page
        Given I am on "/members/maged-saleh/products/"
        Then I should see "There are no products added by maged-saleh"

    @done 
    Scenario: A not logged-in user viewing 20 added products per page
        Given I am on "/members/foss/products/"
        Then I should see 20 ".profile-card" elements
        And I should see "Show more"

    @javascript @done
    Scenario: A not logged-in user loading more added products
        Given I am on "/members/foss/products/"
        When I follow "Show more"
        And I wait to be redirected
        Then I should see more "20" or more ".profile-card" elements
        And I should not see "Show more"

    @javascript @done
    Scenario: A not logged-in user viewing logo, title and last creation date for each product
        Given I am on "/members/foss/products/"
        Then I should see "CDex"
        And I should see "Created at" in the ".profile-card" element
        And I should see an ".card-thumb" element