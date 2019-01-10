<?php

trait NewsTrait
{
    /**
     * @When I Add new news with :arg1, :arg2, :arg3
     */
    public function iAddNewsWith($title, $subtitle, $description)
    {
      $this->fillField('title', $title);
  		$this->fillField('acf-field-subtitle', $subtitle);  
      $js =  sprintf("for (var i = 0; i < tinyMCE.editors.length; i++) {
        if (tinyMCE.editors[i].id.indexOf(\"wysiwyg-acf-field-description\") > -1)
        {
          tinyMCE.editors[i].setContent('%s');
        }
        }",$description);
        $this->getSession()->executeScript($js);
  		//$this->pressButton('Save Changes');
    }
    
    /**
     * @When I Add new frontend news with :arg1, :arg2, :arg3
     */
    public function iAddNewFrontendNewsWith($title, $subtitle, $description)
    {
      $this->fillField('news_title', $title);
      $this->fillField('news_subtitle', $subtitle);
      $this->fillField('news_description', $description);
    }

    /**
     * @When I Add new frontend news with the folllowing :arg1, :arg2, :arg3, :arg4
     */

    public function iAddNewFrontendNewsWiththfollowing($title, $subtitle, $category, $description)
    {
      $this->fillField('news_title', $title);
      $this->fillField('news_subtitle', $subtitle);
      $this->fillField('news_category',$category);
      $this->fillField('news_description', $description);
    }
    
    /**
     * @Then :arg1 should be visible
     */
    public function shouldBeVisible($arg1)
    {
        $session = $this->getSession(); 
        $page = $session->getPage();
        if($arg1 == "Author Name")
        {                  
            $css = $page->find('css', '.news-author');
            if($css == null)
            {
                PHPUnit_Framework_Assert::assertEquals("Author name should exists", false);
                return;
            }
        }else if ($arg1 == "Post Date")
        {                  
            $css = $page->find('css', '.post-date');
            if($css == null)
            {
                PHPUnit_Framework_Assert::assertEquals("Post Date should exists", false);
                return;
            }
        }else if ($arg1 == "Thumbnail")
        {                  
            $css = $page->find('css', '.attachment-news-thumbnail');
            if($css == null)
            {
                PHPUnit_Framework_Assert::assertEquals("Thumbnail Image should exists", false);
                return;
            }
        }else if ($arg1 == "Title")
        {                  
            $css = $page->find('css', '.card-summary');
            if($css == null)
            {
                PHPUnit_Framework_Assert::assertEquals("Title should exists", false);
                return;
            }
        }else if ($arg1 == "Description")
        {                  
            $css = $page->find('css', '.card-summary');
            if($css == null)
            {
                PHPUnit_Framework_Assert::assertEquals("Description should exists", false);
                return;
            }
        }
        
        return false;
    }
    
    /**
     * @When I comment on the news with :arg1
     */
    public function iCommentOnTheNewsWith($arg1)
    {
        $this->fillField('comment', $arg1);
        $session = $this->getSession(); 
        $page = $session->getPage();
        $comment_number = $page->find('css', '.comments-number');
        if (!empty($comment_number)){
          $x = $comment_number->getText();
          $this->temp_var = (int)$x;
        }
        else{
          $this->temp_var = 0 ;
        }
        $this->pressButton('submit');
    }

    /**
     * @Then news comments counter should add more one
     */
    public function newsCommentsCounterShouldAddMoreOne()
    {
        $session = $this->getSession(); 
        $page = $session->getPage();
        $comment_number = $page->find('css', '.comments-number');
        $x = $comment_number->getText();

        PHPUnit_Framework_Assert::assertEquals($this->temp_var+1, (int) $x);
    }


    
}