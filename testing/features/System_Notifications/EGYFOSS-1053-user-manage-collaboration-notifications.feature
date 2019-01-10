Feature: User manage his notifications settings for spaces/documents in the system
  In order to manage notifications settings for spaces/documents in the system
  As a logged-in User
  I need to be able to navigate to settings page and adjust my settings

    @javascript @Done
    Scenario: Logged in user can create a space
        Given I am a logged in user with "foss" and "F0$$"
        When I resize window with height 800 and width 2048 in px
        And I am on "en/collaboration-center/spaces/"
        And I click on the element with css selector "a#new_space"
        And I fill in "new_space_title" with "inserted space test"
        And I click on the element with css selector "button[data-action='addNewCollaborativeSpace']"
        Then I should be on "en/collaboration-center/spaces/"
        And I should see "inserted space test"

    @Done @javascript    
    Scenario: User can invite a group with interest "interest1" or "php" to his space
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "en/collaboration-center/spaces/"
        Then I click on the element with css selector "#space_42 div.options .invite-space-document"
        And I click on the element with css selector "a[href='#invite-groups']"
        When I add "interest1,php" to a auto-multi-select "share_interests"
        And i wait 2 seconds
        And I click on the element with css selector "button#save_invited"
        And i wait 2 seconds
        Then  I should see "Sharing settings saved successfully"
        And I click on the element with css selector "button#user-nav2"
        And I click on the element with css selector "a:contains(Log out)"
        When I am a logged in user with "maii.test" and "123456789"
        And I am on "en/collaboration-center/shared/"
        Then I should see "grp share space #3"

    @javascript @Done
    Scenario: A logged-in user adding a not exist interest to his profile
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/about/edit/group/1/"
        When I add "interest1" to a auto-multi-select "interest"
        And I select "academia" from "sub_type"
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
        And I should see "New Spaces/Documents Email Notifications"

    @Done
    Scenario: A logged-in user seeing "Never" selected by default in requests updates notifications section
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/members/espace/settings/notifications-settings/"
        Then "#collaboration_center_updates_value" should contains "Never"

    @javascript @Done
    Scenario: A logged-in user selecting "Daily" notification in collaboration settings
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/settings/notifications-settings/"
        When I press "collaboration_center_updates"
        And I select "Daily" from "collaboration_center_updates-notification-list"
        And I press "save_collaboration_center_updates"
        And I wait 3 seconds
        Then "#collaboration_center_updates_value" should contains "Daily"
        And I should receive notification email "+1 day" at "<espace_1@espace.com.eg>" titled "Daily Spaces/Documents Updates"
        When I go to "/en/"
        And I go to "/en/members/espace/settings/notifications-settings/"
        Then "#collaboration_center_updates_value" should contains "Daily"
        And I close the browser

    @javascript @Done
    Scenario: A logged-in user selecting "Weekly" notification in collaboration settings
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/settings/notifications-settings/"
        When I press "collaboration_center_updates"
        And I select "Weekly" from "collaboration_center_updates-notification-list"
        And I press "save_collaboration_center_updates"
        And I wait 3 seconds
        Then "#collaboration_center_updates_value" should contains "Weekly"
        And I should receive notification email "next friday" at "<espace_1@espace.com.eg>" titled "Weekly Spaces/Documents Updates"
        When I go to "/en/"
        And I go to "/en/members/espace/settings/notifications-settings/"
        Then "#collaboration_center_updates_value" should contains "Weekly"
        And I close the browser

    @javascript @Done
    Scenario: A logged-in user selecting "Monthly" notification in collaboration settings
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/settings/notifications-settings/"
        When I press "collaboration_center_updates"
        And I select "Monthly" from "collaboration_center_updates-notification-list"
        And I press "save_collaboration_center_updates"
        And I wait 3 seconds
        Then "#collaboration_center_updates_value" should contains "Monthly"
        And I should receive notification email "first day of next month" at "<espace_1@espace.com.eg>" titled "Monthly Spaces/Documents Updates"
        When I go to "/en/"
        And I go to "/en/members/espace/settings/notifications-settings/"
        Then "#collaboration_center_updates_value" should contains "Monthly"

    @javascript @Done
    Scenario: A logged-in user selecting "Never" notification in events settings
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/settings/notifications-settings/"
        When I press "collaboration_center_updates"
        And I select "Never" from "collaboration_center_updates-notification-list"
        And I press "save_collaboration_center_updates"
        And I wait 3 seconds
        Then "#collaboration_center_updates_value" should contains "Never"
        And I should not receive notification email on "+1 day" at "<espace_1@espace.com.eg>" titled "Monthly Spaces/Documents Updates"
        When I go to "/en/"
        And I go to "/en/members/espace/settings/notifications-settings/"
        Then "#collaboration_center_updates_value" should contains "Never"
