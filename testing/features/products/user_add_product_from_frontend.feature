Feature: User adds products to the system
  In order to add a product in the system
  As an User
  I need to provide all attributes needed to add a product in the system

  Background:
    Given I am on "/"
    And there are following users:
            | username | email | plain_password | enabled |
            | bougy.tamtam | bougy.tamtam10@gmail.com | 123456789 | yes |

    @javascript @done
    Scenario: A not logged-in user Adding new product to the system
        Given I am on "/add-product"
        And I wait to be redirected
        Then I should be on "/wp-login.php?redirected=addproduct"
        And I should see "Please log in to suggest a new product"

    @javascript @done
    Scenario: A logged-in user Adding new product to the system with valid inputs
        Given I am a logged in user with "esl4m.test" and "123456789"
        And I am on "/add-product"
        And I Add new frontend product with "Komikmss3" , "A very nice app that crawl all jokes from different facebook pages to list them in one simple interface" , "Mohamed Header" , "entertainment" , "software" , "very easy to use" , "eSpace" , "www.test-link.com" , "type1" , "tech1" , "platform1" , "license1" and "EGYFOSS"
        And I press "submit"
        Then I should see "Product Komikmss3 Added successfully, it is now under review"
       
    @javascript @done
    Scenario: A logged-in user Adding new product with only required inputs
        Given I am a logged in user with "esl4m.test" and "123456789"
        And I am on "/add-product"
        And I Add new frontend product with "TaskCards2" , "A simple and neat TO-Do list management application" , "" , "" , "" , "" , "" , "" , "" , "" , "" , "" and ""
        And I press "submit"
        Then I should see "Product TaskCards2 Added successfully, it is now under review"

    @javascript @done
    Scenario: A logged-in user Adding new product with already exist title
        Given I am a logged in user with "esl4m.test" and "123456789"
        And I am on "/add-product"
        And I Add new frontend product with "TaskCards" , "A simple and neat TO-Do list management application" , "" , "" , "" , "" , "" , "" , "" , "" , "" , "" and ""
        And I press "submit"
        Then I should see "Product TaskCards already exists."

    @javascript @not_implemented
    Scenario: A logged-in user Listing all products in the system
        Given I log in with "bougy.tamtam" and "123456789"
        And I am on "/products"
        Then I should be on "Products"
        And I should see "Komikms"
        And I should see "Task Cards"

    @javascript @done
    Scenario: A logged-in user Adding new product with null inputs
        Given I am a logged in user with "esl4m.test" and "123456789"
        And I am on "/add-product"
        And I Add new frontend product with "" , "" , "" , "" , "" , "" , "" , "" , "" , "" , "" , "" and ""
        And I press "submit"
        Then I should see "Product title required"

    @javascript @done
    Scenario: A logged-in user Adding new product with numbers only in the textfields
        Given I am a logged in user with "esl4m.test" and "123456789"
        And I am on "/add-product"
        And I Add new frontend product with "1234" , "324" , "124443" , "3241" , "324324" , "3214354" , "2315" , "" , "" , "" , "" and ""
        Then I should see "Product title Must includes one letter"

    @javascript @done
    Scenario: A logged-in user Adding new product with special characters only in the textfields
        Given I am a logged in user with "esl4m.test" and "123456789"
        And I am on "/add-product"
        And I Add new frontend product with ",,,," , ",,,," , ";;;;;" , ";;;;;" , ";;;;;" , ";;;;;" , ";;;;" , "" , "" , "" , "" , "" and ""
        Then I should see "Product title Must includes one letter"
