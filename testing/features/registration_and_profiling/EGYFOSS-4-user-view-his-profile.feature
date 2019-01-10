Feature: Logged-in user view his profile
  In order to view my profile
  As a logged-in user
  I need to navigate to my profile and view all details inserted from registration and from edit profile

    @javascript @Done
    Scenario: A user navigating to view his profile page
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/"
        When I follow "foss"
        Then I should be on "/en/members/foss/profile/"

    @javascript @not_implemented
    Scenario: A user seeing his profile with all data inserted in registration
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "members/foss/profile"
        And I should see "Development"
        And I should see "It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout."
        And I should see "assembly language"

    @javascript @not_implemented
    Scenario: A user seeing his contact info
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "members/foss/profile"
        Then I should see "43 building1, floor3, aprt.11, Alexandria,Egypt"
        And I should see "03-32154698"

    @javascript @not_implemented @review
    Scenario: A user seeing only two inserted of social profiles from his profile
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "members/foss/profile"
        Then I should see "facebook.com"
        And I should see "-"
        And I should see "linkedin.com"
        And I should see "-"

    @javascript @not_implemented
    Scenario: A user seeing more than one interest in his profile
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "members/foss/profile"
        Then I should see "Java"
        And I should see "Python"
        And I should see "PHP"
        And I should see "Internet Of Things"

    @javascript @not_implemented
    Scenario: A user seeing more than one ICT Technology in his profile
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "members/foss/profile"
        Then I should see "Nanotechnology"
        And I should see "assembly language"

    @javascript @not_implemented
    Scenario: An entity seeing his contact person info to his profile
        Given I am a logged in user with "espace" and "123456789"
        And I am on "members/espace/profile"
        Then I should see "Maii ELnagar"
        And I should see "340 building 2, floor 1, aprt 5, Cairo, Egypt"
        And I should see "01234567890"
        And I should see "aaa@aaa.aaa"