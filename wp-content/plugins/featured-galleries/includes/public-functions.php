<?php

function get_post_gallery_ids( $postID = null, $maxImages = -1, $method = 'array' ) {

	global $post;

	// CHECK TO SEE IF AN ID HAS BEEN PASSED. IF NOT, LOAD THE ID FROM $POST, IF POSSIBLE

	if ( $postID == null ) {

		// IF NO ID HAS BEEN PASSED, CHECK TO SEE IF WE ARE IN THE LOOP AND HAVE A $post. IF
		// WE DO, THEN LOAD THE POST ID FROM THE CURRENT POST. IF NOT, RETURN AN ERROR.

		if ( $post !== null ) {

			$postID = $post->ID;

		} else {

			return $method == 'array' ? ['Error, requires a valid postID'] : 'Error, requires a valid postID';

		}

	}

	// CHECK TO SEE IF WE ARE IN A PREVIEW. IF SO, LOAD THE TEMP METADATA. IF NOT, LOAD THE
	// PERM METADATA

	if ( is_preview( $postID ) ) {

		$galleryString = get_post_meta( $postID, 'fg_temp_metadata', 1 );

	} else {

		$galleryString = get_post_meta( $postID, 'fg_perm_metadata', 1 );

	}

	// BREAK THE STRING INTO AN ARRAY TO COUNT THE NUMBER OF IDS THAT WE HAVE. THIS IS
	// REQUIRED BECAUSE WE HAVE TO RESPECT THE $maxImages PARAMETER.

	if ( $galleryString == '' ) {

		$galleryArray = [];

	} else {

		if ( $maxImages == -1 ) {

			$galleryArray = explode(',', $galleryString);

		} else {

			$galleryArray = array_slice(explode(',', $galleryString), 0, $maxImages);

		}

	}

	// REBUILD THE $galleryString VARIABLE NOW THAT WE HAVE CUT DOWN THE NUMBER OF
	// IMAGES BASED ON $maxImages.

	$galleryString = implode(',', $galleryArray);

	// CHECK THE $method PARAMETER. IF IT IS SET TO 'string', RETURN THE GALLERY IDS AS
	// A COMMA DELIMITED STRING. OTHERWISE, RETURN THE GALLERY IDS AS AN ARRAY.

	if ( $method == 'string' || $maxImages == 'string' ) {

		return $galleryString;

	} else {

		// RETURN ARRAY

		return $galleryArray;

	}

}