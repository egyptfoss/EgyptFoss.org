<?php
$routes = array(
    "GET:/wiki/{language}/pages/{pageName}" => "MediaWikiController/getPage",
    "GET:/wiki/{language}/pages/{pageName}/versions" => "MediaWikiController/getPageHistory",
    "GET:/wiki/{language}/pages/{pageName}/versions/{versionNumber}" => "MediaWikiController/getPageVersion",
    "POST:/wiki/{language}/pages" => "MediaWikiController/addPage",
    "PUT:/wiki/{language}/pages/{pageName}" => "MediaWikiController/editPage",
    "PUT:/wiki/{language}/pages/{pageName}/versions/{versionNumber}" => "MediaWikiController/revertPage",
    "POST:/wiki/{language}/files" => "MediaWikiController/uploadFile",

    "POST:/register" => "WPRegistrationController/Register",
    "POST:/login/social" => "WPAuthenticator/AuthenticateWithSocialMedia",
    "POST:/login" => "WPAuthenticator/authenticate",
    "POST:/logout" => "WPAuthenticator/revoke",
    "POST:/reset-password" => "WPAuthenticator/resetPasswordRequest",
    "POST:/sys-data" => "WPAuthenticator/sysData",

    //"GET:/profiles/me/info" => "WPProfileController/viewMyProfile",
    "PUT:/profiles/me/info" => "WPProfileController/editProfile",
    "POST:/profiles/me/social-links" => "WPUserController/linkWithSocialMedia",
    "DELETE:/profiles/me/social-links" => "WPUserController/unlinkWithSocialMedia",
    "GET:/profiles/me/social-links" => "WPProfileController/getSocialLinks",
    "POST:/profiles/me/pictures" => "WPProfileController/changeProfilePhoto",
    "POST:/profiles/me/passwords" => "WPProfileController/editGeneralSettings",
    "POST:/profiles/me/change-email" => "WPProfileController/editMyEmail",
    "GET:/profiles/me/notifications" => "WPProfileController/getNotificationsSettings",
    "PUT:/profiles/me/notifications" => "WPProfileController/updateNotificationsSettings",
    "POST:/profiles/me/activities" => "WPProfileController/addProfileUpdates",
    "DELETE:/profiles/me/activities/{id}" => "WPProfileController/deleteProfileUpdate",
    "POST:/profiles/activities/{id}/like" => "WPProfileController/likeProfileUpdate",
    "DELETE:/profiles/activities/{id}/like" => "WPProfileController/removeLikeProfileUpdate",
    "GET:/profile/{profileName}/info" => "WPProfileController/viewProfile", // view other profile
    "GET:/profiles/{profileName}/activities" => "WPProfileController/listProfileActivity",
    "GET:/profiles/activities/{id}/likes" => "WPProfileController/getLikesUsers",
    "POST:/profiles/activities/{postActivityID}/comments" => "WPProfileController/addUpdateComments",
    "GET:/profiles/activities/{parentActivityID}/comments" => "WPProfileController/getComment",
    "POST:/profiles/activities/{parentActivityID}/comments/{commentID}/replies" => "WPProfileController/addCommentReply",
    "GET:/profiles/comments/{commentId}/replies" => "WPProfileController/listRepliesonAComment", // List Replies on Comments
    "GET:/profile/contributions/{profileName}/events" => "WPProfileController/listEventsByUser",
    "GET:/profile/contributions/{profileName}/news" => "WPProfileController/listNewsByUser",
    "GET:/profile/contributions/{profileName}/documents" => "WPProfileController/listDocumentsByUser",
    //"GET:/my-profile/contributions/news" => "WPProfileController/listNewsByMe",
    //"GET:/my-profile/contributions/events" => "WPProfileController/listEventsByMe",
    "GET:/profile/contributions/{profileName}/products/additions" => "WPProfileController/listAddedProductsByUser",
    "GET:/profile/contributions/{profileName}/products/edits" => "WPProfileController/listContributedProductsByUser",
    "GET:/profile/contributions/{profileName}/success-stories" => "WPProfileController/listSuccessStoriesByUser",
    //"GET:/my-profile/contributions/success-stories" => "WPProfileController/listSuccessStoriesByMe",
    "GET:/profile/contributions/{profileName}/open-datasets" => "WPProfileController/listDatasetsByUser",
    //"GET:/my-profile/contributions/open-datasets" => "WPProfileController/listDatasetsByMe",    
    "GET:/profile/contributions/{profileName}/fosspedia/additions" => "WPProfileController/listFosspediaByUser",
    //"GET:/my-profile/contributions/fosspedia/additions" => "WPProfileController/listFosspediaByMe",        
    "GET:/profile/contributions/{profileName}/fosspedia/edits" => "WPProfileController/listFosspediaEditsByUser",
    "GET:/profile/contributions/{profileName}/request-center/requests" => "WPProfileController/listRequestsByUser",
    "GET:/profile/contributions/me/request-center/responses" => "WPProfileController/listResponsesByUser",
    "GET:/profile/contributions/{profileName}/open-datasets/edits" => "WPProfileController/listContributedDataSetsByUser",
    "GET:/profile/contributions/{profileName}/expert-thoughts" => "WPProfileController/listExpertThoughtsByUser",
    "GET:/profile/{profileName}/badges" => "WPProfileController/listUserBadges",
    "GET:/users" => "WPProfileController/listSystemUsers",
    "GET:/profile/{profileName}/services" => "WPProfileController/listServicesByUser",
    "GET:/profile/me/services/responses" => "WPProfileController/listServiceResponsesByUser",
    "GET:/profile/me/quizzes/" => "WPProfileController/listMyQuizzes",
    "POST:/profiles/me/social-login/resend-activation" => "WPProfileController/sendProfileActivation",
    //"GET:/my-profile/contributions/fosspedia/edits" => "WPProfileController/listFosspediaEditsByMe",            
    // "GET:/comments/{commentID}" => "WPProfileController/getComment", // View Comment

    "GET:/products" => "WPProductController/listingProduct",
    "GET:/products/topTen" => "WPProductController/listingTopTenProducts",
    "POST:/products" => "WPProductController/addProduct", // Product route
    "PUT:/products/{product_id}" => "WPProductController/editProduct",  
    "GET:/products/{product_id}" => "WPProductController/viewProduct",
    "GET:/products/{productId}/comments" => "WPProductController/listProductComments", // List Product Comments
    "GET:/products/comments/{commentId}/replies" => "WPProductController/listRepliesonAComment", // List Replies on Comments
    "POST:/products/{product_name}/comments" => "WPProductController/addProductComments", // Add Product Comments
    "POST:/products/{productId}/comments/{commentId}/replies" => "WPProductController/addReplyToComment", // Add Reply to Comment on Product
    "GET:/products/{product_id}/contributors" => "WPProductController/listingProductContributors",
    "POST:/events" => "WPEventController/addEvent",
    "PUT:/events/{event_id}" => "WPEventController/editEvent",
    "GET:/events" => "WPEventController/listingEvents",
    "GET:/event/{eventId}" => "WPEventController/viewEvent",
    "GET:/event/{eventId}/comments" => "WPEventController/listEventComments", // List Event Comments
    "GET:/event/comments/{commentId}/replies" => "WPEventController/listRepliesonAComment", // List Replies on Comments
    "POST:/event/{eventId}/comments" => "WPEventController/addEventComments", // Add Event Comments
    "POST:/event/{eventId}/comments/{commentId}/replies" => "WPEventController/addReplyToComment", // Add Reply to Comment on Event
    "POST:/locations" => "GeolocationsController/addLocation",
    "GET:/location/me" => "GeolocationsController/getLocation",
    "GET:/locations" => "GeolocationsController/listingLocations",
  
    "POST:/news" => "WPNewsController/addNews",
    "GET:/news" => "WPNewsController/listNews",
    "GET:/news/{newsId}" => "WPNewsController/viewNews",
    "GET:/news/{newsId}/comments" => "WPNewsController/listNewsComments", // List News Comments
    "GET:/news/comments/{commentId}/replies" => "WPNewsController/listRepliesonAComment", // List Replies on Comments
    "POST:/news/{newsId}/comments" => "WPNewsController/addNewsComments", // Add News Comments
    "POST:/news/{newsId}/comments/{commentId}/replies" => "WPNewsController/addReplyToComment", // Add Reply to Comment on News

    "GET:/setupdata/industry" => "WPSetupDataController/listIndustries",
    "GET:/setupdata/theme" => "WPSetupDataController/listThemes",
    "GET:/setupdata/license" => "WPSetupDataController/listLicences",
    "GET:/setupdata/platform" => "WPSetupDataController/listPlatforms",
    "GET:/setupdata/technology" => "WPSetupDataController/listTechnologies",
    "GET:/setupdata/type" => "WPSetupDataController/listTypes",
    "GET:/setupdata/subtypes" => "WPSetupDataController/listSubTypes",
    "GET:/setupdata/open-dataset/type" => "WPSetupDataController/listOpenDatasetType",
    "GET:/setupdata/open-dataset/license" => "WPSetupDataController/listOpenDatasetLicense",
    "GET:/setupdata/interests" => "WPSetupDataController/listInterests",
    "GET:/setupdata/request-center/type" => "WPSetupDataController/listRequestCenterTypes",
    "GET:/setupdata/request-center/target-relationships" => "WPSetupDataController/listRequestCenterBussinessRelationships",
    "GET:/setupdata/services/category" => "WPSetupDataController/listServicesCategories",
    "GET:/setupdata/success-story/category" => "WPSetupDataController/listSuccessStoriesCategories",
    "GET:/setupdata/news/category" => "WPSetupDataController/listNewsCategories",
    "GET:/setupdata/sections" => "WPSetupDataController/listSystemSections",
    "GET:/setupdata/quiz/category" => "WPSetupDataController/listQuizCategories",
    "GET:/setupdata/event/venues" => "WPSetupDataController/listEventVenues",
    "GET:/setupdata/event/organizers" => "WPSetupDataController/listEventOrganizers",

    
    "POST:/feedback" => "WPFeedbackController/addFeedback",

    "POST:/success-story" => "WPSuccessStoryController/addSuccessStory",
    "GET:/success-stories" => "WPSuccessStoryController/listSuccessStories",
    "GET:/success-story/{successId}" => "WPSuccessStoryController/viewSuccessStory",
    "GET:/success-story/{successId}/comments" => "WPSuccessStoryController/listSuccessStoryComments",
    "GET:/success-story/comments/{commentId}/replies" => "WPSuccessStoryController/listRepliesonAComment",
    "POST:/success-story/{successId}/comments" => "WPSuccessStoryController/addSuccessStoryComments",
    "POST:/success-story/{successId}/comments/{commentId}/replies" => "WPSuccessStoryController/addReplyToComment",
    
    "POST:/open-dataset" => "WPOpenDataSetController/addOpenDataSet",
    "POST:/open-datasets/{dataset_id}/comments" => "WPOpenDataSetController/addDatasetComments",
    "POST:/open-datasets/{dataset_id}/comments/{commentId}/replies" => "WPOpenDataSetController/addReplyToComment", // Add Reply to Comment on Open Dataset
    "GET:/open-datasets/{dataset_id}/comments" => "WPOpenDataSetController/listDatasetsComments", // List Open Dataset Comments
    "GET:/open-datasets/comments/{commentId}/replies" => "WPOpenDataSetController/listRepliesonAComment", // List Replies on Comments
    "GET:/open-datasets" => "WPOpenDataSetController/listOpenDataSets",
    "GET:/open-datasets/{dataset_id}" => "WPOpenDataSetController/viewOpenDataSet",
    "POST:/open-datasets/resources/{dataset_id}" => "WPOpenDataSetController/addResourceOpendataset",

    //"GET:/request-center/types/{lang}" => "WPRequestCenterController/listRequestCenterTypes",
    "POST:/request-center" => "WPRequestCenterController/addRequest",
    "PUT:/request-center/{request_id}" => "WPRequestCenterController/editRequest",
    "GET:/request-center/thread/{thread_id}" => "WPRequestCenterController/viewThread",
    "GET:/request-center/{request_id}" => "WPRequestCenterController/viewRequest",
    "POST:/request-center/{request_id}/response" => "WPRequestCenterController/addResponse",
    "GET:/request-center/{request_id}/my/responses" => "WPRequestCenterController/listThreads",
    "GET:/request-center" => "WPRequestCenterController/listRequestCenter",
    "POST:/request-center/response/{thread_id}/archive" => "WPRequestCenterController/archiveThread",
    "POST:/request-center/{request_id}/archive" => "WPRequestCenterController/archiveRequest",
  
    "GET:/collaboration/shared/" => "CollaborationCenterController/listSharedItems",
    "GET:/collaboration/spaces/" => "CollaborationCenterController/listSpaces",
    "GET:/collaboration/spaces/{space_id}/documents" => "CollaborationCenterController/listSpaceDocuments",
    "POST:/collaboration/spaces/" => "CollaborationCenterController/createSpace",
    "POST:/collaboration/spaces/{space_id}/documents/" => "CollaborationCenterController/addDocument",
    "PUT:/collaboration/documents/{document_id}" => "CollaborationCenterController/editDocument",
    "GET:/collaboration/share-settings/users/{item_id}" => "CollaborationCenterController/listInvitedUser",
    "POST:/collaboration/share-settings/users/{item_id}" => "CollaborationCenterController/addUserToItem",
    "POST:/collaboration/share-settings/groups/{item_id}" => "CollaborationCenterController/shareItemByGroup",
    "DELETE:/collaboration/share-settings/users/{item_id}" => "CollaborationCenterController/deleteUserPermissionToItem",
    "GET:/collaboration/share-settings/groups/{item_id}" => "CollaborationCenterController/listSharedGroups",
    "GET:/collaboration/published/" => "CollaborationCenterController/listPublishedDocuments",
    "GET:/collaboration/valid-users/{item_id}/" => "CollaborationCenterController/listValidUsersPerItem",
    "DELETE:/collaboration/spaces/delete/{item_id}" => "CollaborationCenterController/deleteSpace",
    "DELETE:/collaboration/documents/delete/{item_id}" => "CollaborationCenterController/deleteDocument",
    "GET:/collaboration/documents/{document_id}" => "CollaborationCenterController/getDocument",
    "GET:/collaboration/published/documents/{document_id}" => "CollaborationCenterController/getPublishedDocument",
    "PUT:/collaboration/spaces/{item_id}" => "CollaborationCenterController/editSpace",
  
    "POST:/expert-thoughts" => "WPExpertThoughtController/addExpertThought",
    "GET:/expert-thoughts" => "WPExpertThoughtController/listExpertThoughts",
    "GET:/expert-thoughts/{thought_id}" => "WPExpertThoughtController/viewExpertThought",
    "POST:/expert-thoughts/{thought_id}" => "WPExpertThoughtController/editExpertThought",
    "POST:/expert-thoughts/{thought_id}/comments" => "WPExpertThoughtController/addExpertThoughtComments",
    "POST:/expert-thoughts/{thought_id}/comments/{commentId}/replies" => "WPExpertThoughtController/addReplyToComment", // Add Reply to Comment on Expert Thought
    "GET:/expert-thoughts/{thought_id}/comments" => "WPExpertThoughtController/listExpertThoughtComments", // List Expert Thoughts Comments
    "GET:/expert-thoughts/comments/{commentId}/replies" => "WPExpertThoughtController/listRepliesonAComment", // List Replies on Comments,

    "POST:/market-place/services" => "WPMarketPlaceController/addService",
    "POST:/market-place/services/{service_id}" => "WPMarketPlaceController/editService",
    "GET:/market-place/service/thread/{thread_id}" => "WPMarketPlaceController/viewThread",
    "GET:/market-place/service/{service_id}" => "WPMarketPlaceController/viewService",
    "POST:/market-place/service/{service_id}/request" => "WPMarketPlaceController/addRequestReply",
    "GET:/market-place/service/{service_id}/my/requests" => "WPMarketPlaceController/listThreads",
    "POST:/market-place/service/request/{thread_id}/archive" => "WPMarketPlaceController/archiveThread",
    "POST:/market-place/service/{service_id}/archive" => "WPMarketPlaceController/archiveService",
    "GET:/market-place" => "WPMarketPlaceController/listServices",
    "POST:/market-place/service/{service_id}/review" => "WPMarketPlaceController/saveReview",
    "GET:/market-place/service/{service_id}/rate" => "WPMarketPlaceController/viewRate",
    "GET:/market-place/service/{service_id}/reviews" => "WPMarketPlaceController/listReviews",
    "GET:/market-place/service/{service_id}/review/{review_id}" => "WPMarketPlaceController/viewReview",
    "GET:/market-place/top-services/" => "WPMarketPlaceController/listTopServices",
    "GET:/market-place/top-providers/" => "WPMarketPlaceController/listTopProviders",
    
    "GET:/search/{post_type}/{search_keyword}/" => "SearchController/listSearch",
    
    "GET:/awareness-center/" => "WPAwarnessCenterController/listQuizes",
    "GET:/awareness-center/{quiz_id}" => "WPAwarnessCenterController/getQuiz",
    "POST:/awareness-center/{quiz_id}/take" => "WPAwarnessCenterController/takeQuiz",
    "GET:/awareness-center/quiz/result/{result_id}" => "WPAwarnessCenterController/getResult",
    
    "GET:/activist-center" => "WPActivistCenterController/listTopUsers",
    
    "POST:/post/attachment/{post_type}/{post_id}/{attachment_type}" => "WPPostController/addAttachment",
  
    "GET:/badges/unnotified" => "BadgesController/getUnNotifiedBadgesByUser",
    "GET:/badges" => "BadgesController/listBadges",
    
    "GET:/homepage" => "WPHomepageController/listHomepage",
    
) ;
