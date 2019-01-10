Feature: User manage his notifications settings for profile updates in the system
  In order to manage notifications settings for profile updates in the system
  As a logged-in User
  I need to be able to navigate to settings page and adjust my settings

    @not_implemented
    Scenario: A not logged-in can't reach notifications settings page
        Given I am on "/members/espace/settings/notifications-settings/"
        Then I should be on "/en/login/?redirect_to=http%3A%2F%2Ffoss.espace.ws%2Fmembers%2Fespace%2Fsettings%2F&action=bpnoaccess"
        And I should see "You must log in to access the page you requested."

    @done
    Scenario: A logged-in user navigating to notification settings page
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/"
        When I follow "Settings" in certain place ".login-sub"
        And I follow "Notifications Settings"
        Then I should be on "/members/espace/settings/notifications-settings/"
        And I should see "Profile Updates Email Notifications"

    @done
    Scenario: A logged-in user seeing "Never" selected by default in profile updates notifications section
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/members/espace/settings/notifications-settings/"
        Then "#profile_updates_value" should contains "Never"

    @javascript @done
    Scenario: A logged-in user selecting "Daily" notification in profile updates settings
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/members/espace/settings/notifications-settings/"
        When I press "profile_updates"
        And I select "Daily" from "profile_updates-notification-list"
        And I press "Save"
        And I wait 3 seconds
        Then "#profile_updates_value" should contains "Daily"
        And I should receive notification email "+1 day" at "<maii.elnagar@espace.com.eg>" titled "Interesting Posts at EgyptFOSS"
        When I go to "/en/"
        And I go to "/members/espace/settings/notifications-settings/"
        Then "#profile_updates_value" should contains "Daily"

    @javascript @done
    Scenario: A logged-in user selecting "Weekly" notification in profile updates settings
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/members/espace/settings/notifications-settings/"
        When I press "profile_updates"
        And I select "Weekly" from "profile_updates-notification-list"
        And I press "Save"
        And I wait 3 seconds
        Then "#profile_updates_value" should contains "Weekly"
        And I should receive notification email "next friday" at "<maii.elnagar@espace.com.eg>" titled "Interesting Posts at EgyptFOSS"
        When I go to "/en/"
        And I go to "/members/espace/settings/notifications-settings/"
        Then "#profile_updates_value" should contains "Weekly"

    @javascript @done
    Scenario: A logged-in user selecting "Monthly" notification in profile updates settings
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/members/espace/settings/notifications-settings/"
        When I press "profile_updates"
        And I select "Monthly" from "profile_updates-notification-list"
        And I press "Save"
        And I wait 3 seconds
        Then "#profile_updates_value" should contains "Monthly"
        And I should receive notification email "first day of next month" at "<maii.elnagar@espace.com.eg>" titled "Interesting Posts at EgyptFOSS"
        When I go to "/en/"
        And I go to "/members/espace/settings/notifications-settings/"
        Then "#profile_updates_value" should contains "Monthly"

    @javascript @done
    Scenario: A logged-in user selecting "Never" notification in profile updates settings
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/members/espace/settings/notifications-settings/"
        When I press "profile_updates"
        And I select "Never" from "profile_updates-notification-list"
        And I press "Save"
        And I wait 3 seconds
        Then "#profile_updates_value" should contains "Never"
        And I should not receive notification email on "+1 day" at "<maii.elnagar@espace.com.eg>" titled "Interesting Posts at EgyptFOSS"
        When I go to "/en/"
        And I go to "/members/espace/settings/notifications-settings/"
        Then "#profile_updates_value" should contains "Never"
