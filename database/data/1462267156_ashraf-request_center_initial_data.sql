INSERT INTO `wpRuvF8_terms` VALUES (NULL,'service request','service-request','0','service request');
SET @lastInsertedID = (select last_insert_id());
INSERT INTO `wpRuvF8_term_taxonomy` VALUES (NULL,(select @lastInsertedID),'request_center_type','','0','0');

INSERT INTO `wpRuvF8_terms` VALUES (NULL,'product request','product-request','0','product request');
SET @lastInsertedID = (select last_insert_id());
INSERT INTO `wpRuvF8_term_taxonomy` VALUES (NULL,(select @lastInsertedID),'request_center_type','','0','0');

INSERT INTO `wpRuvF8_terms` VALUES (NULL,'dataset request','dataset-request','0','dataset request');
SET @lastInsertedID = (select last_insert_id());
INSERT INTO `wpRuvF8_term_taxonomy` VALUES (NULL,(select @lastInsertedID),'request_center_type','','0','0');

INSERT INTO `wpRuvF8_terms` VALUES (NULL,'resource request','resource-request','0','resource request');
SET @lastInsertedID = (select last_insert_id());
INSERT INTO `wpRuvF8_term_taxonomy` VALUES (NULL,(select @lastInsertedID),'request_center_type','','0','0');

INSERT INTO `wpRuvF8_terms` VALUES (NULL,'business relationship request','business-relationship-request','0','business relationship request');
SET @lastInsertedID = (select last_insert_id());
INSERT INTO `wpRuvF8_term_taxonomy` VALUES (NULL,(select @lastInsertedID),'request_center_type','','0','0');

INSERT INTO `wpRuvF8_terms` VALUES (NULL,'support request','support-request','0','support request');
SET @lastInsertedID = (select last_insert_id());
INSERT INTO `wpRuvF8_term_taxonomy` VALUES (NULL,(select @lastInsertedID),'request_center_type','','0','0');
# 
INSERT INTO `wpRuvF8_terms` VALUES (NULL,'commercial agreement','commercial-agreement','0','commercial agreement');
SET @lastInsertedID = (select last_insert_id());
INSERT INTO `wpRuvF8_term_taxonomy` VALUES (NULL,(select @lastInsertedID),'target_bussiness_relationship','','0','0');

INSERT INTO `wpRuvF8_terms` VALUES (NULL,'license agreement','license-agreement','0','license agreement');
SET @lastInsertedID = (select last_insert_id());
INSERT INTO `wpRuvF8_term_taxonomy` VALUES (NULL,(select @lastInsertedID),'target_bussiness_relationship','','0','0');

INSERT INTO `wpRuvF8_terms` VALUES (NULL,'technical cooperation','technical-cooperation','0','technical cooperation');
SET @lastInsertedID = (select last_insert_id());
INSERT INTO `wpRuvF8_term_taxonomy` VALUES (NULL,(select @lastInsertedID),'target_bussiness_relationship','','0','0');

INSERT INTO `wpRuvF8_terms` VALUES (NULL,'joint venture agreement','joint-venture-agreement','0','joint venture agreement');
SET @lastInsertedID = (select last_insert_id());
INSERT INTO `wpRuvF8_term_taxonomy` VALUES (NULL,(select @lastInsertedID),'target_bussiness_relationship','','0','0');