<?php
include_once('RCCWP_Options.php');

class RCCWP_OptionsPage
{

function Main()
{
	global $flutter_domain;
	$customWritePanels = RCCWP_CustomWritePanel::GetCustomWritePanels();
	$customWritePanelOptions = RCCWP_Options::Get();

	if (function_exists('is_site_admin') && !is_site_admin())
		update_option("Flutter_notTopAdmin", true);
	else
		update_option("Flutter_notTopAdmin", false);

	?>
	
	<div class="wrap">

	<h2><?php _e('Flutter Options', $flutter_domain); ?></h2>
	
	<form action="" method="post" id="custom-write-panel-options-form">	
	

	<h3><?php _e('Write Panel Options', $flutter_domain); ?></h3>
	<table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6"> 

	<tr valign="top">
		<th scope="row"><?php _e('Hide Post Panel', $flutter_domain); ?></th>
        	<td>
			<label for="hide-write-post"> 
			<input name="hide-write-post" id="hide-write-post" value="1" <?php echo RCCWP_OptionsPage::GetCheckboxState($customWritePanelOptions['hide-write-post'])?> type="checkbox">
			&nbsp; <?php _e('Hide Wordpress Post panel', $flutter_domain); ?></label> 
		</td>
        </tr>
 
    	<tr valign="top">
		<th scope="row"><?php _e('Hide Page Panel', $flutter_domain); ?></th>
		<td>
			<label for="hide-write-page"> 
			<input name="hide-write-page" id="hide-write-page" value="1" <?php echo RCCWP_OptionsPage::GetCheckboxState($customWritePanelOptions['hide-write-page'])?> type="checkbox">
			&nbsp; <?php _e('Hide Wordpress Page panel', $flutter_domain); ?></label> 
 		</td>
        </tr>
	
	<tr valign="top">
		<th scope="row"><?php _e('Hide Visual Editor (multiline)', $flutter_domain); ?></th>
		<td>
			<label for="hide-visual-editor"> 
			<input name="hide-visual-editor" id="hide-visual-editor" value="1"  <?php echo RCCWP_OptionsPage::GetCheckboxState($customWritePanelOptions['hide-visual-editor'])?> type="checkbox">
			&nbsp; <?php _e('Hide Visual Editor (multiline)', $flutter_domain); ?></label> 
 		</td>
        </tr>

    	<tr valign="top">
		<th scope="row"><?php _e('Editing Prompt', $flutter_domain); ?></th>
		<td>
			<label for="prompt-editing-post"> 
			<input name="prompt-editing-post" id="prompt-editing-post" value="1" <?php echo RCCWP_OptionsPage::GetCheckboxState($customWritePanelOptions['prompt-editing-post'])?> type="checkbox"> 
			&nbsp; <?php _e('Prompt when editing a Post not created with Custom Write Panel.', $flutter_domain); ?></label> 
		</td>
        </tr>

    	<tr valign="top">
		<th scope="row"><?php _e('Assign to Role', $flutter_domain); ?></th>
        	<td>
			<label for="assign-to-role"> 
			<input name="assign-to-role" id="assign-to-role" value="1" <?php echo RCCWP_OptionsPage::GetCheckboxState($customWritePanelOptions['assign-to-role'])?> type="checkbox"> 
			&nbsp; <?php _e('This option will create a capability for each write panel such that the write panel is accessible by the Administrator only by default.
			 You can assign the write panel to other roles using ', $flutter_domain); ?></label><a target="_blank" href="http://sourceforge.net/projects/role-manager">&nbsp; Role Manager Plugin</a>. 
		</td>
        </tr>

    	<tr valign="top">
		<th scope="row"><?php _e('Default Panel', $flutter_domain); ?></th>
		<td>
		
			<label for="default-custom-write-panel">
			<select name="default-custom-write-panel" id="default-custom-write-panel">
				<option value=""><?php _e('(None)', $flutter_domain); ?></option>
			<?php
				$defaultCustomWritePanel = $customWritePanelOptions['default-custom-write-panel'];
				foreach ($customWritePanels as $panel) :
					$selected = $panel->id == $defaultCustomWritePanel ? 'selected="selected"' : '';
			?>
				<option value="<?php echo $panel->id?>" <?php echo $selected?>><?php echo $panel->name?></option>
			<?php
				endforeach;
			?>
			</select>
			</label>
		
		</td>
        </tr>

	</table>


	<br />
	<h3><?php _e('Layout Options', $flutter_domain); ?></h3>
	<p><?php _e('Allows you to add modules to the blog.', $flutter_domain); ?></p>
	<table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6"> 

	<tr valign="top">
		<th scope="row"><?php _e('Layout Tab', $flutter_domain); ?></th>
		<td>
			<label for="canvas_show"> 
			<input name="canvas_show" id="canvas_show" value="1" <?php echo RCCWP_OptionsPage::GetCheckboxState($customWritePanelOptions['canvas_show'])?> type="checkbox"> 
			&nbsp; <?php _e('Show Layout tab.', $flutter_domain); ?></label> 
		</td>
        </tr>


	<tr valign="top">
		<th scope="row"><?php _e('Style Tab', $flutter_domain); ?></th>
		<td>
			<label for="ink_show"> 
			<input name="ink_show" id="ink_show" value="1" <?php echo RCCWP_OptionsPage::GetCheckboxState($customWritePanelOptions['ink_show'])?> type="checkbox"> 
			&nbsp; <?php _e('Show Style tab.', $flutter_domain); ?></label> 
		</td>
        </tr>


	<tr valign="top">
		<th scope="row"><?php _e('Layout Instructions', $flutter_domain); ?></th>
		<td>
			<label for="canvas_show_instructions"> 
			<input name="canvas_show_instructions" id="canvas_show_instructions" value="1" <?php echo RCCWP_OptionsPage::GetCheckboxState($customWritePanelOptions['canvas_show_instructions'])?> type="checkbox"> 
			&nbsp; <?php _e('Display the instructions on the Layout page.', $flutter_domain); ?></label> 
		</td>
        </tr>


	<tr valign="top">
		<th scope="row"><?php _e('Zones Names', $flutter_domain); ?></th>
		<td>
			<label for="canvas_show_zone_name"> 
			<input name="canvas_show_zone_name" id="canvas_show_zone_name" value="1" <?php echo RCCWP_OptionsPage::GetCheckboxState($customWritePanelOptions['canvas_show_zone_name'])?> type="checkbox"> 
			&nbsp; <?php _e('Show zones names on droppable zones.', $flutter_domain); ?>	</label> 
		</td>
        </tr>

	</table>

	
	<h3><?php _e('Other Options', $flutter_domain); ?></h3>
	<table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">

    	<!-- <tr valign="top">
		<th scope="row"><?php _e('Snipshot', $flutter_domain); ?></th>
		<td>
			<label for="use-snipshot"> 
			<input name="use-snipshot" id="use-snipshot" value="1" <?php echo RCCWP_OptionsPage::GetCheckboxState($customWritePanelOptions['use-snipshot'])?> type="checkbox"> 
			&nbsp; <?php _e('Use Snipshot services instead of cropper to edit photos.', $flutter_domain); ?></label> 
		</td>
        </tr> -->

	<tr valign="top">
		<th scope="row"><?php _e('Edit-n-place', $flutter_domain); ?></th>
		<td>
			<label for="enable-editnplace"> 
			<input name="enable-editnplace" id="enable-editnplace" value="1" <?php echo RCCWP_OptionsPage::GetCheckboxState($customWritePanelOptions['enable-editnplace'])?> type="checkbox"> 
			&nbsp; <?php _e('Edit posts instantly from the post page.', $flutter_domain); ?></label> 
		</td>
        </tr>
	<script type='text/javascript' src='<?=FLUTTER_URI?>js/sevencolorpicker.js'></script>
	<script type="text/javascript">
		jQuery('document').ready(function(){
			jQuery('#eip-highlight-color').SevenColorPicker();
		});
	</script>
	<tr>
		<th scope="row"><?php _e('EIP highlight color', $flutter_domain); ?> </th>
		<td>
			<label for="eip-highlight-color">
			<input name="eip-highlight-color" id="eip-highlight-color" value="<?php echo $customWritePanelOptions['eip-highlight-color']; ?>"  >
			&nbsp; <?php _e('Use color to highlight areas EIP', $flutter_domain); ?></label>
		</td>
	</tr>

	<tr>
		<th scope="row"><?php _e('Browser uploader', $flutter_domain); ?> </th>
		<td>
			<label for="enable-browserupload">
			<input name="enable-browserupload" id="enable-browserupload" value="1" <?php echo RCCWP_OptionsPage::GetCheckboxState($customWritePanelOptions['enable-browserupload']) ?> type="checkbox">
			&nbsp; <?php _e('Use Browser uploader instead Flash Uploader', $flutter_domain); ?></label>
		</td>
	</tr>

	</table>

	<br />	
	<h3><?php _e('Uninstall Flutter', $flutter_domain); ?></h3>
	<table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6"> 

    	<tr valign="top">
		<th scope="row"><?php _e('Uninstall Flutter', $flutter_domain); ?></th>
		<td>
			<input type="text" id="uninstall-custom-write-panel" name="uninstall-custom-write-panel" size="25" /><br />
			<label for="uninstall-custom-write-panel">
			&nbsp; <?php _e('Type <strong>uninstall</strong> into the textbox, click <strong>Update Options</strong>, and all the tables created by this plugin will be deleted', $flutter_domain); ?></label>
		
		</td>
        </tr>

	</table>

	<p class="submit" ><input name="update-custom-write-panel-options" type="submit" value="<?php _e('Update Options', $flutter_domain); ?>" /></p>
	
	</form>

	</div>
	
	<?php
}

function GetCheckboxState($optionValue)
{
	if ($optionValue == '' || $optionValue == 0)
		return '';
	else 
		return 'checked="checked"';
}

}

?>
