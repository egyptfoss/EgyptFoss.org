Feature: User manage his notifications settings for requests in the system
  In order to manage notifications settings for requests in the system
  As a logged-in User
  I need to be able to navigate to settings page and adjust my settings

    @javascript @Done
    Scenario: A logged-in user Adding new request center to the system with valid inputs
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/request-center/add"
        When I User Add new request with "new request center title1" and "business-relationship-request" and "commercial-agreement" and "theme1" and "new request center description" and "new request center requirements" and "new request center constraints" and "new techno" and "new interest" and "2016-05-05" 
        And I press "submit"   
        Then I should see "added successfully"

    @javascript @Done
    Scenario: Admin Accepts new request center to the system with valid inputs
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/wp-admin/edit.php?post_type=request_center"
        Then I follow "new request center title1"
        And I select "new interest" from "acf-field-interest"
        And I press "publish"
        And I am on "/wp-admin/edit.php?post_type=request_center"

    @javascript @Done
    Scenario: A logged-in user adding a not exist interest to his profile
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/members/espace/profile/edit/group/1"
        When I add "new interest" to a auto-select "interest"
        And I press "Save"

    @Done
    Scenario: A not logged-in can't reach notifications settings page
        Given I am on "/members/espace/settings/notifications-settings/"
        Then I should be on "/en/login/?redirect_to=http%3A%2F%2Ffoss.espace.ws%2Fmembers%2Fespace%2Fsettings%2F&action=bpnoaccess"
        And I should see "You must log in to access the page you requested."

    @Done
    Scenario: A logged-in user navigating to notification settings page
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/"
        When I follow "Settings" in certain place ".login-sub"
        And I follow "Notifications"
        Then I should be on "/en/members/espace/settings/notifications-settings/"
        And I should see "New Requests Email Notifications"

    @Done
    Scenario: A logged-in user seeing "Never" selected by default in requests updates notifications section
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/members/espace/settings/notifications-settings/"
        Then "#request_center_updates_value" should contains "Never"

    @javascript @Done
    Scenario: A logged-in user selecting "Daily" notification in requests settings
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/settings/notifications-settings/"
        When I press "request_center_updates"
        And I select "Daily" from "request_center_updates-notification-list"
        And I press "save_request_center_updates"
        And I wait 3 seconds
        Then "#request_center_updates_value" should contains "Daily"
        And I should receive notification email "+1 day" at "<maii.elnagar@espace.com.eg>" titled "Interesting Requests at EgyptFOSS"
        When I go to "/en/"
        And I go to "/en/members/espace/settings/notifications-settings/"
        Then "#request_center_updates_value" should contains "Daily"

    @javascript @Done
    Scenario: A logged-in user selecting "Weekly" notification in events settings
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/settings/notifications-settings/"
        When I press "request_center_updates"
        And I select "Weekly" from "request_center_updates-notification-list"
        And I press "save_request_center_updates"
        And I wait 3 seconds
        Then "#request_center_updates_value" should contains "Weekly"
        And I should receive notification email "next friday" at "<maii.elnagar@espace.com.eg>" titled "Interesting Requests at EgyptFOSS"
        When I go to "/en/"
        And I go to "/en/members/espace/settings/notifications-settings/"
        Then "#request_center_updates_value" should contains "Weekly"

    @javascript @Done
    Scenario: A logged-in user selecting "Monthly" notification in events settings
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/settings/notifications-settings/"
        When I press "request_center_updates"
        And I select "Monthly" from "request_center_updates-notification-list"
        And I press "save_request_center_updates"
        And I wait 3 seconds
        Then "#request_center_updates_value" should contains "Monthly"
        And I should receive notification email "first day of next month" at "<maii.elnagar@espace.com.eg>" titled "Interesting Requests at EgyptFOSS"
        When I go to "/en/"
        And I go to "/en/members/espace/settings/notifications-settings/"
        Then "#request_center_updates_value" should contains "Monthly"

    @javascript @Done
    Scenario: A logged-in user selecting "Never" notification in events settings
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/settings/notifications-settings/"
        When I press "request_center_updates"
        And I select "Never" from "request_center_updates-notification-list"
        And I press "save_request_center_updates"
        And I wait 3 seconds
        Then "#request_center_updates_value" should contains "Never"
        And I should not receive notification email on "+1 day" at "<maii.elnagar@espace.com.eg>" titled "Interesting Requests at EgyptFOSS"
        When I go to "/en/"
        And I go to "/en/members/espace/settings/notifications-settings/"
        Then "#request_center_updates_value" should contains "Never"
