Feature: User can view related documents 
  In order view a related document
  As a user
  I need to be able to view related documents in every section

    @javascript @Useless
    Scenario: User sees empty related documents in news
      Given I am on "/en/news/"
      When I follow "Related Documents"
      Then I should see "No published documents in this section"
      And I should not see "Show More"

    @javascript @Done
    Scenario: User sees empty related documents in products
      Given I am on "/en/products/"
      When I follow "Related Documents"
      Then I should see "No published documents in this section"
      And I should not see "Show More"

    @javascript @Useless
    Scenario: User sees empty related documents in events
      Given I am on "/en/events/"
      When I follow "Related Documents"
      And I wait for 8 seconds
      Then I should see "No published documents in this section"
      And I should not see "Show More"

    @javascript @Done
    Scenario: User sees empty related documents in open datasets
      Given I am on "/en/open-datasets/"
      When I follow "Related Documents"
      And I wait for 8 seconds
      Then I should see "No published documents in this section"
      And I should not see "Show More"

    @javascript @Done
    Scenario: User sees empty related documents in success stories
      Given I am on "/en/success-stories/"
      When I follow "Related Documents"
      And I wait for 8 seconds
      Then I should see "No published documents in this section"
      And I should not see "Show More"

    @javascript @Done
    Scenario: User sees empty related documents in fossmap
      Given I am on "/en/maps/"
      And I resize window with height 800 and width 2048 in px
      When I follow "Related Documents"
      Then I should see "No published documents in this section"
      And I should not see "Show More"

    @javascript @Done
    Scenario: User sees empty related documents in request center
      Given I am on "/en/request-center/"
      When I follow "Related Documents"
      And I wait for 8 seconds
      Then I should see "No published documents in this section"
      And I should not see "Show More"

    @javascript @Done
    Scenario: User sees empty related documents in fosspedia
      Given I am on "/en/wiki/"
      When I follow "Related Documents"
      Then I should see "No published documents in this section"
      And I should not see "Show More"

    @javascript @Done
    Scenario: User should not see related documents in Collaboration
      Given I am on "/en/collaboration-center/"
      Then I should not see "Related Documents"

    @javascript @Done @set-published-document
    Scenario: Logged-in user can set his own document to published in news section to show in related documents in news
      Given I am on "/en/news/"
      And I follow "Related Documents"
      And I wait for 8 seconds
      Then I should see "user document #2"
      And I should see an ".post-date" element
      And I should see an ".file-icon" element
      And I should see an ".see-more-link" element

    @javascript @Done
    Scenario: User can view related documents in new section
      Given I am on "/en/news/"
      When I follow "Related Documents"
      Then I should see 4 ".file-icon" elements
      And I should see an ".see-more-link" element
    
    @javascript @Done
    Scenario: User can view related documents in new section and click on user
      Given I am on "/en/news/"
      And I follow "Related Documents"
      When I follow "foss"
      Then I should be on "/en/members/foss/about/"

    @javascript @Done
    Scenario: User can view related documents in new section and click on see more
      Given I am on "/en/news/"
      And I follow "Related Documents"
      When I follow "Show More"
      Then I should be on "/en/collaboration-center/published/?section=news"
      
    @javascript @Done
    Scenario: User can view related documents in new section and click on 1st document
      Given I am on "/en/news/"
      And I follow "Related Documents"
      When I follow "my document #1"
      Then I should be on "/en/collaboration-center/published/3/"
      