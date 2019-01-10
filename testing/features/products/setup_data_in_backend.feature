Feature: Manage types, technologies, platforms, licenses and keywords in the system backend by adding, editing and deleting them
  In order to manage types, technologies, platforms, licenses and keywords in the system
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
        And I am on "/wp-admin"

    @javascript @not_implemented
    Scenario Outline: Adding new types, technologies, platforms, licenses and keywords as set-up data for products
        Given I am on "/wp-admin/edit-tags.php?taxonomy="."<item>"."&post_type=product"
        When I fill in "tag-name" with "<name>"
        And I press "submit"
        Then I should see "<name>"  
        
        Examples:
        | item | name |
        |  type | type1 |
        | technology | tech1 |
        |  platform | platform1 |
        | license | license1 |
        | keywords | keyword1 |

    @javascript @not_implemented
    Scenario Outline: Adding new types, technologies, platforms, licenses and keywords with name already exists
        Given I am on "/wp-admin/edit-tags.php?taxonomy="."<item>"."&post_type=product"
        When I fill in "tag-name" with "<name>"
        And I press "submit"
        Then I should see "Already exists"  
        
        Examples:
        | item | name |
        |  type | type1 |
        | technology | tech1 |
        | platform | platform1 |
        | license | license1 |
        | keywords | keyword1 |
       
    @javascript @not_implemented
    Scenario Outline: Listing all types, technologies, platforms, licenses and keywords in the system
        Given I am on "/wp-admin/"
        When I follow "Products"
        And I follow "<Item_Link>"
        And I wait to be redirected
        Then I should be on "wp-admin/edit-tags.php?taxonomy="."<item>"."&post_type=product"
        And I should see "<name>"

        Examples:
        | Item_Link | item | name |
        | Type | type | type1 |
        | Technology | technology | tech1 |
        | Platform | platform| platform1 |
        | License |license | license1 |
        | Keywords | keywords | keyword1 |

    @javascript @not_implemented
    Scenario Outline: Adding new types, technologies, platforms, licenses and keywords with null inputs
        Given I am on "/wp-admin/edit-tags.php?taxonomy="."<item>"."&post_type=product"
        When I fill in "tag-name" with ""
        And I press "submit"
        Then I should see "Required"

        Examples:
        | item |
        |  type |
        | technology |
        | platform |
        | license |
        | keywords |

    @javascript @not_implemented
    Scenario Outline: Adding new types, technologies, platforms, licenses and keywords with numbers only
        Given I am on "/wp-admin/edit-tags.php?taxonomy="."<item>"."&post_type=product"
        When I fill in "tag-name" with "23145312"
        And I press "submit"
        Then I should see "must contain at least one letter"

        Examples:
        | item |
        |  type |
        | technology |
        | platform |
        | license |
        | keywords |

    @javascript @not_implemented
    Scenario Outline: Adding new types, technologies, platforms, licenses and keywords with special characters only
        Given I am on "/wp-admin/edit-tags.php?taxonomy="."<item>"."&post_type=product"
        When I fill in "tag-name" with ",,,,,"
        And I press "submit"
        Then I should see "must contain at least one letter"

        Examples:
        | item |
        |  type |
        | technology |
        | platform |
        | license |
        | keywords |

    @javascript @not_implemented
    Scenario Outline: editing types, technologies, platforms, licenses and keywords in the system
        Given I am on "/wp-admin/edit-tags.php?taxonomy="."<item>"."&post_type=product"
        And I follow "Edit" on the row containing "<name>"
        And I wait to be redirected
        When I edit the type name with "<edited_name>"
        Then I should see "<item> updated."

        Examples:
        | item | name | edited_name |
        |  type | type1 | type2 |
        | technology | tech1 | tech2 |
        | platform | platform1 | platform2 |
        | license | license1 | license2 |
        | keywords | keyword1 | keyword2 |

    @javascript 
    Scenario Outline: Cancelling deleting types, technologies, platforms, licenses and keywords from the list page
        Given I am on "/wp-admin/edit-tags.php?taxonomy="."<item>"."&post_type=product"
        When I follow "Delete" on the row containing "<name>"
        And I switch to the new window
        And I click "Cancel" on the popup window 
        Then I should see "<name>"

        Examples:
        | item | name |
        |  type | type2 |
        | technology | tech2 |
        | platform | platform2 |
        | license | license2 |
        | keywords | keyword2 |  
    
    @javascript   
    Scenario Outline: deleting types, technologies, platforms, licenses and keywords from the list page
        Given I am on "/wp-admin/edit-tags.php?taxonomy="."<item>"."&post_type=product"
        When I follow "Delete" on the row containing "<name>"
        And I switch to the new window
        And I click "Ok" on the popup window 
        Then I should not see "<name>"
        
        Examples:
        | item | name |
        |  type | type2 |
        | technology | tech2 |
        | platform | platform2 |
        | license | license2 |
        | keywords | keyword2 |
        
        
