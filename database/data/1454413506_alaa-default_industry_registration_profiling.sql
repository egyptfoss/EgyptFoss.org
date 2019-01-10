INSERT INTO `wpRuvF8_users` VALUES ('','espace','$P$BxZyqjkzTmxqRfgF2eew15uY9sTp0G/','espace','espace_1@espace.com.eg','','2015-12-06 00:31:06','',0,'espace');
SET @userID = (SELECT ID  FROM wpRuvF8_users where `user_email`='espace_1@espace.com.eg' limit 1);
INSERT INTO `wpRuvF8_usermeta` VALUES ('',(select @userID),'nickname','espace');
INSERT INTO `wpRuvF8_usermeta` VALUES ('',(select @userID),'first_name','');
INSERT INTO `wpRuvF8_usermeta` VALUES ('',(select @userID),'last_name','');
INSERT INTO `wpRuvF8_usermeta` VALUES ('',(select @userID),'description','');
INSERT INTO `wpRuvF8_usermeta` VALUES ('',(select @userID),'rich_editing','true');
INSERT INTO `wpRuvF8_usermeta` VALUES ('',(select @userID),'comment_shortcuts','false');
INSERT INTO `wpRuvF8_usermeta` VALUES ('',(select @userID),'admin_color','fresh');
INSERT INTO `wpRuvF8_usermeta` VALUES ('',(select @userID),'use_ssl','0');
INSERT INTO `wpRuvF8_usermeta` VALUES ('',(select @userID),'show_admin_bar_front','true');
INSERT INTO `wpRuvF8_usermeta` VALUES ('',(select @userID),'registration_data','s:161:"a:6:{s:4:"type";s:6:"Entity";s:8:"sub_type";s:0:"";s:13:"functionality";s:0:"";s:8:"industry";s:0:"";s:14:"ict_technology";s:0:"";s:18:"registeredNormally";i:1;}";');
INSERT INTO `wpRuvF8_usermeta` VALUES ('',(select @userID),'wpRuvF8_capabilities','a:1:{s:13:\"administrator\";b:1;}');
INSERT INTO `wpRuvF8_usermeta` VALUES ('',(select @userID),'wpRuvF8_user_level','10');
INSERT INTO `wpRuvF8_usermeta` VALUES ('',(select @userID),'dismissed_wp_pointers','');
INSERT INTO `wpRuvF8_usermeta` VALUES ('',(select @userID),'show_welcome_panel','1');

