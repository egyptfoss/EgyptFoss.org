Feature: Manage products by adding, editing and deleting them
  In order to manage the products in the system
  As an Admin
  I need to be able to list, add, edit and delete them

  Background:
        Given I am on "/wp-login.php"
        And there are following users:
            | username | email                      | plain_password | enabled |
            | foss     | admin@example.com | F0$$   | yes     |
        When I fill in the following:
            | user_login | foss |
            | user_pass | F0$$ |
        And I press "wp-submit"

    @javascript 
    Scenario Outline: Adding new product from the list page with valid inputs
        Given I am on "/wp-admin/edit.php?post_type=product"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Add new product with "<title>" , "<developer>" , "<desc>" , "<func>" , "<ind>" , "<usage>" , "<ref>" , "<link_to_source>" , "<type>" , "<tech>" , "<platform>" , "<license>" and "<keywords>" 
        And I press "publish"
        Then I should see "product published. "
        
        Examples:
        | title | developer | desc | func | ind | usage | ref | link_to_source | type | tech | platform | license | keywords | 
        | New MYIT | New Maii | MYIT MYIT MYIT  | QA Engineer | IT | A very nice MYIT | ref1 | www.espace.com.eg | type1 | tech1 | platform1 | license1 | MI IT |
        | New EGYFOSS | New Eslam | EGYFOSS EGYFOSS EGYFOSS | Software Engineer | Software | A very nice EGYFOSS | ref2 | www.google.com | type2 | tech2 | platform2 | license2 | EGYFOSS |
        | New Memerise | New Ashraf | Memerise Memerise Memerise | Software Engineer | Computer engineering | A very nice Memrise | ref3 | www.facebook.com | type3 | tech3 | platform3 | license3 | Memrise |
       
    @javascript 
    Scenario Outline: Adding new product from the list page with only required inputs
        Given I am on "/wp-admin/edit.php?post_type=product"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Add new product with "<title>" , "" , "<desc>" , "" , "" , "" , "" , "" , "" , "" , "" , "" and ""
        And I press "publish"
        Then I should see "product published. "  
        
        Examples:
        | title | desc |
        | Jingo | Jingo is a very nice software, try to use it now.  |
        | Twitter | Twitter is a very nice software, try to use it now. |

    @javascript 
    Scenario Outline: Adding new product from the list page with already exist title
        Given I am on "/wp-admin/edit.php?post_type=product"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Add new product with "<title>" , "" , "<desc>" , "" , "" , "" , "" , "" , "" , "" , "" , "" and ""
        And I press "publish"
        Then I should see "already exist"  
        
        Examples:
        | title | desc |
        | Jingo | Jingo is a very nice software, try to use it now.  |
        | Twitter | Twitter is a very nice software, try to use it now. |

    @javascript 
    Scenario: Listing all products in the system
        Given I am on "/wp-admin/"
        When I follow "Products"
        And I follow "All products"
        And I wait to be redirected
        Then I should be on "/wp-admin/edit.php?post_type=product"
        And I should see "EGYFOSS"
  
    @javascript 
    Scenario: Adding new product from the sidebar with valid inputs
        Given I am on "/wp-admin/"
        When I follow "Products"
        And I follow "Add New" in certain place "li#menu-posts-product"   
        And I wait to be redirected
        Then I should be on "/wp-admin/post-new.php?post_type=product"
        And I should see "Add New Product"

    @javascript 
    Scenario: Adding new product with null inputs
        Given I am on "/wp-admin/edit.php?post_type=product"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Add new product with "" , "" , "" , "" , "" , "" , "" , "" , "" , "" , "" , "" and ""
        And I press "publish"
        Then I should see "Required"

    @javascript 
    Scenario: Adding new product with numbers only in the textfields
        Given I am on "/wp-admin/edit.php?post_type=product"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Add new product with "324" , "13124" , "547" , "54312" , "845312." , "21432" , "34" , "324" , "type1" , "tech1" , "platform1" , "license1" and "3241324"
        And I press "publish"
        Then I should see "must contain at least one letter"

    @javascript 
    Scenario: Adding new product with special characters only in the textfields
        Given I am on "/wp-admin/edit.php?post_type=product"
        When I click on the element with css selector "a.page-title-action"
        And I wait to be redirected
        And I Add new product with ",,," , ",,," , ",,," , ",,," , ",,,,,,." , "****" , ";;;" , ";;;;" , "type1" , "tech1" , "platform1" , "license1" and ";;;;"
        And I press "publish"
        Then I should see "must contain at least one letter"

    @javascript @not_including_link
    Scenario: editing a product in the system
        Given I am on "/wp-admin/edit.php?post_type=product"
        And I follow "Edit" on the row containing "EGYFOSS"
        And I wait to be redirected
        When I Edit the product name with "EGYPTFOSS" , "Yomna" , "Gamil gedan, give it a try" , "software engineer" , "IT" , "ay 7aga" , "momtaz" , "type1" , "tech1" , "platform1" , "license1" and "EGYFOSS"
        And I press "publish"
        Then I should see "Product updated"

    @javascript @not_implemented
    Scenario: Cancelling deleting a product from the list page
        Given I am on "/wp-admin/edit.php?post_type=product"
        When I follow "Trash" on the row containing "MYIT"
        And I click "Cancel" on the popup window 
        Then I should see "MYIT"   
    
    @javascript @not_implemented
    Scenario: Cancelling deleting a product from the show page
        Given I am on "/wp-admin/edit.php?post_type=product"
        When I follow "MYIT" on the row containing "MYIT"
        And I follow "Move to Trash"
        And I click "Cancel" on the popup window
        Then I should be on "/wp-admin/post.php?post=9&action=edit"
    
    @javascript @not_implemented
    Scenario: deleting a product from the show page
        Given I am on "/wp-admin/edit.php?post_type=product"
        When I follow "MYIT" on the row containing "MYIT"
        And I follow "Move to Trash"
        And I press "Delete"
        Then I should see "1 product moved to the Trash."
    
    @javascript @not_implemented
    Scenario: Restoring a deleted product in the deleted products list
        Given I am on "/wp-admin/edit.php?post_status=trash&post_type=product"
        When I follow "Restore" on the row containing "MYIT"
        Then I should see "1 product restored from the Trash."    
    
    @javascript @not_implemented
    Scenario Outline: deleting a product from the list page
        Given I am on "/wp-admin/edit.php?post_type=product"
        When I follow "Trash" on the row containing "<product>"
        And I click "Delete" on the popup window 
        Then I should see "1 product moved to the Trash."
        
        Examples:
        | product |
        | MYIT   |
        | EGYPTFOSS   |
        
    @javascript @not_implemented
    Scenario: Viewing a deleted product in the deleted products list
        Given I am on "/wp-admin/edit.php?post_type=product"
        When I follow "Memrise" on the row containing "Memrise"
        And I follow "Move to Trash"
        And I press "Delete"
        And I go to "/wp-admin/edit.php?post_status=trash&post_type=product"
        Then I should see "Memrise"

    @javascript @not_implemented
    Scenario: Cancelling permanently deleting a product in the deleted products list
        Given I am on "/wp-admin/edit.php?post_status=trash&post_type=product"
        When I follow "Delete Permanently" on the row containing "Memrise"
        And I click "Cancel" on the popup window
        Then I should see "Memrise"


    @javascript  @not_implemented
    Scenario Outline: Permanently deleting a product in the deleted products list
        Given I am on "/wp-admin/edit.php?post_status=trash&post_type=products"
        When I follow "Delete Permanently" on the row containing "<product>"
        And I click "Delete" on the popup window
        Then I should see "1 product permanently deleted."
        
        Examples:
        | product |
        | Memrise |
        | MYIT |
        | EGYPTFOSS |