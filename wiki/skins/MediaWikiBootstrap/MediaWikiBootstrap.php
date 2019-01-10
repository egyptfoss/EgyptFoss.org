<?php
/**
 * MediaWikiBootstrap Skin
 *
 * @file
 * @ingroup Skins
 * @author Nasir Khan Saikat http://nasirkhn.com
 */

if ( !defined( 'MEDIAWIKI' ) ) {
        die( 'This is an extension to the MediaWiki package and cannot be run standalone.' );
}

$wgExtensionCredits['skin'][] = array(
        'path'          => __FILE__,
        'name'          => 'MediaWikiBootstrap', 
        'namemsg'       => 'skinname-mediawikibootstrap', 
        'version'       => '1.1.0',
        'url'           => 'https://www.mediawiki.org/wiki/Skin:MediaWikiBootstrap',
        'author'        => '[https://mediawiki.org/wiki/User:nasirkhan Nasir Khan Saikat]',
        'descriptionmsg'=> 'mediawikibootstrap-desc',
        'license'       => 'GPL-2.0+',
);


$wgValidSkinNames['mediawikibootstrap']         = 'MediaWikiBootstrap';
$wgAutoloadClasses['SkinMediaWikiBootstrap']    = __DIR__ . '/MediaWikiBootstrap.skin.php';
$wgMessagesDirs['MediaWikiBootstrap']           = __DIR__ . '/i18n';

 
$wgResourceModules['skins.mediawikibootstrap'] = array(
        'styles' => array(
                'MediaWikiBootstrap/css/bootstrap.min.css'      => array( 'media' => 'screen' ),
                'MediaWikiBootstrap/css/font-awesome.min.css'   => array( 'media' => 'screen' ),
                'MediaWikiBootstrap/css/screen.css'             => array( 'media' => 'screen' ),
                'MediaWikiBootstrap/css/style.css'             => array( 'media' => 'screen' ),
                'MediaWikiBootstrap/css/print.css'              => array( 'media' => 'print' ),
	),
	'scripts' => array(
		'MediaWikiBootstrap/js/bootstrap.min.js',
		'MediaWikiBootstrap/js/mediawikibootstrap.js',
	),
        'remoteBasePath'        => &$GLOBALS['wgStylePath'],
        'localBasePath'         => &$GLOBALS['wgStyleDirectory'],
);


// # Default options to customize skin
 $wgMediaWikiBootstrapSkinLoginLocation = 'footer';
 $wgMediaWikiBootstrapSkinAnonNavbar = false;
 $wgMediaWikiBootstrapSkinUseStandardLayout = false;
 $wgMediaWikiBootstrapSkinDisplaySidebarNavigation = false;
// # Show print/export in navbar by default
 $wgMediaWikiBootstrapSkinSidebarItemsInNavbar = array( 'coll-print_export' );
