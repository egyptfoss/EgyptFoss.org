Feature: As a user I want to view all quizezs I have answered in my profile 
  In order to list all quizzes I answered in awareness center in my profile 
  As an User
  I need to be able to navigate to answered quizzes in my contributions  and load more quizzes

@Done @javascript @add-quizzes @testme
Scenario: A  logged-in user should be able to take a quiz
Given I am a logged in user with "espace" and "123456789"
    And I am on "/en/awareness-center/twelve-quiz/"
    When I check the "question34" radio button with "Answer1" value
    And I check the "question35" radio button with "Answer2" value
    And I check the "question36" radio button with "Answer2" value
    And I click on the element with css selector ".qmn_btn"
    And I wait for 15 seconds
    Then I should see "Your Score: 33%"

@Done @testme
Scenario: A not logged-in user shouldn't be ble to navigate  to quizzes in any uer's contributions
        Given  I am on "/en/members/foss/quizzes/"
        Then I should see "You don't have permission to access this page or you have signed out."	

@Done  @testme
Scenario: Any logged in user shouldn't be able to see answered quzzes in other user's profile
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "en/members/espace/quizzes/"
        Then I should see "You don't have permission to access this page or you have signed out."

@Done  @testme
Scenario: Any logged in user shouldn't be able to see Qizzes in the side menu of any other user's profile
    Given I am a logged in user with "foss" and "F0$$"
    And I am on "en/members/espace/"
    Then I should not see "Quizzes"

 @javascript @add-quizzes  @testme
Scenario: A  logged-in user should be able to take another  quiz
    Given I am a logged in user with "espace" and "123456789"
    And I am on "/en/awareness-center/eleven-quiz/"
    When I check the "question31" radio button with "Answer1" value
    And I check the "question32" radio button with "Answer2" value
    And I check the "question33" radio button with "Answer2" value
    And I click on the element with css selector ".qmn_btn"
    And I wait for 15 seconds
    Then I should see "Your Score: 33%"

@javascript
Scenario: A logged in user can take the same quiz more than once
    Given I am a logged in user with "espace" and "123456789"
    And I am on "/en/awareness-center/twelve-quiz/"
    When I check the "question34" radio button with "Answer1" value
    And I check the "question35" radio button with "Answer1" value
    And I check the "question36" radio button with "Answer1" value
    And I click on the element with css selector ".qmn_btn"
    And I wait for 15 seconds
    Then I should see "Your Score: 100%"

###

@javascript  @testme
Scenario: A logged-in user should be able to see  quiz details that he answered in his profile
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/"
        When I click on "Quizzes"
        Then I should see "Quizzes"
        Then I should see an ".listed-quizzes" element
        And I should see an ".trials-label" element
        And I should see an ".success-rate" element
#####

@javascript  @testme
Scenario: A logged in user should be redirected to questions of quiz when he follows it's link in his profile
    Given I am a logged in user with "espace" and "123456789"
    And I am on "en/members/espace/"
    When I follow "Twelve Quiz"
    Then I should be on "en/awareness-center/twelve-quiz/"

@javascript  @testme
Scenario: A logged in user should be redirected to questions of quiz when he clicks on "Try again" button
    Given I am a logged in user with "espace" and "123456789"
    And I am on "en/members/espace/"
    When I follow "Try again"
    Then I should be on "en/awareness-center/twelve-quiz/"

@Done @javascript
Scenario: A logged-in user viewing 10  answered quizzes per page in his profile
    Given I am on "/en/awareness-center"
    Then I should see 10 ".survey-title" elements
    And I should see "Show more"




