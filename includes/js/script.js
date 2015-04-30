jQuery(document).ready(function($) {
    $('#fb_add_plugin_btn').on('click',function(){
		
		var each_url = false;
		var each_selector;
		if( $('#fb_plugin_type').val() == 'fb-like' )
		{
			each_selector = $('#fb_like_url');
			each_url = validate_url(each_selector.val());
		}
		else if( $('#fb_plugin_type').val() == 'fb-share-button' )
		{
			each_selector = $('#fb_share_button_url');
			each_url = validate_url(each_selector.val());
		}
		else if( $('#fb_plugin_type').val() == 'fb-send' )
		{
			each_selector = $('#fb_send_url');
			each_url = validate_url(each_selector.val());
		}
		else if( $('#fb_plugin_type').val() == 'fb-post' )
		{
			each_selector = $('#fb_post_url');
			each_url = validate_url(each_selector.val());
		}
		else if( $('#fb_plugin_type').val() == 'fb-video' )
		{
			each_selector = $('#fb_video_url');
			each_url = validate_url(each_selector.val());
		}
		else if( $('#fb_plugin_type').val() == 'fb-comments' )
		{
			each_selector = $('#fb_comments_url');
			each_url = validate_url(each_selector.val());
		}
		else if( $('#fb_plugin_type').val() == 'fb-page' )
		{
			each_selector = $('#fb_page_url');
			each_url = validate_url(each_selector.val());
		}
		else if( $('#fb_plugin_type').val() == 'fb-follow' )
		{
			each_selector = $('#fb_follow_url');
			each_url = validate_url(each_selector.val());
		}
		
		if( each_url == true )
		{
			each_selector.css('border','1px solid #ddd').next('span').html('').hide();
			$('#fb_add_plugin_btn').hide();
			$('.loader_img').show();
			$.ajax({
				url: ajax_request_url,
				type: 'POST',
				data: $('#fb_add_plugin_form').serialize()+'&action=fb_add_jr',
				success: function(resp){
					if( $.trim( resp ) == 'error' ){
						alert("There is some error, Please Try Again!");
					}
					else{
						$('#fb_add_plugin_btn').show();
						$('.loader_img').hide();
						$('#jr_post_id').val(resp);
						$('#add_page').submit();
					}
				}
			});
		}
		else
		{
			each_selector.css('border','1px solid #F00').next('span').html('Invalid Url').show();
		}
	});
	
	$('#fb_update_plugin_btn').on('click',function(){
		var each_url = false;
		var each_selector;
		if( $('#fb_plugin_type').val() == 'fb-like' )
		{
			each_selector = $('#fb_like_url');
			each_url = validate_url(each_selector.val());
		}
		else if( $('#fb_plugin_type').val() == 'fb-share-button' )
		{
			each_selector = $('#fb_share_button_url');
			each_url = validate_url(each_selector.val());
		}
		else if( $('#fb_plugin_type').val() == 'fb-send' )
		{
			each_selector = $('#fb_send_url');
			each_url = validate_url(each_selector.val());
		}
		else if( $('#fb_plugin_type').val() == 'fb-post' )
		{
			each_selector = $('#fb_post_url');
			each_url = validate_url(each_selector.val());
		}
		else if( $('#fb_plugin_type').val() == 'fb-video' )
		{
			each_selector = $('#fb_video_url');
			each_url = validate_url(each_selector.val());
		}
		else if( $('#fb_plugin_type').val() == 'fb-comments' )
		{
			each_selector = $('#fb_comments_url');
			each_url = validate_url(each_selector.val());
		}
		else if( $('#fb_plugin_type').val() == 'fb-page' )
		{
			each_selector = $('#fb_page_url');
			each_url = validate_url(each_selector.val());
		}
		else if( $('#fb_plugin_type').val() == 'fb-follow' )
		{
			each_selector = $('#fb_follow_url');
			each_url = validate_url(each_selector.val());
		}
		
		if( each_url == true )
		{
			each_selector.css('border','1px solid #ddd').next('span').html('').hide();
			$('#fb_update_plugin_btn').hide();
			$('.loader_img').show();
			$.ajax({
				url: ajax_request_url,
				type: 'POST',
				data: $('#fb_edit_plugin_form').serialize()+'&action=fb_edit_jr',
				success: function(resp){
					if( $.trim( resp ) == 'error' ){
						alert("There is some error, Please Try Again!");
					}
					else{
						$('#fb_update_plugin_btn').show();
						$('.loader_img').hide();
						$('#edit_page').submit();
					}
				}
			});
		}
		else
		{
			each_selector.css('border','1px solid #F00').next('span').html('Invalid Url').show();
		}
	});
	
	$('#fb_general_settings_btn').on('click',function(){
		if( $.trim($('#fb_api_id').val()) != '' )
		{
			$('#fb_api_id').css('border','1px solid #ddd').next('span').html('').hide();
			$('#fb_general_settings_btn').hide();
			$('.loader_img').show();
			$.ajax({
				url: ajax_request_url,
				type: 'POST',
				data: $('#fb_general_settings_form').serialize()+'&action=fb_general_settings',
				success: function(resp){
					if( $.trim( resp ) == 'error' ){
						alert("There is some error, Please Try Again!");
					}
					else{
						$('#fb_general_settings_btn').show();
						$('.loader_img').hide();
						$('#general_page').submit();
					}
				}
			});
		}
		else
		{
			$('#fb_api_id').css('border','1px solid #F00').next('span').html('<br/>Field Cannot Be Empty!').show();
		}
	});
	
	
	
	
	$('.'+$('#fb_plugin_type').val()).show();
	$('#fb_plugin_type').on('change',function(){
		$('.fb-like, .fb-share-button, .fb-send, .fb-post, .fb-video, .fb-comments, .fb-page, .fb-follow').hide('slow',function(){
			setTimeout(function(){
				$('.'+$('#fb_plugin_type').val()).show('fast');	
			}, 500);
		});
		
	});
	
	
	function validate_url( url ){
				if(/^([a-z]([a-z]|\d|\+|-|\.)*):(\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?((\[(|(v[\da-f]{1,}\.(([a-z]|\d|-|\.|_|~)|[!\$&'\(\)\*\+,;=]|:)+))\])|((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=])*)(:\d*)?)(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*|(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)|((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)|((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)){0})(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(url)) {
		  return true
		} else {
		  return false;
		}
	}
	
});