<?php

require 'vendor/autoload.php';
include '../../system_data.php';
include 'app/helpers/validation_helper.php';
include 'app/helpers/EgyptFOSS_helper.php';
include_once('../../db-config.php');
#include_once "libs/gettext/src/autoloader.php";
#include_once "libs/cldr-to-gettext-plural-rules/src/autoloader.php";

// Database information
$settings = array(
    'driver' => constant('EF_DB_DRIVER'),
    'host' => constant('EF_DB_HOST'),
    'database' => constant('DB_NAME'),
    'username' => constant('DB_USER'),
    'password' => constant('DB_PASSWORD'),
    'charset'   => constant('EF_DB_CHARSET'),
    'collation' => constant('EF_DB_COLLATION'),
    'prefix' => constant('EF_DB_PREFIX')
);

// Bootstrap Eloquent ORM
$container = new Illuminate\Container\Container;
$connFactory = new \Illuminate\Database\Connectors\ConnectionFactory($container);
$conn = $connFactory->make($settings);
$resolver = new \Illuminate\Database\ConnectionResolver();
$resolver->addConnection('default', $conn);
$resolver->setDefaultConnection('default');

// Meidawiki Database information
$mw_settings = array(
    'driver' => constant('EF_DB_DRIVER'),
    'host' => constant('PEDIA_DB_HOST'),
    'database' => constant('PEDIA_DB_NAME'),
    'username' => constant('PEDIA_DB_USER'),
    'password' => constant('PEDIA_DB_PASSWORD'),
    'charset'   => constant('EF_DB_CHARSET'),
    'collation' => constant('EF_DB_COLLATION'),
    'prefix' => constant('PEDIA_DB_PREFIX_EN')
);

$mw_conn = $connFactory->make($mw_settings);
$resolver->addConnection('mediawiki', $mw_conn);

$mw_ar_settings = array(
    'driver' => constant('EF_DB_DRIVER'),
    'host' => constant('PEDIA_DB_HOST'),
    'database' => constant('PEDIA_DB_NAME'),
    'username' => constant('PEDIA_DB_USER'),
    'password' => constant('PEDIA_DB_PASSWORD'),
    'charset'   => constant('EF_DB_CHARSET'),
    'collation' => constant('EF_DB_COLLATION'),
    'prefix' => constant('PEDIA_DB_PREFIX_AR')
);

$mw_ar_conn = $connFactory->make($mw_ar_settings);
$resolver->addConnection('mediawikiar', $mw_ar_conn);

$external_conn_settings = array(
    'driver' => 'mysql',
    'host' => '',
    'database' => '',
    'username' => '',
    'password' => '',
    'charset'   => 'utf8',
    'collation' => 'utf8_general_ci',
    'prefix' => ''
);

$external_conn= $connFactory->make($external_conn_settings);
$resolver->addConnection('externalConnectionProducts', $external_conn);


\Illuminate\Database\Eloquent\Model::setConnectionResolver($resolver);

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];
$c = new \Slim\Container($configuration);
$app = new \Slim\App($c);

$app->db = $conn;

define('SENDER_EMAIL',"testapp@espace.ws");
//error_reporting(-1); ini_set('display_errors', 'On');

// define if version deprecated
define('is_deprecated', false);

// set timezone to work same as WP
$option = new Option();
$timeZone = $option->getOptionValueByKey('timezone_string');
date_default_timezone_set($timeZone);
