<?php

require( dirname(__FILE__) . '/../../../wp-config.php' );
global $flutter_domain;
if (!(is_user_logged_in() && current_user_can('edit_posts'))){
    die(__('Authentication failed!',$flutter_domain));   
}
    //Snipshot callback test
    //
    //Install this script on your server to test and debug callbacks from 
    //www.snipshot.com. Note: this script will need read and write access 
    //to your image directory.

    //CHANGE THIS TO THE DIRECTORY WHERE YOUR IMAGES WILL BE SAVED
    $IMG_DIR = FLUTTER_FILES_PATH;
    chdir($IMG_DIR);
    
    //CHANGE THIS TO THE NAME OF THE FIELD THAT CARRIES THE IMAGE DATA OR URL
    $OUTPUT = 'file';

    function print_pre($r, $desc=''){
        echo '<pre>' . $desc . ' ' . print_r($r) . '</pre>';
    }
    function glob_rsort_modtime( $patt ) {
        if ( ( $files = @glob($patt, GLOB_BRACE) ) === false )
            return array(false, 'Glob error.');
        if ( !count($files) )
            return array(false, 'No files found.');
        $rtn = array();
        foreach ( $files as $filename )
            $rtn[$filename] = filemtime($filename);
        arsort($rtn);
        reset($rtn);
        foreach ( $rtn as $filename => $t )
            $rtn[$filename] = date('r', $t);
        return $rtn;
    }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title><?php _e('Snipshot callback', $flutter_domain); ?></title>
    
    <?	
	$operationSuccess = "false";
        if (!empty($_REQUEST)){
            //print_pre($_REQUEST, 'REQUEST');
            if (!empty($_FILES)){ //snipshot_callback_agent=snipshot
                //print_pre($_FILES[$OUTPUT], 'FILES');    
                move_uploaded_file(
                    $_FILES[$OUTPUT]['tmp_name'], 
                    $_FILES[$OUTPUT]['name']
                );//file_put_contents($filepath, time()."trying ".$_FILES[$OUTPUT]['name']."\n", FILE_APPEND);
		//file_put_contents("log.txt", time()."trying ".basename($_GET[$OUTPUT]));
            } 
            else if (!empty($_GET[$OUTPUT])){ //snipshot_callback_agent=user
                $data = file_get_contents($_GET[$OUTPUT]);
		//echo "\nfrom ----".$_GET[$OUTPUT]."\n";
                $fp = fopen(basename($_GET[$OUTPUT]), 'wb');
		//echo "\nfrom ----".basename($_GET[$OUTPUT])."\n";
                fwrite($fp, $data);
                fclose($fp);
		chmod(basename($_GET[$OUTPUT]), 0644);
		$filename = basename($_GET[$OUTPUT]);

		//$filename = 'hello_'.time() . '.jpg';
 		//rename(basename($_GET[$OUTPUT]), $filename);

		$operationSuccess = "true";
		//file_put_contents("log.txt", var_export($_REQUEST, true)."\n new name = $filename\n",FILE_APPEND);
            }
        }
        
        //$file_list = glob_rsort_modtime('{*.jpg,*.png,*.gif,*.tif,*.psd,*.pdf}');
        //$file_list = array_slice($file_list, 0, 10);
        //print_pre($file_list, 'LAST 10 IMAGES');
        //foreach($file_list as $fn => $mt){
         //   echo '<p><img src="'.$fn.'"/>';
        //}
    ?>
</head>
<body>

	<script language="javascript">

    		var par = window.parent.document;
		
		if (<?php echo $operationSuccess?>){
			


		}
		
	</script>
	<h3> <?php _e('The file was updated successfuly', $flutter_domain); ?></h3>
	<p> <?php _e('Please save the page in order to see the changes.', $flutter_domain); ?> <a href="" onclick="self.parent.tb_remove();"><?php _e('Close this window', $flutter_domain); ?></a> </p>

</body>
</html>
