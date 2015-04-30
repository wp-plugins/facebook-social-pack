<?php
class FB_Shortcodes
{
	function __construct()
	{
		/** Add Shortcode */
		add_shortcode('fb_jr_plugins', array($this, 'fb_main_shortcode'));
	}
	
	function fb_main_shortcode($atts, $content = null){
		extract(shortcode_atts(array('id'=>0), $atts));
		
		$output .= $this->fb_get_html($id);
		return $output;		
	}
	
	function fb_get_html( $post_id  ){
		$post_data = get_post($post_id);
		$get_post_meta = get_post_meta($post_id, '_jr_fb_plugin', true);
		if( $post_data->post_content == 'fb-like' )
		{
			return '<div class="fb-like" 
			'.( (jr_kvalue( $get_post_meta, 'fb_like_url' ) != '') ? 'data-href="'.jr_kvalue( $get_post_meta, 'fb_like_url' ).'"' : '' ).' 
			'.( (jr_kvalue( $get_post_meta, 'fb_like_width' ) != '') ? 'data-width="'.jr_kvalue( $get_post_meta, 'fb_like_width' ).'"' : '' ).' 
			'.( (jr_kvalue( $get_post_meta, 'fb_like_layout' ) != '') ? 'data-layout="'.jr_kvalue( $get_post_meta, 'fb_like_layout' ).'"' : '' ).'
			'.( (jr_kvalue( $get_post_meta, 'fb_like_action_type' ) != '') ? 'data-action="'.jr_kvalue( $get_post_meta, 'fb_like_action_type' ).'"' : '' ).'
			'.( (jr_kvalue( $get_post_meta, 'fb_like_friend_faces' ) != '') ? ' data-show-faces="'.jr_kvalue( $get_post_meta, 'fb_like_friend_faces' ).'"' : 'data-show-faces="false"' ).'
			'.( (jr_kvalue( $get_post_meta, 'fb_like_include_share' ) != '') ? '  data-share="'.jr_kvalue( $get_post_meta, 'fb_like_include_share' ).'"' : 'data-share="false"' ).' ></div>';
		}
		else if( $post_data->post_content == 'fb-share-button' )
		{
			
			return '<div class="fb-share-button" 
			'.( (jr_kvalue( $get_post_meta, 'fb_share_button_url' ) != '') ? 'data-href="'.jr_kvalue( $get_post_meta, 'fb_share_button_url' ).'"' : '' ).'  
			'.( (jr_kvalue( $get_post_meta, 'fb_share_button_layout' ) != '') ? 'data-layout="'.jr_kvalue( $get_post_meta, 'fb_share_button_layout' ).'"' : '' ).' ></div>';
		}
		else if( $post_data->post_content == 'fb-send' )
		{
			return '<div class="fb-send" 
			'.( (jr_kvalue( $get_post_meta, 'fb_send_url' ) != '') ? 'data-href="'.jr_kvalue( $get_post_meta, 'fb_send_url' ).'"' : '' ).' 
			'.( (jr_kvalue( $get_post_meta, 'fb_send_width' ) != '') ? 'data-width="'.jr_kvalue( $get_post_meta, 'fb_send_width' ).'"' : '' ).' 
			'.( (jr_kvalue( $get_post_meta, 'fb_send_height' ) != '') ? 'data-height="'.jr_kvalue( $get_post_meta, 'fb_send_height' ).'"' : '' ).'
			'.( (jr_kvalue( $get_post_meta, 'fb_send_color_scheme' ) != '') ? 'data-colorscheme="'.jr_kvalue( $get_post_meta, 'fb_send_color_scheme' ).'"' : '' ).' ></div>';
		}
		else if( $post_data->post_content == 'fb-post' )
		{
			return '<div class="fb-post" 
			'.( (jr_kvalue( $get_post_meta, 'fb_post_url' ) != '') ? 'data-href="'.jr_kvalue( $get_post_meta, 'fb_post_url' ).'"' : '' ).' 
			'.( (jr_kvalue( $get_post_meta, 'fb_post_width' ) != '') ? 'data-width="'.jr_kvalue( $get_post_meta, 'fb_post_width' ).'"' : '' ).' ><div class="fb-xfbml-parse-ignore"></div></div>';
		}
		else if( $post_data->post_content == 'fb-video' )
		{
			return '<div class="fb-post" 
			'.( (jr_kvalue( $get_post_meta, 'fb_video_url' ) != '') ? 'data-href="'.jr_kvalue( $get_post_meta, 'fb_video_url' ).'"' : '' ).' 
			'.( (jr_kvalue( $get_post_meta, 'fb_video_width' ) != '') ? 'data-width="'.jr_kvalue( $get_post_meta, 'fb_video_width' ).'"' : '' ).' ><div class="fb-xfbml-parse-ignore"></div></div>';
		}
		else if( $post_data->post_content == 'fb-comments' )
		{
			return '<div class="fb-comments" 
			'.( (jr_kvalue( $get_post_meta, 'fb_comment_url' ) != '') ? 'data-href="'.jr_kvalue( $get_post_meta, 'fb_comment_url' ).'"' : '' ).' 
			'.( (jr_kvalue( $get_post_meta, 'fb_comment_width' ) != '') ? 'data-width="'.jr_kvalue( $get_post_meta, 'fb_comment_width' ).'"' : '' ).' 
			'.( (jr_kvalue( $get_post_meta, 'fb_comment_noofposts' ) != '') ? 'data-numposts="'.jr_kvalue( $get_post_meta, 'fb_comment_noofposts' ).'"' : '' ).'
			'.( (jr_kvalue( $get_post_meta, 'fb_comment_color_scheme' ) != '') ? 'data-colorscheme="'.jr_kvalue( $get_post_meta, 'fb_comment_color_scheme' ).'"' : '' ).' ></div>';
		}
		else if( $post_data->post_content == 'fb-page' )
		{	
		
			return '<div class="fb-page" 
			'.( (jr_kvalue( $get_post_meta, 'fb_page_url' ) != '') ? 'data-href="'.jr_kvalue( $get_post_meta, 'fb_page_url' ).'"' : '' ).' 
			'.( (jr_kvalue( $get_post_meta, 'fb_page_width' ) != '') ? 'data-width="'.jr_kvalue( $get_post_meta, 'fb_page_width' ).'"' : '' ).'
			'.( (jr_kvalue( $get_post_meta, 'fb_page_height' ) != '') ? 'data-height="'.jr_kvalue( $get_post_meta, 'fb_page_height' ).'"' : '' ).' 
			'.( (jr_kvalue( $get_post_meta, 'fb_page_show_faces' ) != '') ? 'data-show-facepile="true"' : 'data-show-facepile="false"' ).'
			'.( (jr_kvalue( $get_post_meta, 'fb_page_hide_cover_photo' ) != '') ? ' data-hide-cover="true"' : 'data-hide-cover="false"' ).'
			'.( (jr_kvalue( $get_post_meta, 'fb_page_show_posts' ) != '') ? 'data-show-posts="true"' : 'data-show-posts="false"' ).' ><div class="fb-xfbml-parse-ignore"></div></div>';
		}
		else if( $post_data->post_content == 'fb-follow' )
		{
			return '<div class="fb-follow" 
			'.( (jr_kvalue( $get_post_meta, 'fb_follow_url' ) != '') ? 'data-href="'.jr_kvalue( $get_post_meta, 'fb_follow_url' ).'"' : '' ).' 
			'.( (jr_kvalue( $get_post_meta, 'fb_follow_width' ) != '') ? 'data-width="'.jr_kvalue( $get_post_meta, 'fb_follow_width' ).'"' : '' ).' 
			'.( (jr_kvalue( $get_post_meta, 'fb_follow_height' ) != '') ? 'data-height="'.jr_kvalue( $get_post_meta, 'fb_follow_height' ).'"' : '' ).'
			'.( (jr_kvalue( $get_post_meta, 'fb_follow_layout_style' ) != '') ? 'data-layout="'.jr_kvalue( $get_post_meta, 'fb_follow_layout_style' ).'"' : '' ).'
			'.( (jr_kvalue( $get_post_meta, 'fb_follow_show_faces' ) != '') ? 'data-show-faces="true"' : 'data-show-faces="false"' ).'
			'.( (jr_kvalue( $get_post_meta, 'fb_follow_color_scheme' ) != '') ? 'data-colorscheme="'.jr_kvalue( $get_post_meta, 'fb_follow_color_scheme' ).'"' : '' ).' ></div>';
			
		}
	}
	
}
/** Initiate the Class */
$GLOBALS['fb_shortcodes'] = new FB_Shortcodes;
?>