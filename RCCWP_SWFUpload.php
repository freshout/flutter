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
                        </td>
					</tr>
				<?php
				}
				?>

				<tr style="background:transparent">
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
                                
                                element =  new SWFUpload({
                                        //button settings
	    		                        button_text: '<span class="button">Browse</span>',
                                        button_text_style: '.button { text-align: center; font-weight: bold; font-family:"Lucida Grande","Lucida Sans Unicode",Tahoma,Verdana,sans-serif; }',
			                            button_height: "24",
                            			button_width: "132",
                	    	        	button_image_url: wp_root+'/wp-includes/images/upload.png',
                		            	file_post_name: "async-upload",
                                        
                                        //requeriments settings
                                        upload_url  : flutter_path + "/RCCWP_GetFile.php",
           		                	    flash_url :  wp_root+"/wp-includes/js/swfupload/swfupload.swf",
                                        file_size_limit : "20 MB",
                                        button_placeholder_id : "upload-"+ "<?php echo $inputName;?>",
                                        debug: false,
    
                                        //custom settings
                                        custom_settings :{
                                            'file_id' : "<?php echo $inputName;?>"
                                        },
                                            
                                        //handlers
                                        file_queued_handler : adjust,
                                        upload_success_handler :  completed,
            
                                        post_params : {
		                                    auth_cookie : swf_authentication,
                                        	_wpnonce : swf_nonce
        		                    	}
                        	    	});
                            </script>
 
		<?php
	}
}
?>
