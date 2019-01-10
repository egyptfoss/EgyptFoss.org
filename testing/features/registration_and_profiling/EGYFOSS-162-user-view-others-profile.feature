Feature: User view others profile
  In order to view others profile
  As a user
  I need to navigate to any user profile and view all details inserted by the other user

    @javascript @Done
    Scenario: A not logged-in user navigating to view other profile page
        Given I am on "/en/"
        When I go to "/members/foss/profile/"
        Then I should be on "/en/members/foss/profile/"
        And I should see "foss"

    @javascript @Done
    Scenario: A logged-in user navigating to view other profile page
        Given I am a logged in user with "mohamed.said" and "123456789"
        And I am on "/en/"
        When I go to "/members/foss/profile/"
        Then I should be on "/en/members/foss/profile/"
        And I should see "foss"

    @javascript @Done
    Scenario: A not logged-in user seeing other empty profile
        Given I am on "/members/espace/profile/"
        Then I should see "This info is not shared with you"
        And I should be on "/en/members/espace/profile/"

    @javascript @Done
    Scenario: A not logged-in user seeing other profile with all data inserted in registration
        Given I am on "members/foss/profile"
        And I should see "Development"
        And I should see "It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout."
        And I should see "assembly language"

    @javascript @done
    Scenario: A user doesn't see the contact info in other profile
        Given I am on "members/espace/profile"
        # add address for this user from edit profile
        Then I should not see "340 building 2, floor 1, aprt 5, Cairo, Egypt"
        # add phone for this user from edit profile
        And I should not see "01234567890"

    @javascript @not_implemented @must
    Scenario: A user seeing all other social profiles
        Given I am on "members/mohamed-said/profile"
        # add Facebook link from edit profile
        Then I should see "facebook" in the "social_media_css" element
        # add Twitter link from edit profile
        And I should see "twitter" in the "social_media_css" element
        # add Google+ link from edit profile
        And I should see "G+" in the "social_media_css" element
        # add Linkedin link from edit profile
        And I should see "linkedin" in the "social_media_css" element

    @javascript @not_implemented @must
    Scenario: A user seeing only two inserted of social profiles in other profile
        Given I am on "members/mohamed-said/profile"
        Then I should see "facebook" in the "social_media_css" element
        # remove Twitter link from edit profile
        And I should not see "twitter" in the "social_media_css" element
        # remove Google+ link from edit profile
        And I should not see "G+" in the "social_media_css" element
        # add Linkedin link from edit profile
        And I should see "linkedin" in the "social_media_css" element

    @javascript @done
    Scenario: A user seeing more than one interest in other profile
        Given I am on "members/espace/profile"
        # add interest from edit profile
        Then I should see "Java"
        # add interest from edit profile
        And I should see "Python"
        # add interest from edit profile
        And I should see "PHP"
        # add interest from edit profile
        And I should see "Internet Of Things"

    @javascript @done
    Scenario: A user seeing more than one ICT Technology in other profile
        Given I am on "members/mohamed-said/profile"
        # add ICT technology from edit profile
        Then I should see "Nanotechnology"
        # add ICT technology from edit profile
        And I should see "Nemotechnology"
        # add ICT technology from edit profile
        And I should see "Biometric smart cards"

    @javascript @done
    Scenario: An user seeing contact person info in entity profile
        Given I am on "members/espace2/profile"
        # add representative name from edit profile
        Then I should see "Maii ELnagar"
        # add address from edit profile
        And I should see "340 building 2, floor 1, aprt 5, Cairo, Egypt"
        # add phone from edit profile
        And I should see "01234567890"
        # add email from edit profile
        And I should see "aaa@aaa.aaa"

    @javascript @done
    Scenario: An user doesn't see empty fields in other profile
        Given I am on "members/foss/profile"
        # remove all ICT technologies from edit profile
        Then I should not see "ICT technology"
        # remove theme from edit profile
        # Then I should not see "Theme" 
        # remove all Interests from edit profile
        Then I should not see "Interests" 