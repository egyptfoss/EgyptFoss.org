Feature: Admin update a badge (title, image, Arabic name, English name, limits, Arabic description, English description)
  In order to update a badge (title, image, Arabic name, English name, limits, Arabic description, English description)
  As an admin
  I need to be able to navigate to badge's page in the system

    @Done
    Scenario: Admin edit a badge with valid inputs
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/wp-admin/admin.php?page=efb_badges"
        Then I follow "Events Specialist"
        And I fill in "efb-title" with "Edited Events Contributor Level 2"
        And I fill in "efb-title-ar" with "معدل مشارك فى قسم الفعاليات 2"
        And I fill in "efb-min-threshold" with "50"
        And I fill in "efb-desc" with "Lorem Ipsum is simply dummy text of the printing and typesetting industry."
        And I fill in "efb-desc-ar" with "لوريم إيبسوم(Lorem Ipsum) هو ببساطة نص شكلي (بمعنى أن الغاية هي الشكل وليس المحتوى) ويُستخدم في صناعات المطابع ودور النشر."
        And I press "submit"
        Then I should see "Badge has been updated successfully."
        And I should be on "/wp-admin/admin.php?page=efb_badges&action=edit&badge=49"

    @Done
    Scenario: Admin edit a badge with null title
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/wp-admin/admin.php?page=efb_badges"
        Then I follow "Edited Events Contributor Level 2"
        And I fill in "efb-title" with ""
        And I press "submit"
        Then I should see "This field can't be empty."
        And I should be on "/wp-admin/admin.php?page=efb_badges&action=edit&badge=49"

    @Done
    Scenario: Admin edit a badge with null Ar title
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/wp-admin/admin.php?page=efb_badges"
        Then I follow "Edited Events Contributor Level 2"
        And I fill in "efb-title-ar" with ""
        And I press "submit"
        Then I should see "This field can't be empty."
        And I should be on "/wp-admin/admin.php?page=efb_badges&action=edit&badge=49"

    @Done
    Scenario: Admin edit a badge with null description
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/wp-admin/admin.php?page=efb_badges"
        Then I follow "Edited Events Contributor Level 2"
        And I fill in "efb-desc" with ""
        And I press "submit"
        Then I should see "This field can't be empty."
        And I should be on "/wp-admin/admin.php?page=efb_badges&action=edit&badge=49"

    @Done
    Scenario: Admin edit a badge with null Ar description
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/wp-admin/admin.php?page=efb_badges"
        Then I follow "Edited Events Contributor Level 2"
        And I fill in "efb-desc-ar" with ""
        And I press "submit"
        Then I should see "This field can't be empty."
        And I should be on "/wp-admin/admin.php?page=efb_badges&action=edit&badge=49"

    @Done
    Scenario: Admin edit a badge with empty threshold value
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/wp-admin/admin.php?page=efb_badges"
        Then I follow "Edited Events Contributor Level 2"
        And I fill in "efb-min-threshold" with ""
        And I press "submit"
        Then I should see "This field can't be empty."
        And I should be on "/wp-admin/admin.php?page=efb_badges&action=edit&badge=49"

    @Done
    Scenario: Admin edit a badge with zero threshold value
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/wp-admin/admin.php?page=efb_badges"
        Then I follow "Edited Events Contributor Level 2"
        And I fill in "efb-min-threshold" with "0"
        And I press "submit"
        Then I should see "This field can't be empty."
        And I should be on "/wp-admin/admin.php?page=efb_badges&action=edit&badge=49"