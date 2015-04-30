<?php
class FB_JRPlugin
{
	function __construct()
	{
		register_activation_hook( __FILE__,   array($this, 'jr_fb_default') ); 

		add_action( 'admin_menu', array($this, 'jr_fb_menu')  );
		
		add_action( 'admin_enqueue_scripts',  array($this, 'admin_script') );
		
		
		add_action('wp_footer', array($this, 'jr_fb_script'));
		
		include 'class.jrlisttable.php';
		
	}
	
	function jr_fb_script(){
		$app_id = get_option( 'jr_fb_app_id' ); 
		$output = '<div id="fb-root"></div>
				<script>(function(d, s, id) {
				  var js, fjs = d.getElementsByTagName(s)[0];
				  if (d.getElementById(id)) return;
				  js = d.createElement(s); js.id = id;
				  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.3&appId='.$app_id.'";
				  fjs.parentNode.insertBefore(js, fjs);
				}(document, "script", "facebook-jssdk"));</script>';
		echo $output;		
	}
	
	function jr_fb_default() {
		
	}
	
	function admin_script()
	{
		$page_array = array('jr-fb-plugin', 'jr-fb-pages', 'jr-add-fb-page', 'jr-fb-general-settings');
		if( isset( $_REQUEST['page'] ) )
		{
			if( in_array($_REQUEST['page'],$page_array ) )
			{
				echo "<script> var ajax_request_url = '" . admin_url('admin-ajax.php') . "' </script>";
				wp_enqueue_script( 'fb-admin-script',  plugins_url( 'js/script.js', __FILE__ ) );
				wp_enqueue_style( 'jr-admin-css', plugins_url( 'css/style.css', __FILE__ ) );
			}
		}
	}
	
	function jr_fb_menu() {
		// Create top-level menu item 
		add_menu_page( 'FB Social Plugins', 'FB Social Plugins', 'manage_options', 'jr-fb-plugin',  array($this, 'jr_fb_pageplugin'), plugins_url( 'img/facebook-icon22X22.png', __FILE__ ) ); 
		
		add_submenu_page( 'jr-fb-plugin', 'FB Plugins List', 'FB Plugins List', 'manage_options', 'jr-fb-pages', array($this, 'jr_fb_page_listing') );
		add_submenu_page( 'jr-fb-plugin', 'Add/Edit FB Plugin', 'Add FB Plugin', 'manage_options', 'jr-add-fb-page', array($this, 'jr_add_new_fb_page') );
		add_submenu_page( 'jr-fb-plugin', 'FB General Settings', 'FB General Settings', 'manage_options', 'jr-fb-general-settings', array($this, 'jr_fb_settings') );
		remove_submenu_page('jr-fb-plugin','jr-fb-plugin');
	}
	
	
	function jr_fb_page_listing() {
		$app_id = get_option( 'jr_fb_app_id' );
		if( empty( $app_id ) )
		{
			print('<script>window.location.href="admin.php?page=jr-fb-general-settings"</script>');
		}
		else
		{
			$fb_page_list = new Jr_List_Table();	
			$fb_page_list->fb_pages_display();
		}
		die();
	}
	
	function jr_add_new_fb_page() {	
		$app_id = get_option( 'jr_fb_app_id' );
		if( empty( $app_id ) )
		{
			print('<script>window.location.href="admin.php?page=jr-fb-general-settings"</script>');
		}
		else
		{
			$fb_page_list = new Jr_List_Table();
			if( $_REQUEST['jr_action'] == 'fb_edit' && !empty( $_REQUEST['ID'] ) )
				$fb_page_list->fb_edit_plugin();
			else
				$fb_page_list->fb_add_plugin();
		}
		die();
	}
	
	function jr_fb_settings(){		
		$fb_page_list = new Jr_List_Table();
		$fb_page_list->fb_general_settings();
		die();
	}
}
/** Initiate the Class */
$GLOBALS['fb_jrplugin'] = new FB_JRPlugin;
?>