Feature: expert add thought to the system
  In order to add thought in the system
  As an User
  I need to provide all attributes needed to add a thought in the system

    @Done
    Scenario: A not expert user Adding new expert thought to the system, should go to 404 error page
        Given I am on "/en/expert-thoughts/add"
        And I wait to be redirected
        Then I should be on "en/login/?redirect_to=en/expert-thoughts/add"
        And I should see "Please log in to write a new thought"

    @javascript @not-working 
    Scenario: An expert user Adding new expert thought to the system with valid inputs
        Given I am a logged in user with "expert_user" and "123456789"
        And I am on "/en/expert-thoughts/add"
        And I fill in "expert_thought_title" with " test thought unique"
        And I fill in "expert_thought_description" with " test thought unique"
        And I add "interest1" to a auto-multi-select "interest"
        And I attach the file "testImages/logo.png" to "expert_thought_image" with relative path
        And I press "submit"
        Then I should see "added successfully"
       
    @Done
    Scenario: An expert user Adding new expert thought with only required inputs
        Given I am a logged in user with "expert_user" and "123456789"
        And I am on "/en/expert-thoughts/add"
        And I fill in "expert_thought_title" with "Another test thought unique"
        And I fill in "expert_thought_description" with "Another test thought unique"
        And I press "submit"
        Then I should see "added successfully"

    @javascript @Done
    Scenario: An expert user Adding new expert thought with null inputs
        Given I am a logged in user with "expert_user" and "123456789"
        And I am on "/en/expert-thoughts/add"
        And I fill in "expert_thought_title" with ""
        And I fill in "expert_thought_description" with ""
        And I press "submit"
        Then I should see "Title required"
        And I should see "Content required"

    @javascript @Done
    Scenario: An expert user Adding new expert thought with numbers only in the textfields
        Given I am a logged in user with "expert_user" and "123456789"
        And I am on "/en/expert-thoughts/add"
        And I fill in "expert_thought_title" with "123"
        And I fill in "expert_thought_description" with "123"
        And I press "submit"
        Then I should see "Title must include at least one letter"
        And I should see "Content must include at least one letter"

    @javascript @Done
    Scenario: An expert user Adding new expert thought with special characters only in the textfields
        Given I am a logged in user with "expert_user" and "123456789"
        And I am on "/en/expert-thoughts/add"
        And I fill in "expert_thought_title" with ";;;;"
        And I fill in "expert_thought_description" with ";;;;"
        And I press "submit"
        Then I should see "Title must include at least one letter"
        And I should see "Content must include at least one letter"

    @javascript @Done
    Scenario: An expert user Adding new expert thought with title exceeds the max length
        Given I am a logged in user with "expert_user" and "123456789"
        And I am on "/en/expert-thoughts/add"
        And I fill in "expert_thought_title" with "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec interdum imperdiet nibh eget mattis. Quisque sed vulputate quam. Vestibulum volutpat erat in aliquam vulputate"
        And I fill in "expert_thought_description" with "test max length in title"
        And I press "submit"
        Then I should see "Title should not be more than 100 characters"

    @javascript @Done
    Scenario: An expert user Adding new expert thought with title less than the min length
        Given I am a logged in user with "expert_user" and "123456789"
        And I am on "/en/expert-thoughts/add"
        And I fill in "expert_thought_title" with "title1"
        And I fill in "expert_thought_description" with "test min length in title"
        And I press "submit"
        Then I should see "Title should be at least 10 characters"
