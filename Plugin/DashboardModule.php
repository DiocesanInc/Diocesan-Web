<?php

namespace Diocesan\Plugin;

// prevent direct access
if (!defined('ABSPATH')) exit;

class DashboardModule
{

	private $widgetTitle;
	private $widgetSlug;

	/**
	 *
	 */
	public function __construct(array $config)
	{
		if (!empty($config)) {
			$this->widgetTitle = $config['widgetTitle'];
			$this->widgetSlug = 'dpi_' . strtolower(str_replace(' ', '_', $this->widgetTitle));

			#add_action('wp_dashboard_setup', [$this, 'addDashboardModule']);

			// localize scripts here
			// add_action( 'admin_enqueue_scripts', 'cdw_localize_form' );

			add_action('admin_enqueue_scripts', [$this, 'dpi_styles']);

			add_action('admin_enqueue_scripts', [$this, 'dpi_validate']);

			add_action('admin_init', [$this, 'dashboardCleanup']);

			add_action('admin_footer', [$this, 'dpi_custom_dashboard_widget']);

			#add_action( 'wp_ajax_diocesan_web_send_message', 'diocesan_web_send_message' );

			return $this;
		}

		return trigger_error('A config array must be passed to ' . __METHOD__ . ' in ' . __CLASS__ . '!', E_USER_ERROR);
	}


	/**
	 *
	 */
	public function addDashboardModule()
	{
		wp_add_dashboard_widget($this->widgetSlug, $this->widgetTitle, [$this, 'renderWidget']);
	}

	/**
	 *
	 */
	function cdw_localize_form()
	{
		wp_enqueue_script('jquery');
		wp_enqueue_script('scfjs', plugins_url('js/scf.js', __FILE__), array('jquery'));
		global $cdw_data;
		$localize = array(
			'ajaxurl' => admin_url('admin-ajax.php'),
			'sending' => __('Submitting message...', 'cdw_widget'),
			'invalid' => __('Fill in all fields, some fields are empty', 'cdw_widget')
		);
		wp_localize_script('scfjs', 'SCF', $localize);
	}

	/**
	 *
	 */
	function dpi_styles()
	{
		wp_enqueue_style('diocesan-web', plugins_url('css/diocesan-web.css', DIOCESAN_WEB_ROOT), null, DIOCESAN_WEB_VER, 'all');
	}


	/**
	 *
	 */
	function dpi_validate()
	{
		wp_enqueue_script('jquery-validate', plugins_url('js/jquery.validate.min.js', DIOCESAN_WEB_ROOT), array('jquery'));
	}

	/**
	 *
	 */
	public function renderWidget()
	{
		echo "Hello World, I'm a great Dashboard Widget";
		return include DIOCESAN_WEB_DIR . '/includes/dashboard-form.php';
		#return include DIOCESAN_WEB_DIR . '/includes/create-ticket.php';
	}

	/**
	 *
	 */
	public function displayForm()
	{
		include DIOCESAN_WEB_DIR . '/includes/dashboard-form.php';
	}

	/**
	 *
	 */
	public function dashboardCleanup()
	{
		remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');
		remove_meta_box('dashboard_plugins', 'dashboard', 'normal');
		remove_meta_box('dashboard_primary', 'dashboard', 'side');
		remove_meta_box('dashboard_secondary', 'dashboard', 'normal');
		remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
		remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');
		remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
		remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
		remove_meta_box('dashboard_activity', 'dashboard', 'normal'); //since 3.8
	}


	public function dpi_custom_dashboard_widget()
	{

		// Bail if not viewing the main dashboard page
		if (get_current_screen()->base !== 'dashboard') {
			return;
		}

?>

<?php
		$noticeURL = "http://help.diocesanweb.com/notifications.php";

		// Use get_headers() function
		$headers = @get_headers($noticeURL);

		// Use condition to check the existence of URL
		if ($headers && strpos($headers[0], '200')) {
			echo file_get_contents($noticeURL);
		}

		?>

<div id="diocesan-web-support" class="welcome-panel" style="display: none;">

    <div class="welcome-panel-content">

        <img id="diocesan-logo" src="<?php echo plugins_url('images/diocesan-logo.png', DIOCESAN_WEB_ROOT); ?>"
            alt="Diocesan">
        <p class="about-description">Web Support &nbsp;|&nbsp; We’ve assembled some links to get you started:</p>
        <div class="welcome-panel-column-container">
            <!---<div class="welcome-panel-column">
					<h3>Submit A Ticket</h3>
					<?php #$this->displayForm(); 
					?>
				</div>--->
            <div class="welcome-panel-column">
                <h3>Manage Your Site:</h3>
                <ul>
                    <li><a href="<?php echo get_edit_post_link(get_option('page_on_front')); ?>"
                            class="welcome-icon welcome-edit-page">Edit your Home Page</a></li>
                    <li><a href="https://diocesan.com/knowledge-base/how-do-i-add-a-new-page-to-my-website/"
                            target="_blank" class="welcome-icon welcome-add-page">Add Additional Pages</a></li>
                    <li><a href="https://diocesan.com/knowledge-base/wordpress-basics-terminology/" target="_blank"
                            class="welcome-icon welcome-widgets-menus">Wordpress Basics</a></li>
                    <li><a href="https://diocesan.com/knowledge-base/how-do-i-schedule-a-post/" target="_blank"
                            class="welcome-icon dashicons-welcome-add-page">Adding and Scheduling a Post</a></li>
                    <li><a href="/" class="welcome-icon welcome-view-site">View your site</a></li>
                </ul>
            </div>
            <div class="welcome-panel-column">
                <h3>Popular Help Articles:</h3>
                <ul>
                    <li><a href="https://diocesan.com/knowledge-base/how-do-i-upload-documents-to-my-website/"
                            target="_blank" class="welcome-icon dashicons-upload">Uploading Documents</a></li>
                    <li><a href="https://diocesan.com/knowledge-base/how-do-i-add-pages-to-my-menu/" target="_blank"
                            class="welcome-icon dashicons-menu">Adding Pages to a Menu</a></li>
                    <li><a href="https://diocesan.com/knowledge-base/how-do-i-add-a-youtube-vimeo-video-to-my-page-post/"
                            target="_blank" class="welcome-icon dashicons-video-alt3">Adding a YouTube or Vimeo
                            Video</a></li>
                    <li><a href="https://diocesan.com/knowledge-base/how-do-i-add-an-image-to-my-page-post/"
                            target="_blank" class="welcome-icon dashicons-format-image">Adding an Image to your
                            Pages/Posts</a></li>
                    <li><a href="https://diocesan.com/knowledge-base/how-do-i-use-columns-in-gutenberg/" target="_blank"
                            class="welcome-icon dashicons-columns">Columns Block</a></li>
                </ul>
            </div>
            <div class="welcome-panel-column">
                <h3>Knowledgebase Topics:</h3>
                <ul>
                    <li><a href="https://diocesan.com/article-categories/menu-web/" target="_blank"
                            class="welcome-icon dashicons-menu">Menus</a></li>
                    <li><a href="https://diocesan.com/article-categories/pages-posts-web/" target="_blank"
                            class="welcome-icon dashicons-admin-page">Pages & Posts</a></li>
                    <li><a href="https://diocesan.com/article-categories/image-gallery-envira-web/" target="_blank"
                            class="welcome-icon dashicons-format-gallery">Galleries</a></li>
                    <li><a href="https://diocesan.com/article-categories/forms-web/" target="_blank"
                            class="welcome-icon dashicons-forms">Forms</a></li>
                </ul>
            </div>
            <div class="welcome-panel-column welcome-panel-last">
                <h3>Additional Support:</h3>
                <ul>
                    <li><a href="https://diocesan.com/article-categories/web/" target="_blank"
                            class="welcome-icon welcome-learn-more">Visit the Knowledgebase</a></li>
                    <li><a href="mailto:websupport@diocesan.com?subject=Support%20Request%20for%20<?php echo get_bloginfo('name'); ?>&body=<?php echo home_url(); ?>"
                            target="_blank" class="welcome-icon dashicons-email">Email Us at websupport@diocesan.com</a>
                    </li>
                    <li><a href="tel:1-800-994-9817" class="welcome-icon dashicons-phone">Give us a Call at (800)
                            994-9817</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<script>
jQuery(document).ready(function($) {
    $('#welcome-panel').after($('#diocesan-web-support').show());
});
</script>

<?php }
}