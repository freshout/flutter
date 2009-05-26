<?php
require_once( dirname(__FILE__) . '/../../../wp-config.php' );
global $wpdb;

if (!defined('DIRECTORY_SEPARATOR'))
{
	if (strpos(php_uname('s'), 'Win') !== false )
		define('DIRECTORY_SEPARATOR', '\\');
	else 
		define('DIRECTORY_SEPARATOR', '/');
}

// General Constants
define('RC_CWP_DB_VERSION', 40);
define('RC_CWP_POST_WRITE_PANEL_ID_META_KEY', '_rc_cwp_write_panel_id');
define('RC_CWP_OPTION_KEY', 'rc_custom_write_panel');


// Flutter paths
preg_match('/wp-content(.*)(RCCWP_Constant\.php)$/',__FILE__,$flutterpath);
$flutterpath = str_replace('\\', '/', $flutterpath);
define('FLUTTER_PLUGIN_DIR', dirname(plugin_basename(__FILE__))); // returns Flutter
//define("FLUTTER_PATH", str_replace('/RCCWP_Constant.php', '', str_replace('\\', '/', __FILE__)));
define("FLUTTER_PATH", dirname(__FILE__));
define("FLUTTER_URI", get_bloginfo('wpurl').'/wp-content'.$flutterpath[1]); //returns somthing similar to "http://127.0.0.1/wp-content/plugins/Flutter/"
define("FLUTTER_URI_RELATIVE", 'wp-content'.$flutterpath[1]); //returns somthing similar to "wp-content/plugins/Flutter/"
define("PHPTHUMB",FLUTTER_URI."thirdparty/phpthumb/phpThumb.php");

// -- Tables names

// Tables containing somehow constant data
define('RC_CWP_TABLE_CUSTOM_FIELD_TYPES', (isset($current_blog)?$wpdb->base_prefix:$wpdb->prefix)  . 'rc_cwp_custom_field_types');
define('RC_CWP_TABLE_STANDARD_FIELDS', (isset($current_blog)?$wpdb->base_prefix:$wpdb->prefix)  . 'rc_cwp_standard_fields');

// Panels - Groups - Fields
define('RC_CWP_TABLE_PANELS', (isset($current_blog)?$wpdb->base_prefix:$wpdb->prefix)  . 'rc_cwp_write_panels');
define('RC_CWP_TABLE_PANEL_GROUPS', (isset($current_blog)?$wpdb->base_prefix:$wpdb->prefix)  . 'rc_cwp_module_groups');
define('RC_CWP_TABLE_GROUP_FIELDS', (isset($current_blog)?$wpdb->base_prefix:$wpdb->prefix)  . 'rc_cwp_panel_custom_field');

// Extra information about panels
define('RC_CWP_TABLE_PANEL_CATEGORY', (isset($current_blog)?$wpdb->base_prefix:$wpdb->prefix)  . 'rc_cwp_panel_category');
define('RC_CWP_TABLE_PANEL_STANDARD_FIELD', (isset($current_blog)?$wpdb->base_prefix:$wpdb->prefix)  . 'rc_cwp_panel_standard_field');
define('RC_CWP_TABLE_PANEL_HIDDEN_EXTERNAL_FIELD', (isset($current_blog)?$wpdb->base_prefix:$wpdb->prefix)  . 'rc_cwp_panel_hidden_external_field');

// Extra information about fields
define('RC_CWP_TABLE_CUSTOM_FIELD_OPTIONS', (isset($current_blog)?$wpdb->base_prefix:$wpdb->prefix)  . 'rc_cwp_custom_field_options');
define('RC_CWP_TABLE_CUSTOM_FIELD_PROPERTIES', (isset($current_blog)?$wpdb->base_prefix:$wpdb->prefix)  . 'rc_cwp_custom_field_properties');

// Modules
define('RC_CWP_TABLE_MODULES', (isset($current_blog)?$wpdb->base_prefix:$wpdb->prefix)  . 'rc_cwp_modules');
define('FLUTTER_TABLE_MODULES_DUPLICATES', (isset($current_blog)?$wpdb->base_prefix:$wpdb->prefix)  . 'rc_cwp_modules_duplicates');

// Extra information about post meta values.
define('RC_CWP_TABLE_POST_META', $wpdb->prefix . 'rc_cwp_post_meta');

// Layout info.
define('FLUTTER_TABLE_LAYOUT_MODULES', $wpdb->prefix . 'flutter_layout');
define('FLUTTER_TABLE_LAYOUT_VARIABLES', $wpdb->prefix . 'flutter_layout_vars');
define('FLUTTER_TABLE_LAYOUT_SETTINGS', $wpdb->prefix . 'flutter_layout_settings');

//define('RC_CWP_TABLE_PANEL_MODULES', (isset($current_blog)?$wpdb->base_prefix:$wpdb->prefix)  . 'rc_cwp_panel_modules');

// Field Types
global $FIELD_TYPES;
$FIELD_TYPES = array(
					"textbox" => 1,
					"multiline_textbox" => 2,
					"checkbox" => 3,
					"checkbox_list" => 4,
					"radiobutton_list" => 5,
					"dropdown_list" => 6,
					"listbox" => 7,
					"file" => 8,
					"image" => 9,
					"date" => 10,
					"audio" => 11
					);

// Field Types
global $STANDARD_FIELDS;
$STANDARD_FIELDS = array();

// Standard fields
$STANDARD_FIELDS[12] = new FlutterPanelFields(12, 'Post/Page', array('postdivrich'), true, false, true, true, 1000);
$STANDARD_FIELDS[2] = new FlutterPanelFields(2, 'Categories', array('categorydiv'), false, false, true, false, 1000);
$STANDARD_FIELDS[14] = new FlutterPanelFields(14, 'Tags', array('tagsdiv'), true, false, true, false, 1000);

// Common advanced fields
$STANDARD_FIELDS[11] = new FlutterPanelFields(11, 'Custom Fields', array('postcustom', 'pagepostcustom', 'pagecustomdiv'), true, true, true, true, 1000);
$STANDARD_FIELDS[3] = new FlutterPanelFields(3, 'Comments & Pings', array('commentstatusdiv', 'pagecommentstatusdiv'), true, true, true, true, 1000);
$STANDARD_FIELDS[4] = new FlutterPanelFields(4, 'Password', array('passworddiv', 'pagepassworddiv'), true, true, true, true, 1000);
$STANDARD_FIELDS[18] = new FlutterPanelFields(4, 'Post/Page Author', array('authordiv', 'pageauthordiv'), true, true, true, true, 1000);

// Post-specific advanced fields
$STANDARD_FIELDS[9] = new FlutterPanelFields(9, 'Excerpt', array('postexcerpt'), true, true, true, false, 1000);
$STANDARD_FIELDS[10] = new FlutterPanelFields(10, 'Trackbacks', array('trackbacksdiv'), true, true, true, false, 1000);
$STANDARD_FIELDS[5] = new FlutterPanelFields(5, 'Post Slug', array('slugdiv'), true, true, true, false, 1000);

// Page-specific advanced fields
$STANDARD_FIELDS[15] = new FlutterPanelFields(15, 'Page Parent', array('pageparentdiv'), true, true, false, true, 1000);
$STANDARD_FIELDS[16] = new FlutterPanelFields(16, 'Page Template', array('pagetemplatediv'), true, true, false, true, 1000);
$STANDARD_FIELDS[17] = new FlutterPanelFields(17, 'Page Order', array('pageorderdiv'), true, true, false, true, 1000);										


// define name the folder of plugin (flutter, Flutter or fresh-page)
define('FLUTTER_NAME', dirname(plugin_basename(__FILE__)));

// Important folders
define('FLUTTER_CONTENT_PATH', WP_CONTENT_DIR.DIRECTORY_SEPARATOR.FLUTTER_NAME);
define('FLUTTER_CONTENT_URI', WP_CONTENT_URL.DIRECTORY_SEPARATOR.FLUTTER_NAME);

// files of flutter is wp-content/files_flutter/
define('FLUTTER_FILES_NAME','files_flutter');
define('FLUTTER_FILES_PATH', WP_CONTENT_DIR.DIRECTORY_SEPARATOR.FLUTTER_FILES_NAME.DIRECTORY_SEPARATOR);
define('FLUTTER_FILES_URI', WP_CONTENT_URL.DIRECTORY_SEPARATOR.FLUTTER_FILES_NAME.DIRECTORY_SEPARATOR);


define('FLUTTER_UPLOAD_FILES_DIR', FLUTTER_FILES_PATH);
define('FLUTTER_IMAGES_CACHE_DIR', WP_CONTENT_DIR.DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR.FLUTTER_NAME.DIRECTORY_SEPARATOR.'thirdparty'.DIRECTORY_SEPARATOR.'phpthumb'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR);
define('FLUTTER_MODULES_DIR', FLUTTER_FILES_PATH."modules".DIRECTORY_SEPARATOR);
// Capabilities names
define('FLUTTER_CAPABILITY_PANELS', "Create Flutter Panels");
define('FLUTTER_CAPABILITY_MODULES', "Create Flutter Modules");
define('FLUTTER_CAPABILITY_LAYOUT', "Theme Settings");
define('FLUTTER_CAPABILITY_STYLE', "Change Flutter Style");

if (!defined('DIRECTORY_SEPARATOR'))
{
	if (strpos(php_uname('s'), 'Win') !== false )
		define('DIRECTORY_SEPARATOR', '\\');
	else 
		define('DIRECTORY_SEPARATOR', '/');
}
?>