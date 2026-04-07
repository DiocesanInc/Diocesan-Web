<?php namespace Diocesan\Plugin;

// prevent direct access
if ( !defined( 'ABSPATH' ) ) exit;

// singleton!
class Controller {

	// singleton instance
	private static $instance = false;

	// options config, make these global so other objects can refer to them  
	protected $idOptionName = 'dpi_bulletin_id';
	protected $idOrgName = 'diocesan_support_org_id';

	/**
	* Get or create controller instance
	*/
	public static function getInstance() {
		if (!self::$instance) {
			self::$instance = new Controller();
		}

		return self::$instance;
	}

	/**
	* Load up necessary plugin objects based on the current view
	*/
	public function init() {

		if (is_admin()) {
			new DashboardModule([
				'widgetTitle' => 'Diocesan Support'
			]);
		} else {
			// silence
		}
	}
	
	/**
	* Restrict user roles from editing themes and plugins
	*/
	public function restrictCaps( ) {
		
		// Get all the roles
		global $wp_roles;
		$roles = $wp_roles->role_names;

		// Restricted capabilities
		$caps = array(
			'activate_plugins',
			'delete_plugins',
			'edit_plugins',
			'install_plugins',
			'update_plugins' 
		);
		
		// Remove caps from all roles
		foreach ( $roles as $role => $name) {
			$role = get_role( $role );
			foreach ( $caps as $cap ) {
				$role->remove_cap( $cap );
			}
		}
		
		// Add them back for diocesan administrators
		$admins = get_users( array( 'role__in' => array( 'administrator' ) ) );
		
		// Array of WP_User objects.
		foreach ( $admins as $dpiadmin ) {
			if( strpos( $dpiadmin->user_login, 'diocesan' ) !== false ) {
				foreach ( $caps as $cap ) {
					$dpiadmin->add_cap( $cap );
				}
			}
		}				
	}
}