<?php
require( dirname(__FILE__) . '/../../../wp-config.php' );
if (!(is_user_logged_in() && current_user_can('edit_posts')))
	die("Athentication failed!");
	global $flutter_domain;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

	<title>
		<?php //bloginfo('name') ?> &rsaquo; 
		<?php //echo wp_specialchars( strip_tags( $title ) ); ?> 
		&#8212; WordPress
	</title>

	<?php //wp_admin_css(); ?>
    	<link href="../../../wp-admin/wp-admin.css" rel="stylesheet" type="text/css" media="all" />

	<script type="text/javascript" src="js/greybox.js"></script>	
	<script type="text/javascript" src="js/prototype.js"></script>	
 	<script type="text/javascript" src="js/scriptaculous/scriptaculous.js"></script>
	<script type="text/javascript" src="js/cropper.js"></script>
	<script type="text/javascript">
	function MM_swapImgRestore() { //v3.0
	  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
	}
	
	function MM_preloadImages() { //v3.0
	  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
		var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
		if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
	}
	
	function MM_findObj(n, d) { //v4.01
	  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
		d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
	  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
	  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
	  if(!x && d.getElementById) x=d.getElementById(n); return x;
	}
	
	function MM_swapImage() { //v3.0
	  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
	   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
	}
	//-->
	</script>
	<script type="text/javascript" charset="utf-8">
		// setup the callback function
		function onEndCrop( coords, dimensions ) {
			$( 'x1' ).value = coords.x1;
			$( 'y1' ).value = coords.y1;
			$( 'x2' ).value = coords.x2;
			$( 'y2' ).value = coords.y2;
			$( 'width' ).value = dimensions.width;
			$( 'height' ).value = dimensions.height;
		}
		
		// basic example
		Event.observe( 
			window, 
			'load', 
			function() { 
				new Cropper.Img( 
					'testImage',
					{
						onEndCrop: onEndCrop 
					}
				) 
			}
		); 		
		
		
		if( typeof(dump) != 'function' ) {
			Debug.init(true, '/');
			
			function dump( msg ) {
				Debug.raise( msg );
			};
		} else dump( '---------------------------------------\n' );
	</script>
	<script type="text/javascript" charset="utf-8">

		function imageCrop()
		{
			if(document.getElementById('x1').value != '0')
			{
				x1 = document.getElementById('x1').value;
				y1 = document.getElementById('y1').value;
				x2 = document.getElementById('x2').value;
				y2 = document.getElementById('y2').value;

				w = document.getElementById('width').value;
				h = document.getElementById('height').value;

				sourceImage = document.getElementById('sourceImage').value;

				if(sourceImage.indexOf("&sw=") != "-1")
				{
//					cropValue1 = document.getElementById("cropValues2").value;
//					cropValue2 = document.getElementById("cropValues3").value;
					cropValue3 = document.getElementById("cropValues4").value;
					cropValue4 = document.getElementById("cropValues5").value;

					x1 = parseInt(cropValue3) + parseInt(x1);
					y1 = parseInt(cropValue4) + parseInt(y1);

					sourceImage = sourceImage.substring(0, sourceImage.indexOf("&sw="));
				}

				requiredString = sourceImage+"&sw="+w+"&sh="+h+"&sx="+x1+"&sy="+y1;
			}
			else
				requiredString = document.getElementById('sourceImage').value;
				
			window.parent.parent.document.getElementById("<?php echo $_GET["input_name"]?>").value = requiredString;
			
			requiredString = "<?php echo PHPTHUMB.'?src='.FLUTTER_FILES_PATH ?>" + requiredString; 

			document.getElementById('tempSrc').value = requiredString;
			window.parent.parent.exchangeValues(requiredString, document.getElementById('imageThumbId').value); 
//			parent.$("img_thumb_1").src = requiredString + "&w=150&h=120";
			window.parent.parent.$(document.getElementById('imageThumbId').value).src = requiredString + "&w=150&h=120";
			window.parent.parent.document.getElementById(document.getElementById('imageThumbId').value).src = requiredString + "&w=150&h=120";
			window.parent.parent.GB_hide();
			//console.log(requiredString);
		}
	</script>
	<link rel="stylesheet" type="text/css" href="debug.css" media="all" />
	<style type="text/css">
		label { 
			clear: left;
			margin-left: 50px;
			float: left;
			width: 5em;
			}
		
		html, body { 
			margin: 0;
			}
		
		#testWrap {
			margin:8px; /* Just while testing, to make sure we return the correct positions for the image & not the window */
			}
	</style>
</head>
<body onload="MM_preloadImages('./images/ajax-loader.gif')">
	<div align="center" id="freshpostProgressBar" name="freshpostProgressBar" style="position:absolute; bottom:50%; left:30%; z-index:0; display:inline; background:#fff; border:1px solid #666;">
		Loading...
		<div><img src="./images/ajax-loader.gif" /></div>
	</div>
	<form name="frmCroper" action="" method="post">
	<?php 
		if(isset($_GET['sw'])) {
			$finalSrc = $_GET['id']."&sw=".$_GET['sw']."&sh=".$_GET['sh']."&sx=".$_GET['sx']."&sy=".$_GET['sy'];
		}
		else {
			$finalSrc = $_GET['id'];
		}
		if (isset($_GET['url']))
			$url = $_GET['url']."&post=".$_GET['post'];
			
		$j = 0;
		$tmpArr = explode('&', $finalSrc);
		$pureImage = FLUTTER_FILES_URI.$tmpArr[0];
			
	?>
		<div id="testWrap" style="z-index:1;">
			<img src="<?php echo $pureImage; ?>" alt="test image" id="testImage" onload="javascript: document.getElementById('freshpostProgressBar').style.display = 'none';" />
		</div>

<?php

$finalSrc = $tmpArr[0];
foreach(explode("&", $finalSrc) as $item)
	{
		if($j++ == 0)
			continue;
		$cropValueArr = explode("=",$item);
	?>
		<input type="hidden" name="cropValues<?php echo $j; ?>" id="cropValues<?php echo $j; ?>" value="<?php echo $cropValueArr[1]; ?>" />
	<?php } ?>

		<input type="hidden" name="sourceImage" id="sourceImage" value="<?php echo $finalSrc; ?>" />
		<input type="hidden" name="url" id="url" value="<?php echo $url; ?>" />
		<input type="hidden" name="tempSrc" id="tempSrc" />

		<input type="hidden" name="imageThumbId" id="imageThumbId" value="<?php echo $_GET['imageThumbId']; ?>"/>

		<p align="right" style="position:fixed; top:-10px; right:0px; z-index:1000;">
			<input type="button" value="<?= __("Crop It", $flutter_domain)?>" onclick="javascript: imageCrop();" />
		</p>
		<input type="hidden" name="x1" id="x1" />
		<input type="hidden" name="y1" id="y1" />
		<input type="hidden" name="x2" id="x2" />
		<input type="hidden" name="y2" id="y2" />
		<input type="hidden" name="width" id="width" />
		<input type="hidden" name="height" id="height" />
	</form>
</body>
</html>
