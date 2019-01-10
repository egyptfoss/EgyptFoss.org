<?php

$callingurl = strtolower( $_SERVER['REQUEST_URI'] ); // get the calling url

function wp_user_can($wpuser, $capability) {
	global $capabilities;
	$role = 'subscriber';
	$user_role = $wpuser->caps;
	if(reset($user_role) == true && array_key_exists(key($user_role), $capabilities)) {
		$role = key($user_role);
	}
	return (array_key_exists($role, $capabilities) && in_array($capability, $capabilities[$role])) ? true : false;
}

function wp_user_role($wpuser, $role) {
	global $capabilities;
	$user_role = $wpuser->caps;
	return $user_role[$role];
}

//call marmotta wiki
$config = parse_ini_file("../wp-content/plugins/semantic-wordpress/config.ini");
$wgHooks['PageContentSaveComplete'][] = function( $article, $user, $content, $summary, $isMinor, $isWatch, $section, $flags, $revision, $status, $baseRevId ){
        if(true){
            $current_user = get_user_by('email', $user->mEmail);
            // fosspedia badge
            load_orm();
            $fosspedia_badge = new Badge($current_user->ID, $user->user_email, $current_user->display_name, $current_user->user_nicename);
            $fosspedia_badge->efb_manage_fosspedia_badge();
            
            // send emails to fosspedia author with earned badges;
            foreach( $fosspedia_badge->badges_earned as $badge ) {
              global $wpdb;
              $query = "SELECT * FROM {$wpdb->base_prefix}efb_badges WHERE name = '{$badge->name}'";
              $result = $wpdb->get_results($query, ARRAY_A);

              if( class_exists( 'EFBBadges' ) && !empty( $result ) ) {
                sendNewBadgeAchiever( $current_user->ID, new EFBBadges( $result[0] ) );
              }
            }
            
            global $config;
            $wiki_title = $article->mTitle->mTextform;
            $wiki_content = $content->mText;
            $wiki_content = strip_tags($wiki_content,'<br>');
            
            $post_type = "pedia_en";
            if(pll_current_language() == "ar") {
                $post_type = "pedia_ar";
            }
            
            $page_id = $article->mTitle->mArticleID;
            
            // get enhancments from stanbol
            $stanbol_response = post_to_stanbol($wiki_title. ' '. $wiki_content);
            if($stanbol_response != '')
            {
                $subject = $stanbol_response['body'];

                //get contextId from stanbol response, and make a new tuple to link contextId and wordpress postId
                preg_match_all("/<[^>]*> <http:\/\/fise.iks-project.eu\/ontology\/extracted-from> (?P<contextId><[^>]*>)/", $subject, $output_array);

                $post_id_field = $output_array['contextId'][0] . ' <' . $config['marmotta_server'] . 'ontology/wp/post-id> "' . $page_id . '"^^<http://www.w3.org/2001/XMLSchema#integer> .';

                $post_type_field = $output_array['contextId'][0] . ' <' . $config['marmotta_server'] . 'ontology/wp/post-type> "' . $post_type . '"^^<http://www.w3.org/2001/XMLSchema#string> .';

                $post_title_field = $output_array['contextId'][0] . ' <' . $config['marmotta_server'] . 'ontology/wp/post-title> "' . $wiki_title . '"^^<http://www.w3.org/2001/XMLSchema#string> .';
            
                $post_description_field = $output_array['contextId'][0] . ' <' . $config['marmotta_server'] . 'ontology/wp/post-description> "' . $wiki_content . '"^^<http://www.w3.org/2001/XMLSchema#string> .';
                
                //append the new tuple to stanbol response
                $stanbol_response_augmented = $subject . ' ' . $post_id_field . ' ' . $post_type_field . ' ' . $post_title_field . ' ' . $post_description_field;

                //delete previous triples saved in marmotta
                $marmotaQuery = 'DELETE WHERE { 
                                        ?document ?post_id"'.$page_id.'" ^^xsd:integer .
                                        ?document ?post_type"' . $post_type .'"^^xsd:string .
                                   }';

                $encoded_query = urlencode($marmotaQuery);
                $marmotta_url = $config['marmotta_server'] . 'marmotta/sparql/update?query=' . $encoded_query . '&output=json';
                try
                {
                    $marmotta_response = wp_remote_post($marmotta_url, array(
                      'method' => 'GET',
                      'timeout' => $config['timeout']
                       )
                    );
                }catch(Exception $e) {}

                // save in marmota
                $marmotta_url = $config['marmotta_server'] . 'marmotta/import/upload';

                $marmotta_response = wp_remote_post($marmotta_url, array(
                  'method' => 'POST',
                  'headers' => array('Content-Type' => 'text/turtle'),
                  'body' => $stanbol_response_augmented
                        )
                );
            }
        }
};

$wgHooks['EditPageCopyrightWarning'][] = function( $title,&$message ){
  $message[1] = str_replace("]]", "]", $message[1]) ;
  $message[1] = str_replace("[[", "[", $message[1]) ;
  $message[1] = str_replace("[", "[".  site_url(pll_current_language().'/terms-of-services')." ", $message[1]) ;
};

if ( strpos( $callingurl, '/ar' )  === 0 || ( isset( $_GET["uselang"] ) && $_GET["uselang"] == "ar" ) ) {
        require_once 'LocalSettingsArabic.php';
} else if ( strpos( $callingurl, '/en' )  === 0 ) {
        require_once 'LocalSettingsEnglish.php';
} else {
        require_once 'LocalSettingsEnglish.php';
}

Hooks::register( 'ThumbnailBeforeProduceHTML', 'ef_wiki_register_attrs_hook' );

function ef_wiki_register_attrs_hook( $this, &$attribs, &$linkAttribs ) {
  $attribs['srcset'] = array();
}