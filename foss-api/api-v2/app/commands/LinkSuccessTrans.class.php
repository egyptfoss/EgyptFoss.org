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

class LinkSuccessTrans extends Command {

    private $output;

    protected function configure() {
        $this->setName('LinkSuccessTrans:start')
                ->setDescription('link Success stories translations');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->output = $output;
        $output->writeln("Retrieving Success stories");
        $posts = Post::Where("post_type","=","success_story")->where("post_status","=","publish")->get();
        $output->writeln("start Linking Success stories");
        $output->writeln("---------------");
        foreach($posts as $post)
        {
          $post_translation_tax = array();
          $translation_post_id = $post->getPostTranslationId($post->ID,$post_translation_tax);  
          $post_translation_tax = unserialize($post_translation_tax);
          $post_lang  = array_search($post->ID, $post_translation_tax);
          $post->link_post_translation($post->ID, $post_lang, $translation_post_id,true);
          $output->writeln("Success story ID #".$post->ID." linked with #".$translation_post_id);
        }
        $output->writeln("Success stories Linked successfully");
        $output->writeln("---------------");  
    }
}