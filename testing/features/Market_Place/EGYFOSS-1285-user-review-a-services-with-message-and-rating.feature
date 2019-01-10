Feature: User submit a review on a service in the marketplace with a message and a rating
	In order to submit a review on a service in the marketplace
	As a user
	I need to navigate to service request page and review it from inside

		@Done @add-services
		Scenario: A service owner can't review his own service
			Given I am a logged in user with "foss" and "F0$$"
			And I am on "/en/marketplace/services/service-29/"
			When I follow "Requests"
			And I wait to be redirected
			Then I should be on "/en/service-thread/?pid=239"
			And I should see "Requester Rate"
			And I should not see an "span.live-rating" element

		@Done @add-services
		Scenario: A service requester can't review this service if he has an empty thread
			Given I am a logged in user with "espace" and "123456789"
			And I am on "/en/marketplace/services/service-14/"
			When I follow "Request Service"
			And I wait to be redirected
			Then I should be on "/en/service-thread/?pid=239"
			And I should see "Requester Rate"
			And I should not see an "span.live-rating" element

		@wp @javascript @add-services
		Scenario: A service requester can review this service if he has an un-empty thread
			Given I am a logged in user with "espace" and "123456789"
			And I am on "/en/marketplace/services/service-21/"
			When I follow "Check your request"
			And I wait to be redirected
			Then I should be on "/en/service-thread/?pid=239"

		@add-services
		Scenario: A service requester can't review the same service twice

		@add-services
		Scenario: A service requester can't review this service with zero rate

		@add-services
		Scenario: A service requester can't review this service without a review message

		@add-services
		Scenario: A service requester can review this service even if it is archived

		@add-services
		Scenario: A service requester can't review this service if he is a subscriber
