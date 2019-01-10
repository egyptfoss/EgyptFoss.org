<?php

add_action( 'wp_ajax_ef-bulk-download', 'ef_bulk_download' );
add_action( 'wp_ajax_nopriv_ef-bulk-download', 'ef_bulk_download' );

/**
 * 
 * @global type $extensions
 */
function ef_bulk_download() {
  $post_id = $_REQUEST['id'];
  $_wpnonce = $_REQUEST['_wpnonce'];
  
  if( !get_post( $post_id ) ) {
    // not found
    include( get_query_template( '404' ) );
    header( 'HTTP/1.0 404 Not Found' );
    exit;
  }
//  if( !wp_verify_nonce( $_wpnonce, 'ef-bulk-donwload' ) ) {
//    // access forbiden
//    wp_redirect( home_url( '/?status=403' ) );
//    exit;
//  }
  
  // get post resources
  // only include published resources
	$attachments_ids = get_post_meta($post_id, 'resources_ids', true);
	$attachments = explode("|||", $attachments_ids);
  
	$files = array();
	
	if ( $attachments ) {
		foreach ( $attachments as $attachment ) {
      // get resource URI
			$attachment_uri = get_attached_file( $attachment );
      
      array_push($files, $attachment_uri);
		}
	}
 
	if( !empty( $files ) ) {
    
    // get uploads directory info
    $uploads = wp_upload_dir();
  
    // output folder relative path
    $output_rpath = $uploads['basedir'] . '/zip-files';
    
    //$output_rpath = '../wp-content/uploads/zip-files';
    $zipfile_name = 'open-dataset-' . $post_id . '.zip';
    
		$output_zipfile = $output_rpath . '/' . $zipfile_name;
    
    // create "zipfiles" directory if not exists
    if ( !file_exists( $output_rpath ) ) {
        mkdir( $uploads['basedir'] . '/zip-files', 0777, true );
    }
    
    // file isn't created , create new zip files
    if ( !file_exists( $output_zipfile ) ) {
    
      $zip = new ZipArchive;

      // Opens a new zip archive for writing 
      $zip->open( $output_zipfile, ZipArchive::CREATE );

      foreach ( $files as $file ) {
        // Add a file to a ZIP archive using its contents.
        $zip->addFromString( basename( $file ),  file_get_contents( $file ) );
      }

      // Close opened or created archive and save change
      $zip->close();
    }
    
    // force downloading output
    header( $_SERVER["SERVER_PROTOCOL"] . " 200 OK" );
    header( "Cache-Control: public" ); // needed for internet explorer
    header( "Content-Type: application/zip" );
    header( "Content-Transfer-Encoding: Binary" );
    header( "Content-Length:" . filesize( $output_zipfile ) );
    header( "Content-Disposition: attachment; filename=open-dataset-" . time() . ".zip" );
    readfile( $output_zipfile );
    die();
	}
  else {
    // not found
    include( get_query_template( '404' ) );
    header( 'HTTP/1.0 404 Not Found' );
    exit;
  }
}
