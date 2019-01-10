Feature: User can verify his email
  In order to use the system
  As a user
  I need to activate my account by verifying my email through the activation email I receive

    @javascript @Done
    Scenario: A not verified user logging in to the system with valid credentials
        Given I am on "/en/login"
        And I login to the system with "mainagar" and "123456789"
        And I wait to be redirected
        Then I should be on "/en/login/"
        And I should see "Your account has not been activated."

    @not_working
    Scenario: A user verify his email after registration
        Given I should receive a registration email
        Then I should see "Your account is now active!"