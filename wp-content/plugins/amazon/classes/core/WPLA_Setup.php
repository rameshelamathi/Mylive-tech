<?php

class WPLA_Setup extends WPLA_Core {
	
	// check if setup is incomplete and display next step
	public function checkSetup( $page = false ) {
		global $pagenow;

		// check if cURL is loaded
		if ( ! self::isCurlLoaded() ) return false;

		// check for windows server
		// if ( self::isWindowsServer() ) return false;
		self::isWindowsServer( $page );

		// create folders if neccessary
		// if ( self::checkFolders() ) return false;

		// check for updates
		self::checkForUpdates();

		// check if cron is working properly
		self::checkCron();

		// check if PHP, WooCommerce and WP are up to date
		self::checkVersions();

		// check for multisite installation
		// if ( self::checkMultisite() ) return false;

		// setup wizard
		// if ( self::getOption('amazon_token') == '' ) {
		if ( ( '1' == self::getOption('setup_next_step') ) && ( $page != 'settings') ) {
		
			$msg1 = __('You have not linked WP-Lister to your Amazon account yet.','wpla');
			$msg2 = __('To complete the setup procedure go to %s and follow the instructions.','wpla');
			$link = '<a href="admin.php?page=wpla-settings">'.__('Settings','wpla').'</a>';
			$msg2 = sprintf($msg2, $link);
			$msg = "<p><b>$msg1</b></p><p>$msg2</p>";
			$this->showMessage($msg);
		
			// update_option('wpla_setup_next_step', '0');
		
		}

		
		// db upgrade
		WPLA_UpgradeHelper::upgradeDB();

		// clean db
		// self::cleanDB();
	
	} // checkSetup()


	// clean database of old log records
	// TODO: hook this into daily cron schedule (DONE!)
	public function cleanDB() {
		global $wpdb;

		if ( isset( $_GET['page'] ) && ( $_GET['page'] == 'wpla-settings' ) && ( self::getOption('log_to_db') == '1' ) ) {
			$days_to_keep = self::getOption( 'log_days_limit', 30 );		
			// $delete_count = $wpdb->get_var('SELECT count(id) FROM '.$wpdb->prefix.'amazon_log WHERE timestamp < DATE_SUB(NOW(), INTERVAL 1 MONTH )');
			$delete_count = $wpdb->get_var('SELECT count(id) FROM '.$wpdb->prefix.'amazon_log WHERE timestamp < DATE_SUB(NOW(), INTERVAL '.$days_to_keep.' DAY )');
			if ( $delete_count ) {
				$wpdb->query('DELETE FROM '.$wpdb->prefix.'amazon_log WHERE timestamp < DATE_SUB(NOW(), INTERVAL '.$days_to_keep.' DAY )');
				// $this->showMessage( __('Log entries cleaned: ','wpla') . $delete_count );
			}
		}
	}



	// check if cURL is loaded
	public function isCurlLoaded() {

		if( ! extension_loaded('curl') ) {
			$this->showMessage("
				<b>Required PHP extension missing</b><br>
				<br>
				Your server doesn't seem to have the <a href='http://www.php.net/curl' target='_blank'>cURL</a> php extension installed.<br>
				cURL ist required by WP-Lister to be able to talk with Amazon.<br>
				<br>
				On a recent debian based linux server running PHP 5 this should do the trick:<br>
				<br>
				<code>
					apt-get install php5-curl <br>
					/etc/init.d/apache2 restart
				</code>
				<br>
				<br>
				You'll require root access on your server to install additional php extensions!<br>
				If you are on a shared host, you need to ask your hoster to enable the cURL php extension for you.<br>
				<br>
				For more information on how to install the cURL php extension on other servers check <a href='http://stackoverflow.com/questions/1347146/how-to-enable-curl-in-php' target='_blank'>this page on stackoverflow</a>.
			",1);
			return false;
		}

		return true;
	}

	// check server is running windows
	public function isWindowsServer( $page ) {

		if ( $page != 'settings' ) return;

		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {

			$this->showMessage("
				<b>Warning: Server requirements not met - this server runs on windows.</b><br>
				<br>
				WP-Lister currently only supports unixoid operating systems like Linux, FreeBSD and OS X.<br>
				Support for windows servers is still experimental and should not be used on production sites!
			");
			return true;
		}

		return false;
	}

	// check if WP_Cron is working properly
	public function checkCron() {

		$cron_interval  = get_option( 'wpla_cron_schedule' );
		$next_scheduled = wp_next_scheduled( 'wpla_update_schedule' ) ;

		if ( $cron_interval && ! $next_scheduled ) {

			$this->showMessage( 
				'<p>'
				. '<b>Warning: WordPress Cron Job has been disabled - scheduled WP-Lister tasks are not executed!</b>'
				. '<br><br>'
				. 'The task schedule has been reset just now in order to automatically fix this.'
				. '<br><br>'
				. 'If this message does not disappear, please visit the <a href="admin.php?page=wpla-settings&tab=settings">Settings</a> page and click <i>Save Settings</i> or contact support.'
				. '</p>'
			,1);

			// this should fix it:
			wp_schedule_event( time(), $cron_interval, 'wpla_update_schedule' );

		}

		// schedule daily event if not set yet
		if ( ! wp_next_scheduled( 'wpla_daily_schedule' ) ) {
			wp_schedule_event( time(), 'daily', 'wpla_daily_schedule' );
		}

	}

	// check versions
	public function checkVersions() {

		// WP-Lister for eBay 1.6+
		if ( defined('WPLISTER_VERSION') && version_compare( WPLISTER_VERSION, '1.6', '<') ) {
			$this->showMessage( 
				'<p>'
				. '<b>Warning: Your version of WP-Lister for eBay '.WPLISTER_VERSION.' is not fully compatible with WP-Lister for Amazon.</b>'
				. '<br><br>'
				. 'To prevent any issues, please update to WP-Lister for eBay 1.6 or better.'
				. '</p>'
			,2,1);
		}

		// check if WooCommerce is up to date
		$required_version    = '2.2.4';
		$woocommerce_version = defined('WC_VERSION') ? WC_VERSION : WOOCOMMERCE_VERSION;
		if ( version_compare( $woocommerce_version, $required_version ) < 0 ) {
			$this->showMessage("
				<b>Warning: Your WooCommerce version is outdated.</b><br>
				<br>
				WP-Lister requires WooCommerce $required_version to be installed. You are using WooCommerce $woocommerce_version.<br>
				You should always keep your site and plugins updated.<br>
			",2,1);
		}

		// PHP 5.3+
		if ( version_compare(phpversion(), '5.3', '<')) {
			$this->showMessage( 
				'<p>'
				. '<b>Warning: Your PHP version '.phpversion().' is outdated.</b>'
				. '<br><br>'
				. 'Your server should have PHP 5.3 or better installed.'
				. ' '
				. 'Please contact your hosting support and ask them to update your PHP version.'
				. '</p>'
			,2,1);
		}

	}


	// checks for multisite network
	public function checkMultisite() {

		if ( is_multisite() ) {

			// check for network activation
			if ( ! function_exists( 'is_plugin_active_for_network' ) )
				require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

			if ( function_exists('is_network_admin') && is_plugin_active_for_network( plugin_basename( WPLA_PATH.'/wp-lister-amazon.php' ) ) )
				$this->showMessage("network activated!",1);
			else
				$this->showMessage("not network activated!");


			// $this->showMessage("
			// 	<b>Multisite installation detected</b><br>
			// 	<br>
			// 	This is a site network...<br>
			// ");
			return true;
		}

		return false;
	}


	// check for updates
	public function checkForUpdates() {
		## BEGIN PRO ##
		// global $WPLA_CustomUpdater;
		// if ( ! class_exists('WPLA_CustomUpdater') ) return;

		// check if current user has permission to update plugins
		if ( ! current_user_can( 'update_plugins' ) ) return;

		$update = get_option( 'wpla_update_details' );

		if ( $update && is_object( $update ) ) {

			// check timestamp
			if ( ( time() - $update->timestamp ) > 24*3600 ) {
			
				// $update = $WPLA_CustomUpdater->check_for_new_version();
				$update = WPLAUP()->check_for_new_version( true );

			}

		} else {
			// $update = $WPLA_CustomUpdater->check_for_new_version();
			$update = WPLAUP()->check_for_new_version( true );
		}

		if ( $update && is_object( $update ) ) {

			if ( version_compare( $update->new_version, WPLA_VERSION ) > 0 ) {

				// $install_update_button = '<a href="update-core.php" class="button">'.__('Install Update','wpla') . '</a>';

				// generate update URL with nonce
				$slug    = 'wp-lister-amazon/wp-lister-amazon.php';
				$action  = 'upgrade-plugin';
				$btn_url = wp_nonce_url(
				    add_query_arg(
				        array(
				            'action' => $action,
				            'plugin' => $slug
				        ),
				        admin_url( 'update.php' )
				    ),
				    $action.'_'.$slug
				);
				$install_update_button = '<a href="'.$btn_url.'" class="button button-primary">'.__('Install Update','wpla') . '</a>';

				$this->showMessage( 
					'<p>'. sprintf( __('An update to %s is available.','wpla'),  $update->title . ' ' . $update->new_version )
					// . __('Please visit your WordPress Updates to install the new version.','wpla') . '<br><br>'
					. '&nbsp;&nbsp;'
					. '<a href="#"" onclick="jQuery(\'.update_details_info\').slideToggle();return false;" class="button">'.__('Show Details','wpla') . '</a>'
					. '&nbsp;&nbsp;&nbsp;'
					. $install_update_button
					. '</p>'
					. '<div class="update_details_info" style="display:none; border-top: 2px dashed #eee;">' 
					. ( $update->upgrade_html ? $update->upgrade_html . '<br>' : '' )
					. ( $update->upgrade_notice ? '<em>' . $update->upgrade_notice . '</em>' : '' )
					. '<br>'
					. '<em>Last checked: '.human_time_diff( $update->timestamp ) . ' ago</em>'
					. '</div>'
				,2,1);

			}

		}

		## END PRO ##
	}
	


}

