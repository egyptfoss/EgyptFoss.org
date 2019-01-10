Feature: user can upload file
  As a user
  I need to be able to upload file to wiki

  Background:
    Given I am on "/"
    And there are following users:
            | username | email | plain_password | enabled |
            | bougy.tamtam | bougy.tamtam10@gmail.com | 123456789 | yes |

    @javascript
    Scenario: A non logged-in user should not see upload button
        Given I am on "/en/wiki/FOSSPedia"
        Then I should not see "Upload file"

    @javascript 
    Scenario: A non logged-in user should not access the upload page
        Given I am on "/en/wiki/Special:Upload"
        Then I should see "Please log in to upload files."

    @javascript
    Scenario: A logged-in user uploading image on media wiki
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/wiki/FOSSPedia"
        When I resize window with height 800 and width 1024 in px
        And I click on the element with css selector "a[title='Upload files [u]']"
        And I wait to be redirected
        And I attach the file "testImages/logo.png" to "wpUploadFile" with relative path
        And I press "wpUpload"
        And I wait to be redirected
        Then I should be on "en/wiki/File:Logo.png"
    
    @javascript
    Scenario: A logged-in user uploading already exist image on media wiki
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/wiki/FOSSPedia"
        When I resize window with height 800 and width 1024 in px
        And I click on the element with css selector "a[title='Upload files [u]']"
        And I wait to be redirected
        And I attach the file "testImages/logo.png" to "wpUploadFile" with relative path
        And I press "wpUpload"
        And I wait to be redirected
        Then I should see "A file with this name exists already, please check"    

    @javascript
    Scenario: A logged-in user uploading empty file on media wiki
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/wiki/FOSSPedia"
        When I resize window with height 800 and width 1024 in px
        And I click on the element with css selector "a[title='Upload files [u]']"
        And I wait to be redirected
        And I press "wpUpload"
        And I wait to be redirected
        Then I should see "The file you uploaded seems to be empty. This might be due to a typo in the filename. Please check whether you really want to upload this file."    