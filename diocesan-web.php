<?php

/*
Plugin Name: Diocesan Web
Plugin URI: http://www.diocesan.com
Description: Built in support for Diocesan web clients
Version: 1.5
Author: Diocesan
Author URI: http://www.diocesan.com
License: GPLv2
*/

// prevent direct access
if (!defined('ABSPATH')) exit;

// constants
define('DIOCESAN_WEB_ROOT', __FILE__);
define('DIOCESAN_WEB_DIR', __DIR__);
define('DIOCESAN_WEB_VER', '1.5');
define('DIOCESAN_WEB_PLUGIN', plugin_basename(__FILE__));

// autoload plugin classes
require DIOCESAN_WEB_DIR . '/psr4-autoloader.php';

// disable update warning
require DIOCESAN_WEB_DIR . '/includes/dpi-disable-update-warning.php';

/**
 * Disable site admin email verification.
 * 
 * @see https://www.wpexplorer.com/disable-wordpress-administration-email-verification/
 */
add_filter('admin_email_check_interval', '__return_false');

/**
 * Enforce anti-spam honeypot on all Gravity forms.
 *
 * @param array $form
 * @return array $form
 */
add_filter( 'gform_form_post_get_meta', function ( $form ) { 

    $form['enableHoneypot'] = true;
    $form['honeypotAction'] = "spam";  // or abort 

    return $form;
} );

// initialize
add_action('init', function () {
	$plugin = Diocesan\Plugin\Controller::getInstance();
	$plugin->init();
}, 0);

// Restrict Caps on Activation
register_activation_hook(__FILE__, 'diocesan_web_activate_plugin');

function diocesan_web_activate_plugin()
{ // runs on plugin activation
	$plugin = Diocesan\Plugin\Controller::getInstance();
	$plugin->restrictCaps();
};

// Check for updates
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

require DIOCESAN_WEB_DIR . '/plugin-updates/plugin-update-checker.php';

$UpdateChecker = PucFactory::buildUpdateChecker(

	'https://github.com/DiocesanInc/Diocesan-Web',

	DIOCESAN_WEB_DIR . '/diocesan-web.php',

	'diocesan-web'

);

// Set branch
$UpdateChecker->setBranch('main');
