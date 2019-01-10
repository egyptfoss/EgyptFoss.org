<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
trait OpenDatasetTrait
{    
    /**
     * @When I Add new open dataset with :arg1, :arg2, :arg3, :arg4, :arg5, :arg6, :arg7, :arg8, :arg9
     */
    public function iAddNewOpenDatasetWith($title, $publisher, $description, $type, $theme, $license, $hints, $references, $source_link) {
      $this->fillField('title', $title);
      $this->fillField('acf-field-publisher', $publisher);
      $this->fillField('acf-field-description', $description);
      $select = $this->fixStepArgument('acf-field-dataset_type');
      $option = $this->fixStepArgument($type);
      $this->getSession()->getPage()->selectFieldOption($select, $option);
      $select = $this->fixStepArgument('acf-field-theme');
      $option = $this->fixStepArgument($theme);
      $this->getSession()->getPage()->selectFieldOption($select, $option);
      $select = $this->fixStepArgument('acf-field-datasets_license');
      $option = $this->fixStepArgument($license);
      $this->getSession()->getPage()->selectFieldOption($select, $option);
      $this->fillField('acf-field-usage_hints', $hints);
      $this->fillField('acf-field-references', $references);
      $this->fillField('acf-field-source_link', $source_link);
      $js = <<<HEREDOC
      jQuery('.add-row-end')[0].click();
      jQuery('.add-file')[0].click(); 
      jQuery(document).find('.media-router a:last').click();  
HEREDOC;
      $this->getSession()->executeScript($js);
      $this->getSession()->wait(1000);
      
      $js = <<<HEREDOC
        
      jQuery(document).find("div.filename:contains('test.pdf')").click();
      jQuery(document).find('.media-button-select')[0].click(); 
HEREDOC;
      $this->getSession()->executeScript($js);
      $this->getSession()->wait(1000);
    }
    
    /**
     * @When I Add new frontend open dataset with :arg1, :arg2, :arg3, :arg4
     */
    public function iAddNewFrontendOpenDatasetWith($title, $publisher, $description, $references)
    {
        $this->fillField('open_dataset_title', $title);
        $this->fillField('open_dataset_publisher', $publisher);
        $this->fillField('open_dataset_description', $description);
        $this->fillField('open_dataset_references', $references);
    }
    
    /**
     * @When I Add new frontend open dataset with :arg1, :arg2,:arg3,:arg4,:arg5,:arg6,:arg7,:arg8,:arg9
     */
    public function iAddNewFrontendOpenDatasetWith2($title, $description, $publisher, $references, $source, $usage, $theme, $type, $license)
    {
        $this->fillField('open_dataset_title', $title);
        $this->fillField('open_dataset_publisher', $publisher);
        $this->fillField('open_dataset_description', $description);
        $this->fillField('open_dataset_references', $references);
        $this->fillField('open_dataset_source', $source);
        $this->fillField('open_dataset_usage', $usage);
        $select = $this->fixStepArgument('type');
        $option = $this->fixStepArgument($type);
        $this->getSession()->getPage()->selectFieldOption($select, $option);
        $select = $this->fixStepArgument('theme');
        $option = $this->fixStepArgument($theme);
        $this->getSession()->getPage()->selectFieldOption($select, $option);
        $select = $this->fixStepArgument('license');
        $option = $this->fixStepArgument($license);
        $this->getSession()->getPage()->selectFieldOption($select, $option);
    }
    
    /**
     * @When I comment on the dataset with :arg1
     */
    public function iCommentOnTheDatasetWith($arg1){
        $this->fillField( 'comment', $arg1 );
        $session = $this->getSession(); 
        $page = $session->getPage();
        $comment_number = $page->find( 'css', '.comments-number' );
        if ( !empty( $comment_number ) ) {
          $result = $comment_number->getText();
          $this->temp_var = (int) $result;
        }
        else{
          $this->temp_var = 0 ;
        }
        $this->pressButton('submit');
    }

    /**
     * @Then dataset comments counter should add more one
     */
    public function datasetCommentsCounterShouldAddMoreOne(){
        $session = $this->getSession(); 
        $page = $session->getPage();
        $comment_number = $page->find( 'css', '.comments-number' );
        $x = $comment_number->getText();
  
        PHPUnit_Framework_Assert::assertEquals( $this->temp_var + 1, (int) $x );
    }

}
