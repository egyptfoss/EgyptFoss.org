# Generated by DBDiff
# On 12/06/2016 03:40:47 pm

# Delete option to init the rewrite rules
DELETE from wpRuvF8_options  where option_name ='ef_custom_rewrite_rules_loaded';

# Update start of week WP settings
UPDATE wpRuvF8_options SET option_value = 6 WHERE option_name = 'start_of_week';