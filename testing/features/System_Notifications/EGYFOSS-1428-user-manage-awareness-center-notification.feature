Feature: User manage his notifications settings for awareness center in the system
  In order to manage notifications settings for awareness center in the system
  As a logged-in User
  I need to be able to navigate to settings page and adjust my settings

    @javascript @Done @add-quizzes
    Scenario: A logged-in user adding a not exist interest to his profile
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/about/edit/group/1/"
        When I add "interest1" to a auto-multi-select "interest"
        And I select "academia" from "sub_type"
        And I press "Save"

    @Done
    Scenario: A not logged-in can't reach notifications settings page
        Given I am on "/en/members/espace/settings/notifications-settings/"
        Then I should be on "/en/login/?redirect_to=http%3A%2F%2Fegyptfoss.com%2Fen%2F%2Fmembers%2Fespace%2Fsettings%2Fnotifications-settings%2F&action=bpnoaccess"
        And I should see "You must log in to access the page you requested."

    @Done
    Scenario: A logged-in user navigating to notification settings page
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/"
        When I follow "Settings" in certain place ".login-sub"
        And I follow "Notifications"
        Then I should be on "/en/members/espace/settings/notifications-settings/"
        And I should see "Quizzes Email Notifications"

    @Done
    Scenario: A logged-in user seeing "Never" selected by default in awareness center notifications section
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/settings/notifications-settings/"
        Then "#awarness_center_updates_value" should contains "Never"

    @javascript @Done
    Scenario: A logged-in user selecting "Daily" notification in awareness center settings
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/settings/notifications-settings/"
        When I press "awarness_center_updates"
        #And I fill in "#awarness_center_updates-notification-list" with "Daily"
        And I select "Daily" from "awarness_center_updates-notification-list"
        And I press "save_awarness_center_updates"
        And I wait 10 seconds
        Then "#awarness_center_updates_value" should contains "Daily"
        And I should receive notification email "+1 day" at "<maii.elnagar@espace.com.eg>" titled "Daily Quizzes Updates"
        When I go to "/en/"
        And I go to "/members/espace/settings/notifications-settings/"
        Then "#awarness_center_updates_value" should contains "Daily"

    @javascript @Done 
    Scenario: A logged-in user selecting "Weekly" notification in products settings
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/settings/notifications-settings/"
        When I press "awarness_center_updates"
        And I select "Weekly" from "awarness_center_updates-notification-list"
        And I press "save_awarness_center_updates"
        And I wait 10 seconds
        Then "#awarness_center_updates_value" should contains "Weekly"
        And I should receive notification email "next friday" at "<maii.elnagar@espace.com.eg>" titled "Weekly Quizzes Updates"
        When I go to "/en/"
        And I go to "/members/espace/settings/notifications-settings/"
        Then "#awarness_center_updates_value" should contains "Weekly"


    @javascript @Done
    Scenario: A logged-in user selecting "Monthly" notification in products settings
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/settings/notifications-settings/"
        When I press "awarness_center_updates"
        And I select "Monthly" from "awarness_center_updates-notification-list"
        And I press "save_awarness_center_updates"
        And I wait 10 seconds
        Then "#awarness_center_updates_value" should contains "Monthly"
        And I should receive notification email "first day of next month" at "<maii.elnagar@espace.com.eg>" titled "Monthly Quizzes Updates"
        When I go to "/en/"
        And I go to "/members/espace/settings/notifications-settings/"
        Then "#awarness_center_updates_value" should contains "Monthly"

    @javascript @Done
    Scenario: A logged-in user selecting "Never" notification in products settings
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/settings/notifications-settings/"
        When I press "awarness_center_updates"
        And I select "Never" from "awarness_center_updates-notification-list"
        And I press "save_awarness_center_updates"
        And I wait 10 seconds
        Then "#awarness_center_updates_value" should contains "Never"
        And I should not receive notification email on "+1 day" at "<maii.elnagar@espace.com.eg>" titled "Quizzes Updates at EgyptFOSS"
        When I go to "/en/"
        And I go to "/members/espace/settings/notifications-settings/"
        Then "#awarness_center_updates_value" should contains "Never"
