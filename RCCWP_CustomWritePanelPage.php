<?php
include_once('RCCWP_CustomWritePanel.php');

class RCCWP_CustomWritePanelPage
{
	function Content($customWritePanel = null)
	{
		global $flutter_domain,$wpdb;
		$customWritePanelName = "";
		$customWritePanelDescription = "";
		$write_panel_category_ids = array();
		$defaultTagChecked = 'checked="checked"';
		$customWritePanelAllFieldIds = NULL;
		$customThemePage = NULL;
		$showPost = true;
		if ($customWritePanel != null)
		{
			$customWritePanelName = $customWritePanel->name;
			$customWritePanelDescription = $customWritePanel->description;
			$customWritePanelDisplayOrder = $customWritePanel->display_order;
			$customWritePanelType = $customWritePanel->type;
			if ($customWritePanelType == 'page') $showPost = false;
			$customWritePanelCategoryIds = RCCWP_CustomWritePanel::GetAssignedCategoryIds($customWritePanel->id);
			$customWritePanelStandardFieldIds = RCCWP_CustomWritePanel::GetStandardFields($customWritePanel->id);
			$customWritePanelAllFieldIds = RCCWP_CustomWritePanel::Get($customWritePanel->id);
			
			if ($customWritePanelType == 'page'){
				$customThemePage = RCCWP_CustomWritePanel::GetThemePage($customWritePanel->name); }
			
			
			$defaultTagChecked = '';
			?>
			<input type="hidden" name="custom-write-panel-id" value="<?php echo $customWritePanel->id?>" />
			<?php
		}
		
  		?>


		<table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">
		<tbody>
		<tr valign="top">
			<th scope="row"><?php _e('Placement', $flutter_domain); ?></th>
        		<td>
				<!-- START :: Javascript for Image/Photo' Css Class -->
				<script type="text/javascript" language="javascript">
					jQuery(document).ready( function() {
							<?php if ($showPost){ ?>
								showHide("flutter_forpost", "flutter_forpage");
							<?php } else { ?>
								showHide("flutter_forpage", "flutter_forpost");
							<?php } ?>
						});
						
					function showHide(showClassID, hideClassID)
					{
						jQuery( function($) {
							$("."+showClassID).css("display","");
							$("."+hideClassID).css("display","none");
							});
					}
				</script>
				<!-- END :: Javascript for Image/Photo' Css Class -->
				<input type="radio" name="radPostPage" id="radPostPage" value="post" <?php if(empty($custoWritePanelType) || $customWritePanelType == 'post'){?> checked="checked" <?php } ?> onclick='showHide("flutter_forpost", "flutter_forpage");' /> <strong><?php _e('Post', $flutter_domain); ?> </strong> &nbsp; &nbsp; &nbsp; 
				<input type="radio" name="radPostPage" id="radPostPage" value="page" <?php if(!empty($customWritePanelType)  && $customWritePanelType == 'page'){?> checked="checked" <?php } ?> onclick='showHide("flutter_forpage", "flutter_forpost");' /> <strong><?php _e('Page', $flutter_domain); ?></strong>
			</td>
		</tr>


		<tr valign="top">
			<th scope="row"  align="right"><?php _e('Name', $flutter_domain); ?>:</th>
			<td>
				<input name="custom-write-panel-name" id="custom-write-panel-name" size="40" type="text" value="<?php echo $customWritePanelName?>" />
			</td>
		</tr>

	
		<tr valign="top"  id="catText" class="flutter_forpost">
			<th scope="row"  align="right"><div id="catLabel" style="display:inline;"><?php _e('Assigned Categories', $flutter_domain); ?>:</div></th>
			<td>
				
				<?php
				$cats = RCCWP_Application::GetWpCategories();
	
				foreach ($cats as $cat) : 
					$checked = "";
					if(isset($customWritePanel->id) && !empty($customWritePanel->id))
					{
						if (in_array($cat->cat_ID, $customWritePanelCategoryIds))
						{
							$checked = "checked=\"checked\"";
						}
					}
				?>
					<input type="checkbox" name="custom-write-panel-categories[]" value="<?php echo $cat->cat_ID?>" <?php echo $checked?> /> <?php echo $cat->cat_name ?> <br/>
				<?php
				endforeach;
				?>
				
			</td>
		</tr>
		
		<tr valign="top"  id="catText" class="flutter_forpage">
			<th scope="row"  align="right"><div id="catLabel" style="display:inline;"><?php _e('Assigned Theme', $flutter_domain); ?>:</div></th>
			<td>
				
				<select name="page_template" id="page_template">
					<option value='default'><?php _e('Default Template'); ?></option>
					<?php $themes_defaults = get_page_templates();
					foreach($themes_defaults as $v => $k) {
						if ($customWritePanelType == 'page'){
							$theme_select=NULL;
							if($customThemePage == $k){ $theme_select='SELECTED';}
						}?>
					<option value='<?=$k?>' <?=$theme_select?> ><?=$v?></option>
					<?php } ?>
					<?php  ?>
				</select>
		
			</td>
		</tr>
		
        <tr>
            <th><?php _e('Quantity',$flutter_domain);?></th>
            <td>
				<?php if(isset($customWritePanel->id) && !empty($customWritePanel->id))
					{
						if ($customWritePanelAllFieldIds->single == 0)
						{
							$multiple_checked='checked="checked"';
						}else{
							$single_checked='checked="checked"';
						}
					}else{
						$multiple_checked='checked="checked"';
					}
				?>
				<input type="radio" name="single" id="radPostPage" value="1" <?php echo $single_checked?>  /> <strong><?php _e('Single', $flutter_domain); ?> </strong> &nbsp; &nbsp; &nbsp; 
				<input type="radio" name="single" id="radPostPage" value="0" <?php echo $multiple_checked?>  /> <strong><?php _e('Multiple', $flutter_domain); ?></strong>
             </td>
        </tr>

		<tr valign="top">
			<th scope="row" align="right"><?php _e('Standard Fields', $flutter_domain); ?>:</th>
			<td>
				<?php 
					global $STANDARD_FIELDS, $wp_version;
					foreach ($STANDARD_FIELDS as $field) :
						if ($field->excludeVersion <= substr($wp_version, 0, 3)) continue;
						if ($field->isAdvancedField) continue;
						
						$checked = "";
						$classes = "";
						if ($customWritePanel != null)
						{
							if (in_array($field->id, $customWritePanelStandardFieldIds))
							{
								$checked = "checked=\"checked\"";
							}
						}
						else
						{
							if ($field->defaultChecked)
							{
								$checked = "checked=\"checked\""; 
							}
						}
						
						if ($field->forPost && !$field->forPage) $classes = $classes . " flutter_forpost";
						if ($field->forPage && !$field->forPost) $classes = $classes . " flutter_forpage";
				?>
					<div class="<?php echo $classes?>"> 
						<input type="checkbox" name="custom-write-panel-standard-fields[]" value="<?php echo $field->id?>" <?php echo $checked?> /> 
						<?php echo $field->displayName?> 
						<br />
					</div>
				<?php
					endforeach;
				?>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row" align="right"><?php _e('Advanced Fields', $flutter_domain); ?>:</th>
			<td>
				<?php 
					global $STANDARD_FIELDS, $wp_version;
					foreach ($STANDARD_FIELDS as $field) :
						if ($field->excludeVersion <= substr($wp_version, 0, 3)) continue;
						if (!$field->isAdvancedField) continue;
						
						$checked = "";
						$classes = "";
						if ($customWritePanel != null)
						{
							if (in_array($field->id, $customWritePanelStandardFieldIds))
							{
								$checked = "checked=\"checked\"";
							}
						}
						else
						{
							if ($field->defaultChecked)
							{
								$checked = "checked=\"checked\""; 
							}
						}
						if ($field->forPost && !$field->forPage) $classes = $classes . " flutter_forpost";
						if ($field->forPage && !$field->forPost) $classes = $classes . " flutter_forpage";
						
				?>
					<div class="<?php echo $classes?>"> 
						<input type="checkbox" name="custom-write-panel-standard-fields[]" value="<?php echo $field->id?>" <?php echo $checked?> /> 
						<?php echo $field->displayName?> 
						<br />
					</div>
				<?php
					endforeach;
				?>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row" align="right"><?php _e('Order', $flutter_domain); ?>:</th>
            <?php 
                if(empty($customWritePanelDisplayOrder)){
                    $customWritePanelDisplayOrder = "";
                }
            ?>
			<td><input name="custom-write-panel-order" id="custom-write-panel-order" size="2" type="text" value="<?php echo $customWritePanelDisplayOrder?>" /></td>
		</tr>

		<?php
		if (!isset($customWritePanel)) :
		?>
		<tr>
			<th scope="row" align="right"><?php _e('Custom Fields', $flutter_domain); ?>:</th>
			<td><?php _e('Add custom fields later by editing this custom write panel.', $flutter_domain); ?></td>
		</tr>
		<?php
		endif;
		?>
		</tbody>
		</table>
		
		<?php
	}
	
	function Edit()
	{
		global $flutter_domain;
		$customWritePanel = RCCWP_CustomWritePanel::Get((int)$_REQUEST['custom-write-panel-id']);
		?>
		<div class="wrap">
		
		<h2><?php _e('Edit', $flutter_domain); ?> <?php echo $customWritePanel->name ?> <?php _e('Write Panel', $flutter_domain); ?></h2>
		
		<form action="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('submit-edit-custom-write-panel')?>" method="post" id="submit-edit-custom-write-panel">
		
		<?php
		RCCWP_CustomWritePanelPage::Content($customWritePanel);
		?>
		
		<p class="submit" >
			<a  style="color:black" href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('cancel-edit-custom-write-panel')?>" class="button"><?php _e('Cancel', $flutter_domain); ?></a> 
			<input type="submit" id="submit-edit-custom-write-panel" value="<?php _e('Update', $flutter_domain); ?>" />
		</p>
		</form>
		
		</div>
		
		<?php
	}
	
	function GetAssignedCategoriesString($customWritePanel)
	{
		$results = RCCWP_CustomWritePanel::GetAssignedCategories($customWritePanel);
		$str = '';
		foreach ($results as $r)
		{
			$str .= $r->cat_name . ', ';	
		}
		$str = substr($str, 0, strlen($str) - 2); // deletes last comma and whitespace
		return $str;
	}
	
	function GetStandardFieldsString($customWritePanel)
	{
		$results = RCCWP_CustomWritePanel::GetStandardFields($customWritePanel);
		foreach ($results as $r)
		{
			$str .= $r->name . ', ';	
		}
		$str = substr($str, 0, strlen($str) - 2); // deletes last comma and whitespace
		return $str;
	}
	
	
	/**
	 * View groups/fields of a write panel
	 *
	 */
	function View()
	{
		global $flutter_domain;	
		//if(isset($_GET['custom-write-panel-id']) && !empty($_GET['custom-write-panel-id']) )
			
		//if(isset($_POST['custom-write-panel-id']) && !empty($_POST['custom-write-panel-id']) )
			//$customWritePanelId = (int)$_POST['custom-write-panel-id'];
			
		$customWritePanelId = (int)$_REQUEST['custom-write-panel-id'];

		$customWritePanels = RCCWP_CustomWritePanel::GetCustomWritePanels();
		$customWritePanel = RCCWP_CustomWritePanel::Get($customWritePanelId);
		$custom_groups = RCCWP_CustomWritePanel::GetCustomGroups($customWritePanelId);
		
		// get default group id
		foreach ($custom_groups as $group){
			if ($group->name == '__default'){
				$customDefaultGroupId = $group->id;
				break;
			}
		}
			
		
		?>

		<script type="text/javascript" language="javascript">
			function confirmBeforeDelete()
			{
				return confirm("<?php _e('Are you sure you want to delete this custom Field?', $flutter_domain); ?>");
			}
		</script>
		<div class="wrap">

		<form action="<?php echo RCCWP_ManagementPage::GetPanelPage() . "&flutter_action=view-custom-write-panel"?>" method="post"  id="posts-filter" name="SelectWritePanel">
			<h2>
				<?php echo $customWritePanel->name?>
				<span style="font-size:small">
					&nbsp; &nbsp;
					<a href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('edit-custom-write-panel', $panel->id); ?>" ><?php _e('Edit', $flutter_domain); ?></a>|
					<a href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('delete-custom-write-panel', $panel->id); ?>" onclick="return confirmBeforeDelete();"><?php _e('Delete', $flutter_domain); ?></a>|
					<a href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('export-custom-write-panel', $panel->id); ?>" ><?php _e('Export', $flutter_domain); ?></a>
				</span>
			</h2>
			<p id="post-search" style="margin-top:6px">
				<strong>
					<?php _e('Choose a Write Panel', $flutter_domain)?>
					<select name="custom-write-panel-id" style="margin-top:-2px" onchange="document.SelectWritePanel.submit()">
						<?php
						foreach ($customWritePanels as $panel) :
						?>
							<option <?php echo ($customWritePanelId==$panel->id?' selected ':''); ?> value="<?php echo $panel->id?>"><?php echo $panel->name;?></option>
						<?php
						endforeach;
						?>
					</select>
				</strong>
				<a href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('create-custom-group')?>" class="button-secondary">+ <?php _e('Create a Group', $flutter_domain)?></a>
				<a href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('create-custom-field')."&custom-group-id=$customDefaultGroupId"?>" class="button-secondary">+ <?php _e('Create a Field', $flutter_domain)?></a>
			</p>
		</form>
		
		<br class="clear"/>

  		<table cellpadding="3" cellspacing="3" width="100%" class="widefat">
  		<thead>
	  		<tr>
	  			<th width="60%" scope="col"><?php _e('Name', $flutter_domain)?></th>
	  			<th width="20%" scope="col"><?php _e('Type', $flutter_domain)?></th>
				<th width="20%" scope="col"><?php _e('Actions', $flutter_domain)?></th>
			</tr>
  		</thead>
  		<tbody>
	  		<?php
	  		foreach ($custom_groups as $group) :
				if ($customDefaultGroupId != $group->id){			
	  		?>
		  			<tr>
		  				<td><strong><a style="color:#D54E21" href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('edit-custom-group')."&custom-group-id={$group->id}"?>"><?php echo $group->name?></a></strong>&nbsp;&nbsp;(<a style="font-size:very-small" href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('create-custom-field')."&custom-group-id={$group->id}"?>"><?php _e('create field',$flutter_domain); ?></a>) </td>
		  				<td><?php _e('Group', $flutter_domain)?></td>
		  				<td><a onclick="return confirmBeforeDelete();" href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('delete-custom-group')."&custom-group-id={$group->id}"?>">X <?php _e('Delete',$flutter_domain); ?></a></td>
		  				
		  			</tr>
	  		<?php
	  				RCCWP_CustomWritePanelPage::DisplayGroupFields($group->id, true);
				}
	  		endforeach;
	  		RCCWP_CustomWritePanelPage::DisplayGroupFields($customDefaultGroupId);
	  		?>
  		</tbody>
  		</table>
		</div>
        <br />
        <a href="http://flutter.freshout.us"><img src="<?php echo FLUTTER_URI."/images/flutter_logo.jpg"?>" /></a>
		<?php
	}
	
	function DisplayGroupFields($customGroupId, $intended = false){
		global $flutter_domain;
		$custom_fields = RCCWP_CustomGroup::GetCustomFields($customGroupId);
		foreach ($custom_fields as $field) :
		?>
			<tr>
				<td><a href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('edit-custom-field')."&custom-field-id=$field->id"?> " ><?php if ($intended){ ?><img align="top" src="<?php echo FLUTTER_URI; ?>images/arrow_right.gif" alt=""/> <?php } ?><?php echo $field->description?></a></td>
		  		<td><?php echo $field->type?></td>
		  		<td><a onclick="return confirmBeforeDelete();" href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('delete-custom-field')."&custom-field-id=$field->id"?>" >X <?php _e('Delete',$flutter_domain); ?></a></td>
		  		
			</tr>
			
		<?php
		endforeach;
	}
	
	function Import()
	{
		global $flutter_domain;	
		include_once('RCCWP_CustomWritePanel.php');
		
		if(isset($_FILES['import-write-panel-file']) && !empty($_FILES['import-write-panel-file']['tmp_name']) ) {
			$filePath = $_FILES['import-write-panel-file']['tmp_name'];
		}
		else {
			die(__('Error uploading file!', $flutter_domain));
		}

		$writePanelName = basename($_FILES['import-write-panel-file']['name'], ".pnl");
		$panelID = RCCWP_CustomWritePanel::Import($filePath, $writePanelName);
		unlink($filePath);
		
		
		echo "<div class='wrap'><h3>".__("The Write Panel was imported successfuly.",$flutter_domain)."</h3>";
		echo '<p><a href="' . RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('view-custom-write-panel', $panelID).'">'.__('Click here',$flutter_domain).' </a> '.__('to edit the write panel.',$flutter_domain).'</p>';
		echo "</div>";
		
	}
	
	function ViewWritePanels()
	{
		global $flutter_domain;	
		$customWritePanels = RCCWP_CustomWritePanel::GetCustomWritePanels();
		?>

		<div class="wrap">

		<form action="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('import-write-panel')?>" method="post"  id="posts-filter" name="ImportWritePanelForm" enctype="multipart/form-data">
			<h2><?php _e('Custom Write Panel',$flutter_domain); ?></h2>
			<p id="post-search">					
				<input id="import-write-panel-file" name="import-write-panel-file" type="file" /> 
				<a href="#none" class="button-secondary" style="display:inline" onclick="document.ImportWritePanelForm.submit();"><?php _e('Import a Write Panel',$flutter_domain); ?></a>
				<a href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('create-custom-write-panel'); ?>" class="button-secondary" style="display:inline">+ <?php _e('Create a Write Panel',$flutter_domain); ?></a>
			</p>	
		</form>
				
		<br class="clear"/>
		
		<table cellpadding="3" cellspacing="3" width="100%" class="widefat">
			<thead>
				<tr>
					<th scope="col" width="60%"><?php _e('Name',$flutter_domain); ?></th>
					<th colspan="4" style="text-align:center"><?php _e('Actions',$flutter_domain); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ($customWritePanels as $panel) :
				?>
					<tr">
						<td><?php echo $panel->name ?></td>			
						<td><a href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('view-custom-write-panel', $panel->id)?>" ><?php _e('Edit Fields/Groups',$flutter_domain) ?></a></td>
						<td><a href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('edit-custom-write-panel', $panel->id)?>" ><?php _e('Edit Write Panel',$flutter_domain) ?></a></td>
						<td><a href="<?php echo RCCWP_ManagementPage::GetCustomWritePanelGenericUrl('export-custom-write-panel', $panel->id); ?>" ><?php _e('Export',$flutter_domain); ?></a></td>		
					</tr>
				<?php
				endforeach;
				?>
			</tbody>
		</table>
        <br />
        <a href="http://flutter.freshout.us"><img src="<?php echo FLUTTER_URI."/images/flutter_logo.jpg"?>" /></a>
		</div>
		<?php 
	}
	
	
}
?>
