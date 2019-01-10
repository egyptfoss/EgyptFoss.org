Feature: User get auto filled data from single product or event
  In order to get auto filled data from product or event
  As an User
  I need to click in add request from single product or event

    @Done @add-events
    Scenario: A not logged-in user click on add request from product
        Given I am on "/en/products/producttest_6with_taxs_en/"
        And I follow "Add Request"
        Then I am on "/en/products/producttest_6with_taxs_en/"
        #Then I should be on url with redirecto "/en/login/?redirect_to=http://egyptfoss.com/en/request-center/add/?technology=php,java&interest=interest1"
        #And I should see "Please log in to suggest a new request"

    @javascript @Done
    Scenario: A logged-in user click on add request from product with auto filled data
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/products/producttest_6with_taxs_en/"
        And I follow "Add Request"
        And I wait to be redirected
        Then I should be on "en/request-center/add/?technology=php,java&interest=interest1" 
        And I should find element with css selector "div#filter-technology li.select2-selection__choice:contains('php') "
        And I should find element with css selector "div#filter-technology li.select2-selection__choice:contains('python') "
        And I should find element with css selector "div#filter-interest li.select2-selection__choice:contains('interest1') "

    @javascript @Done
    Scenario: A logged-in user click on add request from product without data
        Given I am a logged in user with "espace" and "123456789"
        And I am on "/en/products/40no_taxs_en/"
        And I follow "Add Request"
        And I wait to be redirected
        Then I should be on "/en/request-center/add/" 
        And I should not find element with css selector "div#filter-technology li.select2-selection__choice"
        And I should not find element with css selector "div#filter-interest li.select2-selection__choice"

    @Done
    Scenario: A not logged-in user click on add request from event
        Given I am on "en/events/new-test-event-title-egypt-foss/"
        And I follow "Add Request"
        And I wait to be redirected
        Then I am on "en/events/new-test-event-title-egypt-foss/"
        #Then I should be on url with redirecto "/en/login/?redirect_to=http://egyptfoss.com/en/request-center/add/"
        #And I should see "Please log in to suggest a new request"

    @javascript @Done
    Scenario: A logged-in user click on add request from event without data
        Given I am a logged in user with "espace" and "123456789"
        And I am on "en/events/new-test-event-title-egypt-foss/"
        And I follow "Add Request"
        And I wait to be redirected
        Then I should be on "/en/request-center/add/" 
        And I should not find element with css selector "div#filter-technology li.select2-selection__choice"
        And I should not find element with css selector "div#filter-interest li.select2-selection__choice"    
           
       