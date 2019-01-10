<?php

abstract class Messages {

  private static $errorMessages = array(
    "incorrect" => array("code" => "1", "message" => "incorrect %s%"),
    "notFound" => array("code" => "2", "message" => "%s% Not Found"),
    "missingValue" => array("code" => "3", "message" => "missing values !"),
    "wrongValue" => array("code" => "4", "message" => "Unprocessable Entity, Wrong value For %s%"),
    "wrong" => array("code" => "5", "message" => "Wrong %s%"),
    "duplicate" => array("code" => "6", "message" => "Duplicate %s% %range%"),
    "length-between" => array("code" => "7", "message" => "%s% length must be between %range%"),
    "length-more" => array("code" => "8", "message" => "%s% length must be more than %range%"), 
    "emptyValue" => array("code" => "9", "message" => "%s% can not be empty"),
    "wikiPageNotFound" => array("code" => "10", "message" => "Couldn't find page content"),
    "wikiPageRevisionNotFound" => array("code" => "11", "message" => "Couldn't find the requested version"),
    "unexpectedError" => array("code" => "12", "message" => "Something unexpected occured"),
    "formatError" => array("code" => "13", "message" => "%s% must at least contain one letter"),
    "mismatch" => array("code" => "14", "message" => "%s% confirmation must match"),
    "noaction" => array("code" => "15", "message" => "No Action Taken"),
    "activate" => array("code" => "16", "message" => "%s% is not active."),
    "nothingToDisplay" => array("code" => "17", "message" => "Nothing to display"),
    "notNumber" => array("code" => "18", "message" => "%s% must be a nubmer"),
    "exists" => array("code" => "19", "message" => "%s% already exists"),
    "unauthorized" => array("code" => "20", "message" => "User is not authorized to perform this action %s% %range%"),
    "wrongFormat" => array("code" => "21", "message" => "Wrong file format, should be one of: %range%"),
    "wrongSize" => array("code" => "22", "message" => "Wrong file size, should be max: %range%"),
    "wrongInput" => array("code" => "23", "message" => "%s% must at least contain one letter"),
    "wrongUrl" => array("code" => "24", "message" => "%s% is not valid url"),
    "dependingMissingValue" => array("code" => "25", "message" => "%s% is required since %range% is added"),
    "notAccessed" => array("code" => "26", "message" => "This item not accessed to you"),  
    "notValidUser" => array("code" => "27", "message" => "You can't perform this action as you are the owner"),  
    "notValidUserPermission" => array("code" => "28", "message" => "User doesn't have permission on this item to remove it"),  
    "notValidUserStatus" => array("code" => "29", "message" => "You can't change to this status"),  
    "notValidSection" => array("code" => "30", "message" => "Section should be empty since status is not published"),    
    "same" => array("code" => "30", "message" => "Same %s% as yours. no action taken"),
    "archived" => array("code" => "31", "message" => "%s% archived"),
    "invalidItemPermission" => array("code" => "32", "message" => "User doesn't have permission on this item"),
    "documentNotEditable" => array("code" => "33", "message" => "Document is %range%,You can't edit it"),
    "notAllowed" => array("code" => "34", "message" => "%s% is not allowed"),
    "existsBySameUser" => array("code" => "35", "message" => "%s% inserted by the same user before"),
    "randomizeCollision" => array("code" => "36", "message" => "you can't paginate result and randomizing it at the same time"),
    "wrongJsonFormat" => array("code" => "37", "message" => "Wrong json format or missing keys"),
    "authorNotActive" => array("code" => "38", "message" => "This Author is no longer active."),
    "responderNotActive" => array("code" => "39", "message" => "This Responder is no longer active."),
    "notAnsweredRequiredQuestions" => array("code" => "40", "message" => "One or more of required questions are not answered."),
    "notSocialLogin" => array("code" => "41", "message" => "User not loggedIn using social network."),
    "alreadyActivated" => array("code" => "42", "message" => "User is already active."),
    "emailPreviouslySent" => array("code" => "43", "message" => "Verification email sent a short time ago.Please try again in few mintutes."),
      
    /** List of error codes required by mobile team and shouldn't change for any reason **/
    "share_user_not_found_username" => array("code" => "1001", "message" => "username not found"),
    "share_user_not_found_permission" => array("code" => "1002", "message" => "permission not found"),
    "share_user_not_valid_permission" => array("code" => "1003", "message" => "permission not valid"),
    "share_user_not_found_user" => array("code" => "1004", "message" => "user not found"),
    "share_user_user_is_owner" => array("code" => "1005", "message" => "space/document owner can't be invited"),
    "share_user_user_added" => array("code" => "1006", "message" => "user already invited to this space/document"),
    "share_user_user_is_subscriber" => array("code" => "1007", "message" => "user of role subscriber can't be invited to a space/document"),
    "share_user_user_is_not_authorized" => array("code" => "1008", "message" => "user of role contributor or user of role contributor or less can't be invited to a space/document as publisher can't be invited to a space/document as publisher"),
  );
  
  private static $successMessages = array(
    "Registered" => array("code" => "1", "message" => "%s% Registered Successfully"),
    "Found" => array("code" => "2", "message" => "%s% Found"),
    "Product Added." => array("code" => "3", "message" => "%s% Product Added."),
    "SavedSuccessfully" => array("code" => "3", "message" => "%s% Saved Successfully"),
    "sentSuccessfully" => array("code" => "3", "message" => "%s% has been sent Successfully"),
    "Success" => array("code" => "3", "message" => "%s% Successfully"),
    "PageRevertedSuccessfully" => array("code" => "13", "message" => "Page reverted successfully"),
    "DeletedSuccessfully" => array("code" => "3", "message" => "%s% deleted Successfully"),
    "nothingToDisplay" => array("code" => "17", "message" => "Nothing to display"),  
  );
  
  public static function getErrorMessageAsString($errorKey, $variable_name, $args = array()) {
    if(empty($variable_name)) {
      $variable_name = "";
    }
    $errorMessage = str_replace("%s%",$variable_name,(self::$errorMessages[$errorKey]["message"]));
    if(!empty($args)) {
      if($args["range"]) {
        $errorMessage = str_replace("%range%",$args["range"],$errorMessage);
      }
    }
    $errorMessage = "Error ". (self::$errorMessages[$errorKey]["code"])." : ".$errorMessage;
    return $errorMessage;
  }
  
  public static function getErrorMessage($errorKey, $variable_name = "", $args = array()) { 
    $result = array("type" => "Error");
    if(!empty($variable_name)) {
      $result["field-name"] = $variable_name;
    }
    $errorMessage = str_replace("%s%",$variable_name,(self::$errorMessages[$errorKey]["message"]));
    if(!empty($args)) {
      if($args["range"]) {
        $errorMessage = str_replace("%range%",$args["range"],$errorMessage);
      }
    }else{
      $errorMessage = str_replace("%range%",'',$errorMessage);
    }
    $result["code"] = self::$errorMessages[$errorKey]["code"];
    $result["message"] = $errorMessage;
    return $result;
  }
  
  public static function getSuccessMessageAsString($successKey, $variable_name="") {
    $successMessage = "Success ". (self::$successMessages[$successKey]["code"])." : ". (self::$successMessages[$successKey]["message"]);
      $successMessage = str_replace("%s%",$variable_name,$successMessage);
    return $successMessage;
  }
  
  public static function getSuccessMessage($successKey, $variable_name="") {
    $successMessage = self::$successMessages[$successKey]["message"];
    $successMessage = str_replace("%s%",$variable_name,$successMessage);
    return array("type" => "Success",
                 "code" => self::$successMessages[$successKey]["code"],
                 "message" => $successMessage,
                 );
  }

}
