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

class LinkProductTrans extends Command {

    private $output;

    protected function configure() {
        $this->setName('LinkProductTrans:send')
                ->setDescription('link product translations');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->output = $output;
        $output->writeln("Retrieving External Products");
        $posts = Post::Where("post_type","=","product")->where("post_status","=","publish")->get();
        $output->writeln("start Linking Products");
        $output->writeln("---------------");
        foreach($posts as $post)
        {
          $post_translation_tax = array();
          $translation_post_id = $post->getPostTranslationId($post->ID,$post_translation_tax);  
          $post_translation_tax = unserialize($post_translation_tax);
          $post_lang  = array_search($post->ID, $post_translation_tax);
          $post->link_post_translation($post->ID, $post_lang, $translation_post_id,true);
          $output->writeln("Product ID #".$post->ID." linked with #".$translation_post_id);
        }
        $output->writeln("products Linked successfully");
        $output->writeln("---------------");  
    }
}