<?php
if(!class_exists('WP_List_Table')){

    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

}



class Jr_List_Table extends WP_List_Table {

    function __construct(){

        global $status, $page;

                

        //Set parent defaults

        parent::__construct( array(

            'singular'  => 'fbpagelist',     //singular name of the listed records

            'plural'    => 'fbpagelists',    //plural name of the listed records

            'ajax'      => false        //does this table support ajax?

        ) );

        

    }


    function column_default($item, $column_name){

        switch($column_name){

			case 'post_title':

				return $item[$column_name];

			case 'post_content':

				return $this->fb_plugin_type_mapping($item[$column_name]);
			
			case 'ID':

				return '[fb_jr_plugins id="'.$item[$column_name].'"]';			

            default:

                return '';

        }

    }

    function column_post_title($item){

        //Build row actions

        $actions = array(

            'edit'      => sprintf('<a href="?page=jr-add-fb-page&jr_action=fb_edit&ID='.$item['ID'].'">Edit</a>',$_REQUEST['page'],'viewdetail',$item['ID']),
            'delete'    => sprintf('<a href="?page=%s&action=%s&ID=%s" onclick="return confirm(\'Are you sure?\')">Delete</a>',$_REQUEST['page'],'delete',$item['ID']),

        );

        

        //Return the title contents

        return sprintf('%1$s %3$s',

            $item['post_title'],

            $item['ID'],

            $this->row_actions($actions)

        );

    }


    function column_cb($item){

        return sprintf(

            '<input type="checkbox" name="%1$s[]" value="%2$s" />',

            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")

            /*$2%s*/ $item['ID']                //The value of the checkbox should be the record's id

        );

    }


    function get_columns(){

        $columns = array(

            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text

            'post_title'     => 'Facebook Plugin Title',
			
			'post_content'     => 'Plugin Type',

			'ID'	=> 'Shortcode',

        );

        return $columns;

    }
	


    function get_sortable_columns() {

        $sortable_columns = array(

            'post_title'     => array('post_title',false),     //true means it's already sorted
			
			'post_content'     => array('post_content',false),     //true means it's already sorted

            'ID'     => array('ID',false),

        );

        return $sortable_columns;

    }



    function get_bulk_actions() {

        $actions = array(

            'delete'    => 'Delete'

        );

        return $actions;

    }


    function process_bulk_action() {

        global $wpdb;

        //Detect when a bulk action is being triggered...

        if( 'delete'===$this->current_action() ) {

			if( isset( $_REQUEST['ID'] ) )

			{

				$dQuery1 = "DELETE FROM ".$wpdb->prefix."posts WHERE ID='".$_REQUEST['ID']."'";
				$dQuery2 = "DELETE FROM ".$wpdb->prefix."postmeta WHERE post_id='".$_REQUEST['ID']."'";
				$wpdb->query($dQuery1);
				$wpdb->query($dQuery2);

			}

			else if( isset( $_REQUEST['fbpagelist'] ) )

			{

				$idListString = implode(",",$_REQUEST['fbpagelist']);

				$dQuery1 = "DELETE FROM ".$wpdb->prefix."posts WHERE ID IN ($idListString)";
				$dQuery2 = "DELETE FROM ".$wpdb->prefix."postmeta WHERE post_id IN ($idListString)";

				$wpdb->query($dQuery1);
				$wpdb->query($dQuery2);

			}
        }
    }


    function prepare_items() {

        global $wpdb; //This is used only if making any database queries


        $per_page = 30;


        $columns = $this->get_columns();

        $hidden = array();

        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);

        $this->process_bulk_action();

        
		$orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'ID'; //If no sort, default to title

		$order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc'; //If no order, default to asc
		
		$query="SELECT * FROM ".$wpdb->prefix."posts WHERE post_type='jr_fb_social_plugin' AND post_status='publish' order by ". $orderby . " " .$order;	

		$data = $wpdb->get_results($query, 'ARRAY_A');	   

        $current_page = $this->get_pagenum();

        $total_items = count($data);

        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);

        $this->items = $data;

        $this->set_pagination_args( array(

            'total_items' => $total_items,                  //WE have to calculate the total number of items

            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page

            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages

        ) );

    }
	
	
	function fb_pages_display(){
		$this->prepare_items();
		echo '<div class="wrap">
				<div id="icon-users" class="icon32"><br/></div>
				<h2>Facebook Social Plugin List
					<div class="f_right w_p_70">
						<div class="f_left m_r_5">
							<a href="admin.php?page=jr-add-fb-page"><input type="button" name="fb_add_new" id="fb_add_new" class="button button-primary button-large" value="Add New" /></a>
						</div>
					</div>	
				</h2>
					<form id="movies-filter" method="get">
							<input type="hidden" name="page" value="'.$_REQUEST['page'].'" />';          
							$this->display();     
		echo 		'</form>
			   </div>';
	}
	
	function fb_edit_plugin(){
		$get_post_meta = get_post_meta($_REQUEST['ID'],'_jr_fb_plugin',true);
		
		echo '<div class="wrap w_60p">
				<div id="icon-users" class="icon32"><br/></div>
				<h2>Update Facebook Social Plugin
					<div class="f_right w_p_33">
						<div class="f_left m_r_5">
							<a href="admin.php?page=jr-add-fb-page"><input type="button" name="fb_add_new" id="fb_add_new" class="button button-primary button-large" value="Add New" /></a>
						</div>
						<div class="f_left">
							<a href="admin.php?page=jr-fb-pages"><input type="button" name="fb_plugin_list" id="fb_plugin_list" class="button button-primary button-large" value="Back To Plugin List" /></a>
						</div>
					</div>	
					
				</h2>
				
					<form id="edit_page" method="get">
							<input type="hidden" name="page" value="'.$_REQUEST['page'].'" />
							<input type="hidden" name="ID" value="'.$_REQUEST['ID'].'" />
							<input type="hidden" name="jr_action" value="fb_edit" />
					</form>';              
		echo 	'<form id="fb_edit_plugin_form" name="fb_edit_plugin_form" method="post" enctype="multipart/form-data">
					<input type="hidden" name="hidden_post_id" id="hidden_post_id" value="'.$_REQUEST['ID'].'" />
					<table class="wp-list-table widefat fixed m_t_20p p_5p table_bg">
						<tbody>
							<tr class="alternate">
								<td>Plugin Name</td>
								<td>
									<input type="text" name="fb_plugin_name" id="fb_plugin_name" placeholder="Enter the plugin name to remember" value="'.jr_kvalue( $get_post_meta, 'fb_plugin_name' ).'" />
								</td>
							</tr>
							<tr class="alternate">
								<td>Plugin Type</td>
								<td>
									<select name="fb_plugin_type" id="fb_plugin_type">
										<option '.( ( jr_kvalue( $get_post_meta, 'fb_plugin_type' ) == 'fb-like' ) ? 'selected="selected"' : '' ).'  value="fb-like">Like Button</option>
										<option '.( ( jr_kvalue( $get_post_meta, 'fb_plugin_type' ) == 'fb-share-button' ) ? 'selected="selected"' : '' ).' value="fb-share-button">Share Button</option>
										<option '.( ( jr_kvalue( $get_post_meta, 'fb_plugin_type' ) == 'fb-send' ) ? 'selected="selected"' : '' ).' value="fb-send">Send Button</option>
										<option '.( ( jr_kvalue( $get_post_meta, 'fb_plugin_type' ) == 'fb-post' ) ? 'selected="selected"' : '' ).' value="fb-post">Embedded Posts</option>
										<option '.( ( jr_kvalue( $get_post_meta, 'fb_plugin_type' ) == 'fb-video' ) ? 'selected="selected"' : '' ).' value="fb-video">Embedded Videos</option>
										<option '.( ( jr_kvalue( $get_post_meta, 'fb_plugin_type' ) == 'fb-comments' ) ? 'selected="selected"' : '' ).' value="fb-comments">Comments</option>
										<option '.( ( jr_kvalue( $get_post_meta, 'fb_plugin_type' ) == 'fb-page' ) ? 'selected="selected"' : '' ).' value="fb-page">Page Plugin</option>
										<option '.( ( jr_kvalue( $get_post_meta, 'fb_plugin_type' ) == 'fb-follow' ) ? 'selected="selected"' : '' ).' value="fb-follow">Follow Button</option>									
									</select>	
								</td>
							</tr>
							
							<!-- fb like start -->
							
							<tr class="alternate fb-like">
								<td>
									URL to Like
								</td>
								<td>
									<input type="text" name="fb_like_url" id="fb_like_url" placeholder="The URL to like" value="'.jr_kvalue( $get_post_meta, 'fb_like_url' ).'" />
									<span class="error"></span>
								</td>
							</tr>
							<tr class="alternate fb-like">
								<td>
									Width
								</td>
								<td>
									<input type="text" name="fb_like_width" id="fb_like_width" placeholder="The pixel width of the plugin" value="'.jr_kvalue( $get_post_meta, 'fb_like_width' ).'" />
								</td>
							</tr>
							<tr class="alternate fb-like">
								<td>
									Layout
								</td>
								<td>
									<select name="fb_like_layout" id="fb_like_layout">
										<option '.( ( jr_kvalue( $get_post_meta, 'fb_like_layout' ) == 'standard' ) ? 'selected="selected"' : '' ).' value="standard">Standard</option>
										<option '.( ( jr_kvalue( $get_post_meta, 'fb_like_layout' ) == 'box_count' ) ? 'selected="selected"' : '' ).' value="box_count">Box Count</option>
										<option '.( ( jr_kvalue( $get_post_meta, 'fb_like_layout' ) == 'button_count' ) ? 'selected="selected"' : '' ).' value="button_count">Button Count</option>
										<option '.( ( jr_kvalue( $get_post_meta, 'fb_like_layout' ) == 'button' ) ? 'selected="selected"' : '' ).' value="button">Button</option>						
									</select>	
								</td>
							</tr>		
							<tr class="alternate fb-like">
								<td>
									Action Type
								</td>
								<td>
									<select name="fb_like_action_type" id="fb_like_action_type">
										<option '.( ( jr_kvalue( $get_post_meta, 'fb_like_action_type' ) == 'like' ) ? 'selected="selected"' : '' ).' value="like">Like</option>
										<option '.( ( jr_kvalue( $get_post_meta, 'fb_like_action_type' ) == 'recommend' ) ? 'selected="selected"' : '' ).' value="recommend">Recomment</option>				
									</select>	
								</td>
							</tr>
							<tr class="alternate fb-like">
								<td>
									Show Friend&#39;s Faces
								</td>
								<td>
									<input '.( ( jr_kvalue( $get_post_meta, 'fb_like_friend_faces' ) == 'yes' ) ? 'checked="checked"' : '' ).' type="checkbox" name="fb_like_friend_faces" id="fb_like_friend_faces" value="yes" />
								</td>
							</tr>
							
							<tr class="alternate fb-like">
								<td>
									Include Share Button
								</td>
								<td>
									<input '.( ( jr_kvalue( $get_post_meta, 'fb_like_include_share' ) == 'yes' ) ? 'checked="checked"' : '' ).' type="checkbox" name="fb_like_include_share" id="fb_like_include_share" value="yes" />
								</td>
							</tr>
							<!-- fb like end -->
							
							<!-- fb share start -->
							
							<tr class="alternate fb-share-button">
								<td>
									URL to share
								</td>
								<td>
									<input type="text" name="fb_share_button_url" id="fb_share_button_url" placeholder="URL used with the Share Button" value="'.jr_kvalue( $get_post_meta, 'fb_share_button_url' ).'" />
									<span class="error"></span>
								</td>
							</tr>
							<tr class="alternate fb-share-button">
								<td>
									Layout
								</td>
								<td>
									<select name="fb_share_button_layout" id="fb_share_button_layout">
										<option '.( ( jr_kvalue( $get_post_meta, 'fb_share_button_layout' ) == 'box_count' ) ? 'selected="selected"' : '' ).' value="box_count">Box Count</option>
										<option '.( ( jr_kvalue( $get_post_meta, 'fb_share_button_layout' ) == 'button_count' ) ? 'selected="selected"' : '' ).' value="button_count">Button Count</option>
										<option '.( ( jr_kvalue( $get_post_meta, 'fb_share_button_layout' ) == 'button' ) ? 'selected="selected"' : '' ).' value="button">Button</option>	
										<option '.( ( jr_kvalue( $get_post_meta, 'fb_share_button_layout' ) == 'icon_link' ) ? 'selected="selected"' : '' ).' value="icon_link">Icon Link</option>
										<option '.( ( jr_kvalue( $get_post_meta, 'fb_share_button_layout' ) == 'icon' ) ? 'selected="selected"' : '' ).' value="icon">Icon</option>
										<option '.( ( jr_kvalue( $get_post_meta, 'fb_share_button_layout' ) == 'link' ) ? 'selected="selected"' : '' ).' value="link">Link</option>						
									</select>	
								</td>
							</tr>	
							
							<!-- fb share end -->
							
							<!-- fb send start -->
							
							<tr class="alternate fb-send">
								<td>
									URL to send
								</td>
								<td>
									<input type="text" name="fb_send_url" id="fb_send_url" placeholder="URL used with the Send Button" value="'.jr_kvalue( $get_post_meta, 'fb_send_url' ).'" />
									<span class="error"></span>
								</td>
							</tr>
							<tr class="alternate fb-send">
								<td>
									Width
								</td>
								<td>
									<input type="text" name="fb_send_width" id="fb_send_width" placeholder="The pixel width of the plugin" value="'.jr_kvalue( $get_post_meta, 'fb_send_width' ).'" />
								</td>
							</tr>							
							<tr class="alternate fb-send">
								<td>
									Height
								</td>
								<td>
									<input type="text" name="fb_send_height" id="fb_send_height" placeholder="The pixel height of the plugin" value="'.jr_kvalue( $get_post_meta, 'fb_send_height' ).'" />
								</td>
							</tr>
							<tr class="alternate fb-send">
								<td>
									Color Scheme
								</td>
								<td>
									<select name="fb_send_color_scheme" id="fb_send_color_scheme">
										<option '.( ( jr_kvalue( $get_post_meta, 'fb_send_color_scheme' ) == 'light' ) ? 'selected="selected"' : '' ).' value="light">Light</option>
										<option '.( ( jr_kvalue( $get_post_meta, 'fb_send_color_scheme' ) == 'dark' ) ? 'selected="selected"' : '' ).' value="dark">Dark</option>					
									</select>	
								</td>
							</tr>
							
							<!-- fb send end -->
							
							<!-- fb embedded post start -->
							
							<tr class="alternate fb-post">
								<td>
									URL of post
								</td>
								<td>
									<input type="text" name="fb_post_url" id="fb_post_url" placeholder="URL of the post that you want to embed" value="'.jr_kvalue( $get_post_meta, 'fb_post_url' ).'" />
									<span class="error"></span>
								</td>
							</tr>
							<tr class="alternate fb-post">
								<td>
									The pixel width of the post (between 350 and 750)
								</td>
								<td>
									<input type="text" name="fb_post_width" id="fb_post_width" placeholder="The pixel width of the post" value="'.jr_kvalue( $get_post_meta, 'fb_post_width' ).'" />	
								</td>
							</tr>	
							
							<!-- fb embedded post end -->
							
							<!-- fb embedded video start -->
							
							<tr class="alternate fb-video">
								<td>
									URL of video
								</td>
								<td>
									<input type="text" name="fb_video_url" id="fb_video_url" placeholder="URL of the video that you want to embed" value="'.jr_kvalue( $get_post_meta, 'fb_video_url' ).'" />
									<span class="error"></span>
								</td>
							</tr>
							<tr class="alternate fb-video">
								<td>
									The pixel width of the video
								</td>
								<td>
									<input type="text" name="fb_video_width" id="fb_video_width" placeholder="The pixel width of the video" value="'.jr_kvalue( $get_post_meta, 'fb_video_width' ).'" />	
								</td>
							</tr>	
							
							<!-- fb embedded video end -->
							
							<!-- fb comments start -->
							
							<tr class="alternate fb-comments">
								<td>
									URL to comment on
								</td>
								<td>
									<input type="text" name="fb_comments_url" id="fb_comments_url" placeholder="The URL that this comments box is associated with" value="'.jr_kvalue( $get_post_meta, 'fb_comments_url' ).'" />
									<span class="error"></span>
								</td>
							</tr>
							<tr class="alternate fb-comments">
								<td>
									Width
								</td>
								<td>
									<input type="text" name="fb_comments_width" id="fb_comments_width" placeholder="The pixel width of the plugin" value="'.jr_kvalue( $get_post_meta, 'fb_comments_width' ).'" />
								</td>
							</tr>							
							<tr class="alternate fb-comments">
								<td>
									Number of Posts
								</td>
								<td>
									<input type="text" name="fb_comments_noofposts" id="fb_comments_noofposts" placeholder="The number of posts to display by default" value="'.jr_kvalue( $get_post_meta, 'fb_comments_noofposts' ).'" />
								</td>
							</tr>
							<tr class="alternate fb-comments">
								<td>
									Color Scheme
								</td>
								<td>
									<select name="fb_comments_color_scheme" id="fb_comments_color_scheme">
										<option '.( ( jr_kvalue( $get_post_meta, 'fb_comments_color_scheme' ) == 'light' ) ? 'selected="selected"' : '' ).' value="light">Light</option>
										<option '.( ( jr_kvalue( $get_post_meta, 'fb_comments_color_scheme' ) == 'dark' ) ? 'selected="selected"' : '' ).' value="dark">Dark</option>					
									</select>	
								</td>
							</tr>
							
							<!-- fb comments end -->
							
							<!-- fb page start -->
							
							<tr class="alternate fb-page">
								<td>
									Facebook Page URL
								</td>
								<td>
									<input type="text" name="fb_page_url" id="fb_page_url" placeholder="The URL of the Facebook Page" value="'.jr_kvalue( $get_post_meta, 'fb_page_url' ).'" />
									<span class="error"></span>
								</td>
							</tr>
							<tr class="alternate fb-page">
								<td>
									Width
								</td>
								<td>
									<input type="text" name="fb_page_width" id="fb_page_width" placeholder="The pixel width of the embed (Min. 280 to Max. 500)" value="'.jr_kvalue( $get_post_meta, 'fb_page_width' ).'" />
								</td>
							</tr>
							<tr class="alternate fb-page">
								<td>
									Height
								</td>
								<td>
									<input type="text" name="fb_page_height" id="fb_page_height" placeholder="The pixel height of the embed (Min. 130)" value="'.jr_kvalue( $get_post_meta, 'fb_page_height' ).'" />
								</td>
							</tr>
							<tr class="alternate fb-page">
								<td>
									Show Friend&#39;s Faces
								</td>
								<td>
									<input '.( ( jr_kvalue( $get_post_meta, 'fb_page_show_faces' ) == 'yes' ) ? 'checked="checked"' : '' ).' type="checkbox" name="fb_page_show_faces" id="fb_page_show_faces" value="yes" />
								</td>
							</tr>
							<tr class="alternate fb-page">
								<td>
									Hide Cover Photo
								</td>
								<td>
									<input '.( ( jr_kvalue( $get_post_meta, 'fb_page_hide_cover_photo' ) == 'yes' ) ? 'checked="checked"' : '' ).' type="checkbox" name="fb_page_hide_cover_photo" id="fb_page_hide_cover_photo" value="yes" />
								</td>
							</tr>	
							<tr class="alternate fb-page">
								<td>
									Show Page Posts
								</td>
								<td>
									<input '.( ( jr_kvalue( $get_post_meta, 'fb_page_show_posts' ) == 'yes' ) ? 'checked="checked"' : '' ).' type="checkbox" name="fb_page_show_posts" id="fb_page_show_posts" value="yes" />
								</td>
							</tr>	
							
							<!-- fb page end -->
							
							<!-- fb follow start -->
							
							<tr class="alternate fb-follow">
								<td>
									Profile URL
								</td>
								<td>
									<input type="text" name="fb_follow_url" id="fb_follow_url" placeholder="URL of the profile of the person to follow" value="'.jr_kvalue( $get_post_meta, 'fb_follow_url' ).'" />
									<span class="error"></span>
									
								</td>
							</tr>
							<tr class="alternate fb-follow">
								<td>
									Width
								</td>
								<td>
									<input type="text" name="fb_follow_width" id="fb_follow_width" placeholder="The pixel width of the plugin" value="'.jr_kvalue( $get_post_meta, 'fb_follow_width' ).'" />
								</td>
							</tr>							
							<tr class="alternate fb-follow">
								<td>
									Height
								</td>
								<td>
									<input type="text" name="fb_follow_height" id="fb_follow_height" placeholder="The pixel height of the plugin" value="'.jr_kvalue( $get_post_meta, 'fb_follow_height' ).'" />
								</td>
							</tr>
							<tr class="alternate fb-follow">
								<td>
									Color Scheme
								</td>
								<td>
									<select name="fb_follow_color_scheme" id="fb_follow_color_scheme">
										<option '.( ( jr_kvalue( $get_post_meta, 'fb_follow_color_scheme' ) == 'light' ) ? 'selected="selected"' : '' ).' value="light">Light</option>
										<option '.( ( jr_kvalue( $get_post_meta, 'fb_follow_color_scheme' ) == 'dark' ) ? 'selected="selected"' : '' ).' value="dark">Dark</option>					
									</select>	
								</td>
							</tr>
							<tr class="alternate fb-follow">
								<td>
									Layout Style
								</td>
								<td>
									<select name="fb_follow_layout_style" id="fb_follow_layout_style">
										<option '.( ( jr_kvalue( $get_post_meta, 'fb_follow_layout_style' ) == 'standard' ) ? 'selected="selected"' : '' ).' value="standard">Standard</option>
										<option '.( ( jr_kvalue( $get_post_meta, 'fb_follow_layout_style' ) == 'box_count' ) ? 'selected="selected"' : '' ).' value="box_count">Box Count</option>
										<option '.( ( jr_kvalue( $get_post_meta, 'fb_follow_layout_style' ) == 'button_count' ) ? 'selected="selected"' : '' ).' value="button_count">Button Count</option>
										<option '.( ( jr_kvalue( $get_post_meta, 'fb_follow_layout_style' ) == 'button' ) ? 'selected="selected"' : '' ).' value="button">Button</option>						
									</select>	
								</td>
							</tr>
							<tr class="alternate fb-follow">
								<td>
									Show Faces
								</td>
								<td>
									<input '.( ( jr_kvalue( $get_post_meta, 'fb_follow_show_faces' ) == 'yes' ) ? 'checked="checked"' : '' ).' type="checkbox" name="fb_follow_show_faces" id="fb_follow_show_faces" value="yes" />
								</td>
							</tr>
							
							<!-- fb follow end -->
							
							
							<tr class="alternate">
								<td>
									<input type="button" name="fb_update_plugin_btn" id="fb_update_plugin_btn" class="button button-primary button-large" value="Update" />
									<img class="loader_img" width="30" height="30" src="'.plugins_url( 'img/loading.gif', __FILE__ ).'" />
								</td>
								<td>
									&nbsp;
								</td>
							</tr>
							
						</tbody>
					</table>		
							
				</form>
				
				';
		
		
		echo	'</div>';
		
	}
	
	function fb_add_plugin(){
		
		echo '<div class="wrap w_60p">
				<div id="icon-users" class="icon32"><br/></div>
				<h2>Add New Facebook Social Plugin
					<div class="f_right w_p_33">
						
						<div class="f_right">
							<a href="admin.php?page=jr-fb-pages"><input type="button" name="fb_plugin_list" id="fb_plugin_list" class="button button-primary button-large" value="Back To Plugin List" /></a>
						</div>
					</div>	
				</h2>
					<form id="add_page" method="get">
							<input type="hidden" name="page" value="'.$_REQUEST['page'].'" />
							<input type="hidden" id="jr_post_id" name="ID" value="" />
							<input type="hidden" name="jr_action" value="fb_edit" />					
					</form>';              
		echo 	'<form id="fb_add_plugin_form" name="fb_add_plugin_form" method="post" enctype="multipart/form-data">
					<table class="wp-list-table widefat fixed m_t_20p p_5p table_bg">
						<tbody>
							<tr class="alternate">
								<td>Plugin Name</td>
								<td>
									<input type="text" name="fb_plugin_name" id="fb_plugin_name" placeholder="Enter the plugin name to remember" />
								</td>
							</tr>
							<tr class="alternate">
								<td>Plugin Type</td>
								<td>
									<select name="fb_plugin_type" id="fb_plugin_type">
										<option value="fb-like">Like Button</option>
										<option value="fb-share-button">Share Button</option>
										<option value="fb-send">Send Button</option>
										<option value="fb-post">Embedded Posts</option>
										<option value="fb-video">Embedded Videos</option>
										<option value="fb-comments">Comments</option>
										<option value="fb-page">Page Plugin</option>
										<option value="fb-follow">Follow Button</option>									
									</select>	
								</td>
							</tr>
							
							<!-- fb like start -->
							
							<tr class="alternate fb-like">
								<td>
									URL to Like
								</td>
								<td>
									<input type="text" name="fb_like_url" id="fb_like_url" placeholder="The URL to like" />
									<span class="error"></span>
								</td>
							</tr>
							<tr class="alternate fb-like">
								<td>
									Width
								</td>
								<td>
									<input type="text" name="fb_like_width" id="fb_like_width" placeholder="The pixel width of the plugin" />
								</td>
							</tr>
							<tr class="alternate fb-like">
								<td>
									Layout
								</td>
								<td>
									<select name="fb_like_layout" id="fb_like_layout">
										<option value="standard">Standard</option>
										<option value="box_count">Box Count</option>
										<option value="button_count">Button Count</option>
										<option value="button">Button</option>						
									</select>	
								</td>
							</tr>		
							<tr class="alternate fb-like">
								<td>
									Action Type
								</td>
								<td>
									<select name="fb_like_action_type" id="fb_like_action_type">
										<option value="like">Like</option>
										<option value="recommend">Recomment</option>				
									</select>	
								</td>
							</tr>
							<tr class="alternate fb-like">
								<td>
									Show Friend&#39;s Faces
								</td>
								<td>
									<input type="checkbox" name="fb_like_friend_faces" id="fb_like_friend_faces" value="yes" />
								</td>
							</tr>
							
							<tr class="alternate fb-like">
								<td>
									Include Share Button
								</td>
								<td>
									<input type="checkbox" name="fb_like_include_share" id="fb_like_include_share" value="yes" />
								</td>
							</tr>
							<!-- fb like end -->
							
							<!-- fb share start -->
							
							<tr class="alternate fb-share-button">
								<td>
									URL to share
								</td>
								<td>
									<input type="text" name="fb_share_button_url" id="fb_share_button_url" placeholder="URL used with the Share Button" />
									<span class="error"></span>
								</td>
							</tr>
							<tr class="alternate fb-share-button">
								<td>
									Layout
								</td>
								<td>
									<select name="fb_share_button_layout" id="fb_share_button_layout">
										<option value="box_count">Box Count</option>
										<option value="button_count">Button Count</option>
										<option value="button">Button</option>	
										<option value="icon_link">Icon Link</option>
										<option value="icon">Icon</option>
										<option value="link">Link</option>						
									</select>	
								</td>
							</tr>	
							
							<!-- fb share end -->
							
							<!-- fb send start -->
							
							<tr class="alternate fb-send">
								<td>
									URL to send
								</td>
								<td>
									<input type="text" name="fb_send_url" id="fb_send_url" placeholder="URL used with the Send Button" />
									<span class="error"></span>
								</td>
							</tr>
							<tr class="alternate fb-send">
								<td>
									Width
								</td>
								<td>
									<input type="text" name="fb_send_width" id="fb_send_width" placeholder="The pixel width of the plugin" />
								</td>
							</tr>							
							<tr class="alternate fb-send">
								<td>
									Height
								</td>
								<td>
									<input type="text" name="fb_send_height" id="fb_send_height" placeholder="The pixel height of the plugin" />
								</td>
							</tr>
							<tr class="alternate fb-send">
								<td>
									Color Scheme
								</td>
								<td>
									<select name="fb_send_color_scheme" id="fb_send_color_scheme">
										<option value="light">Light</option>
										<option value="dark">Dark</option>					
									</select>	
								</td>
							</tr>
							
							<!-- fb send end -->
							
							<!-- fb embedded post start -->
							
							<tr class="alternate fb-post">
								<td>
									URL of post
								</td>
								<td>
									<input type="text" name="fb_post_url" id="fb_post_url" placeholder="URL of the post that you want to embed" />
									<span class="error"></span>
								</td>
							</tr>
							<tr class="alternate fb-post">
								<td>
									The pixel width of the post (between 350 and 750)
								</td>
								<td>
									<input type="text" name="fb_post_width" id="fb_post_width" placeholder="The pixel width of the post" />	
								</td>
							</tr>	
							
							<!-- fb embedded post end -->
							
							<!-- fb embedded video start -->
							
							<tr class="alternate fb-video">
								<td>
									URL of video
								</td>
								<td>
									<input type="text" name="fb_video_url" id="fb_video_url" placeholder="URL of the video that you want to embed" />
									<span class="error"></span>
								</td>
							</tr>
							<tr class="alternate fb-video">
								<td>
									The pixel width of the video
								</td>
								<td>
									<input type="text" name="fb_video_width" id="fb_video_width" placeholder="The pixel width of the video" />	
								</td>
							</tr>	
							
							<!-- fb embedded video end -->
							
							<!-- fb comments start -->
							
							<tr class="alternate fb-comments">
								<td>
									URL to comment on
								</td>
								<td>
									<input type="text" name="fb_comments_url" id="fb_comments_url" placeholder="The URL that this comments box is associated with" />
									<span class="error"></span>
								</td>
							</tr>
							<tr class="alternate fb-comments">
								<td>
									Width
								</td>
								<td>
									<input type="text" name="fb_comments_width" id="fb_comments_width" placeholder="The pixel width of the plugin" />
								</td>
							</tr>							
							<tr class="alternate fb-comments">
								<td>
									Number of Posts
								</td>
								<td>
									<input type="text" name="fb_comments_noofposts" id="fb_comments_noofposts" placeholder="The number of posts to display by default" />
								</td>
							</tr>
							<tr class="alternate fb-comments">
								<td>
									Color Scheme
								</td>
								<td>
									<select name="fb_comments_color_scheme" id="fb_comments_color_scheme">
										<option value="light">Light</option>
										<option value="dark">Dark</option>					
									</select>	
								</td>
							</tr>
							
							<!-- fb comments end -->
							
							<!-- fb page start -->
							
							<tr class="alternate fb-page">
								<td>
									Facebook Page URL
								</td>
								<td>
									<input type="text" name="fb_page_url" id="fb_page_url" placeholder="The URL of the Facebook Page" />
									<span class="error"></span>
								</td>
							</tr>
							<tr class="alternate fb-page">
								<td>
									Width
								</td>
								<td>
									<input type="text" name="fb_page_width" id="fb_page_width" placeholder="The pixel width of the embed (Min. 280 to Max. 500)" />
								</td>
							</tr>
							<tr class="alternate fb-page">
								<td>
									Height
								</td>
								<td>
									<input type="text" name="fb_page_height" id="fb_page_height" placeholder="The pixel height of the embed (Min. 130)" />
								</td>
							</tr>
							<tr class="alternate fb-page">
								<td>
									Show Friend&#39;s Faces
								</td>
								<td>
									<input type="checkbox" name="fb_page_show_faces" id="fb_page_show_faces" value="yes" />
								</td>
							</tr>
							<tr class="alternate fb-page">
								<td>
									Hide Cover Photo
								</td>
								<td>
									<input type="checkbox" name="fb_page_hide_cover_photo" id="fb_page_hide_cover_photo" value="yes" />
								</td>
							</tr>	
							<tr class="alternate fb-page">
								<td>
									Show Page Posts
								</td>
								<td>
									<input type="checkbox" name="fb_page_show_posts" id="fb_page_show_posts" value="yes" />
								</td>
							</tr>	
							
							<!-- fb page end -->
							
							<!-- fb follow start -->
							
							<tr class="alternate fb-follow">
								<td>
									Profile URL
								</td>
								<td>
									<input type="text" name="fb_follow_url" id="fb_follow_url" placeholder="URL of the profile of the person to follow" />
									<span class="error"></span>
								</td>
							</tr>
							<tr class="alternate fb-follow">
								<td>
									Width
								</td>
								<td>
									<input type="text" name="fb_follow_width" id="fb_follow_width" placeholder="The pixel width of the plugin" />
								</td>
							</tr>							
							<tr class="alternate fb-follow">
								<td>
									Height
								</td>
								<td>
									<input type="text" name="fb_follow_height" id="fb_follow_height" placeholder="The pixel height of the plugin" />
								</td>
							</tr>
							<tr class="alternate fb-follow">
								<td>
									Color Scheme
								</td>
								<td>
									<select name="fb_follow_color_scheme" id="fb_follow_color_scheme">
										<option value="light">Light</option>
										<option value="dark">Dark</option>					
									</select>	
								</td>
							</tr>
							<tr class="alternate fb-follow">
								<td>
									Layout Style
								</td>
								<td>
									<select name="fb_follow_layout_style" id="fb_follow_layout_style">
										<option value="standard">Standard</option>
										<option value="box_count">Box Count</option>
										<option value="button_count">Button Count</option>
										<option value="button">Button</option>						
									</select>	
								</td>
							</tr>
							<tr class="alternate fb-follow">
								<td>
									Show Faces
								</td>
								<td>
									<input type="checkbox" name="fb_follow_show_faces" id="fb_follow_show_faces" value="yes" />
								</td>
							</tr>
							
							<!-- fb follow end -->
							
							
							<tr class="alternate">
								<td>
									<input type="button" name="fb_add_plugin_btn" id="fb_add_plugin_btn" class="button button-primary button-large" value="Add" />
									<img class="loader_img" width="30" height="30" src="'.plugins_url( 'img/loading.gif', __FILE__ ).'" />
								</td>
								<td>
									&nbsp;
								</td>
							</tr>
							
						</tbody>
					</table>		
							
				</form>';
		
		
		echo	'</div>';
		
	}
	
	function fb_general_settings(){
		
		$app_id = get_option( 'jr_fb_app_id' );
		
		echo '<div class="wrap w_60p">
				<div id="icon-users" class="icon32"><br/></div>
				<h2>FB Social Plugins General Settings</h2>
					<form id="general_page" method="get">
							<input type="hidden" name="page" value="'.$_REQUEST['page'].'" />					
					</form>';              
		echo 	'<form id="fb_general_settings_form" name="fb_general_settings_form" method="post" enctype="multipart/form-data">
					<table class="wp-list-table widefat fixed m_t_20p p_5p table_bg">
						<tbody>
							<tr class="alternate">
								<td>Facebook App Id</td>
								<td>
									<input type="text" name="fb_api_id" id="fb_api_id" placeholder="Enter the facebook app id" value="'.$app_id.'" />
									<span class="error"></span>
								</td>
							</tr>
							<tr class="alternate">
								<td>
									<input type="button" name="fb_general_settings_btn" id="fb_general_settings_btn" class="button button-primary button-large" value="Save" />
									<img class="loader_img" width="30" height="30" src="'.plugins_url( 'img/loading.gif', __FILE__ ).'" />
								</td>
								<td>
									&nbsp;
								</td>
							</tr>
							<tr class="alternate">
								<td>Don&#39;t have facebook app id? <a target="_blank" href="https://developers.facebook.com/quickstarts/?platform=web">Click Here</a></td>
								<td>&nbsp;</td>
							</tr>
						 <tbody>
					 </table>	 
				  </form>';		  
		echo '</div>';
		
		echo '<br/><br/><div class="wrap w_60p">
				<div id="icon-users" class="icon32"><br/></div>
				<h2>About Us</h2>';            
		echo 	'<table class="wp-list-table widefat fixed m_t_20p p_5p table_bg">
						<tbody>
							<tr class="alternate">
								<td><p>We are Wordpress Theme & Plugin Developer, committed with work can meat the deadline of projects. If you want your work would be done in prefessional way then you can also hire us. <br/>
								<strong>Junaid Rajpoot ( Backend Developer )<br/> Rehman Ali (Frontend Developer)<br/></strong>
								You can also download the more free wordpress themes, templates and plugins via these links<br/>
								<a href="http://wpthemesexperts.com">WPthemesexperts.com</a><br/>
								<a href="http://themesrefinery.net/">Themesrefinery.net</a><br/>
								<strong>Contact Info:</strong><br/>
								<a href="mailto:junaidfx@gmail.com">junaidfx@gmail.com</a>, <a href="mailto:rehmanali09.57@gmail.com">rehmanali09.57@gmail.com</a>
								</p>
								</td>
							</tr>
						 <tbody>
				  </table>';		  
		echo '</div>';			  
	}
	
	function fb_plugin_type_mapping($plugin_type){
		switch( $plugin_type ):
			case 'fb-like':
				return 'Like Button';
			case 'fb-share-button':
				return 'Share Button';
			case 'fb-send':	 
				return 'Send Button';
			case 'fb-post':
				return 'Embedded Posts';
			case 'fb-video':
				return 'Embedded Videos';
			case 'fb-comments':
				return 'Comments';
			case 'fb-page':	
				return 'Page Plugin';
			case 'fb-follow':
				return 'Follow Button';	
			default:
				return '';		
		endswitch;
	}
	
}
?>