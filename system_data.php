<?php

$account_types = ['Individual',
    'Entity'];
$locations_types = ['Entity',
    'Individual',
    'Event'];

$account_sub_types = ['developer' => 'Individual',
    'user' => 'Individual',
    'business-owner' => 'Individual',
    'entrepreneur' => 'Individual',
    'investor' => 'Individual',
    'researcher' => 'Individual',
    'fossactivist' => 'Individual',
    'academia' => 'Entity',
    'r-and-d' => 'Entity',
    'ngo' => 'Entity',
    'technology-transfer' => 'Entity',
    'projects' => 'Entity',
    'teams' => 'Entity',
    'government-agencies' => 'Entity',
    'funding-entities' => 'Entity',
    'companies' => 'Entity',
    'foss-supporting-entities' => 'Entity',
    'technology-brokers' => 'Entity'];

$en_sub_types = ['developer' => 'Developer',
    'user' => 'User',
    'business-owner' => 'Business Owner',
    'entrepreneur' => 'Entrepreneur',
    'investor' => 'Investor',
    'researcher' => 'Researcher',
    'fossactivist' => 'FOSS Activist',
    'academia' => 'Academia (universities/ institutes/colleges)',
    'r-and-d' => 'R&D entities (departments/centers)',
    'ngo' => 'Non‐governmental organizations (NGOs)',
    'technology-transfer' => 'Technology transfer offices/organizations',
    'projects' => 'Projects',
    'teams' => 'Teams',
    'government-agencies' => 'Government agencies',
    'funding-entities' => 'Funding entities',
    'companies' => 'Companies',
    'foss-supporting-entities' => 'FOSS supporting entities',
    'technology-brokers' => 'Technology brokers'];

$ar_sub_types = ['developer' => 'مبرمج',
    'user' => 'مستخدم',
    'business-owner' => 'صاحب عمل',
    'entrepreneur' => 'رائد أعمال',
    'investor' => 'مستثمر',
    'researcher' => 'باحث',
    'fossactivist' => 'ناشط في مجال البرمجيات الحرة مفتوحة المصدر',
    'academia' => 'تعليمية (جامعات/معاهد/كليات)',
    'r-and-d' => 'البحث والتطوير (أقسام/مراكز)',
    'ngo' => 'مؤسسات غير حكومية',
    'technology-transfer' => 'مكاتب/مؤسسات نقل التكنولوجيا',
    'projects' => 'مشاريع',
    'teams' => 'فرق',
    'government-agencies' => 'وكالات حكومية',
    'funding-entities' => 'جهات تمويل',
    'companies' => 'شركات',
    'foss-supporting-entities' => 'جهات دعم البرمجيات الحرة مفتوحة المصدر',
    'technology-brokers' => 'وسطاء/سماسرة التكنولوجيا'];


$ef_wsl_social_login_providers = ['Facebook', 'Twitter', 'Google', 'LinkedIn'];
// used in api functions
$ef_registered_taxonomies = array('type', 'technology', 'platform', 'license', 'keywords', 'industry', 'interest', 'theme','quiz_categories');
$ef_registered_taxonomies_labels = array('type' => "Type",
    'technology' => "Technology",
    'platform' => "Platform",
    'license' => "License",
    'keywords' => "Keywords",
    'industry' => "Category",
    'interest' => "Interest",
    'theme' => "Theme",
    'quiz_categories'=> "Quiz Categories");

$ef_product_filtered_taxs = ['industry', 'license', 'platform', 'technology', 'type'];
$foss_prefix = 'wpRuvF8_';

$events_types = array(
    'summits' => 'Summits/conferences',
    'competitions' => 'Competitions',
    'hackathons' => 'Hackathons',
    'fossdays' => 'FOSS days',
    'celebrations' => 'Award parties/celebrations',
    'training' => 'Training',
    'webinars' => 'Webinars',
);
$ar_events_types = array(
    'summits' => 'مؤتمرات/اجتماعات قمة',
    'competitions' => 'مسابقات',
    'hackathons' => 'هاكاثون',
    'fossdays' => 'أيام البرمجيات الحرة مفتوحة المصدر',
    'celebrations' => 'احتفاليات',
    'training' => 'تدريب/دورات',
    'webinars' => 'لقاءات عبر الانترنت (ويبينار)',
);

$system_currencies = array('EGP' => 'EGP', 'USD' => 'USD', 'EUR' => 'EUR');
$ar_system_currencies = array('EGP' => 'جنيه مصرى', 'USD' => 'دولار أمريكي', 'EUR' => 'يورو');

$capabilities = ['administrator' => array('add_new_ef_posts', 'perform_direct_ef_actions'),
    'editor' => array('add_new_ef_posts', 'perform_direct_ef_actions'),
    'author' => array('add_new_ef_posts', 'perform_direct_ef_actions'),
    'contributor' => array('add_new_ef_posts', 'perform_direct_ef_actions'),
    'subscriber' => array()
];

// URLs settings
$page_overrided_links = array(
    "news/add" => array("ar" => 'ar-add-news', "en" => "add-news"),
    "feedback/add" => array("ar" => 'ar-add-feedback', "en" => "add-feedback"),
    "success-stories/add" => array("ar" => 'ar-add-success-story', "en" => "add-success-story"),
    "open-datasets/add" => array("ar" => 'ar-suggest-open-dataset', "en" => "suggest-open-dataset"),
    "events/add" => array("ar" => "ar-add-event", "en" => "add-event"),
    "products/add" => array("ar" => "ar-add-product", "en" => "add-product"),
    "products/edit" => array("ar" => "ar-edit-product", "en" => "edit-product"),
    "events/edit" => array("ar" => "ar-edit-event", "en" => "edit-event"),
    "request-center/edit" => array("ar" => "ar-edit-request-center", "en" => "edit-request-center"),
    "open-dataset/add-resources" => array("ar" => "ar-add-resources-open-dataset", "en" => "add-resources-open-dataset"),
    "maps" => array("ar" => "ar-foss-map", "en" => "foss-map"),
    "terms-of-services" => array("ar" => "ar-terms-of-services", "en" => "terms-of-services"),
    "privacy-policy" => array("ar" => "ar-privacy-policy", "en" => "privacy-policy"),
    "request-center/add" => array("ar" => "ar-add-request-center", "en" => "add-request-center"),
    "collaboration-center" => array("ar" => "ar-collaboration-center", "en" => "collaboration-center", "multiRewrite" => true),
    "collaboration-center/documents/add" => array("ar" => "ar-add-document", "en" => "add-document"),
    "collaboration-center/documents/edit" => array("ar" => "ar-edit-document", "en" => "edit-document"),
    "collaboration-center/published/" => array("ar" => "view-document-ar", "en" => "view-document"),
    "collaboration-center/revisions/" => array("ar" => "view-revision-ar", "en" => "view-revision"),
    "expert-thoughts/add" => array("ar" => 'ar-add-expert-thought', "en" => "add-expert-thought"),
    "expert-thoughts/edit" => array("ar" => 'ar-edit-expert-thought', "en" => "edit-expert-thought"),
    "marketplace/services/add" => array("ar" => "ar-add-service", "en" => "add-service"),
    "marketplace/services/edit" => array("ar" => "ar-edit-service", "en" => "edit-service"),
    "marketplace" => array("ar" => "ar-market-place", "en" => "market-place"),
    "register" => array("ar" => "ar-register", "en" => "register"),
    "login" => array("ar" => "ar-login", "en" => "login"),
    "awareness-center/quiz/result" => array("ar" => "ar-quiz-result", "en" => "quiz-result"),
    "developers" => array("ar" => "ar-developers", "en" => "developers"),
    "newsletter" => array("ar" => "ar-newsletter", "en" => "newsletter"),
    "partners" => array("ar" => "ar-partners", "en" => "partners"));

$pages_to_generate_rewrite_rules = array("add-news" => 'news/add/?',
    "add-event" => 'events/add/?',
    "add-product" => 'products/add/?',
    "edit-product" => 'products/edit/?',
    "suggest-open-dataset" => 'open-datasets/add/?',
    "add-feedback" => 'feedback/add/?',
    "add-success-story" => 'success-stories/add/?',
    "add-request-center" => 'request-center/add/?',
    "edit-event" => 'events/edit/?',
    "add-resources-open-dataset" => 'open-dataset/add-resources/?',
    "edit-request-center" => 'request-center/edit/?',
    "foss-map" => 'maps/?',
    "terms-of-services" => "terms-of-services/?",
    "privacy-policy" => "privacy-policy/?",
    "add-document" => 'collaboration-center/spaces/([^/][0-9]*)/documents/add$',
    "edit-document" => 'collaboration-center/spaces/([^/][0-9]*)/document/([^/][0-9]*)/edit',
    "view-document" => 'collaboration-center/published/([^/][0-9]*)$',
    "view-revision" => 'collaboration-center/revisions/([^/][0-9]*)$',
    // "edit-document" =>'collaboration-center/documents/edit/?',
    "collaboration-center" => array("all" => "collaboration-center$",
        "spaces" => "collaboration-center/spaces$",
        "space_content" => "collaboration-center/spaces/([^/][0-9]*)$",
        "shared" => "collaboration-center/shared$",
        "published" => "collaboration-center/published$",
        "shared_space_content" => "collaboration-center/shared/spaces/([^/][0-9]*)$"),
    "register" => 'register/?',
    "login" => 'login/?',
    "request-thread" => "request-thread/?",
    "add-expert-thought" => 'expert-thoughts/add/?',
    "edit-expert-thought" => 'expert-thoughts/([^/][0-9]*)/edit$',
    "add-service" => "marketplace/services/add/?",
    "edit-service" => "marketplace/services/edit$",
    "market-place" => "marketplace$",
    "service-thread" => "service-thread/?",
    "quiz-result" => "awareness-center/quiz/result/([^/][0-9]*)",
    "activist-center" => "activist-center/?",
    "developers" => "developers/?",
    "newsletter" => "newsletter/?",
    "partners" => "partners/?"
);

$ef_translation_archive_urls = array("news" => 'news', 'products' => 'product', 'events' => 'tribe_events',
    "success-stories" => "success_story", "open-datasets" => "open_dataset",
    'request-center' => 'request_center', 'feedback' => 'feedback', "expert-thoughts" => "expert_thought", 'marketplace/services' => 'service', 'awareness-center' => 'quiz');

$login_page_names = array(
    "login" => array("ar" => "ar-login", "en" => "login"));

$post_type_email_text_override = array("news" => "News", "product" => "Product", "success_story" => "Success story", "tribe_event" => "Event", "open_dataset" => "Open Datasets", "expert_thought" => "Expert thoughts", "market_place" => "Market place", "service" => "Service");

$ef_product_multi_uncreated_tax = array("license", "platform", 'type');

$ef_product_single_uncreated_tax = array('industry');

$ef_multi_add_edit_product_taxs = array("type", "technology", "platform", "license", "interest");

$ef_admin_filter_taxs = array("target_bussiness_relationship", "request_center_type", "theme", "technology", "interest");

$ef_top_nav = array(
        /* array(
          'name'=>"FOSSPedia",
          'url'=>'/wiki/',
          ),
          array(
          'name'=>"FOSS Map",
          'url'=>'/maps/',
          ) */
);

$ef_collaboration_item_status = array(
    'draft' => 'Draft',
    'reviewed' => 'Reviewed',
    'published' => 'Published'
);

$ef_collaboration_item_roles = array(
    'editor' => 'Editor',
    'reviewer' => 'Reviewer',
    'publisher' => 'Publisher'
);

$ef_sections = array(
    'quiz' => 'Awareness Center',
    'collaboration-center' => 'Collaboration Center',
    'event' => 'Events',
    'fossmap' => 'FOSS Map',
    'fosspedia' => 'FOSSPedia',
    'open-dataset' => 'Open Data',
    'news' => 'News',
    'product' => 'Products',
    'success-story' => 'Stories',
    'request-center' => 'Request Center',
    'market-place' => 'Market Place',
    'expert-thought' => 'Expert Thoughts',
);

//open dataset extension arrays
$extensions = array('pdf', 'json', 'csv', 'xml', 'html', 'doc', 'docx', 'xls', 'xlsx', 'jpeg', 'jpg', 'png');
$extension_mime_types = array('pdf' => 'application/pdf', 'json' => 'application/json', 'csv' => 'text/csv', 'xml' => 'text/xml', 'html' => 'text/html', 'jpeg'=>'image/jpeg', 'jpg'=>'image/jpeg', 'png'=>'image/png', 'xls'=>'application/vnd.ms-excel', 'xlsx'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'doc'=>'application/msword', 'docx'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
$extension_mime_types_conv = array('application/pdf' => 'pdf', 'application/json' => 'json', 'text/csv' => 'csv', 'text/xml' => 'xml', 'text/html' => 'html', 'image/jpeg'=>'jpeg', 'image/jpeg'=>'jpg', 'image/png'=>'png', 'application/vnd.ms-excel'=>'xls', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'=>'xlsx', 'application/msword'=>'doc', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'=>'docx');

$market_place_badge_min_rate = 4;

$awareness_success_rate = 50;
$awarness_highest_score_badge_percentage = 80;

$defaultLanguage = "ar";

$activist_center_minimum_points = 200;
$activist_center_users_count = 10;

$local_ips = array('127.0.0.1', "::1");

if(in_array($_SERVER['REMOTE_ADDR'], $local_ips)){
  // for localhost development environment
  $google_maps_key = "AIzaSyBjIlLIrxUrvs59X_C4YJ4iU4PribNt738";
  $google_geocoding_key = "AIzaSyBjIlLIrxUrvs59X_C4YJ4iU4PribNt738";
}
else {
  // for staging/production environment
  $google_maps_key = "AIzaSyDCdGjt6I3mOJeTW1djWGQ3KbHkuFQgwBo";
  $google_geocoding_key = "AIzaSyBYDcYOfKxg-G-TgraFv3Ru-qpyuLwCFkk";
}
