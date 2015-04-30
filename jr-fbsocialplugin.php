<?php 
/* 
Plugin Name: Facebook Social Pack
Plugin URI: http://wpthemesexperts.com/facebook-social-wordpress-plugin/
Description: This wordpress plugin has multiple facebook social plugins that can be used, easily customizeable, shortcode of each plugin. Simple but flexible.
Version: 1.0.0 
Author: Junaid Rajpoot 
Author URI: http://wpthemesexperts.com
*/ 

/*  Copyright 2015 Junaid Rajpoot (email: junaidfx@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; If not, see <http://www.gnu.org/licenses/>
*/
define('JR_PLUGIN_PATH', plugin_dir_path(__FILE__));
define( 'JR_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'JR_PLUGIN_NAME', trim( dirname( JR_PLUGIN_BASENAME ), '/' ) );

require_once JR_PLUGIN_PATH . '/intial_settings.php';

?>