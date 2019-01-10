<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

trait EventTrait
{
    /**
     * @When I comment on the event with :arg1
     */
    public function iCommentOnTheEventWith($arg1)
    {
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
        $this->pressButton('SENT');
    }

    /**
     * @Then event comments counter should add more one
     */
    public function eventCommentsCounterShouldAddMoreOne()
    {
        $session = $this->getSession(); 
        $page = $session->getPage();
        $comment_number = $page->find( 'css', '.comments-number' );
        $x = $comment_number->getText();

        PHPUnit_Framework_Assert::assertEquals( $this->temp_var + 1, (int) $x );
    }
    
    }
