<?php
use Symfony\Component\Console\Application;
use Commands\GreetCommand;
use Commands\SendNotifications;
use Commands\saveExternalProducts;
use Commands\LinkProductTrans;
use Commands\addOpendatasetsTypes;
use Commands\LinkSuccessTrans;
require __DIR__.'/vendor/autoload.php';

require 'settings.php';

$application = new Application();
$application->add(new SendNotifications());
$application->add(new saveExternalProducts());
$application->add(new LinkProductTrans());
$application->add(new \Commands\LinkSuccessTrans());
$application->add(new addOpendatasetsTypes());

$application->run();