# UPDATE espace
SET @userID = (SELECT ID  FROM wpRuvF8_users where `user_email`='espace_1@espace.com.eg' limit 1);
update `wpRuvF8_usermeta` set meta_value = 'a:6:{s:4:"type";s:6:"Entity";s:8:"sub_type";s:8:"projects";s:13:"functionality";s:0:"";s:8:"industry";s:0:"";s:14:"ict_technology";s:0:"";s:18:"registeredNormally";i:1;}' where meta_key = 'registration_data' and user_id = (select @userID);

# UPDATE foss
SET @userID = (SELECT ID  FROM wpRuvF8_users where `user_email`='yomna.fahmy@espace.com.eg' limit 1);
INSERT INTO `wpRuvF8_usermeta` (user_id,meta_key,meta_value) VALUES ((select @userID),'registration_data','a:2:{s:4:"type";s:10:"Individual";s:8:"sub_type";s:14:"business-owner";}');