<?php

class FlutterLayout{
	
	/**
	 * Loads theme settings in an array named $FlutterThemeSettings 
	 *
	 */
	function PrepareThemeSettings(){
		global $FlutterThemeSettings;
		$moduleSettings = FlutterLayoutBlock::GetModuleSettings(-1);
		if (!empty($moduleSettings)) $FlutterThemeSettings = $moduleSettings->GetProcessedVariables();
	}
	
	
	/**
	 * Inserts CSS/Javascript code for Layout feature
	 *
	 */
	function AddHeaderLayoutCode(){
		global $flutter_domain;
		
		$layoutHeaderCode = "";
		
		//TODO Add CSS file links for rendering the page
		$layoutHeaderCode .= "\n\n<!-- TODO: CSS files for rendering pages-->\n";
		$layoutHeaderCode .= "<link rel='stylesheet' href='' type='text/css' media='screen' />\n";				
		
		//TODO Call GenerateCSSSettings() to generate CSS code.
		$layoutHeaderCode .= FlutterLayout::GenerateCSSSettings($settings);
        
		//echo $layoutHeaderCode;
		
		//TODO If the user has enough privileges, add CSS/Javascript file links for editing the Layout
		if (current_user_can(FLUTTER_CAPABILITY_LAYOUT)){
			
			require_once("RCCWP_WritePostPage.php");
	
			$FLUTTER_URI = FLUTTER_URI;
			$jquery_path = FLUTTER_URI."js/layout/jquery.js";
			$jqueryui_path = FLUTTER_URI."js/layout/jquery.ui.js";
			$ui_path = FLUTTER_URI."js/layout/ui.js";
			$ui_css_path = FLUTTER_URI."css/layout_style.css";
			
			global $template, $wpdb;
			$page = basename($template);
			
			RCCWP_WritePostPage::CustomFieldsCSSScripts();
			wp_enqueue_script('tiny_mce');
			
			$templateName = get_option('template');
			$LayoutSettings = $wpdb->get_var("SELECT settings FROM ".FLUTTER_TABLE_LAYOUT_SETTINGS." WHERE theme = '$templateName' AND page='$page'");
			
			?>

			<!-- TODO: CSS/JavaScript files for rendering admin tools on theme pages-->
			
			<link rel='stylesheet' href='<?php echo $ui_css_path?>' type='text/css' media='screen' />
			
			<script language="JavaScript" type="text/javascript" > 
				var JS_FLUTTER_URI = '<?php echo $FLUTTER_URI?>'; // Flutter URL
				var JS_FLUTTER_FILES_PATH = '<?php echo FLUTTER_FILES_PATH ?>'; // Flutter URL
				var CURRENT_PAGE = '<?php echo $page?>'; // The name of the current page
			</script>
			<script type="text/javascript" src="<?php echo $jquery_path?>"></script>
			<script type="text/javascript" src="<?php echo $jqueryui_path?>"></script>
			<script type="text/javascript" src="<?php echo $ui_path?>"></script>
			
			
			<!-- Load theme settings-->
			<script type="text/javascript">
			// Configurable Settings
			var settings = <?php echo $LayoutSettings ;?>;
			
			if (settings.ie) {
				settings.incrementLeft = 10;
				settings.incrementRight = 30
				settings.incrementTop = 10
			}
			
			$(document).ready(function(){
				// Add extra margin to the bottom of entire page:
				$('div#container').css('margin-bottom', '250px');
			
				$('#tools-header li a').click(function() {
					switchTab($(this).attr('href'));
					$(this).addClass('current');
					return false;
				});
				
				$('div#leftcolumn .module, div#rightcolumn .module').dblclick(function() {
					$('div.tools-content').hide();
					$('#tools-header li a').removeClass('current');
					
					$('li#tools-module-properties').show().children('a').addClass('current');
					$('#tools-module-settings').show();
					
					$('#tools-settings-modulename').text('Module "'+$("h3",this).text()+'"');
					
					// load settings form
					$('#tools-form').load('http://www.freshout.co.uk/~fluttert/wp-content/themes/flutter/test.html', {block_id:1}, function() {
						if(window.console) console.log('loaded :)');
					});
					
					// add actions to buttons
					$('#tools-module-settings input[name=s]').click(function(){
						$('#formcontrols').append('<img src="http://www.freshout.co.uk/~fluttert/wp-content/themes/flutter/images/tools-loader.gif" width="16" height="16" border="0" id="forms-loader" />');
						
						// ajax submit was ok?
						if (1) {
							// show OK message
			//				$('#forms-loader').remove();
						} else {
							// show ERROR message
			//				$('#forms-loader').remove();
						}
						if(window.console) console.log('yay! do some ajax mambo!')
					});
					// add settings form
					
					//tb_show('','http://www.freshout.co.uk/~fluttert/wp-content/plugins/Flutter/canvas-plugin_form_test.php?zone=leftsidebar&position=1&block_id='+$(this).attr('rel')+'?height=400&width=500');
					//tb_show('','index.php#TB_inline?height=400&width=400&inlineId=moduleEdit');
					// /~fluttert/wp-content/plugins/Flutter/canvas-plugin_form.php?zone=leftsidebar&position=1&block_id=3
				})
			})
			</script>
			
			<?php
		}
		
		
		 
	}
	
	function GenerateCSSSettings($settings){
		$CSS_string = "";
		$CSS_string .= "\n\n<!-- TODO: print theme settings as CSS code-->\n";
		$CSS_string .= "<style type='text/css'></style>\n";
		return $CSS_string;
	}
	
	/**
	 * Inserts CSS/Javascript code for Layout feature
	 *
	 */
	function AddFooterLayoutCode(){
		
		if (current_user_can(FLUTTER_CAPABILITY_LAYOUT)){
				
			?>
			
			<div id="tools-bg">
				<div id="tools-header">
					<ul id="tools-tabs">
						<li><a class="first current" href="general"><?php _e('General', $flutter_domain); ?></a></li>
			
						<li><a href="options"><?php _e('Theme Options', $flutter_domain); ?></a></li>
						<li><a href="modules"><?php _e('Modules', $flutter_domain); ?></a></li>
						<li id="tools-module-properties" style="display:none;"><a href="module-settings"><?php _e('Module Properties', $flutter_domain); ?></a></li>
					</ul>
					<ul id="tools-cancelsave">
						<li><a href="cancel"><?php _e('Cancel', $flutter_domain); ?></a></li>
						<li><a class="save" href="save"><?php _e('Save', $flutter_domain); ?></a></li>
			
					</ul>
				</div>
				<div class="tools-content" id="tools-general">
					<?php _e('Content Here.', $flutter_domain); ?>
				</div>
				<div class="tools-content" id="tools-options" style="display:none;">
					<ul>
						<li><input type="checkbox" name="resizeboth" value="1" /> <?php _e('Resize both columns', $flutter_domain); ?></li>
			
						<li><input type="checkbox" name="heightresizing" value="1" /> <?php _e('Resize height', $flutter_domain); ?></li>
						<li><input type="checkbox" name="widthresizing" value="1" /> <?php _e('Resize width', $flutter_domain); ?></li>
						<li><input type="checkbox" name="reordermodules" value="1" /> <?php _e('Re-order modules', $flutter_domain); ?></li>
						<li><input type="button" name="reset" value="Undo all changes" /></li>
					</ul>
				</div>
			
				<div class="tools-content" id="tools-modules" style="display:none;">
					<div class="column">
						<?php flutter_modules(''); ?>
					</div>
				</div>
				<div class="tools-content" id="tools-module-settings" style="display:none;">
					<div id="tools-settings-modulename"><?php _e('Module "Recent"', $flutter_domain); ?></div>
					
					<div id="tools-form">
					</div>
			
					<div id="formcontrols">
						<input type="button" name="c" value="Cancel" />
						<input type="submit" name="s" value="Save" />
					</div>
				</div>
			</div>
			
			
			<?php
			
		}
		
		
	}
	
	
	
	function SaveLayoutSettings($pageLayoutSettings, $pageName){
		global $wpdb;
		
		$templateName = get_option('template');
		$settingsID = $wpdb->get_var("SELECT settings_id FROM ".FLUTTER_TABLE_LAYOUT_SETTINGS." WHERE theme = '$templateName' AND page='$pageName'");
		if (empty($settingsID)) {
			$wpdb->query("INSERT INTO ".FLUTTER_TABLE_LAYOUT_SETTINGS." (theme,page,settings) VALUES ('$templateName','$pageName','$pageLayoutSettings')");
			$settingsID = $wpdb->insert_id;
		}else{
			$wpdb->query("UPDATE ".FLUTTER_TABLE_LAYOUT_SETTINGS.
				" SET settings = '$pageLayoutSettings' ". 
				" WHERE settings_id = '$settingsID' ");
		}
		return $settingsID;
	}
	
	function SaveModulesPositions($modulesPositionsStr, $currentPage){
		$modulesPositions = json_decode(stripslashes($modulesPositionsStr));
		
		if (!is_array($modulesPositions)) {
			$modulesPositions = array();
			$modulesPositions[0] = json_decode(stripslashes($modulesPositionsStr));
		}
		 
		foreach($modulesPositions as $modulesPosition){
			foreach($modulesPosition->modules as $blockID){
				$moduleSettings = FlutterLayoutBlock::GetModuleSettingsByBlock($blockID);
				$moduleSettings->position = $modulesPosition->position;
				$moduleSettings->SaveSettings();
			}	
		}
		return true;
	}
	
	function GetModuleSettings($blockID){
		require_once("RCCWP_WritePostPage.php");
		
		// Retieve the settings for $blockID
		$moduleSettings = FlutterLayoutBlock::GetModuleSettingsByBlock($blockID);
		foreach($moduleSettings->variables as $variable) {
				$variable->properties = array();
				$inputName = $variable->variable_name;
				$variableValue = $variable->value;
				?>
					<label for="item3" class="checkbox"><?php echo $variable->description ?>
					<?php
						switch ($variable->type)
						{
							case 'textbox' :
								$variable->properties['size'] = "";
								RCCWP_WritePostPage::TextboxInterface($variable, $inputName, 0, 0, $variableValue);
								break;
							case 'multiline_textbox' :
								$variable->properties['height'] = "10";
								$variable->properties['width'] = "10";
								RCCWP_WritePostPage::MultilineTextboxInterface($variable, $inputName, 0, 0, $variableValue);
								break;
							case 'checkbox' :
								RCCWP_WritePostPage::CheckboxInterface($variable, $inputName, 0, 0, $variableValue);
								break;
							case 'checkbox_list' :
								//$variableValue = unserialize($variableValue);
								RCCWP_WritePostPage::CheckboxListInterface($variable, $inputName, 0, 0, $variableValue);
								break;
							case 'radiobutton_list' :
								RCCWP_WritePostPage::RadiobuttonListInterface($variable, $inputName, 0, 0, $variableValue);
								break;
							case 'dropdown_list' :
								RCCWP_WritePostPage::DropdownListInterface($variable, $inputName, 0, 0, $variableValue);
								break;
							case 'listbox' :
								//$variableValue = unserialize($variableValue);
								$variable->properties['size'] = "";
								RCCWP_WritePostPage::ListboxInterface($variable, $inputName, 0, 0, $variableValue);
								break;
							case 'file' :
								RCCWP_WritePostPage::FileInterface($variable, $inputName, 0, 0, $variableValue);
								break;
							case 'image' :
								RCCWP_WritePostPage::PhotoInterface($variable, $inputName, 0, 0, $variableValue);
								break;
							case 'date' :
								$variable->properties['format'] = "m.d.y";
								RCCWP_WritePostPage::DateInterface($variable, $inputName, 0, 0, $variableValue);
								break;
							case 'audio' :
								RCCWP_WritePostPage::AudioInterface($variable, $inputName, 0, 0, $variableValue);
								break;
						}
					?>
				</label>
			<?php
			}		
	}
	
	function SaveModuleSettings($blockID){
		$moduleSettings = FlutterLayoutBlock::GetModuleSettingsByBlock($blockID);
		foreach($moduleSettings->variables as $varKey => $variable) {
			$moduleSettings->variables[$varKey]->value = $_POST[$variable->variable_name]; 
		}
		$moduleSettings->SaveValues();
	}
	
	
}


/**
 * Loads the modules for the current theme/page/position
 *
 * @param unknown_type $position
 */
function flutter_modules($position){
	
	global $template;
	global $mod_vars;
	$page = basename($template);
	
	// Get all the modules for the current theme-page-position.
	$modulesBlock = FlutterLayoutBlock::GetModules($page, $position);
	
	// For each module, load its settings.
	foreach($modulesBlock as $moduleBlock){
	
		// Put the settings in a global variable for the module.
		$mod_vars = $moduleBlock->GetProcessedVariables();
		
		?>
		
		<div class="module" rel="1" block_id="<?php echo $moduleBlock->block_id ?>">
			<h3><?php echo $moduleBlock->title ?></h3>
			<span>
				<?php include_once($moduleBlock->GetModuleTemplateFile()); ?>
			</span>
		</div>
		
		<?php

	}
	
}


function mod_var($var_name){
	global $mod_vars; 
	return $mod_vars[$var_name];
}


?>
