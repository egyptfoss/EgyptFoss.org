Feature: User adds products to the system
  In order to add a product in the system
  As an User
  I need to provide all attributes needed to add a product in the system

    @javascript @Done
    Scenario: A not logged-in user Adding new product to the system
        Given I am on "/add-product"
        And I wait to be redirected
        Then I should be on "/en/login/?redirected=addproduct"
        And I should see "Please log in to suggest a new product"

    @javascript @not_implemented
    Scenario: A logged-in user Adding new product to the system with valid inputs
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/products/add/"
        And I Add new frontend product with "Komikmss3" , "A very nice app that crawl all jokes from different facebook pages to list them in one simple interface" , "Mohamed Header" , "entertainment" , "Development" , "very easy to use" , "eSpace" , "www.test-link.com" , "application" , "tech1" , "platform1" , "license1" and "EGYFOSS"
        And I press "submit"
        Then I should see "Product Komikmss3 Added successfully, it is now under review"
       
    @javascript @not_working
    Scenario: A logged-in user Adding new product with only required inputs
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/add-product"
        And I Add new frontend product with "TaskCards2" , "A simple and neat TO-Do list management application" , "" , "" , "Development" , "" , "" , "" , "" , "" , "" , "" and ""
        And I press "submit"
        Then I should see "Product TaskCards2 Added successfully, it is now under review"

    @javascript @not_working
    Scenario: A logged-in user Adding new product with already exist title
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/add-product"
        And I Add new frontend product with "TaskCards2" , "A simple and neat TO-Do list management application" , "" , "" , "Development" , "" , "" , "" , "" , "" , "" , "" and ""
        And I press "submit"
        And I wait to be redirected
        Then I should see " Product TaskCards2 already exists"

    @javascript @not_implemented
    Scenario: A logged-in user Listing all products in the system
        Given I log in with "bougy.tamtam" and "123456789"
        And I am on "/products"
        Then I should be on "Products"
        And I should see "Komikms"
        And I should see "Task Cards"

    @javascript @not_working
    Scenario: A logged-in user Adding new product with null inputs
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/add-product"
        And I Add new frontend product with "" , "" , "" , "" , "" , "" , "" , "" , "" , "" , "" , "" and ""
        And I press "submit"
        Then I should see "Product title required"

    @javascript @not_working
    Scenario: A logged-in user Adding new product with numbers only in the textfields
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/add-product"
        And I Add new frontend product with "21321" , "321321321" , "" , "" , "Development" , "" , "" , "" , "" , "" , "" , "" and ""
        Then I should see "Product title must at least contain one letter"

    @javascript @not_working
    Scenario: A logged-in user Adding new product with special characters only in the textfields
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/add-product"
        And I Add new frontend product with ",,,," , ",,,," , ";;;;;" , ";;;;;" , "Development" , ";;;;;" , ";;;;" , "" , "" , "" , "" , "" and ""
        Then I should see "Product title must at least contain one letter"
