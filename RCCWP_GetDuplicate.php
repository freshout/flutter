<?php
	require( dirname(__FILE__) . '/../../../wp-config.php' );
	global $flutter_domain;
	if (!(is_user_logged_in() && current_user_can('edit_posts')))
		die(__('Athentication failed!',$flutter_domain));
		
	require_once("RCCWP_WritePostPage.php");
	require_once("RCCWP_CustomGroup.php");
	require_once ('RCCWP_Options.php');

	if( isset($_POST['flag']) && $_POST['flag'] == "group" )
	{
		$customGroup = RCCWP_CustomGroup::Get( $_POST['groupId'] ) ;
		RCCWP_WritePostPage::GroupDuplicate($customGroup,$_POST['groupCounter']) ;
	}

	else
	{
		$customFieldId = $_POST['customFieldId'];
		$groupCounter = $_POST['groupCounter'];
		$fieldCounter = $_POST['fieldCounter'];
        $groupId = $_POST['groupId'];
		RCCWP_WritePostPage::CustomFieldInterface($customFieldId, $groupCounter, $fieldCounter,$groupId);
		?>
		<?php
	}
?>
