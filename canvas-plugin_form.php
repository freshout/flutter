<?php

/*

___Canvas Plugin Form___________________________________________

Outputs the Lightbox form for plugins, building individual
form elements based upon the plugin definitions.

________________________________________________________________

*/

require( dirname(__FILE__) . '/../../../wp-config.php' );
if (!(is_user_logged_in() && current_user_can('edit_posts')))
	die("Athentication failed!");


global $wpdb, $canvas;

$zone = $_GET['zone'];
$position = $_GET['position'];
$block_id = $_GET['block_id'];
$counter = 0;

// Get module info from database
$module_id= $wpdb->get_var("SELECT module_id FROM ".$canvas->main." WHERE block_id = $block_id");
if ( $wpdb->get_var("SELECT zone FROM ".$canvas->main." WHERE block_id = $block_id") != "shelf"){
	$template_size = $wpdb->get_var("SELECT template_size FROM ".$canvas->main." WHERE block_id = $block_id");
	$template_name = $wpdb->get_var("SELECT template_name FROM ".$canvas->main." WHERE block_id = $block_id");
}

//if ('' == $template_name) $template_name = 'default';
//if ('' == $template_size) $template_size = '-1';


$module_template_size_div_id = 'canvas_'.$zone.'_'.$position.'_'.$block_id.'_module_size';
$module_template_name_div_id = 'canvas_'.$zone.'_'.$position.'_'.$block_id.'_module_template_name';

// Get module name
include_once('RCCWP_CustomWriteModule.php');

$customWriteModule = RCCWP_CustomWriteModule::Get($module_id);
$module_name = $customWriteModule->name;

// Get plugin sizes
$moduleTemplatesFolder = FLUTTER_MODULES_DIR.$module_name."/templates";
$moduleTemplateFolder = FLUTTER_MODULES_DIR.$module_name."/templates/".$template_name;

// Load module template sizes
$otherSizesStr = "";	
if ($handle = @opendir($moduleTemplateFolder)) {
	while (false !== ($file = readdir($handle))) { 
		if (is_numeric($file)){
			if ($template_size == $file)
				$otherSizesStr = $otherSizesStr."<option selected='selected' value='".$file."'>".$file."</option>";	
			else
				$otherSizesStr = $otherSizesStr."<option value='".$file."'>".$file."</option>";	
			
		}
		else{
			$t_size_val = 0;
			switch($file){
				case "small":
					$t_size_val = -1;
					break;
				case "medium":
					$t_size_val = -2;
					break;
				case "large":
					$t_size_val = -3;
					break;
				case "full":
					$t_size_val = -4;
					break;

			}
			if ($t_size_val<0){
				if ($template_size == $t_size_val)
					$otherSizesStr = $otherSizesStr."<option selected='selected' value='".$t_size_val."'>".$file."</option>";	
				else
					$otherSizesStr = $otherSizesStr."<option value='".$t_size_val."'>".$file."</option>";	
			}
		}
			
	}

	closedir($handle);
}

// Load module template names
$templatesNamesStr = "";	
if ($handle = @opendir($moduleTemplatesFolder)) {
	while (false !== ($file = readdir($handle))) { 
		if ($file!= "." && $file!="..")
			if ($template_name == $file)
				$templatesNamesStr = $templatesNamesStr."<option selected='selected' value='".$file."'>".$file."</option>";	
			else
				$templatesNamesStr = $templatesNamesStr."<option value='".$file."'>".$file."</option>";		
	}

	closedir($handle);
}


?>
<div class="lbContent">
<?php
	global $wpdb, $canvas;
	$variables = $wpdb->get_results("SELECT * FROM ".$canvas->variables." WHERE parent = '$block_id' ORDER BY variable_id");
	$title = $wpdb->get_results("SELECT * FROM ".$canvas->main." WHERE block_id = '$block_id'");

	// Get module name
	include_once('RCCWP_CustomWriteModule.php');
	$customWriteModule = RCCWP_CustomWriteModule::Get($title[0]->module_id);
	if ($title[0]->duplicate_id ==0 )
		$title[0]->module_title = $customWriteModule->name;
	else
		$title[0]->module_title = $wpdb->get_var("SELECT duplicate_name FROM ".$canvas->duplicates." WHERE duplicate_id = ".$title[0]->duplicate_id);

	$content = '<h3>'.$title[0]->module_title.'';
	if($title[0]->author != '' && $title[0]->uri != '')
		$content .= '<span>by <a target="new" href="'.$title[0]->uri.'">'.$title[0]->author.'</a></span>';
	if($title[0]->description != '')
		$content .= '<p>'.$title[0]->description.'</p>';


	$F = "\$F";	
	$module_template_name_id = 'canvas_'.$zone.'_'.$position.'_'.$block_id.'_module_template_name';

	$ListboxAJAX = FLUTTER_URI. "ajax/canvas-populate-listbox.php?mod_name=".$module_name."&template_name=";

	$content .= 
<<<EOF

	<input type="hidden" id="updated_module_template_size_id" value="$module_template_size_div_id" />
	<input type="hidden" id="updated_module_template_name_id" value="$module_template_name_div_id" />

	<script language="JavaScript" type="text/javascript" >
		curr_module_size = $F('$module_template_size_div_id');
		curr_module_template_name = $F('$module_template_name_div_id');

		function UpdateModuleBlock(){
			$('$module_template_size_div_id').value = $('template_name').value;
			$('$module_template_name_div_id').value = $('template_size').value;
		}

		function dynamicallyLoadSizeList(){ 
			$('template_name').value = curr_module_template_name;
			$("template_size").innerHTML = "<option>Loading ...</option>";
			
			var myAjax = new Ajax.Request('$ListboxAJAX' + curr_module_template_name + "&template_size=" + curr_module_size, { method:'post',
				onSuccess: function(transport){
					$("template_size").innerHTML = transport.responseText;
					$("template_size").innerHTML = $("template_size").innerHTML + "<option>Hello</option>";
				}
			});
		};

EOF;

	if ($template_name == ""){ //If the template name is not saved in the database, get the default template name stored in the module block
		$content .= 
<<<EOF
		//Event.observe(window, 
		//	'load', 
		//	dynamicallyLoadSizeList ,
		//	false);

		dynamicallyLoadSizeList();

EOF;
	}
	$content .= 
<<<EOF


		function changeTemplateSize(){
			Event.observe($("template_name"), 'change',
			function(){
				var d = new Date();
				var time = d.getTime();
				$("template_size").innerHTML = "<option>Loading ...</option>";
				var myAjax = new Ajax.Request('$ListboxAJAX' + $F("template_name") + '&time='+time, { method:'get',
					onSuccess: function(transport){
						var newContent = '<label for="template_size">Template Size:</label> <select  name="template_size" id="template_size" style="margin-left:11px;width:100px;font-size:11px;" onchange="Canvas.changePublishImg();">';
						newContent += transport.responseText +" </select>";
						$("wrap_template_size").innerHTML = newContent;
					}
				});
	
			}
			);
		}
		
		//Event.observe(window, 
		//	'load', 
		//	changeTemplateSize);
		changeTemplateSize();

		function saveAndReturn(){
			lightbox.prototype.deactivate();
			return false;
		}
		
	
	</script>

	<a id="confirm" href="javascript:void(0)" class="lbAction" rel="deactivate" onclick="$('$module_size_div_id').value = $('template_name').value;$('$module_name_div_id').value = $('template_size').value;">Save and Return</a>
	<a id="cancel" href="javascript:void(0)" class="lbAction" rel="cancel">Cancel</a></h3><div class="lbContent_inside">
	
	<form name="lightbox_form" action="" id="lightbox_form" onsubmit="lightbox.prototype.deactivate();return false;">
		<label for="template_name">Template Name:</label> 
		<select name="template_name" id="template_name" style="width:100px;font-size:11px;"  onchange="Canvas.changePublishImg();">
			$templatesNamesStr
		</select>
		<br />
		<div id="wrap_template_size">
			<label for="template_size">Template Size:</label> 
			<select  name="template_size" id="template_size" style="margin-left:11px;width:100px;font-size:11px;" onchange="Canvas.changePublishImg();">
				$otherSizesStr
			</select>
		</div>
		<br />
		<hr />
		<br />



		
EOF;
	$content .= '<input type="hidden" name="parent" value="'.$block_id.'">'."\n";

	foreach($variables as $variable) {
		if(strtolower($variable->type) == 'text')
			$content .= '<label class="textbox">'.$variable->description.': <input type="text" class="text" name="'.$variable->variable_name.'" value="'.$variable->value.'" /></label>'."\n";
		elseif(strtolower($variable->type) == 'textarea')
			$content .= '<label class="textbox">'.$variable->description.': <textarea rows="8" name="'.$variable->variable_name.'">'.htmlspecialchars($variable->value).'</textarea></label>'."\n";
		elseif(strtolower($variable->type) == 'integer')
			$content .= '<label class="textbox">'.$variable->description.': <input type="text" size="3" name="'.$variable->variable_name.'" value="'.$variable->value.'" /></label>'."\n";
		elseif(strtolower($variable->type) == 'boolean') {
			$content .= '<label class="boolean"><input type="checkbox" name="'.$variable->variable_name.'" value="1" ';
			if($variable->value == 1) $content .= 'checked ';
			$content .= '/>&nbsp;'.$variable->description.'</label>'."\n";
		} elseif(strtolower($variable->type) == 'radio') {
			$content .= '<span><p>'.$variable->description.':</p>';
			
			$options = $wpdb->get_results("SELECT * FROM ".$canvas->options." WHERE var_id = '$variable->variable_id'");
			
			foreach($options as $option) {
				// If is a Wordpress database variable...
				if(preg_match('/(.*)\-\>(.*)\:(.*)/', $option->option_value, $matches)) {
					$content .= canvas_database_plugin($matches, 'radio', $variable->value, $option->option_params);
				} else {
					$content .= '<label><input type="radio" name="'.$variable->variable_name.'" value="'.$option->option_value.'" ';
					if ($option->option_value == $variable->value) $content .= ' checked="checked" ';
					$content .= '/>&nbsp;'.$option->option_text.'</label>';
				}
			}
			$content .= '</span>'."\n";
		} elseif(strtolower($variable->type) == 'select') {
			$content .= '<span><p>'.$variable->description.':</p>';

			$options = $wpdb->get_results("SELECT * FROM ".$canvas->options." WHERE var_id = '$variable->variable_id'");

			$content .= '<select name="'.$variable->variable_name.'" id="'.$variable->variable_name.'">'."\n";
			foreach($options as $option) {
				// If is a Wordpress database variable...
				if(preg_match('/(.*)\-\>(.*)\:(.*)/', $option->option_value, $matches)) {
					$content .= canvas_database_plugin($matches, 'select', $variable->value, $option->option_params);
				} else {
					$content .= '<option value="'.$option->option_value.'" ';
					if ($option->option_value == $variable->value) $content .= ' selected="selected" ';
					$content .= '>&nbsp;'.$option->option_text."</option>\n";
				}
			}
			$content .= '</select>'."\n";
			$content .= '</span>'."\n";
        } elseif(strtolower($variable->type) == 'image') {
			$options = $wpdb->get_results("SELECT * FROM ".$canvas->options." WHERE var_id = '$variable->variable_id'");
			$content .= '<p>'.$variable->description.':</p>';
			$content .= '<input type="hidden" id="directory" value="'.$options[0]->option_value.'">';
			$selected = '<input type="hidden" id="selected_image">';
			$content .= '<input type="hidden" id="path" name="'.$variable->variable_name.'" value="'.$variable->value.'">';
			$content .= '<div class="gallery">';
			$files = scandirectory(ABSPATH.$options[0]->option_value);
			foreach($files as $file) {
				$class = '';
				if(substr($file, -3) == 'jpg' || substr($file, -3) == 'gif' ||  substr($file, -3) == 'png') {
					if($options[0]->option_value.$file == $variable->value) {
						$class= 'class="selected_image"';
						$selected = '<input type="hidden" id="selected_image" value="'.$file.'">';
					}
					list($width, $height) = @getimagesize(get_bloginfo('wpurl').'/'.$options[0]->option_value.$file);
					if($width > 250) { $height = $height * 250 / $width; $width = 250; }
					$content .= '<a href="javascript:void(0)" onclick="Canvas.gallerySwitch(\''.$file.'\')"><img '.$class.' id="'.$file.'" src="'.get_bloginfo('wpurl').'/'.$options[0]->option_value.$file.'" title="'.$file.'" alt="'.$file.'" style="height: '.$height.'px; width: '.$width.'px;" /></a>';
				}
			}
			$content .= $selected;
			$content .= '</div>';
        } elseif(strtolower($variable->type) == 'list') {
        	$listitems = explode('|', $variable->value);
        	$content .= '<label class="textbox">'.$variable->description.' <small>(Note &#124; is an illegal character)</small></label>'."\n".'<span class="list" id="listlist">'."\n";
        	foreach($listitems as $listitem) {
        		$content .= '<span id="'.$variable->variable_name.$counter.'"><input type="text" class="text" name="'.$counter.'canvaslist_'.$variable->variable_name.'" value="'.htmlspecialchars($listitem).'" />'."\n";
				$content .= '<a href="javascript:void(0)" onclick="Canvas.addListItem(this, \''.$variable->variable_name.'\')"><img src="'.FLUTTER_URI.'images/list-duplicate.png" alt="Duplicate" title="Duplicate item"/></a>';
				$content .= '<a href="javascript:void(0)" onclick="Canvas.removeListItem(this)"><img src="'.FLUTTER_URI.'images/list-delete.png" alt="Delete" title="Delete item"/></a>';
				$content .= '</span>';
				$counter++;
			}
        	$content .= '</span>'."\n";
	}
	elseif(strtolower($variable->type) == 'fileupload'){
		
		$value = attribute_escape($variable->value);
		$path = FLUTTER_FILES_URI;
		$valueRelative = $value;
		$value = $path.$value;
		
		$varName = $variable->variable_name;
		$iframePath = FLUTTER_URI."RCCWP_upload.php?input_name=".$varName;
		$content .= '<label class="textbox">'.$variable->description.': </label>'."\n";
		if($valueRelative)
			$content .= '(<a href="' . $value . '" target="_blank">View Current</a>)';

		echo $content;
		echo 
<<<EOF
		
		
		
		<input tabindex="3" 
			id="$varName" 
			name="$varName" 
			type="hidden" 
			size="46"
			onchange=""
			value="$valueRelative"
			/>
		<p id="upload_progress_$varName" style="visibility:hidden;height:0px"></p>

EOF;
		?>

		<?php
		include_once( "RCCWP_SWFUpload.php" ) ;
		RCCWP_SWFUpload::Body($varName, 0, 1, 10) ;
 		$content = "";
	
        } elseif(strtolower($variable->type) == 'gmodule') {
        	//$variable->value WILL THIS BE THE XML FILE?
        	$xmlfile = 'http://www.google.com/ig/modules/datetime.xml';
        	$url = 'http://gmodules.com/ig/creator?synd=open&url='.$xmlfile;
        	echo '<object id="gmodule_content" data="'.$url.'" type="text/html"></object>';
        } elseif(strtolower($variable->type) == 'gmodule_script') {
        	echo '<input type="text" id="gmodule_script" value="'.$variable->value.'">';
        }
	}
	//if(count($variables) == 0) echo 'This plugin has no options (yes, it should be a block instead).';	

	$content .= '<input type="hidden" id="listcount" name="listcount" value="'.$counter.'">'."\n";
	$content .= '</form>'."\n";
		
	echo $content;

?>
</div>
</div>