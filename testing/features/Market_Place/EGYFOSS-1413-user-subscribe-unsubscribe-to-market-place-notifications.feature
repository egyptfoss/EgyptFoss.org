Feature:  User subscribe/unsubscribe to market place services notifications in the system
  In order to subscribe/unsubscribe to market place services notifications in the system
  As a logged-in User
  I need to be able to navigate to settings page and adjust my settings

    @javascript @Done
    Scenario: A user Adding new service to the system with valid inputs
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/marketplace/services/add"
        When I User Add new service with "new service title" and "mobile" and "prince" and "new service description" and "new service constraints" and "new service conditions"
        And I attach the file "testImages/logo.png" to "service_image" with relative path
        And I add "new techno" to a auto-select "technology"
        And I add "new interest" to a auto-select "interest"
        And I press "submit"
        Then I should see "added successfully"

    @javascript @Done
    Scenario: Admin Accepts new service to the system with valid inputs
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/wp-admin/edit.php?post_type=service"
        Then I follow "new service title"
        #And I select "new interest" from "acf-field-interest"
        And I press "publish"
        And I am on "/wp-admin/edit.php?post_type=service"

    @javascript @Done
    Scenario: A logged-in user adding a not exist interest to his profile
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/about/edit/group/1/"
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
        And I should see "New Services Email Notifications"

    @Done
    Scenario: A logged-in user seeing "Never" selected by default in market place place updates notifications section
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/members/espace/settings/notifications-settings/"
        Then "#market_place_updates_value" should contains "Never"

    @javascript @not_working
    Scenario: A logged-in user selecting "Daily" notification in market place settings
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/settings/notifications-settings/"
        When I press "market_place_updates"
        And I select "Daily" from "market_place_updates-notification-list"
        And I press "save_market_place_updates"
        And I wait 3 seconds
        Then "#market_place_updates_value" should contains "Daily"
        And I should receive notification email "+1 day" at "<espace_1@espace.com.eg>" titled "Daily Services Updates"
        When I go to "/en/"
        And I go to "/en/members/espace/settings/notifications-settings/"
        Then "#market_place_updates_value" should contains "Daily"

    @javascript @not_working
    Scenario: A logged-in user selecting "Weekly" notification in market place settings
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/settings/notifications-settings/"
        When I press "market_place_updates"
        And I select "Weekly" from "market_place_updates-notification-list"
        And I press "save_market_place_updates"
        And I wait 3 seconds
        Then "#market_place_updates_value" should contains "Weekly"
        And I should receive notification email "next friday" at "<espace_1@espace.com.eg>" titled "Weekly Services Updates"
        When I go to "/en/"
        And I go to "/en/members/espace/settings/notifications-settings/"
        Then "#market_place_updates_value" should contains "Weekly"

    @javascript @not_working
    Scenario: A logged-in user selecting "Monthly" notification in market place settings
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/settings/notifications-settings/"
        When I press "market_place_updates"
        And I select "Monthly" from "market_place_updates-notification-list"
        And I press "save_market_place_updates"
        And I wait 3 seconds
        Then "#market_place_updates_value" should contains "Monthly"
        And I should receive notification email "first day of next month" at "<espace_1@espace.com.eg>" titled "Monthly Services Updates"
        When I go to "/en/"
        And I go to "/en/members/espace/settings/notifications-settings/"
        Then "#market_place_updates_value" should contains "Monthly"

    @javascript @Done
    Scenario: A logged-in user selecting "Never" notification in market place settings
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/settings/notifications-settings/"
        When I press "market_place_updates"
        And I select "Never" from "market_place_updates-notification-list"
        And I press "save_market_place_updates"
        And I wait 3 seconds
        Then "#market_place_updates_value" should contains "Never"
        And I should not receive notification email on "+1 day" at "<espace_1@espace.com.eg>" titled "Services Updates"
        When I go to "/en/"
        And I go to "/en/members/espace/settings/notifications-settings/"
        Then "#market_place_updates_value" should contains "Never"
