<?php
require_once ('RCCWP_ThemeSettingsPage.php');
require_once ('RCCWP_Application.php');
require_once ('RCCWP_ManagementPage.php');
require_once ('RCCWP_CreateCustomWritePanelPage.php');
require_once ('RCCWP_CreateCustomWriteModulePage.php');
require_once ('RCCWP_CustomWriteModulePage.php');
require_once ('RCCWP_CreateCustomGroupPage.php');
require_once ('RCCWP_CreateCustomFieldPage.php');
require_once ('RCCWP_CustomFieldPage.php');
require_once ('RCCWP_CreatePanelModulePage.php');
require_once ('RCCWP_ModuleDuplicate.php');
require_once ('RCCWP_ModuleDuplicatePage.php');

class RCCWP_Menu
{
	function PrepareModulesPanelsMenuItems()
	{
		$sub_menu_is_modules = false;
		
		if(empty($_REQUEST['flutter_action'])){
            $currentAction = "";
        }else{
            $currentAction = $_REQUEST['flutter_action'];
        }
		
		switch ($currentAction){
			
			// ------------ Custom Fields
			case 'create-custom-field':
				$page_group = 'RCCWP_CreateCustomFieldPage';
				$page_type = 'Main';
				break;

			case 'continue-create-custom-field':		
				if(isset($_REQUEST['custom-group-id']) && !empty($_REQUEST['custom-group-id']) )
					$customGroupId = (int)$_REQUEST['custom-group-id'];
				$customGroup = RCCWP_CustomGroup::Get($customGroupId);
	
				$current_field = RCCWP_CustomField::GetCustomFieldTypes((int)$_REQUEST['custom-field-type']);
				if ($current_field->has_options == "true" || $current_field->has_properties == "true")
				{
					$page_group = 'RCCWP_CreateCustomFieldPage';
					$page_type = 'SetOptions';
				}
				else if ($current_field->has_options == "false")
				{
					RCCWP_CustomField::Create(
						$_POST['custom-group-id'],
						$_POST['custom-field-name'],
						$_POST['custom-field-description'],
						$_POST['custom-field-order'],
						$_POST['custom-field-required'],
						$_POST['custom-field-type'],
						$_POST['custom-field-options'],
						null,null,
						$_POST['custom-field-duplicate']);
	
					$page_group = 'RCCWP_CustomWritePanelPage';
					$page_type = 'View';
					/*if ($customGroup->name=='__default'){
						$page_group = 'RCCWP_CustomWritePanelPage';
						$page_type = 'View';
					}
					else{
						$page_group = 'RCCWP_CustomGroupPage';
						$page_type = 'View';
					}*/
				}
				break;
		
			case 'delete-custom-field':
				$page_group = 'RCCWP_CustomWritePanelPage';
				$page_type = 'View';
				break;
					
			case 'finish-create-custom-field':		
			case 'cancel-edit-custom-field':
			case 'cancel-create-custom-field':
			case 'submit-edit-custom-field':
			case 'copy-custom-field':
				/*$customGroupId = false;
				$customGroupId = (int)$_REQUEST['custom-group-id'];
	
				$customGroup = RCCWP_CustomGroup::Get($customGroupId);
				if ($customGroup->name=='__default'){
					$page_group = 'RCCWP_CustomWritePanelPage';
					$page_type = 'View';
				}
				else{
					$page_group = 'RCCWP_CustomGroupPage';
					$page_type = 'View';
				}*/
				$page_group = 'RCCWP_CustomWritePanelPage';
				$page_type = 'View';
				break;
				
			case 'edit-custom-field':
				$page_group = 'RCCWP_CustomFieldPage';
				$page_type = 'Edit';
				break;
		
			// ------------ Groups
			
			case 'create-custom-group':
				$page_group = 'RCCWP_CreateCustomGroupPage';
				$page_type = 'Main';
				break;
					
			case 'view-custom-group':
				$page_group = 'RCCWP_CustomGroupPage';
				$page_type = 'View';
				break;

			case 'cancel-edit-custom-group':
			case 'cancel-create-custom-group':
			case 'delete-custom-group':
			case 'submit-edit-custom-group':
			case 'finish-create-custom-group':
				$page_group = 'RCCWP_CustomWritePanelPage';
				$page_type = 'View';
				break;
				
			case 'edit-custom-group':
				$page_group = 'RCCWP_CustomGroupPage';
				$page_type = 'Edit';
				break;
				
				

			// ------------ Custom Write Panels

			case 'view-custom-write-panel':
				$page_group = 'RCCWP_CustomWritePanelPage';
				$page_type = 'View';
				break;
				
			case 'create-custom-write-panel':
				$page_group = 'RCCWP_CreateCustomWritePanelPage';
				$page_type = 'Main';
				break;

			case 'finish-create-custom-write-panel':
				$page_group = 'RCCWP_CustomWritePanelPage';
				$page_type = 'View';
				break;
				
			case 'edit-custom-write-panel':
				$page_group = 'RCCWP_CustomWritePanelPage';
				$page_type = 'Edit';
				break;
				
			case 'cancel-edit-custom-write-panel':
				$page_group = 'RCCWP_CustomWritePanelPage';
				$page_type = 'View';
				break;
				
			case 'submit-edit-custom-write-panel':
				$page_group = 'RCCWP_CustomWritePanelPage';
				$page_type = 'View';
				break;
				
			case 'import-write-panel':
				$page_group = 'RCCWP_CustomWritePanelPage';
				$page_type = 'Import';
				break;
				

			// ------------ Modules
			case 'import-module':
				$page_group = 'RCCWP_CustomWriteModulePage';
				$page_type = 'Import';
				$sub_menu_is_modules = true;
				break;
				
			case 'create-custom-write-module':
				$page_group = 'RCCWP_CreateCustomWriteModulePage';
				$page_type = 'Main';
				$sub_menu_is_modules = true;
				break;
				
			case 'prepare-export-write-module':
				$page_group = 'RCCWP_CustomWriteModulePage';
				$page_type = 'PrepareExport';
				$sub_menu_is_modules = true;
				break;
												
			default:
				
				
				
				if (isset($_REQUEST['assign-custom-write-panel']))
				{
					$page_group = 'RCCWP_ManagementPage';
					$page_type = 'AssignCustomWritePanel';
					$sub_menu_is_modules = false;
				}
				// ------- Groups
				
				else if (isset($_REQUEST['cancel-edit-custom-group']))
				{
					$page_group = 'RCCWP_CustomGroupPage';
					$page_type = 'View';
				}
				
				else if (isset($_REQUEST['view-groups']))
				{
					$page_group = 'RCCWP_ManagementPage';
					$page_type = 'ViewGroups';
				}
				
				// ------- Modules
				
				else if (isset($_REQUEST['edit-custom-write-module']))
				{
					$page_group = 'RCCWP_CustomWriteModulePage';
					$page_type = 'Edit';
					$sub_menu_is_modules = true;
				}
				else if (isset($_REQUEST['cancel-edit-custom-write-module']))
				{
					$page_group = 'RCCWP_CustomWriteModulePage';
					$page_type = 'View';
					$sub_menu_is_modules = true;
				}
				else if (isset($_REQUEST['submit-edit-custom-write-module']))
				{
					$page_group = 'RCCWP_CustomWriteModulePage';
					$page_type = 'View';
					$sub_menu_is_modules = true;
				}
				else if (isset($_REQUEST['view-custom-write-module']))
				{
					$page_group = 'RCCWP_CustomWriteModulePage';
					$page_type = 'View';
					$sub_menu_is_modules = true;
				}
				else if (isset($_REQUEST['view-modules']))
				{
					$page_group = 'RCCWP_ManagementPage';
					$page_type = 'ViewModules';
					$sub_menu_is_modules = true;
				}
				
				// ------- Modules Duplicates
				else if (isset($_REQUEST['cancel-edit-module-duplicate']))
				{
					$page_group = 'RCCWP_CustomWriteModulePage';
					$page_type = 'View';
					$sub_menu_is_modules = true;
				}
				else if (isset($_REQUEST['edit-module-duplicate']))
				{
					$page_group = 'RCCWP_ModuleDuplicatePage';
					$page_type = 'Edit';
					$sub_menu_is_modules = true;
				}
				else if (isset($_REQUEST['submit-edit-module-duplicate']))
				{
					$page_group = 'RCCWP_CustomWriteModulePage';
					$page_type = 'View';
					$sub_menu_is_modules = true;
				}
				// ------- Default behavior
				else{
					$page_group = 'RCCWP_CustomWritePanelPage';
					$page_type = 'ViewWritePanels';
					$sub_menu_is_modules = false;
				}
				
		}
		
		
		
		
		
				


		if ($sub_menu_is_modules){
			$result->panelsMenuFunction = array('RCCWP_CustomWritePanelPage', 'ViewWritePanels');
			$result->modulesMenuFunction = array($page_group, $page_type);
		}
		else{
			$result->panelsMenuFunction = array($page_group, $page_type);
			$result->modulesMenuFunction = array('RCCWP_ManagementPage', 'ViewModules');
		}

		return $result;


	}

	/**
     * Adding menus  
     *
     *
     */
	function AttachFlutterMenus()
	{
		global $flutter_domain;
		require_once ('RCCWP_OptionsPage.php');

		//if ((!current_user_can(FLUTTER_CAPABILITY_PANELS) && !current_user_can(FLUTTER_CAPABILITY_MODULES)))
		//	return;

		$panelsAndModulesFunctions = RCCWP_Menu::PrepareModulesPanelsMenuItems();

		// Add top menu
		add_menu_page(__('Flutter > Manage',$flutter_domain), __('Flutter',$flutter_domain), 10, __FILE__, $panelsAndModulesFunctions->panelsMenuFunction);

        // Add Flutter submenus
		add_submenu_page(__FILE__, __('Write Panels',$flutter_domain), __('Write Panels',$flutter_domain), 10, __FILE__, $panelsAndModulesFunctions->panelsMenuFunction);
		add_submenu_page(__FILE__, __('Modules',$flutter_domain), __('Modules',$flutter_domain), 10, 'FlutterManageModules', $panelsAndModulesFunctions->modulesMenuFunction);		
		add_submenu_page(__FILE__, __('Template Options',$flutter_domain),__('Template Options',$flutter_domain),10,'RCCWP_ThemeSettingsPage',array('RCCWP_ThemeSettingsPage','show_layout_settings'));
     }

	function AttachOptionsMenuItem()
	{

		require_once ('RCCWP_OptionsPage.php');
		add_options_page(__('Flutter Options',$flutter_domain), __('Flutter',$flutter_domain), 'manage_options', 'RCCWP_OptionsPage.php', array('RCCWP_OptionsPage', 'Main'));
	}
	
	function AttachCustomWritePanelMenuItems()
	{
		global $wp_version;
		global $submenu,$menu;
		global $flutter_domain,$wpdb;
		require_once ('RCCWP_Options.php');
		$assignToRole = RCCWP_Options::Get('assign-to-role');
		$requiredPostsCap = 'edit_posts';
		$requiredPagesCap = 'edit_pages';

		$customWritePanels = RCCWP_CustomWritePanel::GetCustomWritePanels();

		
		
		if($wp_version < 2.7){
			
			$add_post =  false;
			foreach ($customWritePanels as $panel)
			{
				if($panel->single == 1){
					$has_posts = $wpdb->get_var('SELECT post_id FROM '.$wpdb->prefix.'postmeta  where meta_key = "_rc_cwp_write_panel_id" and  meta_value = '.$panel->id);
					if(empty($has_posts)){
						$add_post = true;
					}else{
						$add_post = false;
					}
				}
			
				if ($assignToRole == 1){
					$requiredPostsCap = $panel->capability_name;
					$requiredPagesCap = $panel->capability_name;
				}
	
				if ($panel->type == "post"){
					if($panel->single == 1){  //if the post is single
						if($add_post){ //if the post is single and don't have any related post
							add_submenu_page('post-new.php', __($panel->name), __($panel->name), $requiredPostsCap, 'post-new.php?custom-write-panel-id=' . $panel->id);
						}else{ //if have one related post we just can  edit the post 
							add_submenu_page('post-new.php',__($panel->name),__($panel->name),8,'post.php?action=edit&post='.$has_posts);
						}   
			                }else{
						add_submenu_page('post-new.php', __($panel->name), __($panel->name), $requiredPostsCap, 'post-new.php?custom-write-panel-id=' . $panel->id);
						add_submenu_page('edit.php', __($panel->name), __($panel->name), $requiredPostsCap, 'edit.php?filter-posts=1&custom-write-panel-id=' . $panel->id);
					}
					
					
					
				}else {
					if($panel->single == 1){ //if the page is single
						if($add_post){ //if the page is single and don't have any related post
							add_submenu_page('post-new.php', __($panel->name), __($panel->name), $requiredPagesCap, 'page-new.php?custom-write-panel-id=' . $panel->id);
						}else{
							add_submenu_page('post-new.php',__($panel->name),__($panel->name),8,'page.php?action=edit&post='.$has_posts);
						}
					}else{
						add_submenu_page('post-new.php', __($panel->name), __($panel->name), $requiredPagesCap, 'page-new.php?custom-write-panel-id=' . $panel->id);
						add_submenu_page('edit.php', __($panel->name), __($panel->name), $requiredPagesCap, 'edit-pages.php?filter-posts=1&custom-write-panel-id=' . $panel->id);
					}
					
					
					
				}
			}
		}
		
		if ( $wp_version >= 2.7 ) {
			$new_indicator_text = __('New',$flutter_domain);
			$edit_indicator_text = __('Manage',$flutter_domain);
		
		
			$new_menu = array();
		
			foreach ($menu as $k => $v) {
				if($k > 5) break;
				$new_menu[$k]=$v;
			}
		
			$base=5;
			$offset=0;
			$add_post =  false;
			

			foreach ($customWritePanels as $panel){
				//exists a single write panel? and if exists  this write panel have posts?
				if($panel->single == 1){
					$has_posts = $wpdb->get_var('SELECT post_id FROM '.$wpdb->prefix.'postmeta  where meta_key = "_rc_cwp_write_panel_id" and  meta_value = '.$panel->id);
					if(empty($has_posts)){
						$add_post = true;
					}else{
						$add_post = false;
					}
				}

				$offset++;
				//thanks a Ashish Puliyel <ashish@gonzobuzz.com> by observing the roles of users
				if ($panel->type == "post"){
					$type_write_panel="edit-posts";
				}else{
					$type_write_panel="edit-pages";	
				}
				
				$new_menu[$base+$offset] = array( __($panel->name), $type_write_panel, $base+$offset.'.php', '', 'wp-menu-open menu-top', 'menu-pages', 'div' );
				if ($assignToRole == 1){
					$requiredPostsCap = $panel->capability_name;
					$requiredPagesCap = $panel->capability_name;
				}   

				if ($panel->type == "post"){
					if($panel->single == 1){  //if the post is single
						if($add_post){ //if the post is single and don't have any related post
							add_submenu_page($base+$offset.'.php', __($panel->name), $new_indicator_text, $requiredPostsCap, 'post-new.php?custom-write-panel-id=' . $panel->id);
						}else{ //if have one related post we just can  edit the post 
							add_submenu_page($base+$offset.'.php',__($panel->name),"Edit",$requiredPostsCap,'post.php?action=edit&post='.$has_posts);
						}   
			                }else{
						add_submenu_page($base+$offset.'.php', __($panel->name), $new_indicator_text, $requiredPostsCap, 'post-new.php?custom-write-panel-id=' . $panel->id);
						add_submenu_page($base+$offset.'.php', __($panel->name), $edit_indicator_text, $requiredPostsCap, 'edit.php?filter-posts=1&custom-write-panel-id=' . $panel->id);
					}
				}else{
					if($panel->single == 1){ //if the page is single
						if($add_post){ //if the page is single and don't have any related post
							add_submenu_page($base+$offset.'.php', __($panel->name), $new_indicator_text, $requiredPagesCap, 'page-new.php?custom-write-panel-id=' . $panel->id);
						}else{
							add_submenu_page($base+$offset.'.php',__($panel->name),"Edit",$requiredPagesCap,'page.php?action=edit&post='.$has_posts);
						}
					}else{
						add_submenu_page($base+$offset.'.php', __($panel->name), $new_indicator_text, $requiredPagesCap, 'page-new.php?custom-write-panel-id=' . $panel->id);
						add_submenu_page($base+$offset.'.php', __($panel->name), $edit_indicator_text, $requiredPagesCap, 'edit-pages.php?filter-posts=1&custom-write-panel-id=' . $panel->id);
					}
				}
			}
		
			foreach ($menu as $k => $v) {
				if($k > 5) $new_menu[$k+$offset]=$v;
			}
			
			$menu = $new_menu;
		} 
		RCCWP_Menu::SetCurrentCustomWritePanelMenuItem();
		
	}


    /** Adding Submenu Fluttler Layout settings**/
    function AttachLayoutSettingsPage(){
        global $wpdb;
	global $flutter_domain;
        $template = get_option('template');
        $template_module_id =  $wpdb->get_var("SELECT module_id  FROM ".FLUTTER_TABLE_LAYOUT_MODULES." WHERE theme = '{$template}'"); 
        $template_block_id =  $wpdb->get_var("SELECT block_id  FROM ".FLUTTER_TABLE_LAYOUT_MODULES." WHERE theme = '{$template}'"); 
        $count_settings  =  $wpdb->get_var("SELECT COUNT(*) FROM ".FLUTTER_TABLE_LAYOUT_VARIABLES." WHERE parent = '{$template_block_id}'");

        if($count_settings != 0){

    		$themeSettingsID = FlutterLayoutBlock::GetModuleSettingsID($template_module_id, '-');
	    	if (!empty($themeSettingsID)){
                add_submenu_page(
                                    "themes.php",
                                    __('Template Options',$flutter_domain),
                                    FLUTTER_CAPABILITY_LAYOUT,
                                    8,
                                    'Flutter_ThemeSettings',
                                    array('RCCWP_ThemeSettingsPage','Main')
                               );  
            }
        }
	}
	

	function AttachCustomWritePanelFavoriteActions()
	{
		global $flutter_domain;
		require_once ('RCCWP_Options.php');
		$assignToRole = RCCWP_Options::Get('assign-to-role');
		$requiredPostsCap = 'edit_posts';
		$requiredPagesCap = 'edit_pages';

		$actions = array(
		'post-new.php' => array(__('New Post',$flutter_domain), 'edit_posts'),
		'edit.php?post_status=draft' => array(__('Drafts',$flutter_domain), 'edit_posts'),	
		'page-new.php' => array(__('New Page',$flutter_domain), 'edit_pages'),
		'media-new.php' => array(__('Upload',$flutter_domain), 'upload_files'),
		'edit-comments.php' => array(__('Comments',$flutter_domain), 'moderate_comments')
		); 


		$customWritePanels = RCCWP_CustomWritePanel::GetCustomWritePanels();

		foreach ($customWritePanels as $panel)
		{
			if ($assignToRole == 1){
				$requiredPostsCap = $panel->capability_name;
				$requiredPagesCap = $panel->capability_name;
			}

			if ($panel->type == "post"){
//				$favorite_actions_panels['post-new.php?custom-write-panel-id=' . $panel->id] = array(__($panel->name).'New ', 'edit_posts');
				$actions['post-new.php?custom-write-panel-id=' . $panel->id] = array('New '.__($panel->name), 'edit_posts');
			}
			else {
//				$favorite_actions_panels['page-new.php?custom-write-panel-id=' . $panel->id] = array(__($panel->name).'New ', 'edit_pages');
				$actions['page-new.php?custom-write-panel-id=' . $panel->id] = array('New '.__($panel->name), 'edit_pages');
			}
		}
		return $actions;
	}
	
	function HighlightCustomPanel(){
		global $wpdb, $submenu_file, $post; 
		
		$result = $wpdb->get_results( " SELECT meta_value
						FROM $wpdb->postmeta
						WHERE post_id = '".$post->ID."' and meta_key = '_rc_cwp_write_panel_id'", ARRAY_A );
		$currPage = basename($_SERVER['SCRIPT_NAME']);
		if (count($result) > 0 && $currPage =="post.php" ){
			$id = $result[0]['meta_value'];
			$submenu_file = "edit.php?filter-posts=1&custom-write-panel-id=$id";
		}
		elseif (count($result) > 0 && $currPage == "page.php" ){
			$id = $result[0]['meta_value'];
			$submenu_file = "edit-pages.php?filter-posts=1&custom-write-panel-id=$id";
		}
		
		
	}

	function FilterPostsPagesList($where){
		global $wpdb;
		if (isset($_GET['filter-posts'])) {
			$panel_id = $_GET['custom-write-panel-id'];
			$where = $where . " AND 0 < (SELECT count($wpdb->postmeta.meta_value)
					FROM $wpdb->postmeta
					WHERE $wpdb->postmeta.post_id = $wpdb->posts.ID and $wpdb->postmeta.meta_key = '_rc_cwp_write_panel_id' and $wpdb->postmeta.meta_value = '$panel_id') ";
		}
		return $where;
		/*$i = 0;
		if (isset($_GET['filter-posts']) && (!empty($posts))) {
			$panel_id = $_GET['custom-write-panel-id'];
			foreach($posts as $my_post){
				$result = $wpdb->get_results( " SELECT meta_value
					FROM $wpdb->postmeta
					WHERE post_id = '$my_post->ID' and meta_key = '_rc_cwp_write_panel_id' and meta_value = '$panel_id'", ARRAY_A );
				if (count($result) == 0 )
					array_splice($posts, $i ,1);
				else
					$i++;
			}

		}
		return $posts;*/
	}
	
	function DetachWpWritePanelMenuItems()
	{
		
		
		global $menu;
		global $submenu;
		global $wp_version;

		require_once ('RCCWP_Options.php');
		
		$options = RCCWP_Options::Get();
		
		if($wp_version < 2.7){
			if ($options['hide-write-post'] == '1'){
				unset($submenu['post-new.php'][5]);
				unset($submenu['edit.php'][5]);
			}
		
			if ($options['hide-write-page'] == '1'){
				unset($submenu['post-new.php'][10]);
				unset($submenu['edit.php'][10]);
			}
		
		}
		
		if($wp_version >= 2.7){
			if ($options['hide-write-post'] == '1'){
				foreach ($menu as $k => $v){
					if ($v[2] == "edit.php"){
						unset($menu[$k]);
					}
				}
			}
	
			if ($options['hide-write-page'] == '1'){
				foreach ($menu as $k => $v){
					if ($v[2] == "edit-pages.php"){
						unset($menu[$k]);
					}
				}
			}
		}
	}
	
	function SetCurrentCustomWritePanelMenuItem()
	{
		
		global $submenu_file;
		global $menu;
		
		require_once ('RCCWP_Options.php');
		$options = RCCWP_Options::Get();
		
		if ($options['default-custom-write-panel'] != '')
		{
			require_once ('RCCWP_CustomWritePanel.php');
			
			$customWritePanel = RCCWP_CustomWritePanel::Get((int)$options['default-custom-write-panel']);
			
			if ($customWritePanel->type == "post")
				$menu[5][2] = 'post-new.php?custom-write-panel-id=' . (int)$options['default-custom-write-panel'];
			else
				$menu[5][2] = 'page-new.php?custom-write-panel-id=' . (int)$options['default-custom-write-panel'];
			
		}
		
        if(empty($_REQUEST['custom-write-panel-id'])){
            $_REQUEST['custom-write-panel-id'] = "";
        }

		if ($_REQUEST['custom-write-panel-id'])
		{
			$customWritePanel = RCCWP_CustomWritePanel::Get((int)$_REQUEST['custom-write-panel-id']);
			if ($_REQUEST['filter-posts']){
				if ($customWritePanel->type == "post")
					$submenu_file = 'edit.php?filter-posts=1&custom-write-panel-id=' . (int)$_REQUEST['custom-write-panel-id'];
				else
					$submenu_file = 'edit-pages.php?filter-posts=1&custom-write-panel-id=' . (int)$_REQUEST['custom-write-panel-id'];
			}
			else{
				if ($customWritePanel->type == "post")
					$submenu_file = 'post-new.php?custom-write-panel-id=' . (int)$_REQUEST['custom-write-panel-id'];
				else
					$submenu_file = 'page-new.php?custom-write-panel-id=' . (int)$_REQUEST['custom-write-panel-id'];
			}
		}

	}

	function AddThickbox()
	{
        if (!empty($GET['page']) && $_GET['page']=='FlutterManageModules') {
			// Overcome bug (http://wordpress.org/support/topic/196884)
			$thickBoxCSS = get_bloginfo('url').'/wp-includes/js/thickbox/thickbox.css';
			?>
			<link rel='stylesheet' href='<?php echo $thickBoxCSS?>' type='text/css' />
			<?php
			
			wp_enqueue_script('prototype');
			wp_enqueue_script('thickbox');
		}
		
	}
	
	function ShowPanel($panel){
		return true;
		require_once ('RCCWP_CustomWritePanel.php');
		global $wpdb, $canvas;

		if ($panel->always_show) return true;

		$custom_panel_modules = RCCWP_CustomWritePanel::GetPanelCustomModules($panel->id);
  		foreach ($custom_panel_modules as $panel_module){
			//echo "SELECT * FROM ".$canvas->main." WHERE module_id = $panel_module->mod_id AND zone <> 'shelf'";
			if($wpdb->get_results("SELECT * FROM ".$canvas->main." WHERE module_id = $panel_module->mod_id AND zone <> 'shelf'"))
				return true;
		}

		if ( 0 < $wpdb->get_var("SELECT count($wpdb->postmeta.meta_value)
			FROM $wpdb->postmeta
			WHERE $wpdb->postmeta.meta_key = '_rc_cwp_write_panel_id' and $wpdb->postmeta.meta_value = '$panel->id'")){
				return true;
		}

		return false;
	}
}
?>
