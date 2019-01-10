UPDATE `wpRuvF8_options` SET option_value='1' WHERE option_name= 'users_can_register';
INSERT INTO `wpRuvF8_terms` VALUES (NULL,'main_navigation','main_navigation',0,'المحرك الرئيسي');
SET @lastInsertedID = (select last_insert_id()); 
INSERT INTO `wpRuvF8_term_taxonomy` VALUES (NULL,(select @lastInsertedID),'nav_menu','',0,2);