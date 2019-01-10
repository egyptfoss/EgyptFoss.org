Feature: User view randomly 10 featured products in the products home page
  In order to list all published product in the system
  As an User
  I need to be able to navigate to products home page and view randomly 10 featured products

    
    Scenario: A not logged-in user viewing 10 featured products in products home page
        Given I am on "/products"
        Then I should see 10 ".featured-card" elements

    
    Scenario: A not logged-in user viewing all attributes of a product in the featured product card
        Given I am on "/products"
        Then I should see "license --" in the ".featured-card" element
        And I should see "description" in the ".featured-card" element
        And I should see "by" in the ".featured-card" element
        And I should see the css selector "div.featured-card div.product-img"
        And I should see the css selector "div.featured-card .featured-icon"

    Scenario: A logged-in user viewing 10 featured products in products home page
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/products"
        Then I should see 10 ".featured-card" elements

    
    Scenario: A logged-in user viewing all attributes of a product in the featured product card
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/products"
        Then I should see "license --" in the ".featured-card" element
        And I should see "description" in the ".featured-card" element
        And I should see "by" in the ".featured-card" element
        And I should see the css selector "div.featured-card div.product-img"
        And I should see the css selector "div.featured-card .featured-icon"