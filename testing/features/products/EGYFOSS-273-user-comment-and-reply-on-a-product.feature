Feature: add comment/reply to a product
  In order to add comment/reply to a product
  As a logged-in user
  I need to be able to navigate to product page and add a comment/reply to this product

    @done
    Scenario: A not logged-in user view comments/replys on products but can't add a comment/reply
        Given I am on "/en/"
        When I go to "/en/product/cdex/"
        And I wait to be redirected
        Then I should see an ".form-control" element
        # shouldn't see comment textbox nor submit button
        And I should not see an ".form-control" element
        And I should not see an ".submit" element

    @javascript @done
    Scenario: A logged-in user adding a comment to a product
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/product/cdex/"
        When I comment on the product with "A very nice app, give it a try"
        Then I should see "espace"
        And I should see "A very nice app, give it a try"
        And product comments counter should add more one

    @javascript @done
    Scenario: A logged-in user adding an empty comment to a product
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/product/cdex/"
        When I comment on the product with ""
        And I wait for 2 seconds
        Then I should see "Comment can not be empty"

    @javascript @done
    Scenario: A logged-in user adding a reply to a comment on a product
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/product/cdex/"
        When I reply on "A very nice app, give it a try" with "More than perfect"
        Then I should see "espace"
        And I should see "More than perfect"
        And product comments counter should add more one

    @javascript @done
    Scenario: A logged-in user adding a reply to 1st reply on a product
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/product/cdex/"
        When I reply on reply "More than perfect" with "Can not agree more"
        Then I should see "espace"
        And I should see "Can not agree more"
        And product comments counter should add more one