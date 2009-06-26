<?php
/**
 * This class content all  type of fields for the panels
 */
class RCCWP_WritePostPage {
    
    function ApplyCustomWritePanelAssignedCategories($content){ 
		global $CUSTOM_WRITE_PANEL;
		global $post,$title;
		
		$assignedCategoryIds = RCCWP_CustomWritePanel::GetAssignedCategoryIds($CUSTOM_WRITE_PANEL->id);
		$customThemePage = RCCWP_CustomWritePanel::GetThemePage($CUSTOM_WRITE_PANEL->name);
		
		//hide all categories
		$all = get_categories( "get=all" );
		foreach($all as $al){
			$toReplace = 'class="selectit"><input value="'.$al->term_id.'" type="checkbox" name="post_category[]" id="in-category-'.$al->term_id.'"/>';
			$replacement = 'class="selectit" style="display:none;"><input value="'.$al->term_id.'" type="checkbox" name="post_category[]" id="in-category-'.$al->term_id.'"/>';
			$content = str_replace($toReplace, $replacement, $content);
		} 
		//display ony categories and child
		
		$dos=$assignedCategoryIds;
		
		foreach($assignedCategoryIds as $id){
			$childs= get_categories( "child_of=".$id."&hierarchical=0&hide_empty=0" );
			foreach($childs as $child){
				array_unshift($dos, $child->term_id);
			}
		}
		$dos=array_unique($dos);
		
		
		
		    foreach($dos as $do){
			$toReplace = 'class="selectit" style="display:none;"><input value="'.$do.'" type="checkbox" name="post_category[]" id="in-category-'.$do.'"';
			$replacement = 'class="selectit"><input value="'.$do.'" type="checkbox" name="post_category[]" id="in-category-'.$do.'"';
			$content = str_replace($toReplace, $replacement, $content);
		    }
		
		if($_GET['custom-write-panel-id']){
		    foreach ($assignedCategoryIds as $categoryId)
		    {
			$toReplace = 'id="in-category-' . $categoryId . '"';
			$replacement = $toReplace . ' checked="checked"';
			$content = str_replace($toReplace, $replacement, $content);
		    }
		}
		//set default theme page
		if($post->ID == 0){
			$toReplace = "value='".$customThemePage."'";
			$replacement = "value='".$customThemePage."'" . ' SELECTED"';
			$content = str_replace($toReplace, $replacement, $content);
		}
		
		
		return $content;
	}

    function FormError(){
		global $flutter_domain;
		if (RCCWP_Application::InWritePostPanel()){
			echo "<div id='flutter-publish-error-message' class='error' style='display:none;'><p><strong>".__("Post was not published - ",$flutter_domain)."</strong> ".__("You have errors in some fields, please check the fields below.",$flutter_domain)."</p></div>";	
		}
	}

	
    function CustomFieldsCSSScripts(){
		?>
		
		<link rel='stylesheet' href='<?php echo FLUTTER_URI?>css/epoch_styles.css' type='text/css' />
		<link href="<?php echo FLUTTER_URI?>js/greybox/gb_styles.css" rel="stylesheet" type="text/css" media="all" />
		<style type="text/css">
			
			.freshout{
				display: block;
    			margin-left: auto;
    			margin-right: auto ;
			}
				
			.photo_edit_link{
				clear:both;
				margin: 0px 0px 0px 0px;
				width:150px;
				text-align:center;
			}
					
		</style>
        
        <!-- Live Query Jquery plugin -->
        <script  type="text/javascript" src="<?php echo FLUTTER_URI?>js/jquery.livequery.js"></script>

		<script language="JavaScript" type="text/javascript" src="<?php echo FLUTTER_URI; ?>js/prototype.js"></script>
		
		<!-- Calendar Control -->

		<script type="text/javascript" src="<?php echo FLUTTER_URI?>js/epoch_classes.js"></script> <!--Epoch's Code-->
		<!-- Calendar Control -->
		
		<script type="text/javascript">
			var GB_ROOT_DIR = "<?php echo FLUTTER_URI?>js/greybox/";
			var flutter_path = "<?php echo FLUTTER_URI ?>" ;
			var JS_FLUTTER_FILES_PATH = '<?php echo FLUTTER_FILES_PATH ?>';
			var swf_authentication = "<?php if ( function_exists('is_ssl') && is_ssl() ) echo $_COOKIE[SECURE_AUTH_COOKIE]; else echo $_COOKIE[AUTH_COOKIE]; ?>" ;
			var swf_nonce = "<?php echo wp_create_nonce('media-form'); ?>" ;
		</script>
		<script type="text/javascript" src="<?php echo FLUTTER_URI?>js/greybox/AJS.js"></script>
		<script type="text/javascript" src="<?php echo FLUTTER_URI?>js/greybox/AJS_fx.js"></script>
		<script type="text/javascript" src="<?php echo FLUTTER_URI?>js/greybox/gb_scripts.js"></script>
        <script type="text/javascript" src="<?php echo FLUTTER_URI?>js/groups.js"></script>
        
		<script type="text/javascript">
				function isset(  ) {
					// http://kevin.vanzonneveld.net
					// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
					// +   improved by: FremyCompany
					// *     example 1: isset( undefined, true);
					// *     returns 1: false
					// *     example 2: isset( 'Kevin van Zonneveld' );
					// *     returns 2: true
					
					var a=arguments; var l=a.length; var i=0;
					
					while ( i!=l ) {
						if (typeof(a[i])=='undefined') { 
						return false; 
						} else { 
						i++; 
						}
					}
					
					return true;
				}
            
            // -------------
			// Edit Photo functions
			
			function setCookie(c_name,value,expiredays)
			{
				var exdate=new Date();
				exdate.setDate(exdate.getDate()+expiredays);
				document.cookie=c_name+ "=" +escape(value)+
				((expiredays==null) ? "" : ";expires="+exdate.toGMTString());
			}
			function prepareUpdatePhoto(inputName){	
				document.getElementById(inputName+'_dorename').value = '1';
				return true;
			}	
			function exchangeValues(e, id)
			{
                //====  ¿? ====//
				//document.getElementById(document.getElementById('parent_text_'+id.substring(10)).value).value = e;
				//document.getElementById(document.getElementById('hidImgValue'+id.substring(10)).value).value = e;
			}
			
			// -------------
			// Date Functions

			var dp_cal = new Array(); // declare the calendars as global variables
			
			function pickDate(inputName){
				if (dp_cal[inputName] && dp_cal[inputName].selectedDates[0]) document.getElementById('date_field_'+inputName).value = dp_cal[inputName].selectedDates[0].dateFormat('Y-m-d');
			}
			
			function InitializeDateObject(inputName, dateFormat, currentValue){
				if (!Object.isElement($('display_date_field_' + inputName))) return;
				
				dp_cal[inputName]  = new Epoch('dp_cal_'+inputName,'popup',document.getElementById('display_date_field_'+inputName), false, 'pickDate', inputName, dateFormat);
				
				var d = new Date();
				
				if (currentValue.length > 0){
					d.setYear(parseInt(currentValue.substr(0,4),10));
					d.setMonth(parseInt(currentValue.substr(5,2),10)-1);
					d.setDate(parseInt(currentValue.substr(8,2),10));
				}
				d.selected = true;
				d.canSelect = true;
				var tmpDatesArray = new Array(d);
				dp_cal[inputName].selectDates(tmpDatesArray, true, true, true);
				if (dp_cal[inputName] && dp_cal[inputName].selectedDates[0]) 
					document.getElementById('display_date_field_'+inputName).value = dp_cal[inputName].selectedDates[0].dateFormat(dateFormat);
			}	

			function today_date(inputName, dateFormat){
				var d = new Date();
				var tmpDatesArray = new Array(d);
				dp_cal[inputName].selectDates(tmpDatesArray, true, true, true);
				if (dp_cal[inputName] && dp_cal[inputName].selectedDates[0]){ 
					document.getElementById('display_date_field_'+inputName).value = dp_cal[inputName].selectedDates[0].dateFormat(dateFormat);
					document.getElementById('date_field_' + inputName).value = dp_cal[inputName].selectedDates[0].dateFormat('Y-m-d');
				}
			}
		</script>
		
		<?php
	}
		
	function ApplyCustomWritePanelHeader()
	{
		global $CUSTOM_WRITE_PANEL;
		global $flutter_domain;

		// Validate capability
		require_once ('RCCWP_Options.php');
		$assignToRole = RCCWP_Options::Get('assign-to-role');
		$requiredPostsCap = 'edit_posts';
		$requiredPagesCap = 'edit_pages';

		if ($assignToRole == 1){
			$requiredPostsCap = $CUSTOM_WRITE_PANEL->capability_name;
			$requiredPagesCap = $CUSTOM_WRITE_PANEL->capability_name;
		}

		if ($CUSTOM_WRITE_PANEL->type == "post")
			$requiredCap = $requiredPostsCap;
		else
			$requiredCap = $requiredPagesCap;
		
		if (!current_user_can($requiredCap)) wp_die( __('You do not have sufficient permissions to access this custom write panel.',$flutter_domain) );

		// --- Apply Flutter CSS and javascript
		?>

		<link rel='stylesheet' href='<?php echo FLUTTER_URI?>css/epoch_styles.css' type='text/css' />
		<link href="<?php echo FLUTTER_URI?>js/greybox/gb_styles.css" rel="stylesheet" type="text/css" media="all" />
		<style type="text/css">
			
			.tr_inside{
				background-color:transparent !important;
			}
			
			.freshout{
				display: block;
    			margin-left: auto;
    			margin-right: auto ;
			}
				
			.photo_edit_link{
				clear:both;
				margin: 0px 0px 0px 0px;
				width:150px;
				text-align:center;
			}
			
			.error_msg_txt{
				font-weight: bold;
				overflow: auto;
			}
			
			.duplicate_button{
				text-decoration:none; 
				font-weight:bold;
				float:right
			}
			
			.duplicate_image{
				vertical-align:middle;
				padding-right:3px;
			}
					
		</style>
		<script language="JavaScript" type="text/javascript" src="<?php echo FLUTTER_URI; ?>js/prototype.js"></script>

		
		<script type="text/javascript">
			var wp_root         = "<?php echo get_bloginfo('wpurl');?>";
			var GB_ROOT_DIR     = "<?php echo FLUTTER_URI?>js/greybox/";
			var flutter_path    = "<?php echo FLUTTER_URI; ?>";
			var flutter_relative = "<?php echo FLUTTER_URI_RELATIVE;?>";
			var phpthumb        = "<?php echo PHPTHUMB;?>";
			var swf_authentication = "<?php if ( function_exists('is_ssl') && is_ssl() ) echo $_COOKIE[SECURE_AUTH_COOKIE]; else echo $_COOKIE[AUTH_COOKIE]; ?>" ;
			var swf_nonce = "<?php echo wp_create_nonce('media-form'); ?>" ;
		</script>
		<script type="text/javascript" src="<?php echo FLUTTER_URI?>js/greybox/AJS.js"></script>
		<script type="text/javascript" src="<?php echo FLUTTER_URI?>js/greybox/AJS_fx.js"></script>
		<script type="text/javascript" src="<?php echo FLUTTER_URI?>js/greybox/gb_scripts.js"></script>
		
		<script type="text/javascript" src="<?php echo FLUTTER_URI?>thirdparty/swfupload/swfupload.js"></script>
		<script type="text/javascript" src="<?php echo FLUTTER_URI?>thirdparty/swfupload/simple/swfupload.queue.js"></script>
		<script type="text/javascript" src="<?php echo FLUTTER_URI?>thirdparty/swfupload/simple/fileprogress.js"></script>
		<script type="text/javascript" src="<?php echo FLUTTER_URI?>thirdparty/swfupload/simple/handlers.js"></script>
		<script type="text/javascript" src="<?php echo FLUTTER_URI; ?>js/swfcallbacks.js" ></script>

		<script type="text/javascript">
				function isset(  ) {
					// http://kevin.vanzonneveld.net
					// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
					// +   improved by: FremyCompany
					// *     example 1: isset( undefined, true);
					// *     returns 1: false
					// *     example 2: isset( 'Kevin van Zonneveld' );
					// *     returns 2: true
					
					var a=arguments; var l=a.length; var i=0;
					
					while ( i!=l ) {
						if (typeof(a[i])=='undefined') { 
						return false; 
						} else { 
						i++; 
						}
					}
					
					return true;
				}
				
			function checkForm(event){
				var stopPublish = false;
				$$('input.field_required','textarea.field_required').each(
						function(inputField){
                            <?php  
		                        $hide_visual_editor = RCCWP_Options::Get('hide-visual-editor');
                                if ($hide_visual_editor == '' || $hide_visual_editor ==  0):
                            ?>
                                re = new RegExp(".*_multiline");
                                if(re.match(inputField.id)){
                                    inputField.value = tinyMCE.get(inputField.id).getContent();
                                }

                            <?php endif;?>

							if ($F(inputField) == "" &&
								!(Object.isElement($(inputField.id+"_last")) && $F(inputField.id+"_last") != "")	){
								stopPublish = true;

								// Update row color
								if (isset($("row_"+inputField.id).style))
									$("row_"+inputField.id).style.backgroundColor = "#FFEBE8";

								// Update iframe color if it exists
								if (Object.isElement($("upload_internal_iframe_"+inputField.id))){
								  	if ($("upload_internal_iframe_"+inputField.id).contentDocument) {
								    	// For FF
								    	$("upload_internal_iframe_"+inputField.id).contentDocument.body.style.backgroundColor = "#FFEBE8"; 
								  	} else if ($("upload_internal_iframe_"+inputField.id).contentWindow) {
									    // For IE5.5 and IE6
									    $("upload_internal_iframe_"+inputField.id).contentWindow.document.body.style.backgroundColor = "#FFEBE8";
								    }
								}
									
								$("fieldcellerror_"+inputField.id).style.display = "";
								$("fieldcellerror_"+inputField.id).innerHTML = "ERROR: Field can not be empty";
							}
							else{
								$("fieldcellerror_"+inputField.id).style.display = "none";
								if (isset($("row_"+inputField.id).style))
									$("row_"+inputField.id).style.backgroundColor = "";
									
								// Update iframe color if it exists
								if (Object.isElement($("upload_internal_iframe_"+inputField.id))){
								  	if ($("upload_internal_iframe_"+inputField.id).contentDocument) {
								    	// For FF
								    	$("upload_internal_iframe_"+inputField.id).contentDocument.body.style.backgroundColor = "#EAF3FA"; 
								  	} else if ($("upload_internal_iframe_"+inputField.id).contentWindow) {
									    // For IE5.5 and IE6
									    $("upload_internal_iframe_"+inputField.id).contentWindow.document.body.style.backgroundColor = "#EAF3FA";
								    }
								}
									
							}
						}
					);
				if (stopPublish){
					$("flutter-publish-error-message").style.display = "";
					Event.stop(event);
					return false;
				}
				
				return true;
			}

			Event.observe(window, 'load', function() {
				Event.observe('post', 'submit', checkForm);
			});
			
			// -------------
			// Edit Photo functions
			
			function setCookie(c_name,value,expiredays)
			{
				var exdate=new Date();
				exdate.setDate(exdate.getDate()+expiredays);
				document.cookie=c_name+ "=" +escape(value)+
				((expiredays==null) ? "" : ";expires="+exdate.toGMTString());
			}
			function prepareUpdatePhoto(inputName){	
				document.getElementById(inputName+'_dorename').value = '1';
				return true;
			}	
			function exchangeValues(e, id)
			{
				//document.getElementById(document.getElementById('parent_text_'+id.substring(10)).value).value = e;
				//document.getElementById(document.getElementById('hidImgValue'+id.substring(10)).value).value = e;
			}
			
			// -------------
			// Date RCCPW_WritePostPage::Functions

			var dp_cal = new Array(); // declare the calendars as global variables
			
			function pickDate(inputName){
				if (dp_cal[inputName] && dp_cal[inputName].selectedDates[0]) document.getElementById('date_field_'+inputName).value = dp_cal[inputName].selectedDates[0].dateFormat('Y-m-d');
			}
			
			function InitializeDateObject(inputName, dateFormat, currentValue){
				if (!Object.isElement($('display_date_field_' + inputName))) return;
				
				dp_cal[inputName]  = new Epoch('dp_cal_'+inputName,'popup',document.getElementById('display_date_field_'+inputName), false, 'pickDate', inputName, dateFormat);
				
				var d = new Date();
				
				if (currentValue.length > 0){
					d.setYear(parseInt(currentValue.substr(0,4),10));
					d.setMonth(parseInt(currentValue.substr(5,2),10)-1);
					d.setDate(parseInt(currentValue.substr(8,2),10));
				}
				d.selected = true;
				d.canSelect = true;
				var tmpDatesArray = new Array(d);
				dp_cal[inputName].selectDates(tmpDatesArray, true, true, true);
				if (dp_cal[inputName] && dp_cal[inputName].selectedDates[0]) 
					document.getElementById('display_date_field_'+inputName).value = dp_cal[inputName].selectedDates[0].dateFormat(dateFormat);
			}	

			function today_date(inputName, dateFormat){
				var d = new Date();
				var tmpDatesArray = new Array(d);
				dp_cal[inputName].selectDates(tmpDatesArray, true, true, true);
				if (dp_cal[inputName] && dp_cal[inputName].selectedDates[0]){ 
					document.getElementById('display_date_field_'+inputName).value = dp_cal[inputName].selectedDates[0].dateFormat(dateFormat);
					document.getElementById('date_field_' + inputName).value = dp_cal[inputName].selectedDates[0].dateFormat('Y-m-d');
				}
			}
			

		</script>

		<?php
		//change title
		global $post,$title;
		if($post->ID == 0){
			$blu = RCCWP_CustomWritePanel::Get($CUSTOM_WRITE_PANEL->id);
			if($post->post_type == "post"){ $name_title = "Post";}
			else{$name_title = "Page";}
			$title="Write ".$name_title." >> " .$blu->name;
		}else{
			$blu = RCCWP_CustomWritePanel::Get($CUSTOM_WRITE_PANEL->id);
			if($post->post_type == "post"){ $name_title = "Post";}
			else{$name_title = "Page";}
			$title="Edit ".$name_title." >> " .$blu->name;
		}

		
		// Show/Hide Panel fields
	 
		global $STANDARD_FIELDS;
		
		$standardFields = RCCWP_CustomWritePanel::GetStandardFields($CUSTOM_WRITE_PANEL->id);
		
		$hideCssIds = array();
		
		foreach($STANDARD_FIELDS as $standardField){
			if (!in_array($standardField->id, $standardFields)){
				foreach($standardField->cssId as $cssID)
					array_push($hideCssIds, $cssID);
			}
		}
		
		if (empty($hideCssIds))
			return;
		
		array_walk($hideCssIds, create_function('&$item1, $key', '$item1 = "#" . $item1;'));
		$hideCssIdString = implode(', ', $hideCssIds);
		?>
		
		<style type="text/css">
			<?php echo $hideCssIdString?> {display: none !important;}
		</style>
		
		<?php
	}
	
	
	function CustomFieldCollectionInterfaceRight(){
		RCCWP_WritePostPage::CustomFieldCollectionInterface(true);
	}
	

	function CustomFieldCollectionInterface($rightOnly = false) {
        global $flutter_domain;
		global $CUSTOM_WRITE_PANEL;
        global $wpdb;
        global $post;

        //if no exists the write panel returni
		if (!isset($CUSTOM_WRITE_PANEL))
			return;
          
		$customGroups = RCCWP_CustomWritePanel::GetCustomGroups($CUSTOM_WRITE_PANEL->id);

		foreach ($customGroups as $customGroup) {
            //render the elements
    		$customFields = RCCWP_CustomGroup::GetCustomFields($customGroup->id);
            
            //when will be edit the  Post
			if(isset( $_REQUEST['post'] ) && count($customFields) > 0){
                //using the first field name we can know 
                //the order  of the groups
                $firstFieldName = $customFields[0]->name;

                $order = RCCWP_CustomField::GetOrderDuplicates($_REQUEST['post'],$firstFieldName);

                ?> 
                <div class="write_panel_wrapper"  id="write_panel_wrap_<?php echo $customGroup->id;?>"><?php
                
                //build the group duplicates 
                foreach($order as $key => $element){
				?>
                    <?php RCCWP_WritePostPage::GroupDuplicate2($customGroup,$element,$key,false);?>
                   <?php 
				}
                ?>
                <?php 
                    //knowing what is the biggest duplicate group
                    if(!empty($order)){
                        $tmp =  $order;
                        sort($tmp);
                        $top = $tmp[count($tmp) -1];
                    }else{
                        $top = 0;
                    }
                ?>
                <input type='hidden' name='g<?php echo $customGroup->id?>counter' id='g<?php echo $customGroup->id?>counter' value='<?php echo $top ?>' />
                <input type="hidden" name="rc-custom-write-panel-verify-key" id="rc-custom-write-panel-verify-key" value="<?php echo wp_create_nonce('rc-custom-write-panel')?>" />
		        <input type="hidden" name="rc-cwp-custom-write-panel-id" value="<?php echo $CUSTOM_WRITE_PANEL->id?>" />
                </div>
            <?php
			}else{
                if(count($customFields) > 0){
            
                ?>
                <div class="write_panel_wrapper" id="write_panel_wrap_<?php echo $customGroup->id;?>">
                <?php
             		      RCCWP_WritePostPage::GroupDuplicate2($customGroup,1,1,false) ;
                          $gc = 1;
                ?>
                <input type='hidden' name='g<?php echo $customGroup->id?>counter' id='g<?php echo $customGroup->id?>counter' value='<?php echo $gc?>' />
           		<input type='hidden' name="rc-custom-write-panel-verify-key" id="rc-custom-write-panel-verify-key" value="<?php echo wp_create_nonce('rc-custom-write-panel')?>" />
		        <input type='hidden' name="rc-cwp-custom-write-panel-id" value="<?php echo $CUSTOM_WRITE_PANEL->id?>" />
                </div>
            <?php 
                }
           }
        }
	}

    /**
     * This method and   groupduplicated  will be merged in nexts commits
     * 
     * @param object $customGroup
     * @param integer $groupCounter
     * @param boolean $fromAjax
     *
     */ 
	function GroupDuplicate2($customGroup, $groupCounter,$order,$fromAjax=true){
		global $flutter_domain;
             
        $counter = "";
        if($groupCounter == 1){
            $counter = "";
        }else{
            $tmp = $groupCounter ;
            $counter = "(".$tmp.")";
        }
 
        //getting the custom fields
		$customFields = RCCWP_CustomGroup::GetCustomFields($customGroup->id);
        
        //if don't have fields then finish
	    if (count($customFields) == 0) return;

        //¿?
		if( $customGroup->duplicate == 0 && $groupCounter != 1 ) return ;
		require_once("RC_Format.php");

        if(empty($customGroup->name) || $customGroup->name == "__default"){
            $title = "Flutter custom fields";
        }else{
            $title = $customGroup->name;
        }
		?>
		<div id="freshpostdiv_group_<?php echo $customGroup->id.'_'.$groupCounter;?>" class="postbox1">	
            <div class="postbox" >
                <h3 class="hndle sortable_flutter">
                    <span><?php echo $title;?> <?php echo $counter;?></span>
                </h3>
            <div class="inside">
			<table class="form-table" style="width: 100%;" cellspacing="2" cellpadding="5">
			    <?php	
	        		foreach ($customFields as $field) {
			            // Render a row for each field in the group
            			//$customField = RCCWP_CustomField::Get($field->id);

		        		$customFieldName = RC_Format::GetInputName(attribute_escape($field->name));
        				$customFieldTitle = attribute_escape($field->description);
                        $groupId  = $customGroup->id;
		        		$inputName = $field->id."_".$groupCounter."_1_".$groupId."_".$customFieldName;

                        
                        if(isset($_REQUEST['post'])){
                            $fc = RCCWP_CustomField::GetFieldDuplicates($_REQUEST['post'],$field->name,$groupCounter);
                            $fields_order =  RCCWP_CustomField::GetFieldsOrder($_REQUEST['post'],$field->name,$groupCounter);
                            foreach($fields_order as $element){
                                RCCWP_WritePostPage::CustomFieldInterface($field->id,$groupCounter,$element,$customGroup->id); 
                            }   
                        }else{
                            RCCWP_WritePostPage::CustomFieldInterface($field->id,$groupCounter,1,$customGroup->id);
                            $fc = 1;
                        }


                    if(!empty($fields_order)){
                        $tmp =  $fields_order;
                        sort($tmp);
                        $top = $tmp[count($tmp) -1];
                    }else{
                        $top = 1;
                    }

                ?>
               <tr style="display:none" id="<?php echo "c".$inputName."Duplicate"?>">
					<th valign="top" scope="row">
					</th>
					<td>
						<img class="duplicate_image"  src="<?php echo FLUTTER_URI; ?>images/spinner.gif" alt=""/> <?php _e('Loading', $flutter_domain); ?> ... 
						<input type="text" name="c<?php echo $inputName ?>Counter" id="c<?php echo $inputName ?>Counter" value='<?php echo $top ?>' /> 
					</td>
			    </tr>
                <?php } ?>
		    </table>
    		<br />
	    	<?php
		        if( $customGroup->duplicate != 0 ){
                    if($groupCounter != 1):?>
                        <a class ="delete_duplicate_button" href="javascript:void(0);" id="delete_duplicate-freshpostdiv_group_<?php echo $customGroup->id.'_'.$groupCounter; ?>"> 
            		        <img class="duplicate_image"  src="<?php echo FLUTTER_URI; ?>images/delete.png" alt="<?php _e('Remove field duplicate', $flutter_domain); ?>"/><?php _e('Remove Group', $flutter_domain); ?>
                	    </a>
                    <?php else:?> 
                        <a class="duplicate_button" id="add_duplicate_<?php echo $customGroup->id."Duplicate"."_".$customGroup->id;?>" href="javascript:void(0);"> 
            		    	<img class="duplicate_image" src="<?php echo FLUTTER_URI; ?>images/duplicate.png" alt="<?php _e('Add group duplicate', $flutter_domain); ?>"/> <?php _e('Duplicate Group', $flutter_domain); ?>
	                	</a>
                   <?php endif;?> 
			<br style="height:2px"/>
			<?php
                }
		    ?>
                </div>
            </div> 
            <input type="hidden" name="order_<?php echo $customGroup->id?>_<?php echo $groupCounter;?>" id="order_<?php echo $customGroup->id?>_<?php echo $groupCounter;?>" value="<?php echo $order?>" />
        </div>
		<?php
	}

    /**
     * This function is for duplicate  a group  (When is clicked the button "Duplicate Group"
     * @param object   $customGroup 
     * @param integer  $groupCounter
     * @param boolean  $fromAjax
     *
     */
	function GroupDuplicate($customGroup, $groupCounter, $fromAjax=true) {
		global $flutter_domain;

        //getting the custom fields
		$customFields = RCCWP_CustomGroup::GetCustomFields($customGroup->id);


		//if don't have any   custom field then finish
        if (count($customFields) == 0) return;

        //if this  group can't be duplicated  and  the group conter is != to 1 then finish
		if( $customGroup->duplicate == 0 && $groupCounter != 1 ) return ;

        //formating
		require_once("RC_Format.php");
		?>
		
		<div id="freshpostdiv_group_<?php echo $customGroup->id.'_'.$groupCounter;?>" class="postbox1">
        <div class="postbox">
            <h3 class="hndle sortable_flutter">
			    <span><?php echo  $customGroup->name." ($groupCounter)" ?> </span>
		    </h3>
		    <div class="inside">
			    <table class="form-table" style="width: 100%;" cellspacing="2" cellpadding="5">
    			    <?php	
	    		        foreach ($customFields as $field){
		    	            // Render a row for each field in the group
        	    			$customField = RCCWP_CustomField::Get($field->id);
		            		$customFieldName = RC_Format::GetInputName(attribute_escape($field->name));
        			    	$customFieldTitle = attribute_escape($customField->description);
                            $groupId = $customGroup->id;
		        		    $inputName = $field->id."_".$groupCounter."_1_".$groupId."_".$customFieldName;
    			    
                            RCCWP_WritePostPage::CustomFieldInterface($field->id,$groupCounter,1,$customGroup->id);
                            $fc = 1;
		    	        }
			        ?>	
			        <tr style="display:none" id="<?php echo "c".$inputName."Duplicate"?>">
    			        <th valign="top" scope="row">
        				</th>
	        			<td>
    		    		    <img class="duplicate_image"  src="<?php echo FLUTTER_URI; ?>images/spinner.gif" alt=""/> <?php _e('Loading', $flutter_domain); ?> ... 
	    		    		<input type="hidden" name="c<?php echo $inputName ?>Counter" id="c<?php echo $inputName ?>Counter" value='<?php echo $fc ?>' /> 
		    		    </td>
    		    	</tr>
                </table>
	    	</div>
    		<br />
	    	<a class ="delete_duplicate_button" href="javascript:void(0);" id="delete_duplicate-freshpostdiv_group_<?php echo $customGroup->id.'_'.$groupCounter ?>"> 
		        <img class="duplicate_image"  src="<?php echo FLUTTER_URI; ?>images/delete.png" alt="<?php _e('Remove field duplicate', $flutter_domain); ?>"/><?php _e('Remove Group', $flutter_domain); ?>
    	    </a>
    		<br style="height:2px"/>
            </div>
            <input type="hidden" name="order_<?php echo $customGroup->id?>_<?php echo $groupCounter;?>" id="order_<?php echo $customGroup->id?>_<?php echo $groupCounter;?>" value="0" />
        </div>
		<?php
	}

	function CustomFieldInterface($customFieldId, $groupCounter=1, $fieldCounter=1,$customGroup_id=0){
		global $flutter_domain;
		require_once("RC_Format.php");
		$customField = RCCWP_CustomField::Get($customFieldId);
		$customFieldName = RC_Format::GetInputName(attribute_escape($customField->name));
		$customFieldTitle = attribute_escape($customField->description);
        $groupId =  $customGroup_id;
		$inputName = $customFieldId."_".$groupCounter."_".$fieldCounter."_".$groupId."_".$customFieldName; // Create input tag name
 		if( $fieldCounter > 1 && $customField->duplicate == 0 ) return ;
 		if( $fieldCounter > 1) $titleCounter = " ($fieldCounter)";
 		
 		$field_group = RCCWP_CustomGroup::Get($customField->group_id);

		?>
		<tr class="form-field" id="row_<?php echo $inputName?>">
			<?php
				// If the field is at right, put the header over the field
				if ($field_group->at_right){
			?>
			<td>
				<label style="font-weight:bold" for="<?php echo $inputName?>"><?php echo $customFieldTitle.$titleCounter?></label>
				<br />
			<?php
				} else {
			?>
			<th valign="top" scope="row">
				<label for="<?php echo $inputName?>"><?php echo $customFieldTitle.$titleCounter?></label>
			</th>
			<td>
			<?php
				}
			?>
				
				<p class="error_msg_txt" id="fieldcellerror_<?php echo $inputName?>" style="display:none"></p>
				<?php		
					switch ($customField->type)
					{
						case 'Textbox' :
							RCCWP_WritePostPage::TextboxInterface($customField, $inputName, $groupCounter, $fieldCounter);
							break;
						case 'Multiline Textbox' :
							RCCWP_WritePostPage::MultilineTextboxInterface($customField, $inputName, $groupCounter, $fieldCounter);
							break;
						case 'Checkbox' :
							RCCWP_WritePostPage::CheckboxInterface($customField, $inputName, $groupCounter, $fieldCounter);
							break;
						case 'Checkbox List' :
							RCCWP_WritePostPage::CheckboxListInterface($customField, $inputName, $groupCounter, $fieldCounter);
							break;
						case 'Radiobutton List' :
							RCCWP_WritePostPage::RadiobuttonListInterface($customField, $inputName, $groupCounter, $fieldCounter);
							break;
						case 'Dropdown List' :
							RCCWP_WritePostPage::DropdownListInterface($customField, $inputName, $groupCounter, $fieldCounter);
							break;
						case 'Listbox' :
							RCCWP_WritePostPage::ListboxInterface($customField, $inputName, $groupCounter, $fieldCounter);
							break;
						case 'File' :
							RCCWP_WritePostPage::FileInterface($customField, $inputName, $groupCounter, $fieldCounter);
							break;
						case 'Image' :
							RCCWP_WritePostPage::PhotoInterface($customField, $inputName, $groupCounter, $fieldCounter);
							break;
						case 'Date' :
							RCCWP_WritePostPage::DateInterface($customField, $inputName, $groupCounter, $fieldCounter);
							break;
						case 'Audio' :
							RCCWP_WritePostPage::AudioInterface($customField, $inputName, $groupCounter, $fieldCounter);
							break;
						case 'Color Picker' :
							RCCWP_WritePostPage::ColorPickerInterface($customField, $inputName, $groupCounter, $fieldCounter);
							break;
						case 'Slider' :
							RCCWP_WritePostPage::SliderInterface($customField, $inputName, $groupCounter, $fieldCounter);
							break;
						default:
							;
					}


				if($fieldCounter == 1)
				{
					?>
					<?php if($customField->duplicate != 0 ){ ?>
					<br />
					
					 <a class ="typeHandler" href="javascript:void(0);" id="type_handler-<?php echo $inputName ?>" > 
						<img class="duplicate_image"  src="<?php echo FLUTTER_URI; ?>images/duplicate.png" alt="<?php _e('Add field duplicate', $flutter_domain); ?>"/>  <?php _e('Duplicate', $flutter_domain); ?>
					</a>
					<?php } ?>
					 
					<?php
				}
				else
				{	
				?>
					<br />
					
					<a class ="delete_duplicate_field" href="javascript:void(0)" id="delete_field_repeat-<?php echo $inputName?>"> 
						<img class="duplicate_image"  src="<?php echo FLUTTER_URI; ?>images/delete.png" alt="<?php _e('Remove field duplicate', $flutter_domain); ?> "/> <?php _e('Remove', $flutter_domain); ?> 
					</a>
				<?php
				}
				?>
				<input type="hidden" name="rc_cwp_meta_keys[]" value="<?php echo $inputName?>" />
			</td>
		</tr>
	<?php
	}
	
	function CheckboxInterface($customField, $inputName, $groupCounter, $fieldCounter)
	{
		$customFieldId = '';
		if (isset($_REQUEST['post']))
		{
			$customFieldId = $customField->id;
			$value = RCCWP_CustomField::GetCustomFieldValues(true, $_REQUEST['post'], $customField->name, $groupCounter, $fieldCounter);
			$checked = $value == 'true' ? 'checked="checked"' : '';
		}
		?>
		
		<input type="hidden" name="<?php echo $inputName?>" value="false" />
		<input tabindex="3" class="checkbox" name="<?php echo $inputName?>" value="true" id="<?php echo $inputName?>" type="checkbox" <?php echo $checked?> />
		
		<?php
	}
	
	function CheckboxListInterface($customField, $inputName, $groupCounter, $fieldCounter)
	{
		$customFieldId = '';
		$values = array();
		if (isset($_REQUEST['post']))
		{
			$customFieldId = $customField->id;
			$values = (array) RCCWP_CustomField::GetCustomFieldValues(false, $_REQUEST['post'], $customField->name, $groupCounter, $fieldCounter);
		}else{
			$values = $customField->default_value;
		}
		?>
		
		
		<?php
		foreach ($customField->options as $option) :
			$checked = in_array($option, (array)$values) ? 'checked="checked"' : '';
			$option = attribute_escape(trim($option));
		?>
		
		    <input tabindex="3" id="<?php echo $option?>" name="<?php echo $inputName?>[]" value="<?php echo $option?>" type="checkbox" <?php echo $checked?> style="width:40px;"/>
			<label for="" class="selectit">
				<?php echo attribute_escape($option)?>
			</label><br />
		
		<?php
		endforeach;
		?>
			
		
		<?php
	}
	
	function DropdownListInterface($customField, $inputName, $groupCounter, $fieldCounter)
	{
		global $flutter_domain;
		$customFieldId = '';
		if (isset($_REQUEST['post']))
		{
			$customFieldId = $customField->id;
			$value = attribute_escape(RCCWP_CustomField::GetCustomFieldValues(true, $_REQUEST['post'], $customField->name, $groupCounter, $fieldCounter));
		}
		else
		{
			$value = $customField->default_value[0];
		}
		
		if ($customField->required_field) $requiredClass = "field_required";
		?>
		
		<select tabindex="3"  class="<?php echo $requiredClass;?>"  name="<?php echo $inputName?>">
			<option value=""><?php _e('--Select--', $flutter_domain); ?></option>
		
		<?php
		foreach ($customField->options as $option) :
			$selected = $option == $value ? 'selected="selected"' : '';
			$option = attribute_escape(trim($option));
		?>
		
			<option value="<?php echo $option?>" <?php echo $selected?>><?php echo $option?></option>
		
		<?php
		endforeach;
		?>
		
		</select>	
		
		
		<?php
	}
	
	function ListboxInterface($customField, $inputName, $groupCounter, $fieldCounter) {

		$customFieldId = '';
		if (isset($_REQUEST['post'])){
			$customFieldId = $customField->id;
			$values = (array) RCCWP_CustomField::GetCustomFieldValues(false, $_REQUEST['post'], $customField->name, $groupCounter, $fieldCounter);
	

        }else{
			$values = $customField->default_value;
		}
		
		$inputSize = (int)$customField->properties['size'];
        $requiredClass = "flutter_listbox";
		if ($customField->required_field) $requiredClass = "flutter_listbox field_required";
		?>
		
		<select  class="<?php echo $requiredClass;?>"  tabindex="3" id="<?php echo $inputName?>" name="<?php echo $inputName?>[]" multiple size="<?php echo $inputSize?>" style="height: 6em;">
		
		<?php
		foreach ($customField->options as $option) :
			$selected = in_array($option, (array)$values) ? 'selected="selected"' : '';
			$option = attribute_escape(trim($option));
		?>
			
			<option value="<?php echo $option?>" <?php echo $selected?>><?php echo $option?></option>
			
		<?php
		endforeach;
		?>
		
		</select>
		
		
		<?php
	}
	
	function MultilineTextboxInterface($customField, $inputName, $groupCounter, $fieldCounter)
	{
		$customFieldId = '';
		
		if (isset($_REQUEST['post']))
		{
			$customFieldId = $customField->id;
			$value = RCCWP_CustomField::GetCustomFieldValues(true, $_REQUEST['post'], $customField->name, $groupCounter, $fieldCounter);
			$value = apply_filters('the_editor_content', $value);

		}else{
			$value = $customField->value;
		}
		
		$inputHeight = (int)$customField->properties['height'];
		$inputWidth = (int)$customField->properties['width'];
		if ($customField->required_field) $requiredClass = "field_required";
		
		?>
		<?php
		
		
		
		$wp_default_editor = wp_default_editor();
		if ( 'html' == $wp_default_editor ) { ?>
		    <script type="text/javascript">
			Event.observe(window, 'load', function() {		    
			    tinyMCE.execCommand('mceAddControl', true, "content");
			    switchEditors.go('content', 'html');
			});
		    </script>
	    <?php	} 
		
		
		$hide_visual_editor = RCCWP_Options::Get('hide-visual-editor');
		if ($hide_visual_editor == '' || $hide_visual_editor == 0){
		?>
		<script type="text/javascript">
			Event.observe(window, 'load', function() {
			    tinyMCE.execCommand('mceAddControl', true, "<?php echo $inputName?>");
			});

			function add_editor(id){
			    tinyMCE.execCommand('mceAddControl', false, id);
			}
			
			function del_editor(id){
			    tinyMCE.execCommand('mceRemoveControl', false, id);
			}
			
			</script>
		<?php } ?>
		<style>
		.tab_multi_flutter {
		    padding-bottom:30px;
		    display: block;
		    margin-right:10px;
		}
		.edButtonHTML_flutter {
		    background-color:#F1F1F1;
		    border-color:#DFDFDF;
		    color:#999999;
		    margin-right:15px;
		    border-style:solid;
		    border-style:solid;
border-width:1px;
cursor:pointer;
display:block;
float:right;
height:18px;
margin:5px 5px 0 0;
padding:4px 5px 2px;

		}
		
		.edButtonPreview_flutter {
		    background-color:#F1F1F1;
		    border-color:#DFDFDF;
		    color:#999999;
		    margin-right:15px;
		    border-style:solid;
		    border-style:solid;
            border-width:1px;
            cursor:pointer;
            display:block;
            float:right;
            height:18px;
            margin:5px 5px 0 0;
            padding:4px 5px 2px;
		}
		</style>
		<?php if ($hide_visual_editor == '' || $hide_visual_editor == 0){ ?>
		<div class="tab_multi_flutter">
		    <a onclick="del_editor('<?php echo $inputName?>');" class="edButtonHTML_flutter">HTML</a>		
		    <a onclick="add_editor('<?php echo $inputName?>');" class="edButtonHTML_flutter" >Visual</a>
		</div>
		<?php } ?>
		
		<div class="mul_flutter">
		<textarea  class="<?php echo $requiredClass;?>" tabindex="3"  id="<?php echo $inputName?>" name="<?php echo $inputName?>" rows="<?php echo $inputHeight?>" cols="<?php echo $inputWidth?>"><?php echo $value?></textarea>
		</div>
		
	<?php
	}
	
	function TextboxInterface($customField, $inputName, $groupCounter, $fieldCounter)
	{
		$customFieldId = '';
		
		if (isset($_REQUEST['post']))
		{
			$customFieldId = $customField->id;
			$value = attribute_escape(RCCWP_CustomField::GetCustomFieldValues(true, $_REQUEST['post'], $customField->name, $groupCounter, $fieldCounter));
		}else{
            $value = $customField->value;
        }

		
		
		$inputSize = (int)$customField->properties['size'];
		if ($customField->required_field) $requiredClass = "field_required";
		
		// If the field is at right, set a constant width to the text box
		$field_group = RCCWP_CustomGroup::Get($customField->group_id);
		if ($field_group->at_right){
			if ($inputSize>14) $inputSize = 14;
		}
		?>
		
		<input class="<?php echo $requiredClass;?>" tabindex="3" id="<?php echo $inputName?>" name="<?php echo $inputName?>" value="<?php echo $value?>" type="text" size="<?php echo $inputSize?>" />
		
		<?php
	}
	


    /**
     * File Field
     *
     */
	function FileInterface($customField, $inputName, $groupCounter, $fieldCounter)
	{
		global $flutter_domain;
		$customFieldId = '';
		$freshPageFolderName = (dirname(plugin_basename(__FILE__)));
		if ($customField->required_field) $requiredClass = "field_required";

		if (isset($_REQUEST['post']))
		{
			$customFieldId = $customField->id;
			$value = attribute_escape(RCCWP_CustomField::GetCustomFieldValues(true, $_REQUEST['post'], $customField->name, $groupCounter, $fieldCounter));
			$path = FLUTTER_FILES_URI;
			$valueRelative = $value;
			$value = $path.$value;
		}
		
		// If the field is at right, set a constant width to the text box
		$field_group = RCCWP_CustomGroup::Get($customField->group_id);
		$urlInputSize = false;
		$is_canvas = 0;
		if ($field_group->at_right){
			$urlInputSize = 5;
			$is_canvas = 1;
		}

		?>
		
		<p class="error_msg_txt" id="upload_progress_<?php echo $inputName?>" style="visibility:hidden;height:0px"></p>
        <script type="text/javascript"> 
            //this script is for remove the  file  related  to the post (using ajax)
            remove_file = function(){
                if(confirm("Are you sure?")){
                    //get  the name to the file
                    id = jQuery(this).attr("id").split("-")[1];
                    file = jQuery('#'+id).val();
                    jQuery.get('<?php echo FLUTTER_URI;?>RCCWP_removeFiles.php',{'action':'delete','file':file},
                                function(message){
                                    jQuery('#actions-'+id).empty();
                                    jQuery('#remove-'+id).empty();
                                    jQuery('#'+id).val("");
                                });

                }
            }


            jQuery(document).ready(function(){
                jQuery("#remove-<?php echo $inputName;?>").click(remove_file);

            });
        </script>
		
		<?php if( $valueRelative ){ 
                echo "<span id='actions-{$inputName}'>(<a href='{$value}' target='_blank'>".__("View Current",$flutter_domain)."</a>)</span>"; 
                echo "&nbsp;<a href='javascript:void(0);' id='remove-{$inputName}'>".__("Delete",$flutter_domain)."</a>";
            } 
        ?>
			
		<input tabindex="3" 
			id="<?php echo $inputName?>" 
			name="<?php echo $inputName?>" 
			type="hidden"
			class="<?php echo $requiredClass;?>" 
			size="46"
			value="<?php echo $valueRelative?>"
			/>
		
		<?php
		include_once( "RCCWP_SWFUpload.php" ) ;
		RCCWP_SWFUpload::Body($inputName, 0, $is_canvas, $urlInputSize) ;
	}


	function PhotoInterface($customField, $inputName, $groupCounter, $fieldCounter)
	{
		global $flutter_domain;
		$customFieldId 	= ''; // <---- ¿?
		$filepath 		= $inputName . '_filepath'; /// <---- ¿?
		$noimage 		= ""; // <---- if no exists image? 
		$freshPageFolderName = (dirname(plugin_basename(__FILE__)));
		if ($customField->required_field) $requiredClass = "field_required";

		//global $countImageThumbID;
		$imageThumbID = "";
		$imageThumbID = "img_thumb_".$inputName; 


		if (isset($_REQUEST['post']))
		{
			$customFieldId = $customField->id;
			$value = RCCWP_CustomField::GetCustomFieldValues(true, $_REQUEST['post'], $customField->name, $groupCounter, $fieldCounter);

            $path = PHPTHUMB."?src=".FLUTTER_FILES_PATH;
			$valueRelative = $value;
			$value = $path.$value;
			if(!(strpos($value, 'http') === FALSE))
				$hidValue = str_replace('"', "'", $valueRelative);
			$value = stripslashes(trim("\<img src=\'".$value."\' class=\"freshout\" \/\>"));
		} else if( !empty($customField->value)){
            $path = PHPTHUMB."?src=".FLUTTER_FILES_PATH;
            $valueRelative = $customField->value;
            $value  = $path.$customField->value;

            if(!(strpos($value, 'http') === FALSE)){
    		    $hidValue = str_replace('"', "'", $valueRelative);
	    	    $value = stripslashes(trim("\<img src=\'".$value."\' class=\"freshout\" \/\>"));
            }


        }else{
			$noimage = "<img src='".FLUTTER_URI."images/noimage.jpg' id='".$imageThumbID."'/>";
		}
		if($valueRelative == '')
		{
			$noimage = "<img src='".FLUTTER_URI."images/noimage.jpg' id='".$imageThumbID."'/>";
		}

		include_once('RCCWP_Options.php');
		$useSnipshot = RCCWP_Options::Get('use-snipshot');

		// If the field is at right, set a constant width to the text box
		$field_group = RCCWP_CustomGroup::Get($customField->group_id);
		$urlInputSize = false;
		$is_canvas = 0;
		if ($field_group->at_right){
			$urlInputSize = 5;
			$is_canvas = 1;
		}
	
		?>

		<p class="error_msg_txt" id="upload_progress_<?php echo $inputName?>" style="visibility:hidden;height:0px"></p>
		

        <!--- This Script is for remove the image -->
	    <script type="text/javascript">
             remove_photo2 = function(ide){
                if(confirm("<?php _e('Are you sure?', $flutter_domain); ?>")){
                        //get the  name to the image
                        //id = ide.split("-")[1];
                        id = ide;
                        image = jQuery('#'+id).val();
                        jQuery.get('<?php echo FLUTTER_URI;?>RCCWP_removeFiles.php',{'action':'delete','file':image},
                                    function(message){
                                        if(message == "true"){
                                            photo = "img_thumb_" + id;
                                            jQuery("#"+photo).attr("src","<?php echo  FLUTTER_URI."images/noimage.jpg"?>");
                                            jQuery("#photo_edit_link_"+id).empty();
                                            jQuery("#"+id).val("");

                                        }
                                    });
                    }
            }

            remove_photo = function(){
                if(confirm("<?php _e('Are you sure?', $flutter_domain); ?>")){
                        //get the  name to the image
                        id = jQuery(this).attr('id').split("-")[1];
                        image = jQuery('#'+id).val();
                        jQuery.get('<?php echo FLUTTER_URI;?>RCCWP_removeFiles.php',{'action':'delete','file':image},
                                    function(message){
                                        if(message == "true"){
                                            photo = "img_thumb_" + id;
                                            jQuery("#"+photo).attr("src","<?php echo FLUTTER_URI."images/noimage.jpg"?>");
                                            jQuery("#photo_edit_link_"+id).empty();
                                            jQuery("#"+id).val("");

                                        }
                                    });
                    }
            }

            jQuery(document).ready(function(){
                jQuery(".remove").click(remove_photo);
            });
        </script>
        <!-- Here finish -->


		<div id="image_photo" style="width:150px;">
		
			<?php
				if($valueRelative != "")
				{ 
					if(!(strpos($value, '<img src') === FALSE))
					{
						$valueLinkArr = explode("'", $value);
						$valueLink = $valueLinkArr[1];
						//$valueLink = $value;

						if(!(strpos($value, '&sw') === FALSE))
						{
							// Calculating Image Width/Height
							$arrSize = explode("=",$value);
							$arrSize1 = explode("&",$arrSize[3]);
							$arrSize2 = explode("&",$arrSize[4]);

							$imageWidth = $arrSize1[0];
							$imageHeight = $arrSize2[0];
							// END

							$valueArr = explode("&sw", $value);
							$valueArr = explode("'", $valueArr[1]);
							$value = str_replace("&sw".$valueArr[0]."'", "&sw".$valueArr[0]."&w=150&h=120' align='center' id='".$imageThumbID."'", $value);
						}
						else if(!(strpos($value, '&w') === FALSE))
						{
							// Calculating Image Width/Height
							$arrSize = explode("=",$value);
							$arrSize1 = explode("&",$arrSize[3]);
							$arrSize2 = explode("'",$arrSize[4]);

							$imageWidth = $arrSize1[0];
							$imageHeight = $arrSize2[0];
							// END

							$valueArr = explode("&", $value);
							$valueArr = explode("'", $valueArr[2]);
							$value = str_replace($valueArr[0], "&w=150&h=120' align='left' id='".$imageThumbID."'", $value);
						}
						else
						{
							// Calculating Image Width/Height
							$arrSize = explode("&",$params);
							$arrSize1 = explode("=",$arrSize[1]);
							$arrSize2 = explode("=",$arrSize[2]);

							$imageWidth = $arrSize1[1];
							$imageHeight = $arrSize2[1];
							// END

							$valueArr = explode("'", $value);
							$value = str_replace($valueArr[1], $valueArr[1]."&w=150' id='".$imageThumbID."' align='", $value);
						}
						if(!empty($imageWidth))
						{
						?>

						<?php
						}
							echo '<a style="display: block;margin-left: auto;margin-right: auto " href="' . $valueLink . '" target="_blank">' . $value .'</a>';
						}
					}
					echo $noimage;
					$arrSize = explode("phpThumb.php?src=",$valueLink);
					$fileLink = $arrSize[1];
					$andPos = strpos($arrSize[1],"?");
					if ($andPos === FALSE)	 $andPos = strpos($arrSize[1],"&");
				
					// Remove & parameters from file path
					if ($andPos>0)	$fileLink = substr($arrSize[1], 0, $andPos);
				
					$ext = substr($fileLink, -3, 3);	
	    ?>	
		
		<div id="photo_edit_link_<?php echo $inputName ?>" class="photo_edit_link"> 
			
				<?php
				if(isset($_REQUEST['post']) && $hidValue != '')
				{ 
					if (False){ 
						echo "<a href='".RCCWP_WritePostPage::snipshot_anchor($fileLink)."' class='thickbox' tittle='Flutter'<strong onclick=prepareUpdatePhoto('$inputName')>".__("Edit",$flutter_domain)."</strong> </a>" ;
					}else{
						$cropperLink = FLUTTER_URI."cropper.php?input_name=".urlencode($inputName)."&id=".urlencode($hidValue)."&url=".urlencode($_SERVER['REQUEST_URI'])."&imageThumbId=$imageThumbID";
				?>
						<a  rel="gb_page_fs[]" href="<?php echo $cropperLink ?>" title="Flutter" class="greybox" id="lnkCropper"> <strong><?php _e('Crop', $flutter_domain); ?></strong> </a>
				<?php 	
					} 
                   echo "&nbsp;<strong><a href='#remove' class='remove' id='remove-{$inputName}'>".__("Delete",$flutter_domain)."</a></strong>";               
				}
				?>			
		    </div>
		</div>
		<br />
		<div id="image_input">
					
			<input tabindex="3" 
				id="<?php echo $inputName?>" 
				name="<?php echo $inputName?>" 
				type="hidden" 
				class="<?php echo $requiredClass;?>"
				size="46"
				value="<?php echo $hidValue?>"
				/>
			
			<?php
			include_once( "RCCWP_SWFUpload.php" ) ;
			RCCWP_SWFUpload::Body($inputName, 1, $is_canvas, $urlInputSize) ;
			?>

		</div>
		
		<input type="hidden" name="rc_cwp_meta_photos[]" value="<?php echo $inputName?>" 	/>
		<input type="hidden" name="<?php echo $inputName?>_dorename" id="<?php echo $inputName?>_dorename" value="0" />
		

		<!-- Used to store name of URL Field -->
		<!--<input type="hidden" name="parent_text_<?php echo $countImageThumbID; ?>" id="parent_text_<?php echo $countImageThumbID; ?>" value="<?php echo $filepath; ?>"/>
		<input type="hidden" name="hidImgValue<?php echo $countImageThumbID; ?>" id="hidImgValue<?php echo $countImageThumbID; ?>" value="<?php echo $inputName; ?>_last" />-->

		<?php
	}

	function snipshot_anchor($fileLink)
	{
/*
		return '<a rel="gb_page_fs[]" href="http://services.snipshot.com/?snipshot_input='. urlencode($fileLink).'&snipshot_callback='.urlencode(FLUTTER_URI."RCCWP_SnipshotCallback.php").'&snipshot_output=file&snipshot_callback_agent=user&test=hello&snipshot_output_options='.urlencode("{\"filetype\":\"$ext\"}").' title="Flutter" class="greybox1" id="lnkCropper">' ;

		return '<a href="http://services.snipshot.com/?snipshot_input='. urlencode($fileLink).'&snipshot_callback='.urlencode(FLUTTER_URI."RCCWP_SnipshotCallback.php").'&snipshot_output=file&snipshot_callback_agent=user&test=hello&snipshot_output_options='.urlencode("{\"filetype\":\"$ext\"}").'&KeepThis=true&TB_iframe=true&height=400&width=600" class="thickbox" title="flutter" >' ;
*/
		return 'http://services.snipshot.com/?snipshot_input='. urlencode($fileLink).'&snipshot_callback='.urlencode(FLUTTER_URI."RCCWP_SnipshotCallback.php").'&snipshot_output=file&snipshot_callback_agent=user&test=hello&snipshot_output_options='.urlencode("{\"filetype\":\"$ext\"}").'&KeepThis=true&TB_iframe=true&height=400&width=600' ;
	}
	
	function RadiobuttonListInterface($customField, $inputName, $groupCounter, $fieldCounter)
	{
		$customFieldId = '';
		
		if (isset($_REQUEST['post']))
		{
			$value = attribute_escape(RCCWP_CustomField::GetCustomFieldValues(true, $_REQUEST['post'], $customField->name, $groupCounter, $fieldCounter));
		}
		else
		{
			$value = $customField->default_value[0];
		}
		?>
		
		<?php
		foreach ($customField->options as $option) :
			$checked = $option == $value ? 'checked="checked"' : '';
			$option = attribute_escape(trim($option));
		?>
			<label for="" class="selectit">
				<input tabindex="3" id="<?php echo $option?>" name="<?php echo $inputName?>" value="<?php echo $option?>" type="radio" <?php echo $checked?>/>
				<?php echo $option?>
			</label><br />
		<?php
		endforeach;
		?>
		
		<?php
	}

	function DateInterface($customField, $inputName, $groupCounter, $fieldCounter)
	{
		global $wpdb;
		$customFieldId = '';
		
		if (isset($_REQUEST['post']))
		{
			$customFieldId = $customField->id;
			$value = attribute_escape(RCCWP_CustomField::GetCustomFieldValues(true, $_REQUEST['post'], $customField->name, $groupCounter, $fieldCounter));
		}
		else
			$value = strftime("%Y-%m-%d");
		
	

		//$sQuery = "SELECT * FROM " . RC_CWP_TABLE_CUSTOM_FIELD_PROPERTIES . " WHERE custom_field_id='".$customField->id."'";
		//$result = $wpdb->get_results($sQuery);

		//$arrDateFormat = explode('"', $result[0]->properties);
		//$dateFormat = $arrDateFormat[3];
		$dateFormat = $customField->properties['format'];

		// If the field is at right, set a constant width to the text box
		$field_group = RCCWP_CustomGroup::Get($customField->group_id);
		$inputSize = 25;
		if ($field_group->at_right){
			$inputSize = 15;
		}
		

?>
		<script type="text/javascript">
			
			addEventHandler(window, 'load',function () {
				InitializeDateObject('<?php echo $inputName?>', '<?php echo $dateFormat?>', '<?php echo $value?>');
			}); 
			
			InitializeDateObject('<?php echo $inputName?>', '<?php echo $dateFormat?>', '<?php echo $value?>');
			
			
		</script>	

		
		<input tabindex="3" id="display_date_field_<?php echo $inputName?>" value="<?php echo $value?>" type="text" size="<?php echo $inputSize?>" READONLY />
		<input tabindex="3" id="date_field_<?php echo $inputName?>" name="<?php echo $inputName?>" value="<?php echo $value?>" type="hidden" />
		<input type="button" value="Pick..." onclick="dp_cal['<?php echo $inputName?>'].toggle();" />
		<input type="button" value="Today" onclick="today_date('<?php echo $inputName?>', '<?php echo $dateFormat?>');" />

		<input type="hidden" name="rc_cwp_meta_date[]" value="<?php echo $inputName?>" 	/>

		
		<?php
	}


    /**
     * Audio  field
     *
     *
     */
	function AudioInterface($customField, $inputName, $groupCounter, $fieldCounter){
		global $flutter_domain;
        $customFieldId = '';
		$freshPageFolderName = (dirname(plugin_basename(__FILE__))); //Flutter
		if ($customField->required_field) $requiredClass = "field_required";
		
		if (isset($_REQUEST['post']))
		{
			$customFieldId = $customField->id;
			$valueOriginal = RCCWP_CustomField::GetCustomFieldValues(true, $_REQUEST['post'], $customField->name, $groupCounter, $fieldCounter);
			$path = FLUTTER_FILES_URI;
			$$valueOriginalRelative = $valueOriginal;
			$valueOriginal = $path.$valueOriginal;
			if (!empty($valueOriginal))
				$value = stripslashes(trim("\<div  id='obj-{$inputName}' style=\'width:260px;padding-top:3px;\'\>\<object classid=\'clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\' codebase='\http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0\' width=\'95%\' height=\'20\' wmode=\'transparent\' \>\<param name=\'movie\' value=\'".FLUTTER_URI."js/singlemp3player.swf?file=".urlencode($valueOriginal)."\' wmode=\'transparent\' /\>\<param name=\'quality\' value=\'high\' wmode=\'transparent\' /\>\<embed src=\'".FLUTTER_URI."js/singlemp3player.swf?file=".urlencode($valueOriginal)."' width=\'100\%\' height=\'20\' quality=\'high\' pluginspage=\'http://www.macromedia.com/go/getflashplayer\' type=\'application/x-shockwave-flash\' wmode=\'transparent\' \>\</embed\>\</object\>\</div\><br />"));			
		}
		
        // If the field is at right, set a constant width to the text box
		$field_group = RCCWP_CustomGroup::Get($customField->group_id);
		$urlInputSize = false;
		$is_canvas = 0;
		if ($field_group->at_right){
			$urlInputSize = 5;
			$is_canvas = 1;
		}

		?>
		<p class="error_msg_txt" id="upload_progress_<?php echo $inputName?>" style="visibility:hidden;height:0px"></p>
		<script type="text/javascript">
            //this script is for remove the audio file using ajax
            remove_audio = function(){
                if(confirm("<?php _e('Are you sure?', $flutter_domain); ?>")){
                    //get the name to the image
                    id = jQuery(this).attr('id').split("-")[1];
                    file = jQuery('#'+id).val(); 
                    jQuery.get('<?php echo FLUTTER_URI;?>RCCWP_removeFiles.php',{'action':'delete','file':file},
                                function(message){
                                    if(message =="true"){
                                        jQuery('#obj-'+id).empty();
                                        jQuery('#actions-'+id).empty();
                                    }

                                });
                }                           
            }

            jQuery(document).ready(function(){
                jQuery("#remove-<?php echo $inputName;?>").click(remove_audio);
            });
        </script>
		<?php if( $$valueOriginalRelative ){ 
                                                echo $value; 
                                                echo "<div id='actions-{$inputName}'><a href='javascript:void(0);' id='remove-{$inputName}'>".__("Delete",$flutter_domain)."</a></div>";
                                            } ?>
		
		
		<input tabindex="3" 
			id="<?php echo $inputName?>" 
			name="<?php echo $inputName?>" 
			type="hidden" 
			class="<?php echo $requiredClass;?>"
			size="46"
			value="<?php echo $$valueOriginalRelative?>"	
			/>
    
		<?php
        //adding the  SWF upload 
		include_once( "RCCWP_SWFUpload.php" ) ;
		RCCWP_SWFUpload::Body($inputName, 2, $is_canvas, $urlInputSize) ;
		
	}
	
	function ColorPickerInterface($customField, $inputName, $groupCounter, $fieldCounter,$fieldValue = NULL){
		if($fieldValue){
			$value=$fieldValue;
		}else{
			$value = attribute_escape(RCCWP_CustomField::GetCustomFieldValues(true, $_REQUEST['post'], $customField->name, $groupCounter, $fieldCounter));
		}
		?>
		<script type='text/javascript' src='<?=FLUTTER_URI?>js/sevencolorpicker.js'></script>
		<script type="text/javascript">
			jQuery('document').ready(function(){
				jQuery('#<?php echo $inputName?>').SevenColorPicker();
			});
		</script>
		<input  id="<?php echo $inputName?>" name="<?php echo $inputName?>" value="<?php echo $value?>"  />
		<?php
	}
	
	function SliderInterface($customField, $inputName, $groupCounter, $fieldCounter,$fieldValue = NULL){
		$customFieldId = $customField->id;
		$value = attribute_escape(RCCWP_CustomField::GetCustomFieldValues(true, $_REQUEST['post'], $customField->name, $groupCounter, $fieldCounter));
		
		
		if($fieldValue){
			$value=$fieldValue;
		}else{
			$value = attribute_escape(RCCWP_CustomField::GetCustomFieldValues(true, $_REQUEST['post'], $customField->name, $groupCounter, $fieldCounter));
		}
		
		if(!$customField->properties['min']){
			$customField->properties['min']=0;
		}
		if(!$value){
			$value=$customField->properties['min'];
		}
		if(!$customField->properties['max']){
			$customField->properties['max']=100;
		}
		if(!$customField->properties['step']){
			$customField->properties['step']=0;
		}
		?>
		<link rel="stylesheet" href="<?=FLUTTER_URI?>css/flora.slider.css" type="text/css" media="screen" title="Flora (Default)">
		<script type="text/javascript" src="<?=FLUTTER_URI?>js/ui.slider.js"></script>

		<script type="text/javascript">
			jQuery('document').ready(function(){
				jQuery('#slider_<?php echo $inputName?>').slider({range: true, start: <?=$value?> , min: <?=$customField->properties['min']?>, max: <?=$customField->properties['max']?>, stepping: <?=$customField->properties['step']?>,
							handles: [ {start: <?=$value?>, stepping: <?=$customField->properties['step']?>,min: <?=$customField->properties['min']?>, max: <?=$customField->properties['max']?>, id: 'slider_<?php echo $inputName?>'} ]
							//,change: function(e,ui) {
							//	jQuery('#slide_value_<?php echo $inputName?>').empty();
							//	jQuery('#slide_value_<?php echo $inputName?>').append(ui.value);
							//	jQuery('#<?php echo $inputName?>').val(ui.value);
							//	}
							,'slide': function(e, ui){ 
                    jQuery('#slide_value_<?php echo $inputName?>').empty();
								jQuery('#slide_value_<?php echo $inputName?>').append(ui.value);
								jQuery('#<?php echo $inputName?>').val(ui.value);
            }

								});
			//jQuery('#slider_<?php echo $inputName?>').slider("moveTo", "+=6")
			//jQuery('#slider_<?php echo $inputName?>').slider("value", "9")
			});
		</script>
		<style>
		.slider_numeber_show{
			margin-top: -16px;
			padding-left: 3px;
		}
		</style>
			<div id='slider_<?php echo $inputName?>' class='ui-slider-2' style="margin:40px;">
				<div class='ui-slider-handle'><div class="slider_numeber_show" id="slide_value_<?php echo $inputName?>">
				<?=$value?>
				</div></div>	
			</div>
			<input  type="hidden" id="<?php echo $inputName?>" name="<?php echo $inputName?>" value="<?php echo $value?>"  />
			



		
		<?php
	}

}

?>
