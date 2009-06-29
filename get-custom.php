<?php
/*

=>> Based on coffee2code: Visit the plugin's homepage for more information and latest updates  <<=
                http://www.coffee2code.com/wp-plugins/

Copyright (c) 2004-2005 by Scott Reilly (aka coffee2code)

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation 
files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, 
modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the 
Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/


require_once 'RCCWP_Constant.php';
require_once 'tools/debug.php';

/**
 * Get number of group duplicates given field name. The function returns 1
 * if there are no duplicates (just the original group), 2 if there is one
 * duplicate and so on.
 *
 * @param string $fieldName the name of any field in the group
 * @return number of group duplicates 
 */
function getGroupDuplicates ($fieldName) {
	require_once("RCCWP_CustomField.php");
	global $post;
	return RCCWP_CustomField::GetFieldGroupDuplicates($post->ID, $fieldName);
}

/**
 * Get number of field duplicates given field name and group duplicate index.
 * The function returns 1 if there are no duplicates (just the original field), 
 * 2 if there is one duplicate and so on.
 *
 * @param string $fieldName
 * @param integer $groupIndex
 * @return number of field duplicates
 */
function getFieldDuplicates ($fieldName, $groupIndex) {
	require_once("RCCWP_CustomField.php");
	global $post;
	return RCCWP_CustomField::GetFieldDuplicates($post->ID, $fieldName, $groupIndex);
}

/**
 * Get the value of an input field.
 *
 * @param string $fieldName
 * @param integer $groupIndex
 * @param integer $fieldIndex
 * @param boolean $readyForEIP if true and the field type is textbox or
 * 				multiline textbox, the resulting value will be wrapped
 * 				in a div that is ready for EIP. The default value is true
 * @return a string or array based on field type
 */
function get ($fieldName, $groupIndex=1, $fieldIndex=1, $readyForEIP=true) {
	require_once("RCCWP_CustomField.php");
	global $wpdb, $post, $FIELD_TYPES;
	
	$fieldID = RCCWP_CustomField::GetIDByName($fieldName);
	$fieldObject = GetFieldInfo($fieldID);
	$fieldType = $wpdb->get_var("SELECT type FROM ".RC_CWP_TABLE_GROUP_FIELDS." WHERE id='".$fieldID."'");
	$single = true;
	switch($fieldType){
		case $FIELD_TYPES["checkbox_list"]:
		case $FIELD_TYPES["listbox"]:
			$single = false;
			break;
	} 
	
	$fieldValues = (array) RCCWP_CustomField::GetCustomFieldValues($single, $post->ID, $fieldName, $groupIndex, $fieldIndex);
	$fieldMetaID = RCCWP_CustomField::GetMetaID($post->ID, $fieldName, $groupIndex, $fieldIndex);
    
	$results = GetProcessedFieldValue($fieldValues, $fieldType, $fieldObject->properties);
    
	//filter for multine line
	if($fieldType == $FIELD_TYPES['multiline_textbox']){
		$results = apply_filters('the_content', $results);
	}
	if($fieldType == $FIELD_TYPES['image']){
		$results = split('&',$results);
		$results = $results[0];
	}
	
	// Prepare fields for EIP 
	include_once('RCCWP_Options.php');
	$enableEditnplace = RCCWP_Options::Get('enable-editnplace');
	if ($readyForEIP && $enableEditnplace == 1 && current_user_can('edit_posts', $post->ID)){
	
	    switch($fieldType){
	        case $FIELD_TYPES["textbox"]:
			if(!$results) $results="&nbsp";
			$results = "<div class='".EIP_textbox($fieldMetaID)."' >".$results."</div>";
			break;

	        case $FIELD_TYPES["multiline_textbox"]:
			if(!$results) $results="&nbsp";
			$results = "<div class='".EIP_mulittextbox($fieldMetaID)."' >".$results."</div>";
			break;
        }

    }
    return $results;

}

function GetProcessedFieldValue($fieldValues, $fieldType, $fieldProperties=array()){
	global $FIELD_TYPES;
	
	$results = array();
	$fieldValues = (array) $fieldValues;
	foreach($fieldValues as $fieldValue){
	
		switch($fieldType){
			case $FIELD_TYPES["audio"]:
			case $FIELD_TYPES["file"]:
			case $FIELD_TYPES["image"]:
				if ($fieldValue != "") $fieldValue = FLUTTER_FILES_URI.$fieldValue;
				break;
	
			case $FIELD_TYPES["checkbox"]: 		
				if ($fieldValue == 'true')  $fieldValue = true; else $fieldValue = false; 
				break;
	
			case $FIELD_TYPES["date"]: 
				$fieldValue = date($fieldProperties['format'],strtotime($fieldValue)); 
				break;
		}
		
		array_push($results, $fieldValue); 
	}
	
	// Return array or single value based on field
	switch($fieldType){
		case $FIELD_TYPES["checkbox_list"]:
		case $FIELD_TYPES["listbox"]:
			return $results;
		 	break;
	}

	if (count($results) == 0 )
		return "";
	else
		return $results[0];
}

// Get Image. 
function get_image ($fieldName, $groupIndex=1, $fieldIndex=1,$tag_img=1) {
	require_once("RCCWP_CustomField.php");
	global $wpdb, $post, $FIELD_TYPES;

	$fieldID = RCCWP_CustomField::GetIDByName($fieldName);
	$fieldObject = GetFieldInfo($fieldID);
	$fieldType = $wpdb->get_var("SELECT type FROM ".RC_CWP_TABLE_GROUP_FIELDS." WHERE id='".$fieldID."'");
	$single = true;
	switch($fieldType){
		case $FIELD_TYPES["checkbox_list"]:
		case $FIELD_TYPES["listbox"]:
			$single = false;
			break;
	} 
	
	$fieldValues = (array) RCCWP_CustomField::GetCustomFieldValues($single, $post->ID, $fieldName, $groupIndex, $fieldIndex);
	 
	if(!empty($fieldValues[0]))
		$fieldValue = $fieldValues[0];
	else 
		return "";
	$url_params= explode("&",$fieldValue,2);
	
	if(count($url_params) >= 2){
		$fieldObject->properties['params'] .="&". $url_params[1];
		$fieldValue= $url_params[0];
	}
	
	if (substr($fieldObject->properties['params'], 0, 1) == "?"){
			$fieldObject->properties['params'] = substr($fieldObject->properties['params'], 1);
		}
	
	 //check if exist params, if not exist params, return original image
	if (empty($fieldObject->properties['params']) && (FALSE == strstr($fieldValue, "&"))){
		$fieldValue = FLUTTER_FILES_URI.$fieldValue;
	}else{
		//check if exist thumb image, if exist return thumb image
		$md5_params = md5($fieldObject->properties['params']);
		if (file_exists(FLUTTER_FILES_PATH.'th_'.$md5_params."_".$fieldValue)) {
			$fieldValue = FLUTTER_FILES_URI.'th_'.$md5_params."_".$fieldValue;
		}else{
			//generate thumb
			//include_once(FLUTTER_URI_RELATIVE.'thirdparty/phpthumb/phpthumb.class.php');
			include_once(dirname(__FILE__)."/thirdparty/phpthumb/phpthumb.class.php");
			$phpThumb = new phpThumb();
			$phpThumb->setSourceFilename(FLUTTER_FILES_PATH.$fieldValue);
			$create_md5_filename = 'th_'.$md5_params."_".$fieldValue;
			$output_filename = FLUTTER_FILES_PATH.$create_md5_filename;
			$final_filename = FLUTTER_FILES_URI.$create_md5_filename;

			$params_image = explode("&",$fieldObject->properties['params']);
			foreach($params_image as $param){
				if($param){
					$p_image=explode("=",$param);
					$phpThumb->setParameter($p_image[0], $p_image[1]);
				}
			}
			if ($phpThumb->GenerateThumbnail()) {
				if ($phpThumb->RenderToFile($output_filename)) {
					$fieldValue = $final_filename;
				}
			}
		}
	}
	
	if($tag_img){
		$cssClass = $wpdb->get_results("SELECT CSS FROM ".RC_CWP_TABLE_GROUP_FIELDS." WHERE name='".$fieldName."'");
		if (empty($cssClass[0]->CSS)){
			$finalString = stripslashes(trim("\<img src=\'".$fieldValue."\' /\>"));
		}else{
			$finalString = stripslashes(trim("\<img src=\'".$fieldValue."\' class=\"".$cssClass[0]->CSS."\" \/\>"));
		}
	}else{
		$finalString=$fieldValue;
	}
	return $finalString;
}

// Get Image function old version. 
function get_image_old ($fieldName, $groupIndex=1, $fieldIndex=1,$tag_img=1) {
	require_once("RCCWP_CustomField.php");
	global $wpdb, $post, $FIELD_TYPES;
	
	$fieldID = RCCWP_CustomField::GetIDByName($fieldName);
	$fieldObject = GetFieldInfo($fieldID);
	$fieldType = $wpdb->get_var("SELECT type FROM ".RC_CWP_TABLE_GROUP_FIELDS." WHERE id='".$fieldID."'");
	$single = true;
	switch($fieldType){
		case $FIELD_TYPES["checkbox_list"]:
		case $FIELD_TYPES["listbox"]:
			$single = false;
			break;
	} 
	
	$fieldValues = (array) RCCWP_CustomField::GetCustomFieldValues($single, $post->ID, $fieldName, $groupIndex, $fieldIndex);
	 
	if(!empty($fieldValues[0]))
		$fieldValue = $fieldValues[0];
	else 
		return "";
	
    
	if (substr($fieldObject->properties['params'], 0, 1) == "?"){
		$fieldObject->properties['params'] = substr($fieldObject->properties['params'], 1);
	}


	if (empty($fieldObject->properties['params']) && (FALSE == strstr($fieldValue, "&"))){
		$fieldValue = FLUTTER_FILES_PATH.$fieldValue; 
	}
	else{
        $path = FLUTTER_FILES_PATH;
		$fieldValue = $path.$fieldValue.$fieldObject->properties['params'];
	}
        
        $fieldValue= PHPTHUMB."?src=".$fieldValue;
    if($tag_img){
	
        $cssClass = $wpdb->get_results("SELECT CSS FROM ".RC_CWP_TABLE_GROUP_FIELDS." WHERE name='".$fieldName."'");
        
	    if (empty($cssClass[0]->CSS)){
		    $finalString = stripslashes(trim("\<img src=\'".$fieldValue."\' /\>"));
    	}
	    else{
		    $finalString = stripslashes(trim("\<img src=\'".$fieldValue."\' class=\"".$cssClass[0]->CSS."\" \/\>"));
    	}
   }else{
        $finalString=$fieldValue;
   }
	    return $finalString;
}

// Get Audio. 
function get_audio ($fieldName, $groupIndex=1, $fieldIndex=1) {
	require_once("RCCWP_CustomField.php");
	global $wpdb, $post, $FIELD_TYPES;
	
	$fieldID = RCCWP_CustomField::GetIDByName($fieldName);
	$fieldObject = GetFieldInfo($fieldID);
	$fieldType = $wpdb->get_var("SELECT type FROM ".RC_CWP_TABLE_GROUP_FIELDS." WHERE id='".$fieldID."'");
	$single = true;
	switch($fieldType){
		case $FIELD_TYPES["checkbox_list"]:
		case $FIELD_TYPES["listbox"]:
			$single = false;
			break;
	} 
	
	$fieldValues = (array) RCCWP_CustomField::GetCustomFieldValues($single, $post->ID, $fieldName, $groupIndex, $fieldIndex);
	
	if(!empty($fieldValues))
		$fieldValue = $fieldValues[0];
	else 
		return "";
		
	$path = FLUTTER_FILES_URI;
	$fieldValue = $path.$fieldValue;
	$finalString = stripslashes(trim("\<div style=\'padding-top:3px;\'\>\<object classid=\'clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\' codebase='\http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0\' width=\'95%\' height=\'20\' wmode=\'transparent\' \>\<param name=\'movie\' value=\'".FLUTTER_URI."js/singlemp3player.swf?file=".urlencode($fieldValue)."\' wmode=\'transparent\' /\>\<param name=\'quality\' value=\'high\' wmode=\'transparent\' /\>\<embed src=\'".FLUTTER_URI."js/singlemp3player.swf?file=".urlencode($fieldValue)."' width=\'50\%\' height=\'20\' quality=\'high\' pluginspage=\'http://www.macromedia.com/go/getflashplayer\' type=\'application/x-shockwave-flash\' wmode=\'transparent\' \>\</embed\>\</object\>\</div\>"));
	return $finalString;
}

// This works outside "the loop"
function c2c_get_recent_custom ($field, $before='', $after='', $none='', $between=', ', $before_last='', $limit=1, $unique=false, $order='DESC', $include_static=true, $show_pass_post=false) {
	global $wpdb;
	if (empty($between)) $limit = 1;
	if ($order != 'ASC') $order = 'DESC';
	$now = current_time('mysql');

	$sql = "SELECT ";
	if ($unique) $sql .= "DISTINCT ";
	$sql .= "meta_value FROM $wpdb->posts AS posts, $wpdb->postmeta AS postmeta ";
	$sql .= "WHERE posts.ID = postmeta.post_id AND postmeta.meta_key = '$field' ";
	$sql .= "AND ( posts.post_status = 'publish' ";
	if ($include_static) $sql .= " OR posts.post_status = 'static' ";
	$sql .= " ) AND posts.post_date < '$now' ";
	if (!$show_pass_post) $sql .= "AND posts.post_password = '' ";
	$sql .= "AND postmeta.meta_value != '' ";
	$sql .= "ORDER BY posts.post_date $order LIMIT $limit";
	$results = array(); $values = array();
	$results = $wpdb->get_results($sql);
	if (!empty($results))
		foreach ($results as $result) { $values[] = $result->meta_value; };
	return c2c__format_custom($field, $values, $before, $after, $none, $between, $before_last);
} //end c2c_get_recent_custom()

/* Helper function */
function c2c__format_custom ($field, $meta_values, $before='', $after='', $none='', $between='', $before_last='') {
	$values = array();
	if (empty($between)) $meta_values = array_slice($meta_values,0,1);
	if (!empty($meta_values))
		foreach ($meta_values as $meta) {
			$meta = apply_filters("the_meta_$field", $meta);
			$values[] = apply_filters('the_meta', $meta);
		}

	if (empty($values)) $value = '';
	else {
		$values = array_map('trim', $values);
		if (empty($before_last)) $value = implode($values, $between);
		else {
			switch ($size = sizeof($values)) {
				case 1:
					$value = $values[0];
					break;
				case 2:
					$value = $values[0] . $before_last . $values[1];
					break;
				default:
					$value = implode(array_slice($values,0,$size-1), $between) . $before_last . $values[$size-1];
			}
		}
	}
	if (empty($value)) {
		if (empty($none)) return;
		$value = $none;
	}
	return $before . $value . $after;
} //end c2c__format_custom()

function GetFieldInfo($customFieldId)
	{
		global $wpdb;
		$sql = "SELECT properties FROM " . RC_CWP_TABLE_CUSTOM_FIELD_PROPERTIES  .
			" WHERE custom_field_id = '" . $customFieldId."'";
		$results = $wpdb->get_row($sql);
		//$results->options = unserialize($results->options);
		$results->properties = unserialize($results->properties);
		//$results->default_value = unserialize($results->default_value);
		return $results;
	}
        
function pt(){
    return PHPTHUMB;
}


/**
 *  Return the value for the  layout option 
 *
 * 
 * @param string $option_name  
 *
 */
function option($variable_name){
  global $wpdb;

  $template = get_option('template'); //template name
  $module_id =  $wpdb->get_var("SELECT block_id  FROM ".FLUTTER_TABLE_LAYOUT_MODULES." WHERE theme = '{$template}'"); //module_id to the template

  //getting the value
  $value = $wpdb->get_var(  "SELECT value FROM ".FLUTTER_TABLE_LAYOUT_VARIABLES.
                            " WHERE  parent  = {$module_id} and  variable_name = '{$variable_name}'"
                         );
  return $value;
}

/**
 * Return a array with the all values in one array 
 *
 * @param string $groupName 
 */
function getGroupOrder($field_name){
    global $post,$wpdb;
    
    $elements  = $wpdb->get_results("SELECT group_count FROM ".RC_CWP_TABLE_POST_META." WHERE post_id = ".$post->ID." AND field_name = '".$field_name."' ORDER BY order_id ASC");
   
    foreach($elements as $element){
       $order[] =  $element->group_count;
    }
     
    return $order;
}

?>