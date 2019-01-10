Feature:  User subscribe/unsubscribe to expert thought notifications in the system
  In order to subscribe/unsubscribe to expert thought notifications in the system
  As a logged-in User
  I need to be able to navigate to settings page and adjust my settings

    @javascript @Done
    Scenario: An expert user Adding new expert thought to the system with valid inputs
        Given I am a logged in user with "expert_user" and "123456789"
        And I am on "/en/expert-thoughts/add"
        And I fill in "expert_thought_title" with "test thought unique"
        And I fill in "expert_thought_description" with "test thought unique"
        And I add "new interest" to a auto-select "interest"
        And I attach the file "testImages/logo.png" to "expert_thought_image" with relative path
        And I press "submit"
        Then I should see "added successfully"

    @javascript @Done
    Scenario: Admin Accepts new expert thought to the system with valid inputs
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/wp-admin/edit.php?post_type=expert_thought"
        Then I follow "test thought unique"
        And I select "new interest" from "acf-field-interest"
        And I press "publish"
        And I am on "/wp-admin/edit.php?post_type=expert_thought"

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
        And I should see "Expert thoughts Email Notifications"

    @Done
    Scenario: A logged-in user seeing "Never" selected by default in expert thoughts updates notifications section
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/members/espace/settings/notifications-settings/"
        Then "#expert_thoughts_updates_value" should contains "Never"

    @javascript
    Scenario: A logged-in user selecting "Daily" notification in expert thoughts settings
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/settings/notifications-settings/"
        When I press "expert_thoughts_updates"
        And I select "Daily" from "expert_thoughts_updates-notification-list"
        And I press "save_expert_thoughts_updates"
        And I wait 3 seconds
        Then "#expert_thoughts_updates_value" should contains "Daily"
        And I should receive notification email "+1 day" at "<espace_1@espace.com.eg>" titled "Daily Expert Thoughts Updates"
        When I go to "/en/"
        And I go to "/en/members/espace/settings/notifications-settings/"
        Then "#expert_thoughts_updates_value" should contains "Daily"

    @javascript
    Scenario: A logged-in user selecting "Weekly" notification in expert thoughts settings
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/settings/notifications-settings/"
        When I press "expert_thoughts_updates"
        And I select "Weekly" from "expert_thoughts_updates-notification-list"
        And I press "save_expert_thoughts_updates"
        And I wait 3 seconds
        Then "#expert_thoughts_updates_value" should contains "Weekly"
        And I should receive notification email "next friday" at "<espace_1@espace.com.eg>" titled "Weekly Expert Thoughts Updates"
        When I go to "/en/"
        And I go to "/en/members/espace/settings/notifications-settings/"
        Then "#expert_thoughts_updates_value" should contains "Weekly"

    @javascript
    Scenario: A logged-in user selecting "Monthly" notification in expert thoughts settings
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/settings/notifications-settings/"
        When I press "expert_thoughts_updates"
        And I select "Monthly" from "expert_thoughts_updates-notification-list"
        And I press "save_expert_thoughts_updates"
        And I wait 3 seconds
        Then "#expert_thoughts_updates_value" should contains "Monthly"
        And I should receive notification email "first day of next month" at "<espace_1@espace.com.eg>" titled "Monthly Expert Thoughts Updates"
        When I go to "/en/"
        And I go to "/en/members/espace/settings/notifications-settings/"
        Then "#expert_thoughts_updates_value" should contains "Monthly"

    @javascript @Done
    Scenario: A logged-in user selecting "Never" notification in expert thoughts settings
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/members/espace/settings/notifications-settings/"
        When I press "expert_thoughts_updates"
        And I select "Never" from "expert_thoughts_updates-notification-list"
        And I press "save_expert_thoughts_updates"
        And I wait 3 seconds
        Then "#expert_thoughts_updates_value" should contains "Never"
        And I should not receive notification email on "+1 day" at "<espace_1@espace.com.eg>" titled "Expert Thoughts Updates"
        When I go to "/en/"
        And I go to "/en/members/espace/settings/notifications-settings/"
        Then "#expert_thoughts_updates_value" should contains "Never"
