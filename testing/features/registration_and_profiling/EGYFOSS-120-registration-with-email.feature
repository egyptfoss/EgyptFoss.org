Feature: Register to the system using my email through the registration form
  In order to use the system
  As a user
  I need to register to the system with my basic data
    
    @Done
    Scenario: Navigating to registration page from Homepage
        Given I am on "/en/"
        And I wait to be redirected
        When I follow "Register" in certain place ".login-sub"
        And I wait to be redirected
        Then I should be on "/en/register/"
        And I should see "Create an Account"
 
    @Done
    Scenario: Registering into the system with all valid inputs
        Given I am on "/en/register/"
        When I fill required data with "mainagar" , "maii.elnagar+55588@espace.com.eg" , "123456789" , "123456789" and "Individual"
        And I check "terms"
        And I press "signup_submit"
        And I wait to be redirected
        Then I should see "You have successfully created your account! To begin using this site you will need to activate your account via the email we have just sent to your address."

    @Done
    Scenario: Registering into the system with an already exists username
        Given I am on "/en/register/"
        When I fill required data with "mainagar" , "maii.elnagar+55588@espace.com.eg" , "123456789" , "123456789" and "Individual"
        And I check "terms"
        And I press "signup_submit"
        Then I should see "Sorry, that username already exists!"

    @javascript @Done 
    Scenario: Registering into the system with username exceeds the max length
        Given I am on "/en/register/"
        When I fill required data with "ajmskbkatqmshrmfks.hg" , "maii.elnagar@espace.com.eg" , "123456789" , "123456789" and "Individual"
        And I check "terms"
        And I press "signup_submit"
        Then I should see "Enter no more than 20 characters"

    @javascript @AfterStep @Done
    Scenario: Registering into the system with username less than the min length
        Given I am on "/en/register/"
        When I fill required data with "hg" , "maii.elnagar@espace.com.eg" , "123456789" , "123456789" and "Individual"
        And I check "terms"
        And I press "signup_submit"
        Then I should see "Enter at least 4 characters"

    @javascript @Done 
    Scenario: Registering into the system with password less than the min length
        Given I am on "/register"
        When I fill required data with "maii.elnagar" , "maii.elnagar@espace.com.eg" , "123" , "123" and "Individual"
        And I check "terms"
        And I press "signup_submit"
        Then I should see "Password must be at least 8 characters"

    @Done 
    Scenario: Registering into the system with already exist email
        Given I am on "/register"
        When I fill required data with "maii.amer" , "maii.elnagar+55588@espace.com.eg" , "123456789" , "123456789" and "Individual"
        And I check "terms"
        And I press "signup_submit"
        Then I should see "Sorry, that email address is already used!"

    @javascript @Done 
    Scenario: Registering into the system with Null
        Given I am on "/register"
        When I fill required data with "" , "" , "" , "" and "Individual"
        And I uncheck "terms"
        And I press "signup_submit"
        Then I should see "Username can includes letters, numbers and (.) (-) (_)"
        And I should see "Please enter a valid email address"
        And I should see "Password must be at least 8 characters"
        And I should see "Please Confirm your password"
        #And I should see "Please select your Account Type"
        And I should see "You have to agree the EgyptFOSS Terms of services"

    @javascript @Done 
    Scenario: Registering into the system with invalid email
        Given I am on "/en/register/"
        When I fill required data with "maii.amer" , "test@test" , "123456789" , "123456789" and "Individual"
        And I check "terms"
        And I press "signup_submit"
        Then I should see "Please enter a valid email address"

    @javascript @Done
    Scenario: Registering into the system with invalid username
        Given I am on "/en/register/"
        When I fill required data with "@@" , "maii.elnagar@espace.com.eg" , "123456789" , "123456789" and "Individual"
        And I check "terms"
        And I press "signup_submit"
        Then the response should contain "Username only includes letters, numbers and (.) (-) (_)"

    @javascript @Done
    Scenario: Registering into the system without confirming the password
        Given I am on "/en/register/"
        When I fill required data with "maii.amer" , "test@test.com" , "123456789" , "" and "Individual"
        And I check "terms"
        And I press "signup_submit"
        Then I should see "Please Confirm your password"

    @Done
    Scenario: Registering into the system without checking the policy and use terms
        Given I am on "/en/register/"
        When I fill required data with "maii.amer" , "test@test.com" , "123456789" , "123456789" and "Individual"
        And I uncheck "terms"
        And I press "signup_submit"
        Then I should see "You have to agree the EgyptFOSS Terms of services"