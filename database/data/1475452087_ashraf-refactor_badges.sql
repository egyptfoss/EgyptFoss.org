SET FOREIGN_KEY_CHECKS=0; 
truncate table wpRuvF8_efb_badges_actions;
truncate table wpRuvF8_efb_badges_users;
truncate table wpRuvF8_efb_credited_user_posts;
truncate table wpRuvF8_efb_actions;
truncate table wpRuvF8_efb_badges;
SET FOREIGN_KEY_CHECKS=1;
delete from `wpRuvF8_usermeta` where meta_key like 'efb_%';

INSERT INTO `wpRuvF8_efb_badges` 
(`id`,`name`,`img`,`granted_permission`,`type`,`min_threshold`,`description`,`description_ar`,`title`,`title_ar`,`parent_id`)
VALUES 
(1,'suggestions_l1','http://egyptfoss.com/wp-content/themes/egyptfoss/img/badges/suggester.png','','action',1,'Suggested at least one contribution to a content section.','اقترح مساهمة واحدة على الأقل في أحد أقسام المحتوى','Suggester','مقترح',NULL),
(2,'request_center_l1','http://egyptfoss.com/wp-content/themes/egyptfoss/img/badges/requestcntr.png','','action',1,'Published at least one request in the Request Center.','نشر طلب واحد على الأقل في مركز الطلبات','Request Center Contributor','مساهم في مركز الطلبات',NULL),
(3,'expert','http://egyptfoss.com/wp-content/themes/egyptfoss/img/badges/expert.png','expert_thought__save','action',1,'Selected as an expert.','تم اختياره كخبير','Expert','خبير',NULL),
(4,'news_l1','http://egyptfoss.com/wp-content/themes/egyptfoss/img/badges/news_lvl1.png','','action',1,'Published at least one news post','نشر خبر واحد على الأقل','News Beginner','مبتدئ في الاخبار',NULL),
(5,'news_l2','http://egyptfoss.com/wp-content/themes/egyptfoss/img/badges/news_lvl2.png','news__publish','action',10,'Published a number of news posts','نشر عدد من الأخبار','News Specialist','متخصص في الاخبار',4),
(6,'success_story_l1','http://egyptfoss.com/wp-content/themes/egyptfoss/img/badges/story_lvl1.png','','action',1,'Published at least one success story','نشر قصة نجاح واحدة على الأقل','Stories Beginner','مبتدئ في قصص النجاح',NULL),
(7,'success_story_l2','http://egyptfoss.com/wp-content/themes/egyptfoss/img/badges/story_lvl2.png','success_story__publish','action',10,'Published a number of success stories','نشر عدد من قصص النجاح','Stories Specialist','متخصص في قصص النجاح',6),
(8,'tribe_events_l1','http://egyptfoss.com/wp-content/themes/egyptfoss/img/badges/event_lvl1.png','','action',1,'Published at least one event','نشر فعالية واحدة على الأقل','Events Beginner','مبتدئ في الفعاليات',NULL),
(9,'tribe_events_l2','http://egyptfoss.com/wp-content/themes/egyptfoss/img/badges/event_lvl2.png','tribe_events__publish','action',10,'Published a number of events','نشر عدد من الفعاليات','Events Specialist','متخصص في الفعاليات',8),
(10,'product_l1','http://egyptfoss.com/wp-content/themes/egyptfoss/img/badges/product_lvl1.png','','action',1,'Published at least one product','نشر منتج واحد على الأقل','Products Beginner','مبتدئ في المنتجات',NULL),
(11,'product_l2','http://egyptfoss.com/wp-content/themes/egyptfoss/img/badges/product_lvl2.png','product__publish','action',10,'Published a number of products','نشر عدد من المنتجات','Products Specialist','متخصص في المنتجات',10),
(12,'open_dataset_l1','http://egyptfoss.com/wp-content/themes/egyptfoss/img/badges/data_lvl1.png','','action',1,'Published at least one dataset','نشر حزمة واحدة من البيانات المفتوحة على الأقل','Open Data Beginner','مبتدئ في البيانات المفتوحة',NULL),
(13,'open_dataset_l2','http://egyptfoss.com/wp-content/themes/egyptfoss/img/badges/data_lvl2.png','open_dataset__publish','action',10,'Published a number of open datasets','نشر عدد من حزم البيانات المفتوحة','Open Data Specialist','متخصص في البيانات المفتوحة',12),
(14,'fosspedia_l1','http://egyptfoss.com/wp-content/themes/egyptfoss/img/badges/pedia.png','','action',1,'Contributed to FOSSpedia content','لديه مساهمة واحدة على الأقل في محتوى مركز المعرفة','Fosspedia Contributor','مساهم في مركز المعرفة',NULL),
(15,'collaboration_l1','http://egyptfoss.com/wp-content/themes/egyptfoss/img/badges/colbcntr.png','','action',1,'Contributed to the Collaboration Center content','لديه مساهمة واحدة على الأقل في محتوى مركز المشاركة','Collaboration Contributor','مساهم في مركز المشاركة',NULL),
(16,'service_provider','http://egyptfoss.com/wp-content/themes/egyptfoss/img/badges/provider_lvl1.png','','action',1,'Published at least one service in the Marketplace','نشر خدمة واحدة على الأقل في معرض الخدمات','Service Provider','مقدم خدمات',NULL),
(17,'top_provider','http://egyptfoss.com/wp-content/themes/egyptfoss/img/badges/provider_lvl3.png','','action',3,'Have a number of top services in the Marketplace','لديه عدد من الخدمات المتميزة في معرض الخدمات','Top Service Provider','مقدم خدمات متميز',16),
(18,'top_service','http://egyptfoss.com/wp-content/themes/egyptfoss/img/badges/provider_lvl2.png','','action',3,'Received a number of reviews and a high average rating','حاصلة على عدد من التقييمات ومتوسط تقييم مرتفع','Top Service','خدمة متميزة',16),
(19,'quiz_l1','http://egyptfoss.com/wp-content/themes/egyptfoss/img/badges/awareness_lvl1.png','','action',1,'Took at least one quiz in the Awareness Center','شارك في اختبار واحد على الأقل في مركز التقييم','FOSS Beginner','مبتدئ في البرمجيات الحرة',NULL),
(20,'quiz_l2','http://egyptfoss.com/wp-content/themes/egyptfoss/img/badges/awareness_lvl2.png','','action',5,'Achieved a high score in a number of quizzes the Awareness Center','حصل على نسبة مرتفعة في عدد من اختبارات مركز التقييم','FOSS Specialist','متخصص في البرمجيات الحرة',19);

INSERT INTO `wpRuvF8_efb_actions`(`id`,`name`,`post_type`,`post_status`,`description`,`is_point_granted`,`parent_id`) 
VALUES (1,'request_center_l1','request_center','publish','',0,NULL),
(2,'publish_news_l1','news','publish','',1,NULL),
(3,'publish_news_l2','news','publish','',1,2),
(4,'publish_success_story_l1','success_story','publish','',1,NULL),
(5,'publish_success_story_l2','success_story','publish','',1,4),
(6,'publish_tribe_events_l1','tribe_events','publish','',1,NULL),
(7,'publish_tribe_events_l2','tribe_events','publish','',1,7),
(8,'publish_product_l1','product','publish','',1,NULL),
(9,'publish_product_l2','product','publish','',1,8),
(10,'publish_open_dataset_l1','open_dataset','publish','',1,NULL),
(11,'publish_open_dataset_l2','open_dataset','publish','',1,10),
(12,'add_edit_fosspedia_l1','','','',0,NULL),
(13,'publish_collaboration_l1','','','',0,NULL),
(14,'publish_service','service','publish','',1,NULL),
(15,'take_quiz_l1','','','',0,NULL),
(16,'take_quiz_l2','','','',0,NULL),
(17,'suggestions_news','news','pending','',0,NULL),
(18,'suggestions_product','product','pending','',0,NULL),
(19,'suggestions_events','tribe_events','pending','',0,NULL),
(20,'suggestions_open_dataset','open_dataset','pending','',0,NULL),
(21,'suggestions_success','success_story','pending','',0,NULL);

INSERT INTO `wpRuvF8_efb_badges_actions`(`badge_id`,`action_id`) 
VALUES (2,1),(4,2),(5,3),(6,4),(7,5),
(8,6),(9,7),(10,8),(11,9),(12,10),
(13,11),(14,12),(15,13),(16,14),
(19,15),(20,16),(1,17),(1,18),
(1,19),(1,20),(1,21);

UPDATE `wpRuvF8_options` SET option_value = 'a:14:{i:0;s:21:"polylang/polylang.php";i:1;s:37:"egyptfoss-badges/egyptfoss-badges.php";i:2;s:29:"acf-repeater/acf-repeater.php";i:3;s:30:"advanced-custom-fields/acf.php";i:4;s:24:"buddypress/bp-loader.php";i:5;s:45:"disable-author-pages/disable-author-pages.php";i:6;s:41:"featured-galleries/featured-galleries.php";i:7;s:36:"quiz-master-next/mlw_quizmaster2.php";i:8;s:41:"sassy-social-share/sassy-social-share.php";i:9;s:41:"semantic-wordpress/semantic-wordpress.php";i:10;s:43:"the-events-calendar/the-events-calendar.php";i:11;s:24:"wordpress-seo/wp-seo.php";i:12;s:42:"wordpress-social-login/wp-social-login.php";i:13;s:29:"wp-mail-smtp/wp_mail_smtp.php";}' WHERE option_name = 'active_plugins';
