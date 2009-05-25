<?php
class RCCWP_Application
{
	
	
	function GetWpCategories()
	{
		global $wpdb;
		//$sql = "SELECT cat_ID, cat_name FROM $wpdb->categories ORDER BY cat_name";

		if( $wpdb->terms != '' )
		{
			$sql = "SELECT t.term_id AS cat_ID, t.name AS cat_name FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON tt.term_id = t.term_id WHERE tt.taxonomy = 'category' ORDER BY t.name";
		}
		else
		{
			$sql = "SELECT cat_ID, cat_name FROM $wpdb->categories ORDER BY cat_name";
		}

		$results = $wpdb->get_results($sql);
		if (!isset($results))
			$results = array();
		return $results;
	}

	


	/**
	* Check whether this is wordpress mu and the logged in user is the top admin and it is the main blog
	*/
	function is_mu_top_admin(){
		global $wpdb;

		if ($wpdb->prefix != $wpdb->base_prefix.'1_') {
			return false;
		}

		return true;
	}

	/**
	* Import modules in default_modules folder
	*/
	function import_default_modules(){

		$modsFolder = dirname(__FILE__)."/default_modules/without_panel";
		if ($handle = opendir($modsFolder)) {
			while (false !== ($file = readdir($handle))) {
				if (is_file($modsFolder.'/'.$file))
					RCCWP_CustomWriteModule::Import($modsFolder.'/'.$file); 
			}
		}

		$modsFolder = dirname(__FILE__)."/default_modules/with_panel";
		if ($handle = opendir($modsFolder)) {
			while (false !== ($file = readdir($handle))) {
				if (is_file($modsFolder.'/'.$file))
					RCCWP_CustomWriteModule::Import($modsFolder.'/'.$file, false, true);
			}
		}

	}

	function ContinueInstallation(){
			RCCWP_Application::SetCaps();
	}

	function SetCaps(){

		// Create capabilities if they are not installed
		if (!current_user_can(FLUTTER_CAPABILITY_PANELS)){
			$role = get_role('administrator');
			if (!(RCCWP_Application::IsWordpressMu()) || is_site_admin()){
				$role->add_cap(FLUTTER_CAPABILITY_PANELS);
				$role->add_cap(FLUTTER_CAPABILITY_MODULES);
			}
			$role->add_cap(FLUTTER_CAPABILITY_LAYOUT);
			$role->add_cap(FLUTTER_CAPABILITY_STYLE);
		}
	}


    /** 
     *
     *
     */
	function Install()
	{
		
		include_once('RCCWP_Options.php');
		global $wpdb;

		// First time installation
		if (get_option(RC_CWP_OPTION_KEY) === false){
	
			// Giving full rights to folders. 
			@chmod(FLUTTER_UPLOAD_FILES_DIR, 777);
			@chmod(FLUTTER_IMAGES_CACHE_DIR, 777);
			@chmod(FLUTTER_MODULES_DIR, 777);
			
			//Initialize options
			$options['hide-write-post'] = 0;
			$options['hide-write-page'] = 0;
			$options['hide-visual-editor'] = 0;
			$options['prompt-editing-post'] = 0;
			$options['assign-to-role'] = 0;
			$options['use-snipshot'] = 0;
			$options['enable-editnplace'] = 1;
			$options['eip-highlight-color'] = "#FFFFCC";
			$options['enable-swfupload'] = 1 ;
			$options['default-custom-write-panel'] = "";
			if (version_compare(PHP_VERSION, '5.0.0') === 1)
				$options['enable-HTMLPurifier'] = 0;
			else
				$options['enable-HTMLPurifier'] = 0;
			$options['tidy-level'] = "medium";
			$options['canvas_show_instructions'] = 1;
			$options['canvas_show_zone_name'] = 0;
			$options['canvas_show'] = 1;
			$options['ink_show'] = 0;
            $options['enable-broserupload'] = 0;

			RCCWP_Options::Update($options);
			
		}

        //for  backward compatibility
        if($options['enable-swfupload'] == 1){
            $options['enable-browserupload'] =  0;
        }else{
            $options['enable-broserupload'] = 1;
        }

    	RCCWP_Options::Update($options);

		

        //comment sniptshot  preference
        $checking_options = RCCWP_Options::Get();
        $checking_options['use-snipshot'] = 0; 
        RCCWP_Options::Update($checking_options);

		// Check blog database
		if (get_option("RC_CWP_BLOG_DB_VERSION") == '') update_option("RC_CWP_BLOG_DB_VERSION", 0);
		
		if (get_option("RC_CWP_BLOG_DB_VERSION") < RC_CWP_DB_VERSION) 
			$BLOG_DBChanged = true;
		else
			$BLOG_DBChanged = false;
				
			
		// Install blog tables
		if (!$wpdb->get_var("SHOW TABLES LIKE '".RC_CWP_TABLE_POST_META."'") == RC_CWP_TABLE_POST_META ||
				$BLOG_DBChanged){	
			$blog_tables[] = "CREATE TABLE " . RC_CWP_TABLE_POST_META . " (
				id integer NOT NULL,
				group_count integer NOT NULL,
				field_count integer NOT NULL,
				post_id integer NOT NULL,
				field_name text NOT NULL,
                order_id integer NOT NULL,
				PRIMARY KEY (id) )" ;

            $blog_tables[] = "CREATE TABLE ". FLUTTER_TABLE_LAYOUT_MODULES ." (
		        block_id INT NOT NULL AUTO_INCREMENT,
	   			module_id INT NOT NULL,
				theme tinytext NOT NULL,
				page text NOT NULL,
				position tinytext NOT NULL,
				template_name text NOT NULL,
				template_size text NOT NULL,
				duplicate_id INT NOT NULL,
				PRIMARY KEY (block_id)
				);";
	
			$blog_tables[] = "CREATE TABLE ".FLUTTER_TABLE_LAYOUT_VARIABLES." (
	   			variable_id INT NOT NULL AUTO_INCREMENT,
	   			variable_name text NOT NULL,
	   			parent INT NOT NULL,
	   			type text NOT NULL,
	   			value text NOT NULL,
	   			default_value text NOT NULL,
	   			description text NOT NULL,
	   			options text NOT NULL,
	   			PRIMARY KEY (variable_id)
				);";
			
			$blog_tables[] = "CREATE TABLE ".FLUTTER_TABLE_LAYOUT_SETTINGS." (
		        settings_id INT NOT NULL AUTO_INCREMENT,
				theme tinytext NOT NULL,
				page text NOT NULL,
				settings text NOT NULL,
				PRIMARY KEY (settings_id)
				);";

			// try to get around
			// these includes like http://trac.mu.wordpress.org/ticket/384 
			// and http://www.quirm.net/punbb/viewtopic.php?pid=832#p832
			if (file_exists(ABSPATH . 'wp-includes/pluggable.php')) {
				require_once(ABSPATH . 'wp-includes/pluggable.php');
			} else {
				require_once(ABSPATH . 'wp-includes/pluggable-functions.php');
			}
			require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
			
			foreach($blog_tables as $blog_table)
				dbDelta($blog_table);
		}
        update_option('RC_CWP_BLOG_DB_VERSION', RC_CWP_DB_VERSION);
		//canvas_install($BLOG_DBChanged);
		
     

		// Upgrade Blog
		if ($BLOG_DBChanged)	RCCWP_Application::UpgradeBlog();

				
		if (RCCWP_Application::IsWordpressMu()){	
			if (get_site_option("RC_CWP_DB_VERSION") == '') update_site_option("RC_CWP_DB_VERSION", 0);
			if (get_site_option("RC_CWP_DB_VERSION") < RC_CWP_DB_VERSION) 
				$DBChanged = true;
			else
				$DBChanged = false;
		}
		else{
			if (get_option("RC_CWP_DB_VERSION") == '') update_option("RC_CWP_DB_VERSION", 0);
			if (get_option("RC_CWP_DB_VERSION") < RC_CWP_DB_VERSION) 
				$DBChanged = true;
			else
				$DBChanged = false;
		}
		
		
		// -- Create Tables if they don't exist or the database changed
		if(!$wpdb->get_var("SHOW TABLES LIKE '".RC_CWP_TABLE_PANELS."'") == RC_CWP_TABLE_PANELS) 	$not_installed = true;

		if( $not_installed ||
			$DBChanged){ 

			$qst_tables[] = "CREATE TABLE " . RC_CWP_TABLE_PANELS . " (
				id int(11) NOT NULL auto_increment,
				name varchar(50) NOT NULL,
                single tinyint(1) NOT NULL default 0,
				description varchar(255),
				display_order tinyint,
				capability_name varchar(50) NOT NULL,
				type varchar(50) NOT NULL,
				PRIMARY KEY (id) )";
			
			$qst_tables[] = "CREATE TABLE " . RC_CWP_TABLE_CUSTOM_FIELD_TYPES . " (
				id tinyint(11) NOT NULL auto_increment,
				name varchar(50) NOT NULL,
				description varchar(100),
				has_options enum('true', 'false') NOT NULL,
				has_properties enum('true', 'false') NOT NULL,
				allow_multiple_values enum('true', 'false') NOT NULL,
				PRIMARY KEY (id) )";
				
			$qst_tables[] = "CREATE TABLE " . RC_CWP_TABLE_GROUP_FIELDS . " (
				id int(11) NOT NULL auto_increment,
				group_id int(11) NOT NULL,
				name varchar(50) NOT NULL,
				description varchar(255),
				display_order tinyint,
				display_name enum('true', 'false') NOT NULL,
				display_description enum('true', 'false') NOT NULL,
				type tinyint NOT NULL,
				CSS varchar(100),
				required_field tinyint,
				duplicate tinyint(1) NOT NULL,
				PRIMARY KEY (id) )";
				
			$qst_tables[] = "CREATE TABLE " . RC_CWP_TABLE_CUSTOM_FIELD_OPTIONS . " (
				custom_field_id int(11) NOT NULL,
				options text,
				default_option text,
				PRIMARY KEY (custom_field_id) )";
			
			$qst_tables[] = "CREATE TABLE " . RC_CWP_TABLE_PANEL_CATEGORY . " (
				panel_id int(11) NOT NULL,
				cat_id int(11) NOT NULL,
				PRIMARY KEY (panel_id, cat_id) )";
				
						
			$qst_tables[] = "CREATE TABLE " . RC_CWP_TABLE_PANEL_STANDARD_FIELD . " (
				panel_id int(11) NOT NULL,
				standard_field_id int(11) NOT NULL,
				PRIMARY KEY (panel_id, standard_field_id) )";
			
			$qst_tables[] = "CREATE TABLE " . RC_CWP_TABLE_CUSTOM_FIELD_PROPERTIES . " (
				custom_field_id int(11) NOT NULL AUTO_INCREMENT,
				properties TEXT,
				PRIMARY KEY (custom_field_id)
				);";
				
			$qst_tables[] = "CREATE TABLE " . RC_CWP_TABLE_MODULES . " (
				id int(11) NOT NULL auto_increment,
				name varchar(50) NOT NULL,
				description text,
				PRIMARY KEY (id) )";
	
			$qst_tables[] = "CREATE TABLE " . RC_CWP_TABLE_PANEL_GROUPS . " (
				id int(11) NOT NULL auto_increment,
				panel_id int(11) NOT NULL,
				name varchar(50) NOT NULL,
				duplicate tinyint(1) NOT NULL,
				at_right tinyint(1) NOT NULL,
				PRIMARY KEY (id) )";

            $qst_tables[] = "CREATE TABLE ".FLUTTER_TABLE_MODULES_DUPLICATES." (
		        duplicate_id INT NOT NULL AUTO_INCREMENT,
	   			module_id INT NOT NULL,
				duplicate_name text NOT NULL,
				PRIMARY KEY(duplicate_id)
				);";

			// try to get around
			// these includes like http://trac.mu.wordpress.org/ticket/384 
			// and http://www.quirm.net/punbb/viewtopic.php?pid=832#p832
			if (file_exists(ABSPATH . 'wp-includes/pluggable.php')) {
				require_once(ABSPATH . 'wp-includes/pluggable.php');
			} else {
				require_once(ABSPATH . 'wp-includes/pluggable-functions.php');
			}
			require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
			
			foreach($qst_tables as $qst_table)
				dbDelta($qst_table);

			if (RCCWP_Application::IsWordpressMu()) {
					update_site_option('RC_CWP_DB_VERSION', RC_CWP_DB_VERSION);
			}
			else{
					update_option('RC_CWP_DB_VERSION', RC_CWP_DB_VERSION);
			}
		
		}

		// Insert standard fields definition
		if($not_installed){
		
			$sql6 = "INSERT IGNORE INTO " . RC_CWP_TABLE_CUSTOM_FIELD_TYPES . " VALUES (1, 'Textbox', NULL, 'false', 'true', 'false')";
			$wpdb->query($sql6);
			
			$sql6 = "INSERT IGNORE INTO " . RC_CWP_TABLE_CUSTOM_FIELD_TYPES . " VALUES (2, 'Multiline Textbox', NULL, 'false', 'true', 'false')";
			$wpdb->query($sql6);
			
			$sql6 = "INSERT IGNORE INTO " . RC_CWP_TABLE_CUSTOM_FIELD_TYPES . " VALUES (3, 'Checkbox', NULL, 'false', 'false', 'false')";
			$wpdb->query($sql6);
			
			$sql6 = "INSERT IGNORE INTO " . RC_CWP_TABLE_CUSTOM_FIELD_TYPES . " VALUES (4, 'Checkbox List', NULL, 'true', 'false', 'true')";
			$wpdb->query($sql6);
			
			$sql6 = "INSERT IGNORE INTO " . RC_CWP_TABLE_CUSTOM_FIELD_TYPES . " VALUES (5, 'Radiobutton List', NULL, 'true', 'false', 'false')";
			$wpdb->query($sql6);
			
			$sql6 = "INSERT IGNORE INTO " . RC_CWP_TABLE_CUSTOM_FIELD_TYPES . " VALUES (6, 'Dropdown List', NULL, 'true', 'false', 'false')";
			$wpdb->query($sql6);
			
			$sql6 = "INSERT IGNORE INTO " . RC_CWP_TABLE_CUSTOM_FIELD_TYPES . " VALUES (7, 'Listbox', NULL, 'true', 'true', 'true')";
			$wpdb->query($sql6);
			
			$sql6 = "INSERT IGNORE INTO " . RC_CWP_TABLE_CUSTOM_FIELD_TYPES . " VALUES (8, 'File', NULL, 'false', 'false', 'false')";
			$wpdb->query($sql6);
			
			$sql6 = "INSERT IGNORE INTO " . RC_CWP_TABLE_CUSTOM_FIELD_TYPES . " VALUES (9, 'Image', NULL, 'false', 'true', 'false')";
			$wpdb->query($sql6);
	
			$sql6 = "INSERT IGNORE INTO " . RC_CWP_TABLE_CUSTOM_FIELD_TYPES . " VALUES (10, 'Date', NULL, 'false', 'true', 'false')";
			$wpdb->query($sql6);
	
			$sql6 = "INSERT IGNORE INTO " . RC_CWP_TABLE_CUSTOM_FIELD_TYPES . " VALUES (11, 'Audio', NULL, 'false', 'false', 'false')";
			$wpdb->query($sql6);
			
			$sql6 = "INSERT IGNORE INTO " . RC_CWP_TABLE_CUSTOM_FIELD_TYPES . " VALUES (12, 'Color Picker', NULL, 'false', 'false', 'false')";
			$wpdb->query($sql6);
			
			$sql6 = "INSERT IGNORE INTO " . RC_CWP_TABLE_CUSTOM_FIELD_TYPES . " VALUES (13, 'Slider', NULL, 'false', 'true', 'false')";
			$wpdb->query($sql6);
			
		}
		
		// Upgrade Blog site
		if ($DBChanged) RCCWP_Application::UpgradeBlogSite();
		
		if(RC_CWP_DB_VERSION >= 36){
			$wpdb->query("INSERT IGNORE INTO " . RC_CWP_TABLE_CUSTOM_FIELD_TYPES . " VALUES (12, 'Color Picker', NULL, 'false', 'false', 'false')");
			
		}
		if(RC_CWP_DB_VERSION >= 36){
			$wpdb->query("INSERT IGNORE INTO " . RC_CWP_TABLE_CUSTOM_FIELD_TYPES . " VALUES (13, 'Slider', NULL, 'false', 'true', 'false')");
			
		}
		
		if(RC_CWP_DB_VERSION >= 40){ 
            $wpdb->query("ALTER TABLE " . RC_CWP_TABLE_PANELS . " MODIFY name varchar(255) NOT NULL");
	    $wpdb->query("ALTER TABLE " . RC_CWP_TABLE_PANELS . " MODIFY capability_name varchar(255) NOT NULL");
	    $wpdb->query("ALTER TABLE " . RC_CWP_TABLE_CUSTOM_FIELD_TYPES . " MODIFY name varchar(255) NOT NULL");
	    $wpdb->query("ALTER TABLE " . RC_CWP_TABLE_GROUP_FIELDS . " MODIFY name varchar(255) NOT NULL");
	    $wpdb->query("ALTER TABLE " . RC_CWP_TABLE_MODULES . " MODIFY name varchar(255) NOT NULL");
	    $wpdb->query("ALTER TABLE " . RC_CWP_TABLE_PANEL_GROUPS . " MODIFY name varchar(255) NOT NULL");
            //$wpdb->query('update '.RC_CWP_TABLE_POST_META.' ps, '.RC_CWP_TABLE_GROUP_FIELDS.' cf, '.RC_CWP_TABLE_PANEL_GROUPS.' mg set ps.order_id=-1 where mg.name="__default" and mg.id=cf.group_id AND cf.name=ps.field_name');
        }
		
		
		//Import Default modules 
		if (RCCWP_Application::IsWordpressMu()){
			if (get_site_option('FLUTTER_fist_time') == ''){
				RCCWP_Application::import_default_modules();
				update_site_option('FLUTTER_fist_time', '1');
			}
		}
		else{
			if (get_option('FLUTTER_fist_time') == ''){
				RCCWP_Application::import_default_modules();
				update_option('FLUTTER_fist_time', '1');
			}
		}
	}
	
	function UpgradeBlog(){
		global $wpdb;
		if (RC_CWP_DB_VERSION == 26){
			// Migrate database from previous versions after introducing models/panels
			// separation concept
			// The following code adds the field name to RC_CWP_TABLE_POST_META.
			
			$fieldMetaIDs = $wpdb->get_results("SELECT id FROM " . RC_CWP_TABLE_POST_META);
			foreach($fieldMetaIDs as $fieldMetaID){
				$metakey = $wpdb->get_var( "SELECT meta_key FROM $wpdb->postmeta WHERE meta_id = '{$fieldMetaID->id}'" );
				$wpdb->query("UPDATE ". RC_CWP_TABLE_POST_META .
							 " SET field_name = '$metakey'".
							 " WHERE id = '{$fieldMetaID->id}'");
					
			}
		}

		if (RC_CWP_DB_VERSION == 36){
			$wpdb->query('ALTER TABLE '.RC_CWP_TABLE_PANELS.' add  column single  tinyint(1) default 0 after name;');
		}
		
		if(RC_CWP_DB_VERSION >= 36){
			$wpdb->query("INSERT IGNORE INTO " . RC_CWP_TABLE_CUSTOM_FIELD_TYPES . " VALUES (12, 'Color Picker', NULL, 'false', 'false', 'false')");
			
		}

        if(RC_CWP_DB_VERSION >= 38){
            $wpdb->query('ALTER TABLE '.RC_CWP_TABLE_POST_META.' add column order_id  integer default -1');

        }
        if(RC_CWP_DB_VERSION >= 39){
            $wpdb->query("update ".RC_CWP_TABLE_POST_META." ps, ".RC_CWP_TABLE_GROUP_FIELDS." cf, ".RC_CWP_TABLE_PANEL_GROUPS." mg set ps.order_id=ps.group_count");
            //$wpdb->query('update '.RC_CWP_TABLE_POST_META.' ps, '.RC_CWP_TABLE_GROUP_FIELDS.' cf, '.RC_CWP_TABLE_PANEL_GROUPS.' mg set ps.order_id=-1 where mg.name="__default" and mg.id=cf.group_id AND cf.name=ps.field_name');
        }
	
	
		
	}

	function UpgradeBlogSite(){
		global $wpdb;
		if (RC_CWP_DB_VERSION == 26){
			
			// Migrate database from previous versions after introducing models/panels
			// separation concept
			// The following code transfers fields/groups fomr modules to panels.
			
			require_once("RCCWP_CustomWritePanel.php");
			if (RCCWP_Application::IsWordpressMu()) {
				$RC_CWP_TABLE_PANEL_MODULES = $wpdb->base_prefix . 'rc_cwp_panel_modules';
			}
			else{
				$RC_CWP_TABLE_PANEL_MODULES = $wpdb->prefix . 'rc_cwp_panel_modules';
			}
			
			$writePanels = RCCWP_CustomWritePanel::GetCustomWritePanels();
			
			foreach($writePanels as $writePanel){
				$sql = "SELECT module_id FROM " . $RC_CWP_TABLE_PANEL_MODULES .
					" WHERE panel_id = " . $writePanel->id;
				$panelModules =$wpdb->get_results($sql);
				
				foreach($panelModules as $panelModule){
					$wpdb->query("UPDATE ". RC_CWP_TABLE_PANEL_GROUPS .
							 " SET panel_id = '{$writePanel->id}'".
							 " WHERE module_id = '{$panelModule->module_id}'");
				}
			}
		}
	}
	
	function Uninstall()
	{
 		global $wpdb;

		// Delete blog tables
		$sql = "DROP TABLE " . RC_CWP_TABLE_POST_META; $wpdb->query($sql);
        $sql = "DROP TABLE " . FLUTTER_TABLE_LAYOUT_MODULES; $wpdb->query($sql);
		$sql = "DROP TABLE " . FLUTTER_TABLE_LAYOUT_VARIABLES; $wpdb->query($sql);

		//include_once "canvas-install.php";
		//canvas_clean_deactivate();

		// Remove options
		delete_option(RC_CWP_OPTION_KEY);

		// Delete meta data
		$sql = "DELETE FROM $wpdb->postmeta WHERE meta_key = '" . RC_CWP_POST_WRITE_PANEL_ID_META_KEY . "'";
 		$wpdb->query($sql);

		if (get_option("Flutter_notTopAdmin")) return;


		
		RCCWP_Application::DeleteModulesFolders();	

		$sql = "DROP TABLE " . RC_CWP_TABLE_PANELS;
		$wpdb->query($sql);
		
		$sql = "DROP TABLE " . RC_CWP_TABLE_CUSTOM_FIELD_TYPES;
		$wpdb->query($sql);
		
		$sql = "DROP TABLE " . RC_CWP_TABLE_GROUP_FIELDS;
		$wpdb->query($sql);
		
		$sql = "DROP TABLE " . RC_CWP_TABLE_CUSTOM_FIELD_OPTIONS;
		$wpdb->query($sql);
		
		$sql = "DROP TABLE " . RC_CWP_TABLE_PANEL_CATEGORY;
		$wpdb->query($sql);
		
		$sql = "DROP TABLE " . RC_CWP_TABLE_STANDARD_FIELDS;
		$wpdb->query($sql);
		
		$sql = "DROP TABLE " . RC_CWP_TABLE_PANEL_STANDARD_FIELD;
		$wpdb->query($sql);
		
		$sql = "DROP TABLE " . RC_CWP_TABLE_CUSTOM_FIELD_PROPERTIES;
		$wpdb->query($sql);

		$sql = "DROP TABLE " . RC_CWP_TABLE_MODULES;
		$wpdb->query($sql);
		
		$sql = "DROP TABLE " . RC_CWP_TABLE_PANEL_GROUPS;
		$wpdb->query($sql);

		$sql = "DROP TABLE " . RC_CWP_TABLE_PANEL_HIDDEN_EXTERNAL_FIELD; 
		$wpdb->query($sql);

		$sql = "DROP TABLE " . RC_CWP_TABLE_PANEL_MODULES; 
		$wpdb->query($sql);
		
		global $canvas;
		$wpdb->query("DROP TABLE IF EXISTS ".FLUTTER_TABLE_MODULES_DUPLICATES."");

		if (RCCWP_Application::is_mu_top_admin()){
			update_site_option('FLUTTER_fist_time', '');
		}
		else{
			update_option('FLUTTER_fist_time', '');
		}
	
		
	}

	function DeleteModulesFolders()
	{
		$customModules = RCCWP_CustomWriteModule::GetCustomModules();
		foreach($customModules as $customModule)
			RCCWP_CustomWriteModule::Delete($customModule->id);
	}
	
	function InCustomWritePanel()
	{
		return RCCWP_Application::InWritePostPanel() && isset($_REQUEST['custom-write-panel-id']);
	}
	
	function InWritePostPanel()
	{
		return (strstr($_SERVER['REQUEST_URI'], '/wp-admin/post-new.php') ||
			strstr($_SERVER['REQUEST_URI'], '/wp-admin/post.php') ||
			strstr($_SERVER['REQUEST_URI'], '/wp-admin/page-new.php') ||
			strstr($_SERVER['REQUEST_URI'], '/wp-admin/page.php'));
	}

	function IsWordpressMu(){
		global $is_wordpress_mu; //$wpdb, $wp_version, $wpmu_version;

		if  ($is_wordpress_mu){      // (isset($wpmu_version)) || (strpos($wp_version, 'wordpress-mu')) ) {
			return true;
		}

		return false;
	}

	function CheckInstallation(){
		global $flutter_domain;
	
		if (!empty($_GET['page']) && stripos($_GET['page'], "flutter") === false && $_GET['page'] != "RCCWP_OptionsPage.php" && !isset($_GET['custom-write-panel-id'])) return;
		
		$dir_list = "";
		$dir_list2 = "";
		//if(!is_dir(FLUTTER_UPLOAD_FILES_DIR)){
		//	copy(dirname(__FILE__).DIRECTORY_SEPARATOR."files_flutter2/", FLUTTER_UPLOAD_FILES_DIR);
		//	mkdir('files_flutter', 0777);
		//	@chmod(FLUTTER_UPLOAD_FILES_DIR, 777);
		//}
		if (!is_dir(FLUTTER_IMAGES_CACHE_DIR)){
			$dir_list2.= "<li>".FLUTTER_IMAGES_CACHE_DIR . "</li>";
		}elseif (!is_writable(FLUTTER_IMAGES_CACHE_DIR)){
			$dir_list.= "<li>".FLUTTER_IMAGES_CACHE_DIR . "</li>";
		}

		if (!is_dir(FLUTTER_UPLOAD_FILES_DIR)){
			$dir_list2.= "<li>".FLUTTER_UPLOAD_FILES_DIR . "</li>";
		}elseif (!is_writable(FLUTTER_UPLOAD_FILES_DIR)){
			$dir_list.= "<li>".FLUTTER_UPLOAD_FILES_DIR . "</li>";
		}

		if (!is_dir(FLUTTER_MODULES_DIR)){
			$dir_list2.= "<li>".FLUTTER_MODULES_DIR . "</li>";
		}elseif (!is_writable(FLUTTER_MODULES_DIR)){
			$dir_list.= "<li>".FLUTTER_MODULES_DIR . "</li>";
		}


        //@todo add the   tmp folder
		if ($dir_list2 != ""){
			echo "<div id='flutter-install-error-message' class='error'><p><strong>".__('Flutter is not ready yet.', $flutter_domain)."</strong> ".__('must create the following folders (and must be writable):', $flutter_domain)."</p><ul>";
			echo $dir_list2;
			echo "</ul></div>";
		}
		if ($dir_list != ""){
			echo "<div id='flutter-install-error-message' class='error'><p><strong>".__('Flutter is not ready yet.', $flutter_domain)."</strong> ".__('The following folders must be writable (usually chmod 777 is neccesary):', $flutter_domain)."</p><ul>";
			echo $dir_list;
			echo "</ul></div>";
		}

	}
	
	
	/**
	 * Checks for the existance of unzip
	 * 
	 * @access private 
	 */

	function CheckDecompressionProgramUnzip() {

		$return = exec("unzip -help", $output, $returnValue);
		if ( 0 != $returnValue ) return false;

		return true;
	}
	
	/*
	 * Checks for the existance of zip
	 */

	function CheckCompressionProgramZip() {

		$return = exec("zip -help", $output, $returnValue);
		if ( 0 != $returnValue ) return false;

		return true;
	}
	
    
    /**
	 * Import default modules/panels in the theme if it is the first time to 
	 * activate the theme and add the theme settings to the database.
	 *
	 */
	function ImportNewTheme($themeName){
		
		// -- Add/update theme settings to the database
		
		FlutterLayoutBlock::UpdateModuleSettings(get_template_directory().'/configure.xml', -1, '-', get_option('template'));
		FlutterLayoutBlock::UpdateAllModulesSettings();


		// -- Import modules and panels
		
		if (RCCWP_Application::IsWordpressMu()){
			if (get_site_option('Flutter_theme_ft_'.$themeName) == '1') return;
			update_site_option('Flutter_theme_ft_'.$themeName, '1');
		}else{
			if (get_option('Flutter_theme_ft_'.$themeName) == '1') return;
			update_option('Flutter_theme_ft_'.$themeName, '1');
		}
		
		$modulesFolder = get_template_directory().'/Flutter_modules/';
		$panelsFolder = get_template_directory().'/Flutter_panels/';
		
		// Import modules
		if ($handle = @opendir($modulesFolder)) {
			while (false !== ($file = readdir($handle))) { 
				$filePath = $modulesFolder.$file;
				RCCWP_CustomWriteModule::Import($filePath);
			}
		}
		
		// Import panels
		if ($handle = @opendir($panelsFolder)) {
			while (false !== ($file = readdir($handle))) { 
				$filePath = $panelsFolder.$file;
				RCCWP_CustomWritePanel::Import($filePath);
			}
		}
				
	}
}
?>
