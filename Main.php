<?php
/*
Plugin Name: Flutter
Plugin URI: http://flutter.freshout.us/
Description: Create custom write panels and easily retrieve their values in your templates.
Author: Freshout
Version: .2.51
Author URI: http://freshout.us
*/


/**
 * This work is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU General Public License 
 * as published by the Free Software Foundation; either version 
 * 2 of the License, or any later version.
 *
 * This work is distributed in the hope that it will be useful, 
 * but without any warranty; without even the implied warranty 
 * of merchantability or fitness for a particular purpose. See 
 * Version 2 and version 3 of the GNU General Public License for
 * more details. You should have received a copy of the GNU General 
 * Public License along with this program; if not, write to the 
 * Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, 
 * Boston, MA 02110-1301 USA
 */


// To generate PHPDoc, use the following command:
// phpdoc -o HTML:Smarty:default -t ./docs -d . -i purifier_lib/ -po FlutterDatabaseObjects -ti 'Flutter API Documentation'

// Globals
global $wpdb, $main_page, $table_prefix, $zones, $parents, $installed, $main_page, $post;
global $current_user;
global $wp_filesystem;
global $FIELD_TYPES;

// Classes
require_once 'classes/FlutterPanelFields.php';

// Include Flutter API related files

require_once 'RCCWP_CustomGroup.php';

// Libs
require_once 'libs/simplexml/simplexml.class.php';


// Classes/Core files
require_once 'classes/FlutterPanelFields.php';
require_once 'classes/FlutterLayoutBlock.php';
require_once 'RCCWP_Constant.php';

// Include Flutter API related files
require_once 'RCCWP_CustomGroup.php';
require_once 'RCCWP_CustomField.php';
require_once 'RCCWP_CustomWriteModule.php';
require_once 'RCCWP_CustomWritePanel.php';

// Include files containing Flutter public functions
require_once 'get-custom.php';

// Include other files used in this script
require_once 'RCCWP_Menu.php';
require_once 'RCCWP_CreateCustomFieldPage.php';
require_once 'tools/debug.php';


global $is_wordpress_mu;
if(isset($current_blog)) 
	$is_wordpress_mu=true;
else
	$is_wordpress_mu=false;
	
 /* function for languajes
  *
  */
global $flutter_domain;
$flutter_domain = 'flutter';	
load_plugin_textdomain($flutter_domain, '/'.PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)).'/languajes', basename(dirname(__FILE__)).'/languages');

		

if (is_admin()) {
	require_once ('RCCWP_Application.php');
	require_once ('RCCWP_WritePostPage.php');
	
	register_activation_hook(dirname(__FILE__) . '/Main.php', array('RCCWP_Application', 'Install'));

	if(isset($current_blog)) {
		RCCWP_Application::Install();
	    add_action('admin_menu', array('RCCWP_Application', 'ContinueInstallation'));
    }

	if (get_option(RC_CWP_OPTION_KEY) !== false) {
		require_once ('RCCWP_Processor.php');
		add_action('init', array('RCCWP_Processor', 'Main'));
		

		add_action('admin_menu', array('RCCWP_Menu', 'AttachCustomWritePanelMenuItems'));
		add_action('admin_menu', array('RCCWP_Menu', 'DetachWpWritePanelMenuItems'));
		add_action('admin_menu', array('RCCWP_Menu', 'AttachOptionsMenuItem'));
		
		add_filter('posts_where', array('RCCWP_Menu', 'FilterPostsPagesList'));
		add_action('admin_head', array('RCCWP_Menu', 'HighlightCustomPanel'));
		add_action('admin_head', array('RCCWP_CreateCustomFieldPage', 'AddAjaxDynamicList'));

        // -- Theme settings page
        add_action('admin_menu',array('RCCWP_Menu','AttachLayoutSettingsPage'));
        add_action('admin_head', array('RCCWP_ThemeSettingsPage', 'ApplyHead'));
		add_action('admin_print_scripts', array('RCCWP_ThemeSettingsPage', 'AddScripts'));

		// -- Hook all functions related to saving posts in order to save custom fields values
		require_once ('RCCWP_Post.php');	
		add_action('save_post', array('RCCWP_Post', 'SaveCustomFields'));
 		add_action('delete_post', array('RCCWP_Post','DeletePostMetaData')) ;
		

		
		add_filter('wp_redirect', array('RCCWP_Processor', 'Redirect'));

		add_action('shutdown', array('RCCWP_Processor', 'FlushAllOutputBuffer'));

		add_action('admin_notices', array('RCCWP_Application', 'CheckInstallation'));  
		add_action('admin_notices', array('RCCWP_WritePostPage', 'FormError'));
	    
        // -- Layout functions
        add_action('switch_theme',array('RCCWP_Application','ImportNewTheme'));
	}
}

add_action('admin_print_scripts', array('RCCWP_Menu', 'AddThickbox'));
add_action('admin_menu', array('RCCWP_Menu', 'AttachFlutterMenus'));

require_once ('RCCWP_EditnPlace.php');
add_action('wp_head', array('RCCWP_EditnPlace', 'EditnHeader'));


require_once ('FlutterLayout.php');
add_action('wp_head', array('FlutterLayout', 'PrepareThemeSettings'));
add_action('wp_head', array('FlutterLayout', 'AddHeaderLayoutCode'));
add_action('wp_footer', array('FlutterLayout', 'AddFooterLayoutCode'));



require_once ('RCCWP_Query.php');
add_action('pre_get_posts', array('RCCWP_Query', 'FilterPrepare'));
add_filter('posts_where', array('RCCWP_Query', 'FilterCustomPostsWhere'));
add_filter('posts_orderby', array('RCCWP_Query', 'FilterCustomPostsOrderby'));
add_filter('posts_fields', array('RCCWP_Query', 'FilterCustomPostsFields'));
add_filter('posts_join_paged', array('RCCWP_Query', 'FilterCustomPostsJoin'));


add_action('edit_page_form','cwp_add_pages_identifiers');
add_action('edit_form_advanced','cwp_add_type_identifier');

// -- KSES filter
add_filter('pre_comment_content','flutter_kses');
add_filter('title_save_pre','flutter_kses');
//add_filter('content_save_pre','flutter_kses');
add_filter('excerpt_save_pre','flutter_kses');
add_filter('content_filtered_save_pre','flutter_kses');



/**
 *This only one wrapper function for wp_kses
 *this will be used for  passed  and empty array  to the wp_kses function
 *(probably this function will be deprecated soon just i need found a best way to-do this)
 *
 *@author David Valdez <david@freshout.us>
 *
 */
function flutter_kses($string){

    /**
     * Tags can't be used
     */
     $used_tags = array('select','script','b'); 


    /**
     * List of tags
     */
     $html_tags  = array(
                            'address','applet','area','a','base','basefont','big','blockquote',
                            'body','br','b','caption','center','cite','code','dd','dfn','dir',
                            'div','dl','dt','em','font','form','h1','h2','h3','h4','h5','h6',
                            'head','hr','html','img','input','isindex','i','kbd','link','li',
                            'map','menu','meta','ol','option','param','pre','p','samp','script',
                            'select','small','strike','strong','style','sub','sup','table','td',
                            'textarea','th','title','tr','tt','ul','u','var'
                        );

     //remove that tag to the html_tag list
     foreach($html_tags as $key => $value){
         if(in_array($value,$used_tags)){
            unset($html_tags[$key]);
         }
     }

    return  wp_kses($string,array($html_tags));

}

function cwp_add_type_identifier(){

	global $wpdb;
	global $post;
	
	if( isset($_GET['custom-write-panel-id']) && !empty($_GET['custom-write-panel-id']))
	{
		$getPostID = $wpdb->get_results("SELECT id, type FROM ". RC_CWP_TABLE_PANELS ." WHERE id='".$_GET['custom-write-panel-id']."'");
		echo "<input type=\"hidden\" id=\"post_type\" name=\"post_type\" value=\"". $getPostID[0]->type ."\" />";

	}
	else{
		if($post->post_type == 'page') { 
			echo "<input type=\"hidden\" id=\"post_type\" name=\"post_type\" value=\"page\" />";
 		} else {
			echo "<input type=\"hidden\" id=\"post_type\" name=\"post_type\" value=\"post\" />";
 		}

 	}
}

function cwp_add_pages_identifiers(){
	global $post;
	global $wpdb;

	$key = wp_create_nonce('rc-custom-write-panel');
	$id = "";
	$result = $wpdb->get_results( " SELECT meta_value
					FROM $wpdb->postmeta
					WHERE post_id = '$post->ID' and meta_key = '_rc_cwp_write_panel_id'", ARRAY_A );
	
	if (count($result) > 0)
		$id = $result[0]['meta_value'];
	echo 
<<<EOF
		<input type="hidden" name="rc-custom-write-panel-verify-key" id="rc-custom-write-panel-verify-key" value="$key" />
		
EOF;
}

if ( !function_exists('sys_get_temp_dir')) {
  function sys_get_temp_dir() {
    if (!empty($_ENV['TMP'])) { return realpath($_ENV['TMP']); }
    if (!empty($_ENV['TMPDIR'])) { return realpath( $_ENV['TMPDIR']); }
    if (!empty($_ENV['TEMP'])) { return realpath( $_ENV['TEMP']); }
    $tempfile=tempnam(uniqid(rand(),TRUE),'');
    if (file_exists($tempfile)) {
    unlink($tempfile);
    return realpath(dirname($tempfile));
    }
  }
}

if ( !function_exists('chmod_R')) {
	function chmod_R($path, $filemode) {
	    if (!is_dir($path))
	        return chmod($path, $filemode);
	
	    $dh = opendir($path);
	    while ($file = readdir($dh)) {
	        if($file != '.' && $file != '..') {
	            $fullpath = $path.'/'.$file;
	            if(is_link($fullpath))
	                return FALSE;
	            elseif(!is_dir($fullpath))
	                if (!chmod($fullpath, $filemode))
	                    return FALSE;
	            elseif(!chmod_R($fullpath, $filemode))
	                return FALSE;
	        }
	    }
	
	    closedir($dh);
	
	    if(chmod($path, $filemode))
	        return TRUE;
	    else
	        return FALSE;
	}
}

function advancedRmdir($path) { //mappát töröl akkor is, ha nem üres
    $origipath = $path;
    $handler = opendir($path);
    while (true) {
        $item = readdir($handler);
        if ($item == "." or $item == "..") {
            continue;
        } elseif (gettype($item) == "boolean") {
            closedir($handler);
            if (!@rmdir($path)) {
                return false;
            }
            if ($path == $origipath) {
                break;
            }
            $path = substr($path, 0, strrpos($path, "/"));
            $handler = opendir($path);
        } elseif (is_dir($path."/".$item)) {
            closedir($handler);
            $path = $path."/".$item;
            $handler = opendir($path);
        } else {
            unlink($path."/".$item);
        }
    }
    return true;
}

if ( !function_exists('dircopy')) {

	 /* Copies a dir to another. Optionally caching the dir/file structure, used to synchronize similar destination dir (web farm).
     *
     * @param $src_dir str Source directory to copy.
     * @param $dst_dir str Destination directory to copy to.
     * @param $verbose bool Show or hide file copied messages
     * @param $use_cached_dir_trees bool Set to true to cache src/dst dir/file structure. Used to sync to web farms
     *                     (avoids loading the same dir tree in web farms; making sync much faster).
     * @return Number of files copied/updated.
     * @example
     *     To copy a dir:
     *         dircopy("c:\max\pics", "d:\backups\max\pics");
     *
     *     To sync to web farms (webfarm 2 to 4 must have same dir/file structure (run once with cache off to make sure if necessary)):
     *        dircopy("//webfarm1/wwwroot", "//webfarm2/wwwroot", false, true);
     *        dircopy("//webfarm1/wwwroot", "//webfarm3/wwwroot", false, true);
     *        dircopy("//webfarm1/wwwroot", "//webfarm4/wwwroot", false, true);
     */
    function dircopy($src_dir, $dst_dir, $verbose = false, $use_cached_dir_trees = false)
    {   
        static $cached_src_dir;
        static $src_tree;
        static $dst_tree;
        $num = 0;

        if (($slash = substr($src_dir, -1)) == "\\" || $slash == "/") $src_dir = substr($src_dir, 0, strlen($src_dir) - 1);
        if (($slash = substr($dst_dir, -1)) == "\\" || $slash == "/") $dst_dir = substr($dst_dir, 0, strlen($dst_dir) - 1); 

        if (!$use_cached_dir_trees || !isset($src_tree) || $cached_src_dir != $src_dir)
        {
            $src_tree = get_dir_tree($src_dir);
            $cached_src_dir = $src_dir;
            $src_changed = true; 
        }
        if (!$use_cached_dir_trees || !isset($dst_tree) || $src_changed)
            $dst_tree = get_dir_tree($dst_dir);
        if (!is_dir($dst_dir)) mkdir($dst_dir, 0777, true); 

          foreach ($src_tree as $file => $src_mtime)
        {
            if (!isset($dst_tree[$file]) && $src_mtime === false) // dir
                mkdir("$dst_dir/$file");
            elseif (!isset($dst_tree[$file]) && $src_mtime || isset($dst_tree[$file]) && $src_mtime > $dst_tree[$file])  // file
            {
                if (copy("$src_dir/$file", "$dst_dir/$file"))
                {
                    if($verbose) echo "Copied '$src_dir/$file' to '$dst_dir/$file'<br>\r\n";
                    touch("$dst_dir/$file", $src_mtime);
                    $num++;
                } else
                    echo "<font color='red'>File '$src_dir/$file' could not be copied!</font><br>\r\n";
            }       
        }

        return $num;
    }

    /* Creates a directory / file tree of a given root directory
     *
     * @param $dir str Directory or file without ending slash
     * @param $root bool Must be set to true on initial call to create new tree.
     * @return Directory & file in an associative array with file modified time as value.
     */
    function get_dir_tree($dir, $root = true)
    {
        static $tree;
        static $base_dir_length;

        if ($root)
        {
            $tree = array(); 
            $base_dir_length = strlen($dir) + 1; 
        }

        if (is_file($dir))
        {
            //if (substr($dir, -8) != "/CVS/Tag" && substr($dir, -9) != "/CVS/Root"  && substr($dir, -12) != "/CVS/Entries")
            $tree[substr($dir, $base_dir_length)] = filemtime($dir);
        } elseif (is_dir($dir) && $di = dir($dir)) // add after is_dir condition to ignore CVS folders: && substr($dir, -4) != "/CVS"
        {
            if (!$root) $tree[substr($dir, $base_dir_length)] = false; 
            while (($file = $di->read()) !== false)
                if ($file != "." && $file != "..")
                    get_dir_tree("$dir/$file", false); 
            $di->close();
        }

        if ($root)
            return $tree;    
    }
 }
 

 
?>
