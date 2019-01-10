Feature: As a user I can take a quiz in the awareness center so that I can measure my FOSS knowledge
  In order take a quiz in the awareness center
  As a user
  I need to be able to navigate/list/view/submit quizzes in the awareness center


   @Done @add-quizzes 
    Scenario: A not logged-in user shouldn't be able to see questions of any quiz
        Given I am on "/en/awareness-center"
        And I follow "Twelve Quiz"
        And I wait to be redirected
        Then I should be on "/en/awareness-center/twelve-quiz/"
        And I should see "Please log in"
        And I should see "to take this quiz and test your FOSS knowledge"
        And I should see "Log In"
        And I follow "Log In"
        Then I should see "Please log in to take the quiz"


 @Done 
 Scenario: A  logged-in user should be able to see  questions of any quiz
 	Given I am a logged in user with "espace" and "123456789"
 	And I am on "/en/awareness-center/twelve-quiz/"
 	Then I should see "Twelve Quiz"
 	And I should see "Welcome to your Twelve Quiz"
 	And I should see "first question"
 	And I should see "Answer1"
 	And I should see "Answer2"
 	And I should see "Answer3"
 	And I should see "Second question"
 	And I should see "Answer1"
 	And I should see "Answer2"
 	And I should see "Answer3"
 	And I should see "Third question"
 	And I should see "Answer1"
 	And I should see "Answer2"
 	And I should see "Answer3"
 	And I should see "Submit"

@testme @javascript @add-quizzes 
Scenario: A  logged-in user should be able to take a quiz
Given I am a logged in user with "espace" and "123456789"
 	And I am on "/en/awareness-center/twelve-quiz/"
 	When I check the "question34" radio button with "Answer1" value
 	And I check the "question35" radio button with "Answer2" value
 	And I check the "question36" radio button with "Answer2" value
 	And I click on the element with css selector ".qmn_btn"
 	And I wait for 15 seconds
 	Then I should see "Your Score: 33%"




