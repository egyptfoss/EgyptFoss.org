Feature: User view list of all products contributed by him/other user in his/other profile page
  In order to list all products contributed by him/other user in his/other profile page
  As an User
  I need to be able to navigate to contributed products page in his/other profile

    @not
    Scenario: A not logged-in user Navigating to contributed products list in profile page
        Given I am on "/members/foss/"
        When I follow "Products" in certain place ".no-ajax"
        And I follow "Contributes" in certain place ".item-list-tabs no-ajax"
        Then I should be on "/members/foss/products/contributes/"

    @not
    Scenario: A not logged-in user seeing an empty contributed products list in profile page
        Given I am on "/members/maged-saleh/products/contributes/"
        Then I should see "There are no contributed products by maged-saleh"

    @not 
    Scenario: A not logged-in user viewing 20 contributed products per page
        Given I am on "/members/foss/products/contributes/"
        Then I should see 20 ".profile-card" elements
        And I should see "Show more"

    @javascript @not
    Scenario: A not logged-in user loading more contributed products
        Given I am on "/members/foss/products/contributes/"
        When I follow "Show more"
        And I wait to be redirected
        Then I should see more "20" or more ".profile-card" elements
        And I should not see "Show more"

    @javascript @not
    Scenario: A not logged-in user viewing logo, title and last modification date for each product
        Given I am on "/members/foss/products/contributes/"
        Then I should see "edited_by_espace_and_foss"
        And I should see "Modified at" in the ".profile-card" element
        And I should see an ".card-thumb" element