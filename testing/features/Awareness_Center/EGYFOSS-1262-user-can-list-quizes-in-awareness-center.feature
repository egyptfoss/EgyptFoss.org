Feature: As a user I want to view all quizezs in awarness center
  In order to list all quizes in awareness center
  As an User
  I need to be able to navigate to quizes in awareness center list page and load more quizzes
@Done
Scenario: A not logged-in user navigating to awarness center list
        Given  I am on "/en/awareness-center/"
        Then I should see "Awareness Center"	
@Done @test
Scenario: A not logged-in user seeing an empty awareness center list
        Given I am on "/en/awareness-center/"
        Then I should see "There are no quizzes in this category"

@Done @javascript @add-quizzes
Scenario: A logged-in user should be able to take a quiz
Given I am a logged in user with "espace" and "123456789"
    And I am on "/en/awareness-center/twelve-quiz/"
    When I check the "question34" radio button with "Answer1" value
    And I check the "question35" radio button with "Answer2" value
    And I check the "question36" radio button with "Answer2" value
    And I click on the element with css selector ".qmn_btn"
    And I wait for 25 seconds
    And take screenshot
    Then I should see "Your Score: 33%"


@Done  @javascript
Scenario: A logged-in user see quizzes detail
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/awareness-center/"
        Then I should see an ".listed-quizzes" element
        And I should see an ".quiz-publish-date" element
        And I should see an ".success-rate" element
        And I should see an ".interest-tag" element

@Done @javascript
Scenario: A not logged-in user can view success rate of a taken quizz 
    Given I am on "/en/awareness-center/"
    Then I should see "Success Rate: 0%"

@Done @javascript
Scenario: A not logged in shouldn't be able to view quizz details 
    Given  I am on "/en/awareness-center/twelve-quiz/"
        Then I should see "Please log in"
        And I should see "to take this quiz and test your FOSS knowledge"
        And I should see "Log In"

@Done javascript 
Scenario: A not logged-in user should be redirected to login page when he clicks on "Log in" button in view quiz details
    Given  I am on "/en/awareness-center/twelve-quiz/"
    Then I should see "You're not logged in"
    When I follow "Log in"
    Then I should see "Please log in to take the quiz" 



@Done @javascript
Scenario: A logged-in user see his score  of taken quiz in listing page of awareness center
    Given I am a logged in user with "espace" and "123456789"
    And I am on "/en/awareness-center/"
    Then I should see an ".listed-quizzes" element
        And I should see an ".quiz-publish-date" element
        And I should see an ".success-rate" element
        And I should see an ".interest-tag" element
        And I should see "Your Highest Score"
        And I should see "33%"

@Done
Scenario: A logged-in user viewing awareness center quizzes according to the language of the interface
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/awareness-center/"
        Then I should not see "Arabic Quizzes"

@Done @javascript
Scenario: A not logged-in user viewing 10 quizes per page
    Given I am on "/en/awareness-center"
    Then I should see 10 ".survey-title" elements
    And I should see "Show more"

@Done
Scenario: A logged-in user viewing awareness center categories
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/awareness-center/"
        Then "categories-list" should be visible
        And I should see "FOSS"
        And I should see "Open Source"

@not-handeled-in-coding @javascript
Scenario: Subsciber shouldn't be able to open any quiz
	Given I am a logged in user with "subscriber_user" and "123456789"
	And I am on "/en/awareness-center/"
	#And I follow "Take Quiz"
	Then I should see an ".disabled" element




