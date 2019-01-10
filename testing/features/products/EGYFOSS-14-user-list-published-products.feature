Feature: User view list of all published products in the system
  In order to list all published product in the system
  As an User
  I need to be able to navigate to products list page and load more products

    @javascript @not_working
    Scenario: A not logged-in user seeing an empty product list
        Given I am on "/products"
        Then I should see "There are no products yet, Add new product"
        When I resize window with height 800 and width 1024 in px
        And I follow "Add new product"
        And I wait to be redirected
        Then I should be on "/en/login/"
        And I should see "Please log in to suggest a new product"

    @javascript @invalid
    Scenario: A not logged-in user seeing no. of all published products in the system
        Given I am on "/products"
        Then I should see "15" in the "#efProductsCount" element

    @javascript @invalid
    Scenario: A not logged-in user viewing 10 products per page
        Given I am on "/products"
        Then I should see 10 ".product-card" elements
        And I should see "load more"

    @javascript @not_working
    Scenario: A logged-in user seeing an empty product list
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/products/?industry=Development"
        Then I should see "There are no products yet, Suggest product"
        When I resize window with height 800 and width 1024 in px
        And I follow "Suggest Product"
        And I wait to be redirected
        Then I should be on "/en/products/add/"
        And I should see "Suggest Product"

    @javascript @invalid  
    Scenario: A logged-in user seeing no. of all published products in the system
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/products/?industry=software-engineering"
        Then I should see "10" in the "#efProductsCount" element

    @javascript @not_working
    Scenario: A logged-in user viewing 10 products per page
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/products/?industry=software-engineering"
        Then I should see 10 ".product-card" elements
        And I should see "Show more"
    
    @javascript @Done
    Scenario: A not logged-in user viewing all attributes of a product in the product card
        Given I am on "/en/products/?industry=software-engineering"
        Then I should see "License"
        And the response should contain "By"
        And "product-img lfloat" should be visible

    @javascript @Done 
    Scenario: A not logged-in user viewing only required attributes of a product in the product card
        Given I am on "/en/products/?industry=software-engineering"
        Then I should see "License"
        And the response should contain "By"
        And "product-img lfloat" should be visible
    
    @javascript @Done
    Scenario: A logged-in user viewing all attributes of a product in the product card
        Given I am a logged in user with "foss" and "F0$$"
        Given I am on "/en/products/?industry=software-engineering"
        Then I should see "License"
        And the response should contain "By"
        And "product-img lfloat" should be visible

    @javascript @Done
    Scenario: A not logged-in user viewing only required attributes of a product in the product card
        Given I am a logged in user with "foss" and "F0$$"
        Given I am on "/en/products/?industry=software-engineering"
        Then I should see "License"
        And the response should contain "By"
        And "product-img lfloat" should be visible


        
        