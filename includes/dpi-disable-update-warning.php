<?php
/**
* Plugin Name: DPI Disable Update Warning
* Plugin URI: http://www.diocesan.com/
* Description: Disable the core update warning
* Version: 1.2
* Author: Diocesan (Kyle Eadie)
* Author URI: http://www.diocesan.com/
* License: GPL12
**/

add_action('after_setup_theme','diocesan_remove_core_updates');
function diocesan_remove_core_updates()
{
	if ( is_user_logged_in() ) {
	    $current_user_obj = wp_get_current_user();
        $user_role_arr = $current_user_obj->roles;
        $user_id = $current_user_obj->ID;
        $current_user_role = $user_role_arr[0];
		
		if ($current_user_role != 'administrator') {
		
	        add_action( 'init', 'diocesan_remove_version_check', 2);
	        add_filter( 'pre_option_update_core','__return_null' );
	        add_filter( 'pre_site_transient_update_core','__return_null' );
		}
	}
}

function diocesan_remove_version_check()
{
	remove_action( 'init', 'wp_version_check' );
}