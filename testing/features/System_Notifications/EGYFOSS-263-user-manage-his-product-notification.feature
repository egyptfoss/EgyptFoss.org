Feature: User manage his notifications settings for products in the system
  In order to manage notifications settings for products in the system
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
        And I should see "Product Updates Email Notifications"

    @done
    Scenario: A logged-in user seeing "Never" selected by default in product updates notifications section
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/members/espace/settings/notifications-settings/"
        Then "#products_updates_value" should contains "Never"

    @javascript @done
    Scenario: A logged-in user selecting "Daily" notification in products settings
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/members/espace/settings/notifications-settings/"
        When I press "products_updates"
        And I select "Daily" from "products_updates-notification-list"
        And I press "save_products_updates"
        And I wait 3 seconds
        Then "#products_updates_value" should contains "Daily"
        And I should receive notification email "+1 day" at "<maii.elnagar@espace.com.eg>" titled "Interesting Products at EgyptFOSS"
        When I go to "/en/"
        And I go to "/members/espace/settings/notifications-settings/"
        Then "#products_updates_value" should contains "Daily"

    @javascript @done
    Scenario: A logged-in user selecting "Weekly" notification in products settings
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/members/espace/settings/notifications-settings/"
        When I press "products_updates"
        And I select "Weekly" from "products_updates-notification-list"
        And I press "save_products_updates"
        And I wait 3 seconds
        Then "#products_updates_value" should contains "Weekly"
        And I should receive notification email "next friday" at "<maii.elnagar@espace.com.eg>" titled "Interesting Products at EgyptFOSS"
        When I go to "/en/"
        And I go to "/members/espace/settings/notifications-settings/"
        Then "#products_updates_value" should contains "Weekly"

    @javascript @done
    Scenario: A logged-in user selecting "Monthly" notification in products settings
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/members/espace/settings/notifications-settings/"
        When I press "products_updates"
        And I select "Monthly" from "products_updates-notification-list"
        And I press "save_products_updates"
        And I wait 3 seconds
        Then "#products_updates_value" should contains "Monthly"
        And I should receive notification email "first day of next month" at "<maii.elnagar@espace.com.eg>" titled "Interesting Products at EgyptFOSS"
        When I go to "/en/"
        And I go to "/members/espace/settings/notifications-settings/"
        Then "#products_updates_value" should contains "Monthly"

    @javascript @done
    Scenario: A logged-in user selecting "Never" notification in products settings
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/members/espace/settings/notifications-settings/"
        When I press "products_updates"
        And I select "Never" from "products_updates-notification-list"
        And I press "save_products_updates"
        And I wait 3 seconds
        Then "#products_updates_value" should contains "Never"
        And I should not receive notification email on "+1 day" at "<maii.elnagar@espace.com.eg>" titled "Interesting Products at EgyptFOSS"
        When I go to "/en/"
        And I go to "/members/espace/settings/notifications-settings/"
        Then "#products_updates_value" should contains "Never"
