<?php


function jr_add_fb_new_plugin(){
	$post_array = array(
	  'post_title'    => $_POST['fb_plugin_name'],
	  'post_content'  => $_POST['fb_plugin_type'],
	  'post_status'   => 'publish',
	  'post_author'   => 1,
	  'post_type' => 'jr_fb_social_plugin'
	);
	$post_id = wp_insert_post( $post_array, $wp_error );
	foreach( $_POST as $key => $value )
	{
		$post_sanitize_array[$key] = sanitize_text_field( $value );
	}
	if( update_post_meta( $post_id, '_jr_fb_plugin', $post_sanitize_array ) )
	{
		echo $post_id;
	}
	else
	{
		echo "error";
	}
	
	exit;
}

function jr_edit_fb_new_plugin(){
	
	$update_array = array(
      'ID'           => $_POST['hidden_post_id'],
      'post_title'   => $_POST['fb_plugin_name'],
      'post_content' => $_POST['fb_plugin_type'],
  	);

	// Update the post into the database
  	wp_update_post( $update_array );
	
	foreach( $_POST as $key => $value )
	{
		$post_sanitize_array[$key] = sanitize_text_field( $value );
	}
	update_post_meta( $_POST['hidden_post_id'], '_jr_fb_plugin', $post_sanitize_array );
	echo $_POST['hidden_post_id'];

	exit;
}

function jr_fb_general_settings(){
	update_option( 'jr_fb_app_id', $_POST['fb_api_id'] );
	echo "sucess";	
	exit;
}



if( !function_exists('jr_kvalue') )
{
	function jr_kvalue( $obj, $val, $def = '' )
	{
		if( is_array($obj) ) 
		{
			if( isset( $obj[$val] ) ) return $obj[$val];
		}
		elseif( is_object( $obj ) )
		{
			if( isset( $obj->$val ) ) return $obj->$val;	
		}
		
		if( $def ) return $def;
		else return false;
	}
}

function jr_fb_enqueue_scripts(){
	wp_enqueue_style( 'fb-social-css', plugins_url( 'css/fb-social-plugin.css', __FILE__ ) );
}

/** Include styles and scripts */
add_action('wp_enqueue_scripts', 'jr_fb_enqueue_scripts');

add_action( 'wp_ajax_nopriv_fb_add_jr', 'jr_add_fb_new_plugin');
add_action( 'wp_ajax_fb_add_jr', 'jr_add_fb_new_plugin');
add_action( 'wp_ajax_nopriv_fb_edit_jr', 'jr_edit_fb_new_plugin');
add_action( 'wp_ajax_fb_edit_jr', 'jr_edit_fb_new_plugin');
add_action( 'wp_ajax_nopriv_fb_general_settings', 'jr_fb_general_settings');
add_action( 'wp_ajax_fb_general_settings', 'jr_fb_general_settings');
add_filter('widget_text', 'do_shortcode');

?>