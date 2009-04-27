<?php

// Import the layout from XML

//if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER ['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') {
	require( dirname(__FILE__) . '/../../../../wp-config.php' );
	if (!(is_user_logged_in() && current_user_can('edit_posts')))
		die("Athentication failed!");

	if (isset($_GET['mod_name'])){
		$module_name = $_GET['mod_name'];
		$selected_template_size = $_GET['template_size'];

		$moduleTemplatesFolder = FLUTTER_MODULES_DIR.$module_name."/templates/";

		if ($_GET['template_name']=="") {
			// Get first template
			
	
			$templatesNamesStr = "";	
			if ($handle = @opendir($moduleTemplatesFolder)) {
				while (false !== ($file = readdir($handle))) { 
					if ($file!= "." && $file!=".."){
						$_GET['template_name'] = $file;	
						break;
					}	
				}
				closedir($handle);
			}
		}


		// Load module template sizes
		$moduleTemplateFolder = $moduleTemplatesFolder.$_GET['template_name'];
		$otherSizesStr = "";
	
		if ($handle = opendir($moduleTemplateFolder)) {
			while (false !== ($file = readdir($handle))) { 
				$set_selected = "";
				if (is_numeric($file)){
					if ($selected_template_size == $file) $set_selected = " selected='selected' ";
					$otherSizesStr = $otherSizesStr."<option $set_selected value='".$file."'>".$file."</option>";
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
						if ($selected_template_size == $t_size_val) $set_selected = " selected='selected' ";
						$otherSizesStr = $otherSizesStr."<option $set_selected value='".$t_size_val."'>".$file."</option>";	
					}
				}
					
			}
		
			closedir($handle);
			echo $otherSizesStr;
		}
		

	}
	
//}


?>