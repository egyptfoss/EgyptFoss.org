<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

trait ShareTrait {
    
    /**
     * @When I follow :arg1 share
     */
    public function iFollowShare($arg1)
    {
        $cssClass = "";
        $indexOf = "https";
        if($arg1 == "Facebook")
        {
            $cssClass = "heateorSssFacebookBackground";
        }else if($arg1 == "Twitter")
        {
            $indexOf = "http";
            $cssClass = "heateorSssTwitterBackground";
        }else if($arg1 == "LinkedIn")
        {
            $indexOf = "http";
            $cssClass = "heateorSssLinkedinBackground";
        }else if($arg1 == "Google+")
        {
            $cssClass = "heateorSssGoogleplusBackground";
        }
        
        $js = "var url = document.getElementsByClassName('".$cssClass."')[0].getAttribute('onclick'); "
                . "url = url.substring(url.indexOf('".$indexOf."'));url = url.substring(0, url.length-2);"
                . "window.open(url);";
        
        $this->getSession()->executeScript($js);
        $this->getSession()->wait(5000);
    }
    
    /**
     * @When I share the news on :arg1
     */
    public function iShareTheNewsOn($arg1)
    {
        if($arg1 == "Google+")
        {
            //submit with js
            $js  = 'try{ var event = document.createEvent("HTMLEvents");';
            $js .= "var element = document.querySelector('[guidedhelpid=\"sharebutton\"]');";
            $js .= 'element.click(); }catch(err){}';  
            $this->getSession()->executeScript($js);
            //$this->getSession()->wait(5000);
        }else if($arg1 == "LinkedIn")
        {
            $js = "try{ document.getElementsByClassName('btn-primary')[1].click(); }catch(err){}";
            $this->getSession()->executeScript($js);
            $this->getSession()->wait(3000);
        }
    }
    
    /**
     * @Given I login to facebook
     */
    public function iLoginToFacebook()
    {
        /*$result =  $this->getSession()->evaluateScript(
            "return (function(){ var myLatLng = {lat: 29.979029278343695, lng: 31.043819040355633};var marker = new google.maps.Marker({ position: myLatLng }); return map.getBounds().contains(marker.getPosition()); })()"
        );
        
        var_dump($result);
*/
        
        
        //Mozilla/5.0 (Macintosh; Intel Mac OS X 10.9; rv:36.0) Gecko/20100101 Firefox/36.0 WebKit
        $this->getSession()->visit("https://www.facebook.com/index.php");

        //Wait for rediretion
        self::waitForRedirection();
        
        $this->fillField('email', 'buggy.tamtam1@gmail.com');
        $this->fillField('pass', 'buggy123');
        
        $js = "try{ document.getElementById('loginbutton').click(); }catch(err){}";
        $this->getSession()->executeScript($js);
        $this->getSession()->wait(3000);
        
        //Wait for rediretion
        self::waitForRedirection();
    }

}