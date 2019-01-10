<?php
    require_once(dirname($_SERVER['PWD']) . '/db-config.php');
    $settings = array(
        'driver' => constant('EF_DB_DRIVER'),
        'host' => constant('EF_DB_HOST'),
        'database' => constant('TESTING_DB_NAME'),
        'username' => constant('TESTING_DB_USER'),
        'password' => constant('TESTING_DB_PASSWORD'),
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
        'host' => constant('EF_DB_HOST'),
        'database' =>  constant('TESTING_PEDIA_DB_NAME'),
        'username' =>  constant('TESTING_PEDIA_DB_USER'),
        'password' =>  constant('TESTING_PEDIA_DB_PASSWORD'),
        'charset'   => constant('EF_DB_CHARSET'),
        'collation' => constant('EF_DB_COLLATION'),
        'prefix' => constant('TESTING_PEDIA_DB_PREFIX'),
    );

    $mw_conn = $connFactory->make($mw_settings);
    $resolver->addConnection('mediawiki', $mw_conn);
    
    \Illuminate\Database\Eloquent\Model::setConnectionResolver($resolver);
