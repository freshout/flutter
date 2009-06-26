<?php
class RCCWP_SWFUpload
{
	function Body($inputName, $fileType, $isCanvas = 0, $urlInputSize = false)
	{
		global $flutter_domain;
		include_once('RCCWP_Options.php');

		if (!$urlInputSize) $urlInputSize = 20;

		if ($isCanvas==0) {
			$iframeInputSize = $urlInputSize;
			$iframeWidth = 380;
			$iframeHeight = 40;
		}
		else{
			$isCanvas = 1;
			$iframeWidth = 150;
			$iframeHeight = 60;
			$iframeInputSize = 3;
			$inputSizeParam = "&inputSize=$iframeInputSize";
		}

		$iframePath = FLUTTER_URI."RCCWP_upload.php?input_name=".urlencode($inputName)."&type=$fileType&imageThumbID=img_thumb_$inputName&canvas=$isCanvas".$inputSizeParam ;
		$enableBrowser = RCCWP_Options::Get('enable-browserupload') ;
		if( $enableBrowser != 0  || $enableBrowser != ''){
		?>
			<div id='upload_iframe_<?php echo $inputName?>'>
			<iframe id='upload_internal_iframe_<?php echo $inputName?>' src='<?php echo $iframePath;?>' frameborder='' scrolling='no' style="border-width: 0px; height: <?php echo $iframeHeight ?>px; width: <?php echo $iframeWidth ?>px;vertical-align:top;"></iframe>
			</div>
		<?php
		}
		?>
		
			<table border="0" style="width:100%">

				<?php
				if(! $enableBrowser )
				{
				?>
					<tr  style="background:transparent" id="swfuploadRow_<?php echo $inputName ?>">
						<td style="border-bottom-width: 0px;padding: 0px">
                           <label for="swfupload" ><?php _e('Upload', $flutter_domain); ?>:</label>
                        </td>
						<td style="border-bottom-width: 0px">
                            <span id="upload-<?php echo $inputName?>" class="upload_file" ></span>
			    <input id="btnCancel" type="button"  disabled="disabled" style="display: none;" />
                        </td>
					</tr>
				<?php
				}
				?>

				<tr >
					<td style="border-bottom-width: 0px;padding: 0; padding-bottom:32px;"><label for="upload_url" ><?php _e('Or URL', $flutter_domain); ?>:</label></td>
					<td style="border-bottom-width: 0px">
						<input id="upload_url_<?php echo $inputName ?>"
							name="upload_url_<?php echo $inputName ?>"
							type="text"
							size="<?php echo $urlInputSize ?>"
							/>
						<input type="button" onclick="uploadurl('<?php echo $inputName ?>','<?php echo $fileType ?>')" value="Upload" class="button" style="width:70px"/>
					</td>
				</tr>

			</table>
			
			<script type="text/javascript">
		var swfu_flutter;

		jQuery(document).ready(function(){
			
			var settings = {
				flash_url : flutter_path + "thirdparty/swfupload/swfupload.swf",
				upload_url: flutter_path + "RCCWP_GetFile.php",
				
				post_params : { auth_cookie : swf_authentication, _wpnonce : swf_nonce },
				file_size_limit : "20 MB",
				file_types : "*.*",
				file_types_description : "All Files",
				file_queue_limit : 0,
				file_post_name: "async-upload",
				custom_settings : {
					cancelButtonId : "btnCancel",
					file_id : "<?php echo $inputName;?>",
					upload_target : "<?php echo $inputName;?>"
				},
				debug: false,

				// Button settings
				button_image_url: wp_root+'/wp-includes/images/upload.png',
				button_width: "132",
				button_height: "24",
				button_placeholder_id: "upload-"+ "<?php echo $inputName;?>",
				button_text: '<span class="button">Browse</span>',	
				button_text_style: '.button { text-align: center; font-weight: bold; font-family:"Lucida Grande","Lucida Sans Unicode",Tahoma,Verdana,sans-serif; }',

				
				// The event handler functions are defined in handlers.js
				file_queued_handler : adjust,
				upload_success_handler : completed
			};

			swfu_flutter = new SWFUpload(settings);
			
			
	     });
	     
	</script>
	
		<?php
	}
}
?>
