<?php

namespace Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use User;
use Usermeta;
use Term;
use Post;
use Postmeta;
use EProducts;

class addOpendatasetsTypes extends Command {
  private $output;

  protected function configure() {
    $this->setName('datasettypes:migrate')
         ->setDescription('Add dataset types for migrated open datasets');
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    echo "Adding dataset types for migrated open datasets\n";
    $post = new Post();
    $post->insertOpendatasetsTypes();
  }
}