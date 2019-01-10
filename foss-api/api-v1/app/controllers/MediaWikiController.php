<?php

class MediaWikiController extends EgyptFOSSController {

  /**
   * @SWG\Get(
   *   path="/wiki/{language}/pages/{pageName}",
   *   tags={"FOSSPedia"},
   *   summary="Finds Wiki Page",
   *   description= "Get a page by its name from either english or arabic wiki based on the passed language",
   *   @SWG\Parameter(name="language", in="path",type="string", description="Wiki Page language <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="pageName", in="path",type="string", description="Page Name [case insensetive] <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="textFormat", in="query",type="string", description="Return Format <br/> <b>Values:</b> wiki or html [wiki by default]"), 
   *   @SWG\Response(response="200", description="wiki page returned successfully Or Validation Error"),
   *   @SWG\Response(response="404",description="Wiki page not found")
   * )
   */
  public function getPage($request, $response, $args) {
    if (!isset($args["language"]) || $args["language"] == "" || $args["language"] == "{language}") {
      return $this->renderJson($response, 200, Messages::getErrorMessage("missingValue", "language"));
    }
    if (!in_array($args["language"], array("en", "ar"))) {
      return $this->renderJson($response, 200, Messages::getErrorMessage("wrong", "language"));
    }

    if (isset($_GET["textFormat"]) && ($_GET["textFormat"] != "wiki" && $_GET["textFormat"] != "html")) {
      return $this->renderJson($response, 200, Messages::getErrorMessage("wrong", "textFormat"));
    } else {
      if (!isset($_GET["textFormat"])) {
        $args["textFormat"] = "wiki";
      } else {
        $args["textFormat"] = $_GET["textFormat"];
      }
    }

    $pageModel = new MwPage($args["language"]);

    //urldecode pageName
    $args["pageName"] = urldecode($args["pageName"]);
    if (!isset($args["pageName"]) || $args["pageName"] == "" || $args["pageName"] == "{pageName}") {
      //return MediaWiki default page
      $pageName = "Mainpage";
      $defaultPage = $pageModel->getMainPage($pageName);
      if (!$defaultPage || $defaultPage == null) {
        //check main_page in both arabic or english version
        $pageName = "Main_Page";
        if ($args["language"] == "ar") {
          $pageName = "الصفحة_الرئيسية";
        }

        $defaultPage = $pageModel->getMainPage($pageName);
        //check if page redirects to another page
        if (strpos($defaultPage, '#REDIRECT') !== false ||
          strpos($defaultPage, '#تحويل_') !== false) {
          $defaultPage = str_replace(" ", "_", trim(self::get_string_between($defaultPage, "[[", "]]")));
        } else {
          $defaultPage = $defaultPage->page_title;
        }
        $args["pageName"] = $defaultPage;
      } else {
        $defaultPage = str_replace(" ", "_", trim($defaultPage->old_text));
        $args["pageName"] = $defaultPage;
      }
    } else {
      $args["pageName"] = str_replace(" ", "_", trim($args["pageName"]));
    }
    $page = $pageModel->getPage($args["pageName"]);
    if ($page) {
      //get revision id
      $revisionModel = new MwRevision($args["language"]);
      $revision = $revisionModel->getRevision($page->page_latest, $page->page_id);
      if (!$revision) {
        return $this->renderJson($response, 404, Messages::getErrorMessage("wikiPageNotFound"));
      }
      $textModel = new MwText($args["language"]);
      $pageContent = $textModel->getText($revision->rev_text_id);
      if (!$pageContent) {
        return $this->renderJson($response, 404, Messages::getErrorMessage("wikiPageNotFound"));
      }

      $langLinks = array();
      if ($args["textFormat"] == "html") {
        $siteUrl = Option::where("option_name", "=", "siteurl")->first()->option_value;
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_RETURNTRANSFER => 1,
          CURLOPT_URL => $siteUrl . "/wiki/api.php?action=parse&page={$args["pageName"]}&uselang={$args["language"]}&disableeditsection=true&format=json",
          CURLOPT_USERAGENT => "curl"  
            ));
        $result = curl_exec($curl);
        curl_close($curl);
        $text = json_decode($result)->parse->text->{'*'};
        $pageContent = ($text) ? $text : $pageContent;
        if (sizeof(json_decode($result)->parse->langlinks) > 0) {
          foreach (json_decode($result)->parse->langlinks as $langLink) {
            if ($langLink->lang == "ar" || $langLink->lang == "en") {
              $langLinks = array(
                'lang' => $langLink->lang,
                'url' => $langLink->url
              );
            }
          }
        }

        //check images full path
        if (sizeof(json_decode($result)->parse->images) > 0) {
          foreach (json_decode($result)->parse->images as $image) {
            //using API
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_RETURNTRANSFER => 1,
              CURLOPT_URL => $siteUrl . "/wiki/api.php?action=query&titles=File:{$image}&prop=imageinfo&iiprop=timestamp|user|url&format=json&uselang={$args["language"]}",
            ));
            $result = curl_exec($curl);
            curl_close($curl);
            $image_info = json_decode($result)->query->pages;

            $first_key = key($image_info);
            $descriptionUrl = $url = "";
            if ($first_key != -1 && isset(json_decode(json_encode($image_info), true)[$first_key]["imageinfo"]) && sizeof(json_decode(json_encode($image_info), true)[$first_key]["imageinfo"]) > 0) {
              //replace with full url
              $descriptionUrl = json_decode(json_encode($image_info), true)[$first_key]["imageinfo"][0]["descriptionurl"];
              $url = json_decode(json_encode($image_info), true)[$first_key]["imageinfo"][0]["url"];

              $doc = new DOMDocument();

              $doc->loadHTML(mb_convert_encoding($pageContent, 'HTML-ENTITIES', 'UTF-8'));
              $xp = new DOMXpath($doc);
              //replace a href
              $nodes = @$xp->query('//a[contains(@href, ' . urlencode($image) . ')]');
              if ($nodes) {
                $node = $nodes->item(0);
                if ($node != NULL) {
                  $node->setAttribute("href", $descriptionUrl);
                  $pageContent = $doc->saveHTML();
                  $pageContent = preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $pageContent);
                }
              }

              //replace img src
              $nodes = @$xp->query('//img[contains(@src, ' . urlencode($image) . ')]');
              if ($nodes) {
                $node = $nodes->item(0);
                if ($node != NULL) {
                  $node->setAttribute("src", $url);
                  $pageContent = $doc->saveHTML();
                  $pageContent = preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $pageContent);
                }
              }
            } else {
              $doc = new DOMDocument();

              $doc->loadHTML(mb_convert_encoding($pageContent, 'HTML-ENTITIES', 'UTF-8'));
              $xp = new DOMXpath($doc);
              //remove empty images
              $nodes = @$xp->query('//a[contains(@href, \'' . $image . '\')]');
              if ($nodes) {
                $node = $nodes->item(0);
                if ($node != NULL) {
                  $node->parentNode->removeChild($node);
                  $pageContent = $doc->saveHTML();
                  $pageContent = preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $pageContent);
                }
              }
            }

            //a attribute href change
            /* $pageContent = str_replace("/{$args["language"]}/wiki/File:$image", "$siteUrl/{$args["language"]}/wiki/File:$image", $pageContent);

              //img src change
              $doc = new DOMDocument();

              $doc->loadHTML(mb_convert_encoding($pageContent, 'HTML-ENTITIES', 'UTF-8'));
              $xp = new DOMXpath($doc);
              $nodes = @$xp->query('//img[contains(@src, '.urlencode($image).')]');
              if($nodes)
              {
              $node = $nodes->item(0);
              if($node != NULL)
              {
              $src_attr = $node->getAttribute("src");
              $node->setAttribute("src", $siteUrl.$src_attr);
              $pageContent = $doc->saveHTML();
              $pageContent = preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $pageContent);
              }
              } */
          }
        }
      }

      $responseArray = array(
        "page_title" => $page->page_title,
        "page_content" => $pageContent,
        "page_language_links" => $langLinks
      );
      return $this->renderJson($response, 200, $responseArray);
    } else {
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound"));
    }
  }

  /**
   * @SWG\Get(
   *   path="/wiki/{language}/pages/{pageName}/versions",
   *   tags={"FOSSPedia"},
   *   summary="Finds Wiki Page Versions",
   *   description= "Get the history of a page by its name from either english or arabic wiki based on the passed language",
   *   @SWG\Parameter(name="language", in="path",type="string", description="Wiki Page language <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="pageName", in="path",type="string", description="Page Name [case insensetive] <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="History of wiki page returned successfully"),
   *   @SWG\Response(response="404", description="Wiki page not found")
   * )
   */
  public function getPageHistory($request, $response, $args) {
    if (!isset($args["language"]) || $args["language"] == "" || $args["language"] == "{language}") {
      return $this->renderJson($response, 200, Messages::getErrorMessage("missingValue", "language"));
    }
    if (!in_array($args["language"], array("en", "ar"))) {
      return $this->renderJson($response, 200, Messages::getErrorMessage("wrong", "language"));
    }
    if (!isset($args["pageName"]) || $args["pageName"] == "" || $args["pageName"] == "{pageName}") {
      return $this->renderJson($response, 200, Messages::getErrorMessage("missingValue", "pageName"));
    }
    $pageModel = new MwPage($args["language"]);
    $page = $pageModel->getPage($args["pageName"]);

    if ($page) {
      $revisionModel = new MwRevision($args["language"]);
      $revisions = $revisionModel->getPageRevisions($page->page_id);
      $revisionsResponse = $this->ef_load_data_counts(sizeof($revisions->get()), -1);
      $index = 0;
      foreach ($revisions->get() as $revision) {
        $revisionsResponse['data'][$index] = array(
          "revision_id" => $revision->rev_id,
          "revision_comment" => $revision->rev_comment,
          "revision_user" => $revision->rev_user,
          "revision_user_text" => $revision->rev_user_text,
          "revision_timestamp" => $revision->rev_timestamp
        );

        $index += 1;
      }
      return $this->renderJson($response, 200, $revisionsResponse);
    } else {
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound"));
    }
  }

  /**
   * @SWG\Get(
   *   path="/wiki/{language}/pages/{pageName}/versions/{versionNumber}",
   *   tags={"FOSSPedia"},
   *   summary="Finds a Version of Wiki Page",
   *   description="Get a specific version of a page by its name and revision number from either english or arabic wiki based on the passed language",
   *   @SWG\Parameter(name="language", in="path",type="string", description="Wiki Page language <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="pageName", in="path",type="string", description="Page Name [case insensetive] <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="versionNumber", in="path",type="integer", description="Version Number returned from Finds Wiki Page Versions <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="textFormat", in="query",type="string", description="Return Format <br/> <b>Values:</b> wiki or html [wiki by default]"),  
   *   @SWG\Response(response="200", description="returns a specific version for a wiki page or validation error"),
   *   @SWG\Response(response="404", description="Wiki page not found or wiki revision not found")
   * )
   */
  public function getPageVersion($request, $response, $args) {
    if (!isset($args["language"]) || $args["language"] == "" || $args["language"] == "{language}") {
      return $this->renderJson($response, 200, Messages::getErrorMessage("missingValue", "language"));
    }
    if (!in_array($args["language"], array("en", "ar"))) {
      return $this->renderJson($response, 200, Messages::getErrorMessage("wrong", "language"));
    }
    if (!isset($args["pageName"]) || $args["pageName"] == "" || $args["pageName"] == "{pageName}") {
      return $this->renderJson($response, 200, Messages::getErrorMessage("missingValue", "pageName"));
    }

    if (isset($_GET["textFormat"]) && ($_GET["textFormat"] != "wiki" && $_GET["textFormat"] != "html")) {
      return $this->renderJson($response, 200, Messages::getErrorMessage("wrong", "textFormat"));
    } else {
      if (!isset($_GET["textFormat"])) {
        $args["textFormat"] = "wiki";
      } else {
        $args["textFormat"] = $_GET["textFormat"];
      }
    }
    //urldecode of pageName
    $args["pageName"] = urldecode($args["pageName"]);

    $pageModel = new MwPage($args["language"]);
    $page = $pageModel->getPage($args["pageName"]);

    if ($page) {
      $revisionModel = new MwRevision($args["language"]);
      $revision = $revisionModel->getRevision($args["versionNumber"], $page->page_id);
      if (!$revision) {
        return $this->renderJson($response, 404, Messages::getErrorMessage("wikiPageRevisionNotFound"));
      }
      $textModel = new MwText($args["language"]);
      $pageContent = $textModel->getText($revision->rev_text_id);
      if (!$pageContent) {
        return $this->renderJson($response, 404, Messages::getErrorMessage("wikiPageNotFound"));
      }

      $langLinks = array();
      if ($args["textFormat"] == "html") {
        $siteUrl = Option::where("option_name", "=", "siteurl")->first()->option_value;
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_RETURNTRANSFER => 1,
          CURLOPT_URL => $siteUrl . "/wiki/api.php?action=parse&page={$args["pageName"]}&uselang={$args["language"]}&&oldid={$revision->rev_text_id}&disableeditsection=true&format=json",
          CURLOPT_USERAGENT => "curl"   
        ));
        $result = curl_exec($curl);
        curl_close($curl);
        $text = json_decode($result)->parse->text->{'*'};
        $pageContent = ($text) ? $text : $pageContent;
        if (sizeof(json_decode($result)->parse->langlinks) > 0) {
          foreach (json_decode($result)->parse->langlinks as $langLink) {
            if ($langLink->lang == "ar" || $langLink->lang == "en") {
              $langLinks = array(
                'lang' => $langLink->lang,
                'url' => $langLink->url
              );
            }
          }
        }

        //check images full path
        if (sizeof(json_decode($result)->parse->images) > 0) {
          foreach (json_decode($result)->parse->images as $image) {
            //a attribute href change
            $pageContent = str_replace("/{$args["language"]}/wiki/File:$image", "$siteUrl/{$args["language"]}/wiki/File:$image", $pageContent);

            //img src change
            $doc = new DOMDocument();

            $doc->loadHTML(mb_convert_encoding($pageContent, 'HTML-ENTITIES', 'UTF-8'));
            $xp = new DOMXpath($doc);

            $nodes = $xp->query('//img[contains(@src, ' . $image . ')]');
            $node = $nodes->item(0);
            $src_attr = $node->getAttribute("src");
            $node->setAttribute("src", $siteUrl . $src_attr);
            $pageContent = $doc->saveHTML();
            $pageContent = preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $pageContent);
          }
        }
      }

      $responseArray = array(
        "page_title" => $page->page_title,
        "page_content" => $pageContent,
        "page_language_links" => $langLinks
      );
      return $this->renderJson($response, 200, $responseArray);
    } else {
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound"));
    }
  }

  /**
   * @SWG\Post(
   *   path="/wiki/{language}/pages",
   *   tags={"FOSSPedia"},
   *   summary="Creates Wiki Page",
   *   description="Create a new wiki page with passed name and content in either english or arabic wiki based on the passed language",
   *   @SWG\Parameter(name="token", in="formData",type="string", description="User token needed to create a new wiki page<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="language", in="path",type="string", description="Wiki Page language <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="pageName", in="formData",type="string", description="Page Name [case insensetive] <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="pageContent", in="formData",type="integer", description="Page Content in wiki style format<br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="returns successful message if added"),
   *   @SWG\Response(response="422", description="invalid access token")
   * )
   */
  public function addPage($request, $response, $args) {
    $loggedin_user = isset($_POST['token']) ? (AccessToken::where('access_token', '=', $_POST['token'])->first()) : null;
    if ($loggedin_user !== null) {
      if (!$this->user_can($loggedin_user->user_id, 'perform_direct_ef_actions')) {
        return $this->renderJson($response, 422, Messages::getErrorMessage("unauthorized"));
      }
      if (!isset($args["language"]) || !in_array($args["language"], array("en", "ar"))) {
        return $this->renderJson($response, 200, Messages::getErrorMessage("missingValue", "language"));
      }
      if (!isset($_POST["pageName"])) {
        return $this->renderJson($response, 200, Messages::getErrorMessage("emptyValue", "Page Name"));
      } else if (str_contains($_POST["pageName"], " ")) {
        return $this->renderJson($response, 200, Messages::getErrorMessage("wrong", "Page Name, Page name shouldn't have spaces"));
      }


      if (!isset($_POST["pageContent"])) {
        return $this->renderJson($response, 200, Messages::getErrorMessage("emptyValue", "Page Content"));
      }

      $textModel = new MwText($args["language"]);
      $text = $textModel->addPageText($_POST["pageContent"]);
      $text->save();

      $pageModel = new MwPage($args["language"]);
      $page = $pageModel->addPage($_POST["pageName"], $text->old_id);
      try {
        $page->save();
      } catch (Exception $e) {
        return $this->renderJson($response, 200, Messages::getErrorMessage("duplicate", "Page Name"));
      }

      $user = User::where("ID", "=", $loggedin_user->user_id)->first();
      $mwUserObj = new MwUser($args["language"]);
      $mwUserId = 0;
      $isUserExists = $mwUserObj->getUser($user->user_login);
      if (!$isUserExists) {
        $mwUserArgs = array("user_name" => ($user->user_login),
          "user_real_name" => ($user->user_nicename),
          "user_password" => $user->user_pass,
          "user_newpassword" => $user->user_pass,
          "user_email" => $user->user_email);
        $mwUserObj->addUser($mwUserArgs);
        $mwUserObj->save();
        $mwUserId = $mwUserObj->user_id;
      } else {
        $mwUserId = $isUserExists->user_id;
      }

      $revisionModel = new MwRevision($args["language"]);
      $revision = $revisionModel->addRevision($page->page_id, $text->old_id, $mwUserId);
      $revision->rev_parent_id = 0;
      $revision->save();

      $page->page_latest = $revision->id;
      $page->save();


      if ($page) {
        // add fosspedia badge
        $fosspedia_badge = new Badge($loggedin_user->user_id);
        $fosspedia_badge->efb_manage_fosspedia_badge();

        $post_type = "pedia_en";
        if( $args["language"] == "ar" ) {
            $post_type = "pedia_ar";
        }
        
        $search = new SearchController;
        $search->save_post_to_marmotta( $page->page_id, $_POST["pageName"], strip_tags( $_POST["pageContent"], '<br>' ), $post_type );
        
        return $this->renderJson($response, 200, Messages::getSuccessMessage("SavedSuccessfully", "Page"));
      } else {
        return $this->renderJson($response, 500, Messages::getErrorMessage("unexpectedError"));
      }
    } else {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
  }
  
  /**
   * @SWG\Put(
   *   path="/wiki/{language}/pages/{pageName}",
   *   tags={"FOSSPedia"},
   *   summary="Updates Wiki Page",
   *   description="Update a new wiki page with passed content in either english or arabic wiki based on the passed language",
   *   @SWG\Parameter(name="token", in="formData",type="string", description="User token needed to edit a wiki page<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="language", in="path",type="string", description="Wiki Page language <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="pageName", in="path",type="string", description="Page Name [case insensetive] <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="pageContent", in="formData",type="string", description="Page Content in wiki style format<br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="returns successful message if added"),
   *   @SWG\Response(response="404", description="wiki page not found"),
   *   @SWG\Response(response="422", description="invalid access token")
   * )
   */
  public function editPage($request, $response, $args) {
    $put = $request->getParsedBody();
    $loggedin_user = isset($put['token']) ? (AccessToken::where('access_token', '=', $put['token'])->first()) : null;
    if ($loggedin_user !== null) {
      if (!$this->user_can($loggedin_user->user_id, 'perform_direct_ef_actions')) {
        return $this->renderJson($response, 422, Messages::getErrorMessage("unauthorized"));
      }
      if (!isset($args["language"]) || !in_array($args["language"], array("en", "ar"))) {
        return $this->renderJson($response, 200, Messages::getErrorMessage("missingValue", "language"));
      }
      if (!isset($args["pageName"])) {
        return $this->renderJson($response, 200, Messages::getErrorMessage("emptyValue", "Page Name"));
      } else if (str_contains($args["pageName"], " ")) {
        return $this->renderJson($response, 200, Messages::getErrorMessage("wrong", "Page Name, Page name shouldn't have spaces"));
      }


      if (!isset($put["pageContent"])) {
        return $this->renderJson($response, 200, Messages::getErrorMessage("emptyValue", "Page Content"));
      }

      $pageModel = new MwPage($args["language"]);
      $page = $pageModel->getPage($args["pageName"]);

      if ($page) {


        $textModel = new MwText($args["language"]);
        $text = $textModel->addPageText($put["pageContent"]);
        $text->save();

        $user = User::where("ID", "=", $loggedin_user->user_id)->first();
        $mwUserObj = new MwUser($args["language"]);
        $mwUserId = 0;
        $isUserExists = $mwUserObj->getUser($user->user_login);
        if (!$isUserExists) {
          $mwUserArgs = array("user_name" => ($user->user_login),
          "user_real_name" => ($user->user_nicename),
            "user_password" => $user->user_pass,
            "user_newpassword" => $user->user_pass,
            "user_email" => $user->user_email);
          $mwUserObj->addUser($mwUserArgs);
          $mwUserObj->save();
          $mwUserId = $mwUserObj->user_id;
        } else {
          $mwUserId = $isUserExists->user_id;
        }

        $revisionModel = new MwRevision($args["language"]);
        $revision = $revisionModel->addRevision($page->page_id, $text->old_id, $mwUserId);
        $revision->rev_parent_id = 0;
        $revision->save();

        $page->page_latest = $revision->id;
        $page->save();

        if ($page) {
          // add fosspedia badge
          $fosspedia_badge = new Badge($loggedin_user->user_id);
          $fosspedia_badge->efb_manage_fosspedia_badge();
          
          $post_type = "pedia_en";
          if( $args["language"] == "ar" ) {
              $post_type = "pedia_ar";
          }
        
          $search = new SearchController;
          $search->save_post_to_marmotta( $page->page_id, $args["pageName"], strip_tags( $put["pageContent"], '<br>' ), $post_type );

          return $this->renderJson($response, 200, Messages::getSuccessMessage("SavedSuccessfully", "Page"));
        } else {
          return $this->renderJson($response, 500, Messages::getErrorMessage("unexpectedError"));
        }
      } else {
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound"));
      }
    } else {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
  }

  /**
   * @SWG\Put(
   *   path="/wiki/{language}/pages/{pageName}/versions/{versionNumber}",
   *   tags={"FOSSPedia"},
   *   summary="Reverts wiki page to a revision",
   *   description="Revert wiki page to a specific version in either english or arabic wiki based on the passed language",
   *   @SWG\Parameter(name="token", in="formData",type="string", description="User token needed to revert a wiki page<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="language", in="path",type="string", description="Wiki Page language <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="pageName", in="path",type="string", description="Page Name [case insensetive] <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="versionNumber", in="path",type="integer", description="Version Number returned from Finds version of wiki page <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="returns successful message if added"),
   *   @SWG\Response(response="404", description="wiki page not found"),
   *   @SWG\Response(response="422", description="invalid access token")
   * )
   */
  public function revertPage($request, $response, $args) {
    $put = $request->getParsedBody();
    $loggedin_user = isset($put['token']) ? (AccessToken::where('access_token', '=', $put['token'])->first()) : null;
    if ($loggedin_user !== null) {
      if (!isset($args["language"]) || !in_array($args["language"], array("en", "ar"))) {
        return $this->renderJson($response, 200, Messages::getErrorMessage("missingValue", "language"));
      }
      if (!isset($args["pageName"])) {
        return $this->renderJson($response, 200, Messages::getErrorMessage("emptyValue", "Page Name"));
      } else if (str_contains($args["pageName"], " ")) {
        return $this->renderJson($response, 200, Messages::getErrorMessage("wrong", "Page Name, Page name shouldn't have spaces"));
      }


      if (!isset($args["versionNumber"])) {
        return $this->renderJson($response, 200, Messages::getErrorMessage("emptyValue", "Revision Number"));
      }

      $pageModel = new MwPage($args["language"]);
      $page = $pageModel->getPage($args["pageName"]);

      if ($page) {

        $revisionModel = new MwRevision($args["language"]);
        $revision = $revisionModel->getRevision($args["versionNumber"], $page->page_id);
        if ($revision) {
          $page->page_latest = $revision->rev_id;
          $page->save();

          if ($page) {
            return $this->renderJson($response, 200, Messages::getSuccessMessage("PageRevertedSuccessfully", "Page"));
          } else {
            return $this->renderJson($response, 500, Messages::getErrorMessage("unexpectedError"));
          }
        } else {
          return $this->renderJson($response, 200, Messages::getErrorMessage("wrong", "Version Number"));
        }
      } else {
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound"));
      }
    } else {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
  }

  /**
   * @SWG\Post(
   *   path="/wiki/{language}/files",
   *   tags={"FOSSPedia"},
   *   summary="Uploads a new image on wiki",
   *   description="Uploads new image in either english or arabic wiki based on the passed language",
   *   @SWG\Parameter(name="token", in="formData",type="string", description="User token needed to upload new wiki image<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="language", in="path",type="string", description="Wiki Page language <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="fileName", in="formData",type="string", description="File name to upload <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="uploadedFile", in="formData", required=false, type="file", description="File to upload <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="returns successful message if added")
   * )
   */
  public function uploadFile($request, $response, $args) {
    $loggedin_user = isset($_POST['token']) ? (AccessToken::where('access_token', '=', $_POST['token'])->first()) : null;
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      if (!$this->user_can($user_id, 'add_new_ef_posts')) {
        return $this->renderJson($response, 422, Messages::getErrorMessage("unauthorized"));
      }

      if (!isset($args["language"]) || $args["language"] == "") {
        return $this->renderJson($response, 200, Messages::getErrorMessage("missingValue", "language"));
      }
      if (!in_array($args["language"], array("en", "ar"))) {
        return $this->renderJson($response, 200, Messages::getErrorMessage("wrong", "language"));
      }
      if (!isset($_POST["fileName"]) || $_POST["fileName"] == "") {
        return $this->renderJson($response, 200, Messages::getErrorMessage("emptyValue", "File Name"));
      } else if (str_contains($_POST["fileName"], " ")) {
        return $this->renderJson($response, 200, Messages::getErrorMessage("wrong", "File Name, File name shouldn't have spaces"));
      }
      $this->handle_mw_image_upload($response, $loggedin_user, $args["language"]);
    } else {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
  }

  public function handle_mw_image_upload($response, $loggedin_user, $language) {
    preg_match("/(\.)+\w+$/", $_FILES['uploadedFile']['name'], $extention);
    $_FILES['uploadedFile']['name'] = ucfirst($_POST['fileName']) . $extention[0];
    $hashedImageName = md5($_FILES['uploadedFile']['name']);
    $path = __DIR__;

    for ($d = 1; $d <= 4; $d++)
      $path = dirname($path);

    $uploaddir = $path . '/wiki/images/' .
      $hashedImageName[0] . "/" . $hashedImageName[0] . $hashedImageName[1] . "/";
    mkdir($uploaddir, 0777, true);
    $uploadfile = $uploaddir . basename($_FILES['uploadedFile']['name']);
    if (move_uploaded_file($_FILES['uploadedFile']['tmp_name'], $uploadfile)) {
      $newText = new MwText();
      $newText->addPageText("");
      $newText->save();

      $newPage = new MwPage($language);
      $newPage->addPageForImage($_FILES['uploadedFile']['name']);
      $newPage->save();

      $user = User::where("ID", "=", $loggedin_user->user_id)->first();
      $mwUserObj = new MwUser($language);
      $mwUserId = 0;
      $isUserExists = $mwUserObj->getUser($user->user_login);
      if (!$isUserExists) {
          $mwUserArgs = array("user_name" => ($user->user_login),
          "user_real_name" => ($user->user_nicename),
          "user_password" => $user->user_pass,
          "user_newpassword" => $user->user_pass,
          "user_email" => $user->user_email);
        $mwUserObj->addUser($mwUserArgs);
        $mwUserObj->save();
        $mwUserId = $mwUserObj->user_id;
      } else {
        $mwUserId = $isUserExists->user_id;
      }

      $revisionModel = new MwRevision($language);
      $revision = $revisionModel->addRevision($newPage->page_id, $newText->old_id, $mwUserId);
      $revision->save();

      $newImage = new MwImage($language);
      $newImage->addImage($_FILES["uploadedFile"], $loggedin_user, $uploadfile);
      $newImage->save();


      return $this->renderJson($response, 200, Messages::getSuccessMessage("Success", "file uploaded"));
    } else {
      return $this->renderJson($response, 422, Messages::getErrorMessage("incorrect", "uploading process"));
    }
  }

  function get_string_between($string, $start, $end) {
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0)
      return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
  }

}
