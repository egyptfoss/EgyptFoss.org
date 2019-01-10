<?php

class Option extends BaseModel {
	protected $table = 'options';

  public static function getOption($optionName,$optionValueKey) {
    $optionValue = null;
    $option = Option::where('option_name', '=', $optionName)->first();
    if($option)
    {
      $optionValue = unserialize($option->option_value);
      if(is_array($optionValue))
      {
        $optionValue = $optionValue[$optionValueKey];
      }
    }
    return $optionValue;
  }
  
  public static function getOptionValueByKey($optionName)
  {
      $option = Option::where('option_name', '=', $optionName)->first();
      if($option)
        return $option->option_value;
      return null;
  }
  
  public static function updateOptionValueByKey($optionName, $optionValue)
  {
    $option = Option::where('option_name', '=', $optionName);
    if($option->first())
    {
        $option->update(array("option_id"=> $option->first()->option_id,"option_value"=> $optionValue));
    } 
  }
  
}