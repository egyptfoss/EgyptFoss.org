Feature: User view a success story page in the system
  In order to view a success story page in the system
  As a user
  I need to navigate to success story page and view all success story's details

    @Done @add-success-stories
    Scenario: A user navigating to view success story page from success stories list page
        Given I am on "/en/"
        When I follow "Stories"
        And I follow "new-test-success-title-EGYPT-FOSS"
        Then I should be on "/en/success-stories/new-test-success-title-egypt-foss/"

    @Done
    Scenario: A user viewing a success story with all its details inserted
        Given I am on "/en/success-stories/new-test-success-title-egypt-foss/"
        Then I should see "new-test-success-title-EGYPT-FOSS"
        And I should see "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book."
        And I should see an ".post-date" element
        And I should see an ".news-author" element
        And I should see an ".story-category" element
        #And I should see an ".news-img" element

    @Done
    Scenario: A user viewing a success story with only required data
        Given I am on "/en/success-stories/new-test-success-title-egypt-foss-55/"
        Then I should see "new-test-success-title-EGYPT-FOSS-55"
        And I should see "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book."
        And I should see an ".post-date" element
        And I should see an ".news-author" element
        And I should see an ".story-category" element

    @Done
    Scenario: A user viewing other success stories in the success story page sidebar
        Given I am on "/en/success-stories/new-test-success-title-egypt-foss-55/"
        Then I should see 5 ".news-list-item" elements