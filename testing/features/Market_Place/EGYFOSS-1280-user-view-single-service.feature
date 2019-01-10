Feature: User view a service page in the system
  In order to view a service page in the system
  As a user
  I need to navigate to service page and view all service's details

    @not_implemented @add-services
    Scenario: A user navigating to view service page from services list page
        Given I am on "/en/marketplace/services"
        And take screenshot
        When I follow "service-1"
        Then I should be on "/en/marketplace/service-1/"

    @Done @add-services
    Scenario: A not logged-in user viewing a published service with all its details inserted and can't see Edit
        Given I am on "/en/marketplace/services/service-1/"
        Then I should see "service-1"
        And I should see "Description"
        And I should see "Conditions"
        And I should see "Constraints"
        And I should see an ".service-rating" element
        And I should see an ".provider-avatar" element
        And I should see an ".user-name" element
        And I should see an ".service-cover" element
        And I should see "Prince"
        And I should see "python"
        And I should see "interest 1"
        And I should see "mobile"
        And I should not see an "a.btn-light" element

    @Done @add-services
    Scenario: A not logged-in user viewing a published service with only required data and can not see Edit
        Given I am on "/en/marketplace/services/service-2/"
        Then I should see "service-2"
        And I should see "Description"
        And I should see an ".service-rating" element
        And I should see an ".provider-avatar" element
        And I should see an ".user-name" element
        And I should see an ".service-cover" element
        And I should not see an "a.btn-light" element
        And I should see "mobile"
        And I should not see "Prince"
        And I should not see "Conditions"
        And I should not see "Constraints"
        And I should not see "Technologies"
        And I should not see "Interests"

    @Done @add-services
    Scenario: A not logged-in user can't view a pending service
        Given I am on "/en/marketplace/services/service-4/"
        Then I should see "Oops! That page can’t be found."

    @not-working @add-services
    Scenario: A logged-in user viewing his published service with all its details inserted and see Edit
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/marketplace/services/service-1/"
        Then I should see "service-1"
        And I should see "Description"
        And I should see "Conditions"
        And I should see "Constraints"
        And I should see an ".service-rating" element
        And I should see an ".provider-avatar" element
        And I should see an ".user-name" element
        And I should see an ".service-cover" element
        And I should see "Prince"
        And I should see "python"
        And I should see "interest1"
        And I should see "mobile"
        And I should see an "a.btn-light" element

    @Done @add-services
    Scenario: A logged-in user viewing his published service with only required data and see Edit
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/marketplace/services/service-2/"
        Then I should see "service-2"
        And I should see "Description"
        And I should see an ".service-rating" element
        And I should see an ".provider-avatar" element
        And I should see an ".user-name" element
        And I should see an ".service-cover" element
        And I should see "mobile"
        And I should not see "Prince"
        And I should not see "Conditions"
        And I should not see "Constraints"
        And I should not see "Technologies"
        And I should not see "Interests"
        And I should see an "a.btn-light" element

    @Done @add-services
    Scenario: A logged-in user viewing his pending service and see Edit
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/marketplace/services/service-4/"
        Then I should see "service-4"
        And I should see "Description"
        And I should see an ".service-rating" element
        And I should see an ".provider-avatar" element
        And I should see an ".user-name" element
        And I should see an ".service-cover" element
        And I should see an "a.btn-light" element
        And I should see "mobile"
        And I should not see "Prince"
        And I should not see "Conditions"
        And I should not see "Constraints"
        And I should not see "Technologies"
        And I should not see "Interests"

    @Done @add-services
    Scenario: A user switching between Ar and En version of a service
        Given I am a logged in user with "foss" and "F0$$"
        And I am on "/en/marketplace/services/service-1/"
        When I click on the element with css selector "a.lan-btn"
        And I wait to be redirected
        Then I should be on "/ar/marketplace/services/service-1/"
        And I should see "الوصف"
        And I should see "القيود"
        And I should see "الشروط"
        And I should see "التقنيات"
        And I should see "الاهتمامات"
        And I should see "المجال"
        And I should see "التصنيف"