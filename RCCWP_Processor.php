<?php
class RCCWP_Processor
{
	function Main()
	{
		require_once('RC_Format.php');
		global $CUSTOM_WRITE_PANEL;
		
        wp_enqueue_script('jquery-ui-sortable');
		
		if (isset($_POST['edit-with-no-custom-write-panel']))
		{
			
			wp_redirect('post.php?action=edit&post=' . $_POST['post-id'] . '&no-custom-write-panel=' . $_POST['custom-write-panel-id']);
		}
		else if (isset($_POST['edit-with-custom-write-panel']))
		{
			
			wp_redirect('post.php?action=edit&post=' . $_POST['post-id'] . '&custom-write-panel-id=' . $_POST['custom-write-panel-id']);
		}
	
        if(empty($_REQUEST['flutter_action'])){
            $currentAction = "";
        }else{
            $currentAction = $_REQUEST['flutter_action'];
        }
		switch ($currentAction){
			
			// ------------ Write Panels
			case 'finish-create-custom-write-panel':
				include_once('RCCWP_CustomWritePanel.php');
					
				$default_theme_page=NULL;
				if($_POST['radPostPage'] == 'page'){ $default_theme_page = $_POST['page_template']; }
				
				$customWritePanelId = RCCWP_CustomWritePanel::Create(
					$_POST['custom-write-panel-name'],
					$_POST['custom-write-panel-description'],
					$_POST['custom-write-panel-standard-fields'],
					$_POST['custom-write-panel-categories'],
					$_POST['custom-write-panel-order'],
					FALSE,
					true,
					$_POST['single'],
					$default_theme_page
				);

				wp_redirect(RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('view-custom-write-panel', $customWritePanelId));
				break;
				
			case 'submit-edit-custom-write-panel':
				include_once('RCCWP_CustomWritePanel.php');
				
				$default_theme_page=NULL;
				if($_POST['radPostPage'] == 'page'){ $default_theme_page = $_POST['page_template']; }

				RCCWP_CustomWritePanel::Update(
					$_POST['custom-write-panel-id'],
					$_POST['custom-write-panel-name'],
					$_POST['custom-write-panel-description'],
					$_POST['custom-write-panel-standard-fields'],
					$_POST['custom-write-panel-categories'],
					$_POST['custom-write-panel-order'],
					FALSE,
					true,
					$_POST['single'],
					$default_theme_page
				);
				
				RCCWP_CustomWritePanel::AssignToRole($_POST['custom-write-panel-id'], 'administrator');
				break;
				
				
			case 'export-custom-write-panel':				
				require_once('RCCWP_CustomWritePanel.php');	
				$panelID = $_REQUEST['custom-write-panel-id'];
				$writePanel = RCCWP_CustomWritePanel::Get($panelID);
				$exportedFilename = $tmpPath = sys_get_temp_dir().DIRECTORY_SEPARATOR. $writePanel->name . '.pnl';
				
				RCCWP_CustomWritePanel::Export($panelID, $exportedFilename);
	
				// send file in header
				header('Content-type: binary');
				header('Content-Disposition: attachment; filename="'.$writePanel->name.'.pnl"');
				readfile($exportedFilename);
				unlink($exportedFilename);
				exit();	
				break;
				
			case 'delete-custom-write-panel':
				include_once('RCCWP_CustomWritePanel.php');
				RCCWP_CustomWritePanel::Delete($_GET['custom-write-panel-id']);
				//wp_redirect('?page=' . urlencode(FLUTTER_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'RCCWP_Menu.php'));
				break;

				
			// ------------ Modules
			
			// ------------ Groups
			case 'finish-create-custom-group':
				include_once('RCCWP_CustomGroup.php');
				$customGroupId = RCCWP_CustomGroup::Create(
						$_POST['custom-write-panel-id'], $_POST['custom-group-name'], $_POST['custom-group-duplicate'], $_POST['custom-group-at_right']);
				break;
				
			case 'delete-custom-group':
				include_once('RCCWP_CustomGroup.php');
				$customGroup = RCCWP_CustomGroup::Get((int)$_REQUEST['custom-group-id']);
				RCCWP_CustomGroup::Delete($_GET['custom-group-id']);
				break;

			case 'submit-edit-custom-group':				
				include_once('RCCWP_CustomGroup.php');
				RCCWP_CustomGroup::Update(
					$_REQUEST['custom-group-id'],
					$_POST['custom-group-name'],
					$_POST['custom-group-duplicate'],
					$_POST['custom-group-at_right']);
				break;
										
			// ------------ Fields
			case 'copy-custom-field':
				include_once('RCCWP_CustomField.php');
				$fieldToCopy = RCCWP_CustomField::Get($_REQUEST['custom-field-id']);
				
				if (RCCWP_Processor::CheckFieldName($fieldToCopy->name, $_REQUEST['custom-write-panel-id'])){
					$newURL = RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('create-custom-field').'&custom-group-id='.$_REQUEST['custom-group-id'].'&err_msg=-1';
					wp_redirect($newURL);
					exit;
				}
								
				RCCWP_CustomField::Create(
					$_REQUEST['custom-group-id'],
					$fieldToCopy->name,
					$fieldToCopy->description,
					$fieldToCopy->display_order,
					$fieldToCopy->required_field,
					$fieldToCopy->type_id,
					$fieldToCopy->options,
					$fieldToCopy->default_value,
					$fieldToCopy->properties,
					$fieldToCopy->duplicate
					);
				
			case 'continue-create-custom-field':
				if (RCCWP_Processor::CheckFieldName($_POST['custom-field-name'], $_REQUEST['custom-write-panel-id'])){
					$newURL = RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('create-custom-field').'&custom-group-id='.$_REQUEST['custom-group-id'].'&err_msg=-1';
					wp_redirect($newURL);
					exit;
				}
				break;
				
			case 'finish-create-custom-field':
				include_once('RCCWP_CustomField.php');
				
				if (RCCWP_Processor::CheckFieldName($_POST['custom-field-name'], $_REQUEST['custom-write-panel-id'])){
					$newURL = RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('create-custom-field').'&custom-group-id='.$_REQUEST['custom-group-id'].'&err_msg=-1';
					wp_redirect($newURL);
					exit;
				}
					
				$current_field = RCCWP_CustomField::GetCustomFieldTypes((int)$_REQUEST['custom-field-type']);
				
				if ($current_field->has_properties)
				{
					$custom_field_properties = array();
					if (in_array($current_field->name, array('Textbox', 'Listbox')))
					{
						$custom_field_properties['size'] = $_POST['custom-field-size'];
					}
					else if (in_array($current_field->name, array('Multiline Textbox')))
					{
						$custom_field_properties['height'] = $_POST['custom-field-height'];
						$custom_field_properties['width'] = $_POST['custom-field-width'];
					}
					else if (in_array($current_field->name, array('Date')))
					{
						$custom_field_properties['format'] = $_POST['custom-field-date-format'];
					}
					else if( in_array( $current_field->name, array('Image') ) )
					{
						$params = '';
						if( $_POST['custom-field-photo-height'] != '' && is_numeric( $_POST['custom-field-photo-height']) )
						{
							$params .= '&h=' . $_POST['custom-field-photo-height'];
						}
	
						if( $_POST['custom-field-photo-width'] != '' && is_numeric( $_POST['custom-field-photo-width']) )
						{
							$params .= '&w=' . $_POST['custom-field-photo-width'];
						}
						
						if( $_POST['custom-field-custom-params'] != '' )
						{
							$params .= '&' . $_POST['custom-field-custom-params'];
						}
	
						if( $params )
						{
							$custom_field_properties['params'] = $params;
						}
					}
					else if (in_array($current_field->name, array('Date')))
					{
						$custom_field_properties['format'] = $_POST['custom-field-date-format'];
					}
					else if (in_array($current_field->name, array('Slider')))
					{
						$custom_field_properties['max'] = $_POST['custom-field-slider-max'];
						$custom_field_properties['min'] = $_POST['custom-field-slider-min'];
						$custom_field_properties['step'] = $_POST['custom-field-slider-step'];
					}
				}
				
				RCCWP_CustomField::Create(
					$_POST['custom-group-id'],
					$_POST['custom-field-name'],
					$_POST['custom-field-description'],
					$_POST['custom-field-order'],
					$_POST['custom-field-required'],
					$_POST['custom-field-type'],
					$_POST['custom-field-options'],
					$_POST['custom-field-default-value'],
					$custom_field_properties,
					$_POST['custom-field-duplicate']
					);
				break;
				
			case 'submit-edit-custom-field':
				
				include_once('RCCWP_CustomField.php');
				
				
				$current_field_obj = RCCWP_CustomField::Get($_POST['custom-field-id']);
				if ($_POST['custom-field-name']!=$current_field_obj->name && RCCWP_Processor::CheckFieldName($_POST['custom-field-name'], $_REQUEST['custom-write-panel-id'])){
					$newURL = RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('edit-custom-field').'&custom-field-id='.$_POST['custom-field-id'].'&err_msg=-1';
					wp_redirect($newURL);
					exit;
				}
				
				$current_field = RCCWP_CustomField::GetCustomFieldTypes((int)$_POST['custom-field-type']);
				
				if ($current_field->has_properties)
				{
					$custom_field_properties = array();
					if (in_array($current_field->name, array('Textbox', 'Listbox')))
					{
						$custom_field_properties['size'] = $_POST['custom-field-size'];
					}
					else if (in_array($current_field->name, array('Multiline Textbox')))
					{
						$custom_field_properties['height'] = $_POST['custom-field-height'];
						$custom_field_properties['width'] = $_POST['custom-field-width'];
					}
					else if( in_array( $current_field->name, array('Image') ) )
					{ 
						$params = '';
						
						if( $_POST['custom-field-photo-height'] != '' && is_numeric( $_POST['custom-field-photo-height']) )
						{
							$params = '&h=' . $_POST['custom-field-photo-height'];
						}
	
						if( $_POST['custom-field-photo-width'] != '' && is_numeric( $_POST['custom-field-photo-width']) )
						{
							$params .= '&w=' . $_POST['custom-field-photo-width'];
						}
						
						if( $_POST['custom-field-custom-params'] != '' )
						{
							$params .= '&' . $_POST['custom-field-custom-params'];
						}
	
						if( $params )
						{
							$custom_field_properties['params'] = $params;
						}
					}
					else if (in_array($current_field->name, array('Date')))
					{
						$custom_field_properties['format'] = $_POST['custom-field-date-format'];
					}
					else if (in_array($current_field->name, array('Slider')))
					{
						$custom_field_properties['max'] = $_POST['custom-field-slider-max'];
						$custom_field_properties['min'] = $_POST['custom-field-slider-min'];
						$custom_field_properties['step'] = $_POST['custom-field-slider-step'];
					}
				}
				
				RCCWP_CustomField::Update(
					$_POST['custom-field-id'],
					$_POST['custom-field-name'],
					$_POST['custom-field-description'],
					$_POST['custom-field-order'],
					$_POST['custom-field-required'],
					$_POST['custom-field-type'],
					$_POST['custom-field-options'],
					$_POST['custom-field-default-value'],
					$custom_field_properties,
					$_POST['custom-field-duplicate']
					);
					
				break;
				
			case 'delete-custom-field':
				
				include_once('RCCWP_CustomField.php');
				
				if(isset($_REQUEST['custom-group-id']) && !empty($_REQUEST['custom-group-id']) )
					$customGroupId = (int)$_REQUEST['custom-group-id'];
	
				$customGroup = RCCWP_CustomGroup::Get($customGroupId);
	
				RCCWP_CustomField::Delete($_REQUEST['custom-field-id']);
	
				break;

            case 'delete-theme-settings':
                include_once('RCCWP_ThemeSettingsPage.php');

                $settings = new RCCWP_ThemeSettingsPage;

                $settings->remove_layout_setting();

                break;

           case 'create-layout-setting':
                include_once('RCCWP_ThemeSettingsPage.php');
                if(!empty($_POST['variable_name'])){
                    RCCWP_ThemeSettingsPage::finish_create_layout_element();
                   break;
                }
            default:
								
				if (RCCWP_Application::InWritePostPanel())
				{
					include_once('RCCWP_Menu.php');
					include_once('RCCWP_WritePostPage.php');
					
					$CUSTOM_WRITE_PANEL = RCCWP_Post::GetCustomWritePanel();
					
					
					if (isset($CUSTOM_WRITE_PANEL) && $CUSTOM_WRITE_PANEL > 0)
					{
								
						ob_start(array('RCCWP_WritePostPage', 'ApplyCustomWritePanelAssignedCategories'));
													
						add_action('admin_head', array('RCCWP_WritePostPage', 'ApplyCustomWritePanelHeader'));
						
						// Allows fields to be added to right
//	commented to test		add_action('submitpost_box', array('RCCWP_WritePostPage', 'CustomFieldCollectionInterfaceRight'), 5); 
//						add_action('submitpage_box', array('RCCWP_WritePostPage', 'CustomFieldCollectionInterfaceRight'), 5);
// commented to test
						add_action('add_meta_box', 'post', 'side', array('RCCWP_WritePostPage', 'CustomFieldCollectionInterfaceRight'));
						
						// Allows fields to be added to the post edit body
						add_action('simple_edit_form', array('RCCWP_WritePostPage', 'CustomFieldCollectionInterface'), 5);
						add_action('edit_form_advanced', array('RCCWP_WritePostPage', 'CustomFieldCollectionInterface'), 5);
						add_action('edit_page_form', array('RCCWP_WritePostPage', 'CustomFieldCollectionInterface'), 5);

					}
					else if (!isset($_REQUEST['no-custom-write-panel']) && isset($_REQUEST['post']))
					{
						include_once('RCCWP_Options.php');
						$promptEditingPost = RCCWP_Options::Get('prompt-editing-post');
						if ($promptEditingPost == 1)
						{
							wp_redirect('?page=' . urlencode(FLUTTER_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'RCCWP_Menu.php') . '&assign-custom-write-panel=' . (int)$_GET['post']);
						}
					}
				}
				
				
				else if (isset($_POST['finish-create-custom-write-module']))
				{
					include_once('RCCWP_CustomWriteModule.php');
					$customWriteModuleId = RCCWP_CustomWriteModule::Create(
							$_POST['custom-write-module-name'], $_POST['custom-write-module-description']);
		
					//RCCWP_CustomWritePanel::AssignToRole($customWritePanelId, 'administrator');
					if ($customWriteModuleId == -1){
						//$_POST['create-custom-write-module'] = 1;
						$modulesURL = '?page=' . 'FlutterManageModules' . '&view-modules=1&create-custom-write-module=1&err_msg=-1';
						wp_redirect($modulesURL);
						
					}
					else
						wp_redirect(RCCWP_ManagementPage::GetCustomWriteModuleEditUrl($customWriteModuleId));
				}
				
				else if (isset($_POST['submit-edit-custom-write-module']))
				{
						include_once('RCCWP_CustomWriteModule.php');
						
						$customWriteModuleId = RCCWP_CustomWriteModule::Update(
							$_REQUEST['custom-write-module-id'],
							$_REQUEST['custom-write-module-name'],
							$_REQUEST['custom-write-module-description']);
		
						if ($customWriteModuleId == -1){
							$customWriteModuleId = $_REQUEST['custom-write-module-id'];
							$modulesURL = '?page=' . 'FlutterManageModules' . "&edit-custom-write-module=1&view-custom-write-module=$customWriteModuleId&custom-write-module-id=$customWriteModuleId&err_msg=-1";
							wp_redirect($modulesURL);
							
						}
						
							
						//RCCWP_CustomWritePanel::AssignToRole($_POST['custom-write-panel-id'], 'administrator');
				}
				
		
				
				
				else if (isset($_POST['update-custom-write-panel-options']))
				{
					if ($_POST['uninstall-custom-write-panel'] == 'uninstall')
					{
						RCCWP_Application::Uninstall();
						wp_redirect('options-general.php');
					}
					else
					{
						include_once('RCCWP_Options.php');
						
						$options['hide-write-post'] = $_POST['hide-write-post'];
						$options['hide-write-page'] = $_POST['hide-write-page'];
						$options['hide-visual-editor'] = $_POST['hide-visual-editor'];
						$options['prompt-editing-post'] = $_POST['prompt-editing-post'];
						$options['assign-to-role'] = $_POST['assign-to-role'];
						$options['use-snipshot'] = $_POST['use-snipshot'];
						$options['enable-editnplace'] = $_POST['enable-editnplace'];
						$options['eip-highlight-color'] = $_POST['eip-highlight-color'];
						$options['enable-swfupload'] = $_POST['enable-swfupload'] ;
						$options['enable-browserupload'] = $_POST['enable-browserupload'];
						$options['default-custom-write-panel'] = $_POST['default-custom-write-panel'];
						$options['enable-HTMLPurifier'] = $_POST['enable-HTMLPurifier'];
						$options['tidy-level'] = $_POST['tidy-level'];
						$options['canvas_show_instructions'] = $_POST['canvas_show_instructions'];
						$options['canvas_show_zone_name'] = $_POST['canvas_show_zone_name'];
						$options['canvas_show'] = $_POST['canvas_show'];
						$options['ink_show'] = $_POST['ink_show'];
		
						
						RCCWP_Options::Update($options);
					}
				}
				else if (isset($_REQUEST['create-module-duplicate']))
				{
					include_once('RCCWP_ModuleDuplicate.php');
					$moduleID = $_REQUEST['custom-write-module-id'];
					RCCWP_ModuleDuplicate::Create($moduleID);
					wp_redirect(RCCWP_ManagementPage::GetCustomWriteModuleEditUrl($moduleID));
				}
				else if (isset($_POST['submit-edit-module-duplicate']))
				{
					include_once('RCCWP_ModuleDuplicate.php');
					$moduleID = $_REQUEST['custom-write-module-id'];
					RCCWP_ModuleDuplicate::Update(
						$_REQUEST['module-duplicate-id'],
						$_REQUEST['module-duplicate-name']);
					wp_redirect('?page=' . urlencode(FLUTTER_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'RCCWP_Menu.php') . '&view-custom-write-module=' . $moduleID . '&custom-write-module-id=' . $moduleID);
				}
				else if (isset($_REQUEST['delete-module-duplicate']))
				{
					include_once('RCCWP_ModuleDuplicate.php');
					$moduleID = $_REQUEST['custom-write-module-id'];
					RCCWP_ModuleDuplicate::Delete($_REQUEST['module-duplicate-id']);
					wp_redirect(RCCWP_ManagementPage::GetCustomWriteModuleEditUrl($moduleID));
				}
				
				else if (isset($_POST['delete-custom-write-module']))
				{
					include_once('RCCWP_CustomWriteModule.php');
					$moduleID = $_REQUEST['custom-write-module-id'];
					RCCWP_CustomWriteModule::Delete($moduleID);
					wp_redirect('?page=' . urlencode(FLUTTER_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'RCCWP_Menu.php'). '&view-modules=1');
				}
		
		}
		
	}
	
	
	
	function FlushAllOutputBuffer() 
	{ 
		
		while (@ob_end_flush()); 
		
	} 
	
	function Redirect($location)
	{
		global $post_ID;
		global $page_ID;

		
		if (!empty($_REQUEST['rc-cwp-custom-write-panel-id']))
		{
			if (strstr($location, 'post-new.php?posted=') || strstr($location, 'page-new.php?posted='))
			{
				$id = ($post_ID=="")?$page_ID:$post_ID;
				$location = $_REQUEST['_wp_http_referer'] . '&posted=' . $id;
			}
		}
		return $location;
	}
	
	function CheckFieldName($fieldName, $panelID){
		global $wpdb;
		
		$sql = "SELECT id, group_id FROM " . RC_CWP_TABLE_GROUP_FIELDS .
				" WHERE name='$fieldName' ";
		$results =$wpdb->get_results($sql);
	
		foreach($results as $result){
			$fieldGroup = RCCWP_CustomGroup::Get($result->group_id);
			if ($panelID == $fieldGroup->panel_id){
				return true;
			}
		}
		return false;
	}

}
?>
