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

class saveExternalProducts extends Command {

    private $output;

    protected function configure() {
        $this->setName('externalproducts:send')
                ->setDescription('Save External Products');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->output = $output;
        $output->writeln("Deleting Existing Products");
        $post = new Post();
        $post->deleteAllProducts();
        $output->writeln("Deleted All Published Products");
        $output->writeln("Retrieving External Products");
        $external_products = EProducts::whereNotNull('unique_category')
                ->where('unique_category','!=', '')
                ->whereNotNull('ar_title')
                ->where('ar_title','!=', '')
                ->where('is_merged','=',0)->get();
        
        //$external_products = EProducts::whereNotNull('unique_category')->whereNotNull('ar_title')->get();        
        $output->writeln("Retrieved ".sizeof($external_products)." Products");
        $user_id = 1;        
        //for($i = 0; $i < sizeof($external_products); $i++)
        for($i = 0; $i < sizeof($external_products); $i++)
        {
            $output->writeln("Start Saving Product #".$i);
            //check unique product name
            if (!empty(Post::where('post_title' , '=', $external_products[$i]->title)->where('post_type' , '=', 'product')->first())) {
                $output->writeln("Product #".$i." not added because it already exists");
                $output->writeln("---------------");
                continue;
            }
            $industry = null;
            if($external_products[$i]->unique_category != null)
            {
                $industry = Term::join('term_taxonomy','term_taxonomy.term_id','=','terms.term_id')
                    ->where('name','=',  htmlentities ($external_products[$i]->unique_category))
                    ->where('term_taxonomy.taxonomy','=','industry')
                    ->first();
                
                if($industry == null || !$industry)
                {
                    $output->writeln("Product #".$i." not added because industry not found");
                    $output->writeln("---------------");
                    continue;
                }
            }
            
            $type = null;
            if($external_products[$i]->product_type != null)
            {
                $type = Term::join('term_taxonomy','term_taxonomy.term_id','=','terms.term_id')
                    ->where('name','=',$external_products[$i]->product_type)
                    ->where('term_taxonomy.taxonomy','=','type')
                    ->first();
            }
            //icon
            $icon_url = $external_products[$i]->icon;
            if($icon_url != ""){
                $icon_url = str_replace ("//", 'http://', $icon_url);
            }
            else {
                $icon_url = '';
            }
            
            //screenshots
            $en_description = strip_tags(str_replace("More Info »","",$external_products[$i]->long_description),"<br>");
            //$en_description = substr($en_description, 1);
            //$en_description = substr($en_description, 0, -1);
            $en_description = trim($en_description);
            $screenshots = explode(',',  $external_products[$i]->screenshots);
            $args_en = array(
                'user_id' => $user_id,
                'post_title' => $external_products[$i]->title,
                'lang'  => 'en',
                'post_status'  => 'publish',
                'link_to_source' => ($external_products[$i]->website != null)?$external_products[$i]->website:'',
                'post_industry' => ($industry != null)?$industry->name:'',
                'description' => ($external_products[$i]->long_description != null)?$en_description:'',
                'developer' => ($external_products[$i]->developer != null)?$external_products[$i]->developer:'',
                'post_type' => ($type != null)?$type->name:'',
                'shouldCheck'   => false,
                'uploadIcon'   => true,
                'uploadScreenshots' => true,
                'icon' => $icon_url,
                'screenshots' => $screenshots,
                'license'   => '',
                'technology'  => '',
                'platform' => '',
                'interest' => '',
                'functionality' => '',
                'usage_hints'   => '',
                'references'   => ''
            );
           
            $post_addProduct_en = new Post();
            $returnPOstID_en = $post_addProduct_en->addExternalProduct($args_en);
            $returnPOstID_en = explode('|',$returnPOstID_en);
            $output->writeln("Saved English Product #".$i);
            if($external_products[$i]->ar_title != null)
            {
                $args_ar = array(
                    'user_id' => $user_id,
                    'post_title' => $external_products[$i]->ar_title,
                    'lang'  => 'ar',
                    'post_status'  => 'publish',
                    'link_to_source' => ($external_products[$i]->website != null)?$external_products[$i]->website:'',
                    'post_industry' => ($industry != null)?$industry->name:'',
                    'description' => ($external_products[$i]->ar_description != null)?strip_tags($external_products[$i]->ar_description,"<br>"):'',
                    'developer' => ($external_products[$i]->developer_ar != null)?$external_products[$i]->developer_ar:'',
                    'post_type' => ($type != null)?$type->name:'',
                    'screenShots' => $external_products[$i]->screenshots,
                    'icon' => $icon_url,
                    'shouldCheck'   => false,
                    'uploadIcon'   => false,
                    'uploadScreenshots' => false,
                    'screenshots_id' => $returnPOstID_en[1],
                    'icon_id' => $returnPOstID_en[2],
                    'license'   => '',
                    'technology'  => '',
                    'platform' => '',
                    'interest' => '',
                    'functionality' => '',
                    'usage_hints'   => '',
                    'references'   => ''
                );

                $post_addProduct_ar = new Post();
                $returnPOstID_ar = $post_addProduct_ar->addExternalProduct($args_ar);
                $output->writeln("Saved Arabic Product #".$i);
                //link translated Products
                $post_link = new Post();
                $post_link->link_post_translation($returnPOstID_en[0],'en',$returnPOstID_ar);
                $output->writeln("Linked Both Products");
            }
            $output->writeln("---------------");
            
            //update is_merged to 1
            $update_merged = array(
              'is_merged'  => 1
            );
            EProducts::where('id', '=', $external_products[$i]->id)->update($update_merged);
        }
    }
}