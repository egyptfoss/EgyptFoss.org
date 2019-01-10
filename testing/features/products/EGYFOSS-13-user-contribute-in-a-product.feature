Feature: User edit a product in the system
  In order to edit a product in the system
  As an User
  I need to be able to edit all product attributes

    @not_implemented
    Scenario: A not logged-in user contributing in a product
        Given I am on "/en/product/cmder"
        When I follow "Edit Product"
        Then I should be on "/wp-login.php?redirected=editproduct"
        And I should see "Please log in to edit a product"

    @not_implemented
    Scenario: A logged-in user editing a product to the system with valid inputs
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/product/cmder"
        When I follow "Edit Product"
        And I edit product with "Komikmss3", "A very nice app that crawl all jokes from different facebook pages to list them in one simple interface", "Mohamed Header", "entertainment", "software", "very easy to use", "eSpace", "www.test-link.com", "type1", "tech1", "platform1", "license1" and "EGYFOSS"
        And I press "submit"
        Then I should see "Product updated successfully"
       
    @not_implemented
    Scenario: A logged-in user editing new product with only required inputs
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/product/cmder"
        When I follow "Edit Product"
        And I edit product with "TaskCards2", "A simple and neat TO-Do list management application", "", "", "", "", "", "", "", "", "", "" and ""
        And I press "submit"
        Then I should see "Product updated successfully"

    @not_implemented
    Scenario: A logged-in user editing new product with already exist title
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/product/cmder"
        When I follow "Edit Product"
        And I edit product with "TaskCards2", "A simple and neat TO-Do list management application", "", "", "", "", "", "", "", "", "", "" and ""
        And I press "submit"
        Then I should see "This product already exists"

    @not_implemented
    Scenario: A logged-in user editing new product with null inputs
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/product/cmder"
        When I follow "Edit Product"
        And I edit product with "", "", "", "", "", "", "", "", "", "", "", "" and ""
        And I press "submit"
        Then I should see "Product title required"

    @not_implemented
    Scenario: A logged-in user editing new product with numbers only in the textfields
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/product/cmder"
        When I follow "Edit Product"
        And I edit product with "214", "3214", "3214", "324", "1234", "3214", "1324", "321434", "", "", "", "" and ""
        Then I should see "Product title Must includes one letter"

    @not_implemented
    Scenario: A logged-in user editing new product with special characters only in the textfields
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/product/cmder"
        When I follow "Edit Product"
        And I edit product with ";;;", ";;;", ";;;;;", ";;;;;", ";;;;;", ";;;;;", ";;;;;", ";;;;;", "", "", "", "" and ""
        Then I should see "Product title Must includes one letter"
