<?php
  require_once(ABSPATH . 'db-config.php');
  $settings = array(
    'driver' => constant('EF_DB_DRIVER'),
    'host' => constant('DB_HOST'),
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

  \Illuminate\Database\Eloquent\Model::setConnectionResolver($resolver);
