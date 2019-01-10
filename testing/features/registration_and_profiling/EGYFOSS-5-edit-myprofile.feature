Feature: Edit User Profile main info
  In order to change my profile main info
  As a user
  I need to be logged in and able to edit my profile main info

    @Done
    Scenario: A not logged-in user can't access edit profile page
        Given I am on "/members/foss/profile/edit/group/1"
        Then I should be on "/en/login/"
        And I should see "You must log in to access the page you requested."

    @Done
    Scenario: A logged-in user navigating to edit profile page
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/members/foss/profile"
        And I follow "Edit"
        Then I should be on "/en/members/foss/profile/edit/group/1/"

    @javascript @Done
    Scenario: A logged-in user completing only the info from registration step
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/members/foss/profile/edit/group/1"
        When I edit my main info with "Entrepreneur" and "It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout." and "Development" and "assembly language" 
        Then I should be on "/en/members/foss/profile/"
        And I should see "Changes saved."
        And the response should contain "Entrepreneur"
        And the response should contain "Development"
        And the response should contain "It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout."
        And the response should contain "assembly language"

    @javascript @Done
    Scenario: A logged-in user clearing the info from registration step
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/members/foss/profile/edit/group/1"
        When I should remove "assembly language" from "ict_technology"
        And I edit my main info with "User" and "" and "Development" and ""
        Then I should be on "/en/members/foss/profile/"
        And I should see "Changes saved."
        And the response should not contain "Entrepreneur"
        #And the response should not contain "Development"
        And the response should not contain "It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout."
        #And the response should not contain "assembly language"

    @Done
    Scenario: A logged-in user adding the contact info
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/members/foss/profile/edit/group/1"
        When I Add contact info with "43 building1, floor3, aprt.11, Alexandria,Egypt" and "03-32154698"
        Then I should be on "/en/members/foss/profile/"
        And I should see "Changes saved."
        And the response should contain "43 building1, floor3, aprt.11, Alexandria,Egypt"
        And the response should contain "03-32154698"

    @Done 
    Scenario: A logged-in user clearing the contact info
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/members/foss/profile/edit/group/1"
        When I Add contact info with "" and ""
        #And I press "Save"
        Then I should be on "/en/members/foss/profile/"
        And I should see "Changes saved."
        And the response should not contain "43 building1, floor3, aprt.11, Alexandria,Egypt"
        And the response should not contain "03-32154698"

    @Done
    Scenario: A logged-in user adding the socail profiles
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/members/foss/profile/edit/group/1"
        When I Add social profiles with "https://facebook.com/foss" and "https://twitter.com/foss" and "https://www.plus.google.com/foss" and "https://linkedin.com/foss"
        Then I should be on "/en/members/foss/profile/"
        And I should see "Changes saved."
        And the response should contain "https://facebook.com/foss"
        And the response should contain "https://twitter.com/foss"
        And the response should contain "https://www.plus.google.com/foss"
        And the response should contain "https://linkedin.com/foss"

    @Done
    Scenario: A logged-in user removing two of the socail profiles from his profile
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/members/foss/profile/edit/group/1"
        When I Add social profiles with "https://facebook.com/foss" and "" and "" and "https://linkedin.com/foss"
        Then I should be on "/en/members/foss/profile/"
        And I should see "Changes saved."
        And the response should contain "https://facebook.com/foss"
        And the response should not contain "twitter.com/foss"
        And the response should not contain "google.plus"
        And the response should contain "https://linkedin.com/foss"

    @javascript @Done
    Scenario: A logged-in user Adding invalid social profiles from his profile
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/members/foss/profile/edit/group/1"
        #When I Add social profiles with "dfdsf" and "12" and ",,,," and "trt"
        When I Add social profiles with "dfdsf" and "12" and "sdas" and "trt"
        Then I should see "Please enter a valid URL"
        And I close the browser

    @javascript @Done 
    Scenario: A logged-in user selecting more than one interest to his profile
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/members/foss/profile/edit/group/1"
        When I add "Java" to a auto-select "interest"
        And I add "PHP" to a auto-select "interest"
        And I add "Python" to a auto-select "interest"
        And I press "Save"
        Then I should be on "/en/members/foss/profile/"
        And I should see "Changes saved."
        And the response should contain "Java"
        And the response should contain "PHP"
        And the response should contain "Python"

    @javascript @Done 
    Scenario: A logged-in user adding a not exist interest to his profile
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/members/foss/profile/edit/group/1"
        When I add "Internet Of Things" to a auto-select "interest"
        And I press "Save"
        Then I should be on "/en/members/foss/profile/"
        And I should see "Changes saved."
        And the response should contain "Internet Of Things"

    @javascript @Done
    Scenario: A logged-in user adding a not exist ICT Technology to his profile
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/members/foss/profile/edit/group/1"
        When I add "Nanotechnology" to a auto-select "ict_technology"
        And I press "Save"
        Then I should be on "/en/members/foss/profile/"
        And I should see "Changes saved."
        And I should see "Nanotechnology"

    @Done
    Scenario: A logged-in entity adding contact person info to his profile
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/members/espace/profile/edit/group/1"
        When I Add contact person info with "Maii ELnagar" and "aaa@aaa.aaa" and "340 building 2, floor 1, aprt 5, Cairo, Egypt" and "01234567890"
        Then I should be on "/en/members/espace/profile/"
        And the response should contain "Maii ELnagar"
        And the response should contain "340 building 2, floor 1, aprt 5, Cairo, Egypt"
        And the response should contain "01234567890"
        And the response should contain "aaa@aaa.aaa"

    @javascript @Done
    Scenario: A logged-in user completing only the info from registration step
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/members/foss/profile/edit/group/1"
        When I edit my main info with "Entrepreneur" and "It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout." and "Development" and "assembly language" 
        Then I should be on "/en/members/foss/profile/"
        And I should see "Changes saved."
        And the response should contain "Entrepreneur"
        And the response should contain "Development"
        And the response should contain "It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout."
        And the response should contain "assembly language"