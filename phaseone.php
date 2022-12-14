<?php

/**
 * Plugin Name: Phaseone - v1
 * Plugin URI:  Plugin URL Link
 * Author:      Plugin Author Name
 * Author URI:  Plugin Author Link
 * Description: This plugin does 
 * Version:     0.1.0
 * License:     GPL-2.0+
 * License URL: http://www.gnu.org/licenses/gpl-2.0.txt
 * text-domain: Phaseone for woocommerce
 */
if (!defined('ABSPATH')) exit; // Exit if accessed directly


if (!class_exists("WC_Phaseone")) {

	/**
	 *Main  WC_Phaseone class
	 */
	class WC_Phaseone
	{
		// Minimum PHP version required by this plugin.
		const MINIMUM_PHP_VERSION = '7.2.0';

		// Minimum WordPress version required by this plugin.
		const MINIMUM_WP_VERSION = '4.4';

		// Minimum WooCommerce version required by this plugin.
		const MINIMUM_WC_VERSION = '5.3';

		// The plugin name, for displaying notices.
		const PLUGIN_NAME = 'Phaseone for WooCommerce';

		//The single instance class
		public static $instance = null;

		//Add Admin notice
		public $notices = array();


		public function __construct()
		{
			register_activation_hook(__FILE__, array($this, 'activation_check'));

			add_action('admin_init', array($this, 'environment_check'));

			add_action('admin_notices', array($this, 'add_plugin_notices')); // admin_init is too early for the get_current_screen() function.
			add_action('admin_notices', array($this, 'admin_notices'), 15);

			// If the environment check fails, initialize the plugin.
			if ($this->is_environment_compatible()) {
				add_action('plugins_loaded', array($this, 'init_plugin'));
			}
		}

		/**
		 * loning instances is forbidden due to singleton pattern.
		 */
		public function __clone()
		{
			_doing_it_wrong(__FUNCTION__, sprintf('You cannot clone instances of %s.', get_class($this)), '0.1.0');
		}

		/**
		 * Unserializing instances is forbidden due to singleton pattern.
		 */
		public function __wakeup()
		{
			_doing_it_wrong(__FUNCTION__, sprintf('You cannot unserialize instances of %s.', get_class($this)), '0.1.0');
		}

		/**
		 * Initializes the plugin.
		 */
		public function init_plugin()
		{

			if (!$this->plugins_compatible()) {
				return;
			}
			// require_once plugin_dir_path( __FILE__ ) . 'phaseone_core.php';
			 require_once plugin_dir_path( __FILE__ ) . 'connection_page.php' ;
			 require_once plugin_dir_path( __FILE__ ) . 'cart_connection.php' ;
			$wc_plugin_path = trailingslashit(WP_PLUGIN_DIR) . 'woocommerce/woocommerce.php';
			require_once($wc_plugin_path);

			// Loads Session data and Cart Data
			wc_load_cart();
			 //add_action('admin_menu', array($this, 'admin_menu_phaseone'));
			
		}

		/**
		 * Checks the server environment and other factors and deactivates plugins as necessary.
		 */
		public function activation_check()
		{
			if (!$this->is_environment_compatible()) {
				$this->deactivate_plugin();
				wp_die(self::PLUGIN_NAME . ' could not be activated. ' . $this->get_environment_message());
			}
		}

		/**
		 * Checks the environment on loading WordPress, just in case the environment changes after activation.
		 */
		public function environment_check()
		{
			if (!$this->is_environment_compatible() && is_plugin_active(plugin_basename(__FILE__))) {
				$this->deactivate_plugin();
				$this->add_admin_notice('bad_environment', 'error', self::PLUGIN_NAME . ' has been deactivated. ' . $this->get_environment_message());
			}
		}

		/**
		 * Adds notices for out-of-date WordPress and/or WooCommerce versions.
		 */
		public function add_plugin_notices()
		{
			if (!$this->is_wp_compatible()) {
				if (current_user_can('update_core')) {
					$this->add_admin_notice(
						'update_wordpress',
						'error',
						sprintf(
							/* translators: %1$s - plugin name, %2$s - minimum WordPress version required, %3$s - update WordPress link open, %4$s - update WordPress link close */
							esc_html__('%1$s requires WordPress version %2$s or higher. Please %3$supdate WordPress &raquo;%4$s', 'Phaseone-for-woocommerce'),
							'<strong>' . self::PLUGIN_NAME . '</strong>',
							self::MINIMUM_WP_VERSION,
							'<a href="' . esc_url(admin_url('update-core.php')) . '">',
							'</a>'
						)
					);
				}
			}

			// Notices to install and activate or update WooCommerce.
			$screen = get_current_screen();
			if (isset($screen->parent_file) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id) {
				return; // Do not display the install/update/activate notice in the update plugin screen.
			}

			$plugin = 'woocommerce/woocommerce.php';
			// Check if WooCommerce is activated.
			if (!$this->is_wc_activated()) {
				if ($this->is_wc_installed()) {
					// WooCommerce is installed but not activated. Ask the user to activate WooCommerce.
					if (current_user_can('activate_plugins')) {
						$activation_url = wp_nonce_url('plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin);
						$message        = sprintf(
							/* translators: %1$s - Plugin Name, %2$s - activate WooCommerce link open, %3$s - activate WooCommerce link close. */
							esc_html__('%1$s requires WooCommerce to be activated. Please %2$sactivate WooCommerce%3$s.', 'phaseone-for-woocommerce'),
							'<strong>' . self::PLUGIN_NAME . '</strong>',
							'<a href="' . esc_url($activation_url) . '">',
							'</a>'
						);
						$this->add_admin_notice(
							'activate_woocommerce',
							'error',
							$message
						);
					}
				} else {
					// WooCommerce is not installed. Ask the user to install WooCommerce.
					if (current_user_can('install_plugins')) {
						$install_url = wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=woocommerce'), 'install-plugin_woocommerce');
						$message     = sprintf(
							/* translators: %1$s - Plugin Name, %2$s - install WooCommerce link open, %3$s - install WooCommerce link close. */
							esc_html__('%1$s requires WooCommerce to be installed and activated. Please %2$sinstall WooCommerce%3$s.', 'phaseone-for-woocommerce'),
							'<strong>' . self::PLUGIN_NAME . '</strong>',
							'<a href="' . esc_url($install_url) . '">',
							'</a>'
						);
						$this->add_admin_notice(
							'install_woocommerce',
							'error',
							$message
						);
					}
				}
			} elseif (!$this->is_wc_compatible()) { // If WooCommerce is activated, check for the version.
				if (current_user_can('update_plugins')) {
					$update_url = wp_nonce_url(self_admin_url('update.php?action=upgrade-plugin&plugin=') . $plugin, 'upgrade-plugin_' . $plugin);
					$this->add_admin_notice(
						'update_woocommerce',
						'error',
						sprintf(
							/* translators: %1$s - Plugin Name, %2$s - minimum WooCommerce version, %3$s - update WooCommerce link open, %4$s - update WooCommerce link close, %5$s - download minimum WooCommerce link open, %6$s - download minimum WooCommerce link close. */
							esc_html__('%1$s requires WooCommerce version %2$s or higher. Please %3$supdate WooCommerce%4$s to the latest version, or %5$sdownload the minimum required version &raquo;%6$s', 'phaseone-for-woocommerce'),
							'<strong>' . self::PLUGIN_NAME . '</strong>',
							self::MINIMUM_WC_VERSION,
							'<a href="' . esc_url($update_url) . '">',
							'</a>',
							'<a href="' . esc_url('https://downloads.wordpress.org/plugin/woocommerce.' . self::MINIMUM_WC_VERSION . '.zip') . '">',
							'</a>'
						)
					);
				}
			}
		}

		/**
		 * Determines if the required plugins are compatible.
		 */
		private function plugins_compatible()
		{
			return $this->is_wp_compatible() && $this->is_wc_compatible();
		}

		/*
		* Determines if the WordPress compatible.
		*/
		private function is_wp_compatible()
		{
			if (!self::MINIMUM_WP_VERSION) {
				return true;
			}
			return version_compare(get_bloginfo('version'), self::MINIMUM_WP_VERSION, '>=');
		}

		/**
		 * Determines if the WooCommerce compatible.
		 */
		private function is_wc_compatible()
		{

			if (!self::MINIMUM_WC_VERSION) {
				return true;
			}

			return defined('WC_VERSION') && version_compare(WC_VERSION, self::MINIMUM_WC_VERSION, '>=');
		}

		/**
		 * check WooCommerce activation.

		 */
		private function is_wc_activated()
		{
			return class_exists('WooCommerce') ? true : false;
		}

		/**
		 * Determins if WooCommerce is installed.
		 */
		private function is_wc_installed()
		{
			$plugin            = 'woocommerce/woocommerce.php';
			$installed_plugins = get_plugins();
			return isset($installed_plugins[$plugin]);
		}

		/**
		 * Deactivates the plugin.
		 */
		protected function deactivate_plugin() 
		{
			deactivate_plugins( plugin_basename( __FILE__ ) );

			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}
		}

		/**
		 * Adds an admin notice to be displayed.
		 */
		private function add_admin_notice( $slug, $class, $message ) {

			$this->notices[ $slug ] = array(
				'class'   => $class,
				'message' => $message,
			);
		}

		/**
		 * Displays any admin notices added with \WC_Facebook_Loader::add_admin_notice()
		 */
		public function admin_notices() {

			foreach ( (array) $this->notices as $notice_key => $notice ) {

				?>
				<div class="<?php echo esc_attr( $notice['class'] ); ?>">
					<p>
					<?php
					echo wp_kses(
						$notice['message'],
						array(
							'a'      => array(
								'href' => array(),
							),
							'strong' => array(),
						)
					);
					?>
					</p>
				</div>
				<?php
			}
		}

		/**
		 * Determines if the server environment is compatible with this plugin.
		 *
		 * Override this method to add checks for more than just the PHP version.
		 */
		private function is_environment_compatible() {
			return version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '>=' );
		}

		/**
		 * Gets the message for display when the environment is incompatible with this plugin.
		 */
		private function get_environment_message() {

			return sprintf( 'The minimum PHP version required for this plugin is %1$s. You are running %2$s.', self::MINIMUM_PHP_VERSION, PHP_VERSION );
		}

		/**
		 * Get the instance of the class
		 */
		public static function instance()
		{
			if (null === self::$instance) {
				self::$instance = new self();
			}
			return self::$instance;
		}
	}
}

WC_Phaseone::instance();
