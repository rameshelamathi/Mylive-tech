<?php
/*
Plugin Name: Smart Manager
Plugin URI: http://www.storeapps.org/product/smart-manager/
Description: <strong>Lite Version Installed</strong> The most popular store admin plugin for WooCommerce. 10x faster, inline updates. Price, inventory, variations management. 200+ features.
Version: 3.9.15
Author: Store Apps
Author URI: http://www.storeapps.org/
Copyright (c) 2010, 2011, 2012, 2013, 2014, 2015, 2016 Store Apps All rights reserved.
License: GPLv3
Text Domain: smart-manager-for-wp-e-commerce
Domain Path: /languages/
*/

//Hooks

register_activation_hook( __FILE__, 'smart_activate' );
register_deactivation_hook( __FILE__, 'smart_deactivate' );


/**
 * Registers a plugin function to be run when the plugin is activated.
 */

function smart_activate() {
	global $wpdb;

	$index_queries = generate_db_index_queries();
	process_db_indexes( $index_queries ['add'] );

	$sm_advanced_search_temp = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}sm_advanced_search_temp` (
					  `product_id` bigint(20) unsigned NOT NULL default '0',
					  `flag` bigint(20) unsigned NOT NULL default '0',
					  `cat_flag` bigint(20) unsigned NOT NULL default '0') ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sm_advanced_search_temp );

	// Redirect to SM 
	if (!is_network_admin()) {
		add_option( '_sm_activation_redirect', 'pending' );	
	}
}

/**
 * Registers a plugin function to be run when the plugin is deactivated.
 */
function smart_deactivate() {
	global $wpdb;

	$index_queries = generate_db_index_queries();
	process_db_indexes( $index_queries ['remove'] );

	$wpdb->query( "DROP TABLE {$wpdb->prefix}sm_advanced_search_temp" );
}



function smart_get_latest_version() {
	$sm_plugin_info = get_site_transient( 'update_plugins' );
	$latest_version = isset( $sm_plugin_info->response [SM_PLUGIN_FILE]->new_version ) ? $sm_plugin_info->response [SM_PLUGIN_FILE]->new_version : '';
	return $latest_version;
}

function smart_get_user_sm_version() {
	$sm_plugin_info = get_plugins();
	$user_version = $sm_plugin_info [SM_PLUGIN_FILE] ['Version'];
	return $user_version;
}

function smart_is_pro_updated() {
	$user_version = smart_get_user_sm_version();
	$latest_version = smart_get_latest_version();
	return version_compare( $user_version, $latest_version, '>=' );
}


$plugin_path  = untrailingslashit( plugin_dir_path( __FILE__ ) );

/**
 * Throw an error on admin page when WP e-Commerece plugin is not activated.
 */
//if (is_admin ()) {

// require_once (ABSPATH . 'wp-includes/pluggable.php'); // Sometimes conflict with SB-Welcome Email Editor

require_once (ABSPATH . WPINC . '/default-constants.php');
$plugin = plugin_basename( __FILE__ );
define( 'SM_PLUGIN_DIR', dirname( $plugin ) );
define( 'SM_PLUGIN_FILE', $plugin );
define( 'STORE_APPS_URL', 'http://www.storeapps.org/' );
define( 'SM_TEXT_DOMAIN', 'smart-manager-for-wp-e-commerce' );

include_once ABSPATH . 'wp-admin/includes/plugin.php';
include_once (ABSPATH . WPINC . '/functions.php');

if (is_plugin_active( 'woocommerce/woocommerce.php' ) || is_plugin_active( 'wp-e-commerce/wp-shopping-cart.php' )) {
	include_once $plugin_path . '/new/classes/class-smart-manager-admin-welcome.php';
}

// add_action ( 'admin_notices', 'smart_admin_notices' );
//	admin_init is triggered before any other hook when a user access the admin area. 
// This hook doesn't provide any parameters, so it can only be used to callback a specified function.
add_action ( 'admin_init', 'smart_admin_init' );

//Language loader

add_action ( 'admin_init', 'localize_smart_manager' );
	 
function localize_smart_manager() {

    $text_domain = SM_TEXT_DOMAIN;

    $plugin_dirname = dirname( plugin_basename(__FILE__) );

    $locale = apply_filters( 'plugin_locale', get_locale(), $text_domain );

    $loaded = load_textdomain( $text_domain, WP_LANG_DIR . '/' . $plugin_dirname . '/' . $text_domain . '-' . $locale . '.mo' );    

    if ( ! $loaded ) {
        $loaded = load_plugin_textdomain( $text_domain, false, $plugin_dirname . '/languages/' );
    }

}

function smart_manager_get_data() {
	return get_plugin_data( __FILE__ );
}

	
	function smart_admin_init() {
                global $wp_version,$wpdb;

				include_once ('new/smart-manager.php');

                $plugin = plugin_basename( __FILE__ );
                $old_plugin = 'smart-manager/smart-manager.php';
                if (is_plugin_active( $old_plugin )) {
					deactivate_plugins( $old_plugin );
					$action_url = "plugins.php?action=activate&plugin=$plugin&plugin_status=all&paged=1";
					$url = wp_nonce_url( $action_url, 'activate-plugin_' . $plugin );
					update_option( 'recently_activated', array ($plugin => time() ) + ( array ) get_option( 'recently_activated' ) );
					
					if (headers_sent())
						echo "<meta http-equiv='refresh' content='" . esc_attr( "0;url=plugins.php?deactivate=true&plugin_status=$status&paged=$page" ) . "' />";
					else {
						wp_redirect( str_replace( '&amp;', '&', $url ) );
						exit();
					}
				}

                add_action( 'admin_head', 'remove_help_tab'); // For removing the help tab
                
                $plugin_info = get_plugins ();
		$sm_plugin_info = $plugin_info [SM_PLUGIN_FILE];
		$ext_version = '3.3.1';
                $sm_plugin_data = get_plugin_data(__FILE__);
                $sm_version = $sm_plugin_data['Version'];
                define ( 'SM_VERSION', $sm_version );
                load_plugin_textdomain( SM_TEXT_DOMAIN, false, dirname( plugin_basename( __FILE__ ) ).'/languages' );
                if (is_plugin_active ( 'woocommerce/woocommerce.php' ) && is_plugin_active ( 'wp-e-commerce/wp-shopping-cart.php' )) {
			define('WPSC_WOO_ACTIVATED',true);
		} elseif (is_plugin_active ( 'wp-e-commerce/wp-shopping-cart.php' )) {
			define('WPSC_ACTIVATED',true);
		} elseif (is_plugin_active ( 'woocommerce/woocommerce.php' )) {
			define('WOO_ACTIVATED', true);
		}

		// Including Scripts for using the wordpress new media manager
        if (version_compare ( $wp_version, '3.5', '>=' )) {
            define ( 'IS_WP35', true);
            
            if ( isset($_GET['page']) && ($_GET['page'] == "smart-manager-woo" || $_GET['page'] == "smart-manager-wpsc" || $_GET['page'] == "smart-manager-settings")) {
                wp_enqueue_media();
                wp_enqueue_script( 'custom-header' );
                // wp_enqueue_script( 'media-upload' );
            }
            
        }

        //Flag for handling changes since WP 4.0+
        if (version_compare ( $wp_version, '4.0', '>=' )) {
        	define ( 'IS_WP40', true);
        }

        if ( !wp_script_is( 'jquery' ) ) {
            wp_enqueue_script( 'jquery' );
        }

        wp_register_script ( 'sm_visualsearch_underscore', plugins_url ( '/visualsearch/underscore.js', __FILE__ ), array ('jquery'), '0.0.1' );
        wp_register_script ( 'sm_visualsearch_jquery_ui_widget', plugins_url ( '/visualsearch/jquery.ui.widget.js', __FILE__ ), array ('sm_visualsearch_underscore'), '0.0.1' );
        wp_register_script ( 'sm_visualsearch_jquery_ui_menu', plugins_url ( '/visualsearch/jquery.ui.menu.js', __FILE__ ), array ('sm_visualsearch_jquery_ui_widget'), '0.0.1' );
        wp_register_script ( 'sm_visualsearch_jquery_ui_autocomplete', plugins_url ( '/visualsearch/jquery.ui.autocomplete.js', __FILE__ ), array ('sm_visualsearch_jquery_ui_menu'), '0.0.1' );
        wp_register_script ( 'sm_visualsearch_jquery_ui_position', plugins_url ( '/visualsearch/jquery.ui.position.js', __FILE__ ), array ('sm_visualsearch_jquery_ui_autocomplete'), '0.0.1' );
        wp_register_script ( 'sm_visualsearch_dependencies', plugins_url ( '/visualsearch/backbone.js', __FILE__ ), array ('sm_visualsearch_jquery_ui_position'), '0.0.1' );
        wp_register_script ( 'sm_search', plugins_url ( '/visualsearch/search.js', __FILE__ ), array ('sm_visualsearch_dependencies'), '0.0.1' );
        wp_register_script ( 'sm_ext_base', plugins_url ( '/ext/ext-base.js', __FILE__ ), array ('sm_search'), $ext_version );

		wp_register_script ( 'sm_ext_all', plugins_url ( '/ext/ext-all.js', __FILE__ ), array ('sm_ext_base' ), $ext_version );
		if ( ( isset($_GET['post_type']) && $_GET['post_type'] == 'wpsc-product' ) || ( isset($_GET['page']) && $_GET['page'] == 'smart-manager-wpsc' ) ) {
			wp_register_script ( 'sm_main', plugins_url ( '/sm/smart-manager.js', __FILE__ ), array ('sm_ext_all'), $sm_plugin_info ['Version'] );
			define('WPSC_RUNNING', true);
			define('WOO_RUNNING', false);

			// checking the version for WPSC plugin
			define ( 'IS_WPSC37', version_compare ( WPSC_VERSION, '3.8', '<' ) );
			define ( 'IS_WPSC38', version_compare ( WPSC_VERSION, '3.8', '>=' ) );
			if ( IS_WPSC38 ) {		// WPEC 3.8.7 OR 3.8.8 OR 3.8.14
                define('IS_WPSC387', version_compare ( WPSC_VERSION, '3.8.8', '<' ));
				define('IS_WPSC388', version_compare ( WPSC_VERSION, '3.8.8', '>=' ));
				define('IS_WPSC3814', version_compare ( WPSC_VERSION, '3.8.14', '>=' )); // Added as Database upgrade since 3.8.14
			}

		}  else if ( ( isset($_GET['post_type']) && $_GET['post_type'] == 'product' ) || ( isset($_GET['page']) && $_GET['page'] == 'smart-manager-woo' ) ) {
			wp_register_script ( 'sm_main', plugins_url ( '/sm/smart-manager-woo.js', __FILE__ ), array ('sm_ext_all' ), $sm_plugin_info ['Version'] );
			define('WPSC_RUNNING', false);
			define('WOO_RUNNING', true);

			// checking the version for WooCommerce plugin
			define ( 'IS_WOO13', version_compare ( WOOCOMMERCE_VERSION, '1.4', '<' ) );
			
			//todo change 2.1-beta-2 to 2.1  before release

						if (version_compare ( WOOCOMMERCE_VERSION, '2.2.0', '<' )) {

							if (version_compare ( WOOCOMMERCE_VERSION, '2.1.0', '<' )) {

								if (version_compare ( WOOCOMMERCE_VERSION, '2.0', '<' )) {
		                            define ( 'SM_IS_WOO16', "true" );
		                        } else {
		                        	define ( 'SM_IS_WOO16', "false" );	
		                        }
	                            define ( 'SM_IS_WOO21', "false" );
	                        } else {
	                        	define ( 'SM_IS_WOO16', "false" );
	                            define ( 'SM_IS_WOO21', "true" );
	                        }
	                        define ( 'SM_IS_WOO22', "false" );
						} else {
							define ( 'SM_IS_WOO16', "false" );
                            define ( 'SM_IS_WOO21', "false" );
							define ( 'SM_IS_WOO22', "true" );
						}
                        
//			define ( 'IS_WOO20', version_compare ( WOOCOMMERCE_VERSION, '2.0', '>=' ) );
                        
		}


		wp_register_style ( 'sm_search_reset', plugins_url ( '/visualsearch/reset.css', __FILE__ ), array (), '0.0.1' );
		wp_register_style ( 'sm_search_icons', plugins_url ( '/visualsearch/icons.css', __FILE__ ), array ('sm_search_reset'), '0.0.1' );
		wp_register_style ( 'sm_search_workspace', plugins_url ( '/visualsearch/workspace.css', __FILE__ ), array ('sm_search_icons'), '0.0.1' );
		wp_register_style ( 'sm_ext_all', plugins_url ( '/ext/ext-all.css', __FILE__ ), array ('sm_search_workspace'), $ext_version );
		wp_register_style ( 'sm_ext_theme_grey', plugins_url ( '/ext/ext-theme-grey.css', __FILE__ ), array ('sm_ext_all'), $ext_version );
		wp_register_style ( 'sm_main', plugins_url ( '/sm/smart-manager.css', __FILE__ ), array ('sm_ext_theme_grey' ), $sm_plugin_info ['Version'] );
		
		if (file_exists ( (dirname ( __FILE__ )) . '/pro/sm.js' )) {
			wp_register_script ( 'sm_functions', plugins_url ( '/pro/sm.js', __FILE__ ), array ('sm_main' ), $sm_plugin_info ['Version'] );
			define ( 'SMPRO', true );
		} else {
			define ( 'SMPRO', false );
		}
		if (SMPRO === true) {
			include ('pro/upgrade.php');
			// this allows you to add something to the end of the row of information displayed for your plugin - 
			// like the existing after_plugin_row filter, but specific to your plugin, 
			// so it only runs once instead of after each row of the plugin display
//			add_action ( 'after_plugin_row_' . plugin_basename ( __FILE__ ), 'smart_plugin_row' );
//			add_action ( 'after_plugin_row_' . plugin_basename ( __FILE__ ), 'show_registration_upgrade');
//			add_action ( 'in_plugin_update_message-' . plugin_basename ( __FILE__ ), 'smart_update_notice' );
//			add_action ( 'all_admin_notices', 'smart_update_overwrite' );
		} else {
			// Code to hide SM Promo
			if ( is_admin() ) {
				if(isset($_GET['sm_dismiss_admin_notice']) && $_GET['sm_dismiss_admin_notice'] == '1'){
		            update_option('sm_dismiss_admin_notice', true);
		            wp_safe_redirect($_SERVER['HTTP_REFERER']);
		        } else if ( !get_option('sm_dismiss_admin_notice') ) { // Code to handle SM IN App Promo
					add_action( 'admin_notices', 'sm_add_promo_notices');
				}
			}
		}
		//wp-ajax action
		if (is_admin() ) {
            add_action ( 'wp_ajax_sm_include_file', 'sm_include_file' ); 
            add_action ( 'wp_ajax_sm_klawoo_subscribe', 'sm_klawoo_subscribe' );

       //      if ( false !== get_option( '_sm_activation_redirect' ) ) {
       //      	// Delete the redirect transient
		    	// delete_option( '_sm_activation_redirect' );

		    	// if ( WPSC_WOO_ACTIVATED === true || WOO_ACTIVATED === true ) {
		    	// 	wp_redirect( admin_url( 'edit.php?post_type=product&page=smart-manager-woo' ) );
		    	// } else if ( WPSC_ACTIVATED === true ) {
		    	// 	wp_redirect( admin_url( 'edit.php?post_type=wpsc-product&page=smart-manager-wpsc' ) );
		    	// }
		    	
       //      }
        }

	}

	// Function for klawoo subscribe
	function sm_klawoo_subscribe() {
        $url = 'http://app.klawoo.com/subscribe';

        if( !empty( $_POST ) ) {
            $params = $_POST;
        } else {
            exit();
        }

        if( empty($params['name']) ) {
        	$params['name'] = '';
        }

        $method = 'POST';
        $qs = http_build_query( $params );

        $options = array(
            'timeout' => 15,
            'method' => $method
        );

        if ( $method == 'POST' ) {
            $options['body'] = $qs;
        } else {
            if ( strpos( $url, '?' ) !== false ) {
                $url .= '&'.$qs;
            } else {
                $url .= '?'.$qs;
            }
        }

        $response = wp_remote_request( $url, $options );
        if ( wp_remote_retrieve_response_code( $response ) == 200 ) {
            $data = $response['body'];
            if ( $data != 'error' ) {
                $message_start = substr( $data, strpos( $data,'<body>' ) + 6 );
                $remove = substr( $message_start, strpos( $message_start,'</body>' ) );
                $message = trim( str_replace( $remove, '', $message_start ) );

                update_option('sm_dismiss_admin_notice', true); // for hiding the promo message

                echo ( $message );
                exit();                
            }
        }
        exit();
    }

	// Function to handle SM IN App Promo
	function sm_add_promo_notices() {

		if ( empty($_GET['page']) || (!empty($_GET['page']) && $_GET['page'] != 'smart-manager-woo' && $_GET['page'] != 'smart-manager-wpsc') ) {
			return;
		}

		$sm_promo_msg = '';

		$timezone_format = _x('Y-m-d H:i:s', 'timezone date format');
		$current_wp_date = date_i18n($timezone_format);

		$sm_lite_activation_date = get_option('sm_lite_activation_date');

		if ( $sm_lite_activation_date === false ) {
			
			$sm_lite_activation_date = $current_wp_date;
			add_option('sm_lite_activation_date',$sm_lite_activation_date);
		}

		$date_diff = date_diff( date_create($sm_lite_activation_date),date_create($current_wp_date) );

		$sm_resp_msg = '<b>'. __('Congratulations!!!', 'smart-manager') .'</b> ' . __('Kindly check your mail to avail the discount', 'smart-manager') ;
		$sm_promo_cond = __('*Only For Today*', 'smart-manager');
		$sm_promo_hide_msg = __('No, I don\'t like offers...', 'smart-manager');

		if ( $date_diff->days == 0 ) {
			$sm_promo_msg = '<b>'. __('Big Savings!!!', 'smart-manager') .' </b> <span style="color:#E34F4C;font-weight:bold;">' . __('20% OFF ', 'smart-manager') . ' </span>' . __('on Smart Manager Pro!', 'smart-manager');
			$sm_klawoo_list_id = 'OFvZfJBDn4FLsDOz3Ulpww';
		} else if ( $date_diff->days == 1 ) {
			$sm_promo_msg = '<b>'. __('Missed yesterday?', 'smart-manager') .' </b> <span style="color:#E34F4C;font-weight:bold;">' .  __('15% OFF ', 'smart-manager') . ' </span>' . __('on Smart Manager Pro', 'smart-manager');
			$sm_klawoo_list_id = '0mHi7635Zb4L2vN7hmngdYDQ';
		} else if ( $date_diff->days == 2 ) {
			$sm_promo_msg = '<b>'. __('Last chance!!!', 'smart-manager') .' </b> <span style="color:#E34F4C;font-weight:bold;">' . __('10% OFF ', 'smart-manager') . ' </span>' . __('on Smart Manager Pro!', 'smart-manager');
			$sm_klawoo_list_id = 'gSlZ3HGl3OMOjE5ZEnAJUQ';
		} else if ( $date_diff->days > 2 ) {
			$sm_promo_msg = '<b>'. __('Sign up to get updates, insights & tips...', 'smart-manager');
			$sm_klawoo_list_id = 'eGXNNhOHHcRHSDOv3hmSsA';
			$sm_resp_msg = '<b>'. __('Thank you for Subscribing!!!') .'</b>';
			$sm_promo_cond = '';
			$sm_promo_hide_msg = __('No, I don\'t need help...', 'smart-manager');
		}

		if ( !empty($sm_promo_msg) ) {

		    $current_url = (strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') === FALSE ? 'http' : 'https') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING'];

			echo '<div id="sm_promo_msg" class="updated fade" style="display:block !important;"> 
					<table style="width:100%;"> 
						<tbody> 
							<tr>
								<td> 
									<span class="dashicons dashicons-awards" style="font-size:3em;color:#b32727;margin-left: -0.2em;margin-right: 0.4em;margin-bottom: 0.45em;"></span> 
								</td> 
								<td id="sm_promo_msg_content" style="padding:0.5em;">
									<div style="padding-top: 0.3em;float: left;font-size:1.1em;">'. $sm_promo_msg .'</div>
									<form name="sm_klawoo_subscribe" action="#" method="POST" accept-charset="utf-8" style="float:left;padding-left:0.5em;">
			                            <input class="regular-text ltr" type="text" name="email" id="email" placeholder="Email" style="width:18em;height:1.75em;"/>
			                            <input type="hidden" name="list" value="'. $sm_klawoo_list_id .'"/>
			                            <span id="resp_message" style="display:none;">'. $sm_resp_msg .'</span>
			                            <input type="submit" name="submit" id="submit" class="button button-primary" value="Subscribe" style="height:1.75em;line-height:1.6em;margin-top:0;">
			                        </form>
			                        <div id="sm_promo_valid_msg">'. $sm_promo_cond .'</div>
			                        <div style="padding-top: 0.5em;font-size:0.8em;width:100%;float:left;">
			                        	<div style="float:left;"><a href="http://www.storeapps.org/product/smart-manager" target=_storeapps> '. __( 'Learn more about Pro version', 'smart-manager' ) . '</a> ' . __( 'or take a', 'smart-manager' ) . ' <a href="http://demo.storeapps.org/?demo=sm-woo" target=_livedemo> ' . __( 'Live Demo', 'smart-manager' ) . ' </a>	</div>
										<div style="float:right;"><a href="'.$current_url.'&sm_dismiss_admin_notice=1">'. $sm_promo_hide_msg .'</a></div>
									</div>
								</td> 
							</tr>
						</tbody> 
					</table> 
				</div>
				<script type="text/javascript">
		            jQuery(function () {
		                jQuery("form[name=sm_klawoo_subscribe]").submit(function (e) {
		                    e.preventDefault();
		                    
		                    params = jQuery("form[name=sm_klawoo_subscribe]").serializeArray();
		                    params.push( {name: "action", value: "sm_klawoo_subscribe" });
		                    
		                    jQuery.ajax({
		                        method: "POST",
		                        type: "text",
		                        url: "'.admin_url( 'admin-ajax.php' ).'",
		                        data: params,
		                        success: function(response) {
		                        	var resp = jQuery(response);
		                            if (resp.find("h2").text() == "You\'re subscribed!") {
		                                jQuery("td[id=sm_promo_msg_content]").html(jQuery("form[name=sm_klawoo_subscribe]").find("#resp_message").html());
		                                jQuery(".dashicons-awards").css("margin-right","0em");
		                            }
		                        }
		                    });
		                });
		            });
		        </script>';
		}
	}

	function sm_include_file() {

		check_ajax_referer('smart-manager-security','security');

		$json_filename = $_REQUEST['file'];
		$base_path = WP_PLUGIN_DIR . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ ) ) . 'sm/' . $json_filename . '.php';
		include_once ( $base_path );
	}

	function generate_db_index_queries() {
		global $wpdb;
		
		$index_queries = array ('add' => array (), 'remove' => array () );
		
		$index_queries ['add'] [] = "ALTER TABLE {$wpdb->prefix}posts
	                                                ADD KEY `sm_idx_post_parent` ( `post_parent` ),
	                                                ADD KEY `sm_idx_post_date` ( `post_date` )";
		$index_queries ['remove'] [] = "ALTER TABLE {$wpdb->prefix}posts
	                                                DROP KEY `sm_idx_post_parent`,
	                                                DROP KEY `sm_idx_post_date`";
		
		if (is_plugin_active( 'wp-e-commerce/wp-shopping-cart.php' )) {
			
			$index_queries ['add'] [] = "ALTER TABLE {$wpdb->prefix}wpsc_cart_contents 
	                                                    ADD KEY `sm_idx_cart_contents_purchaseid`( `purchaseid` ),
	                                                    ADD KEY `sm_idx_cart_contents_prodid`( `prodid` ),
	                                                    ADD KEY `sm_idx_cart_contents_name`( `name` )";
			$index_queries ['remove'] [] = "ALTER TABLE {$wpdb->prefix}wpsc_cart_contents 
	                                                    DROP KEY `sm_idx_cart_contents_purchaseid`,
	                                                    DROP KEY `sm_idx_cart_contents_prodid`,
	                                                    DROP KEY `sm_idx_cart_contents_name`";
			
			$index_queries ['add'] [] = "ALTER TABLE {$wpdb->prefix}wpsc_purchase_logs 
	                                                    ADD KEY `sm_idx_purchase_logs_userid` ( `user_ID` ),
	                                                    ADD KEY `sm_idx_purchase_logs_date` ( `date` )";
			$index_queries ['remove'] [] = "ALTER TABLE {$wpdb->prefix}wpsc_purchase_logs 
	                                                    DROP KEY `sm_idx_purchase_logs_userid`,
	                                                    DROP KEY `sm_idx_purchase_logs_date`";
			
	        // ADD KEY `sm_idx_submited_form_data_value` ( `value` )

			$index_queries ['add'] [] = "ALTER TABLE {$wpdb->prefix}wpsc_submited_form_data 
	                                                    ADD KEY `sm_idx_submited_form_data_log_id` ( `log_id` )";
			$index_queries ['remove'] [] = "ALTER TABLE {$wpdb->prefix}wpsc_submited_form_data 
	                                                    DROP KEY `sm_idx_submited_form_data_log_id`";
		
		}
		
		return $index_queries;
	}

	function process_db_indexes($queries) {
		global $wpdb;
		
		foreach ( $queries as $query ) {
			$wpdb->query( $query );
		}
	}

	function smart_admin_notices() {
		if (! is_plugin_active( 'woocommerce/woocommerce.php' ) && ! is_plugin_active( 'wp-e-commerce/wp-shopping-cart.php' )) {
			echo '<div id="notice" class="error"><p>';
			echo '<b>' . __( 'Smart Manager', SM_TEXT_DOMAIN ) . '</b> ' . __( 'add-on requires', SM_TEXT_DOMAIN ) . ' <a href="http://www.storeapps.org/wpec/">' . __( 'WP e-Commerce', SM_TEXT_DOMAIN ) . '</a> ' . __( 'plugin or', SM_TEXT_DOMAIN ) . ' <a href="http://www.storeapps.org/woocommerce/">' . __( 'WooCommerce', SM_TEXT_DOMAIN ) . '</a> ' . __( 'plugin. Please install and activate it.', SM_TEXT_DOMAIN );
			echo '</p></div>', "\n";
		}
	}

	function smart_admin_scripts() {

		if( !empty($_GET['landing-page']) ) {
			return;
		}

		if( (isset($_GET['sm_beta']) && $_GET['sm_beta'] == '1') || 
			(! is_plugin_active( 'woocommerce/woocommerce.php' ) && ! is_plugin_active( 'wp-e-commerce/wp-shopping-cart.php' )) ){
		    $GLOBALS['smart_manager_beta']->enqueue_admin_scripts();
		} else if ( !empty($_GET['page']) && ($_GET['page'] == 'smart-manager-woo' || $_GET['page'] == 'smart-manager-wpsc') ) {
			if (file_exists( (dirname( __FILE__ )) . '/pro/sm.js' )) {
				wp_enqueue_script( 'sm_functions' );
			}
			wp_enqueue_script( 'sm_main' );
		}
	}

	function smart_admin_styles() {

		if( !empty($_GET['landing-page']) ) {
			return;
		}
		
		if( (isset($_GET['sm_beta']) && $_GET['sm_beta'] == '1') || 
			(! is_plugin_active( 'woocommerce/woocommerce.php' ) && ! is_plugin_active( 'wp-e-commerce/wp-shopping-cart.php' )) ){
		    $GLOBALS['smart_manager_beta']->enqueue_admin_styles();
		} else if ( !empty($_GET['page']) && ($_GET['page'] == 'smart-manager-woo' || $_GET['page'] == 'smart-manager-wpsc') ) {
			wp_enqueue_style( 'sm_main' );
		}
	}

	function smart_woo_add_modules_admin_pages() {
		global $wpdb, $current_user;

		if (!function_exists('wp_get_current_user')) {
			require_once (ABSPATH . 'wp-includes/pluggable.php'); // Sometimes conflict with SB-Welcome Email Editor
		}

		$current_user = wp_get_current_user(); // Sometimes conflict with SB-Welcome Email Editor

	        if ( (!current_user_can( 'edit_pages' )) && is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	            $page = add_menu_page( 'Smart Manager', 'Smart Manager','read', 'smart-manager-woo', 'sm_admin_page' );
	        } else if( current_user_can( 'edit_pages' ) && (! is_plugin_active( 'woocommerce/woocommerce.php' ) && ! is_plugin_active( 'wp-e-commerce/wp-shopping-cart.php' )) ) {
	        	$page = add_menu_page( 'Smart Manager', 'Smart Manager','read', 'smart-manager-beta', 'sm_admin_page' );
	        } else {
	            $page = add_submenu_page( 'edit.php?post_type=product', 'Smart Manager', 'Smart Manager', 'edit_pages', 'smart-manager-woo', 'sm_admin_page' );
	        }
	            
	        $sm_action = (isset($_GET['action']) ? $_GET['action'] : '');
		if ($sm_action != 'sm-settings') { // not be include for settings page
			add_action( 'admin_print_scripts-' . $page, 'smart_admin_scripts' );
		}
		add_action( 'admin_print_styles-' . $page, 'smart_admin_styles' );
	}

	function smart_wpsc_add_modules_admin_pages($page_hooks, $base_page) {
		global $wpdb, $current_user;

		if (!function_exists('wp_get_current_user')) {
			require_once (ABSPATH . 'wp-includes/pluggable.php'); // Sometimes conflict with SB-Welcome Email Editor
		}

		$current_user = wp_get_current_user(); // Sometimes conflict with SB-Welcome Email Editor
	        
	        if ( (!current_user_can( 'edit_posts' )) && (is_plugin_active( 'wp-e-commerce/wp-shopping-cart.php' )) ) {
	            $page = add_menu_page( 'Smart Manager', 'Smart Manager','read', 'smart-manager-wpsc', 'sm_admin_page' );
	        }
	        else {
	            $page = add_submenu_page( $base_page, 'Smart Manager', 'Smart Manager', 'edit_posts', 'smart-manager-wpsc', 'sm_admin_page' );
	        }
	        
	        $sm_action = (isset($_GET['action']) ? $_GET['action'] : '');
		if ($sm_action != 'sm-settings') { // not be include for settings page
			add_action( 'admin_print_scripts-' . $page, 'smart_admin_scripts' );
		}
	        
		add_action( 'admin_print_styles-' . $page, 'smart_admin_styles' );
		$page_hooks [] = $page;
		return $page_hooks;
	}

	function smart_add_menu_access() {
		global $wpdb, $current_user;

		if (!function_exists('wp_get_current_user')) {
			require_once (ABSPATH . 'wp-includes/pluggable.php'); // Sometimes conflict with SB-Welcome Email Editor
		}

		$current_user = wp_get_current_user(); // Sometimes conflict with SB-Welcome Email Editor
			
	        if ( !isset( $current_user->roles[0] ) ) {
	            $roles = array_values( $current_user->roles );
	        } else {
	            $roles = $current_user->roles;
	        }

	        //Fix for the client
			if ( !empty( $current_user->caps ) ) {
	        	$caps = array_keys($current_user->caps);
	        	$current_user_caps = $roles[0] = (!empty($caps)) ? $caps[0] : '';
	        }

		$query = "SELECT option_value FROM {$wpdb->prefix}options WHERE option_name = 'sm_" . $roles [0] . "_dashboard'";
		$results = $wpdb->get_results( $query );

	        if (! empty( $results [0]->option_value ) || $current_user->roles [0] == 'administrator' //modified cond for client fix
	        	|| (!empty($current_user_caps) && $current_user_caps == 'administrator')) {
			add_filter( 'wpsc_additional_pages', 'smart_wpsc_add_modules_admin_pages', 10, 2 );
			add_action( 'admin_menu', 'smart_woo_add_modules_admin_pages' );
		}
	}

	add_action( 'admin_menu', 'smart_add_menu_access', 9 );

	//if (is_multisite() && is_network_admin()) {
	//	
	//	function smart_add_license_key_page() {
	//		$page = add_submenu_page( 'settings.php', 'Smart Manager', 'Smart Manager', 'manage_options', 'sm-settings', 'smart_settings_page' );
	//		add_action( 'admin_print_styles-' . $page, 'smart_admin_styles' );
	//	}
	//	
	//	if (file_exists( (dirname( __FILE__ )) . '/pro/sm.js' ))
	//		add_action( 'network_admin_menu', 'smart_add_license_key_page', 11 );
	//
	//} else if (is_admin()) {
	if (is_admin()) {
		
		function smart_show_privilege_page() {
			$plugin_base = WP_PLUGIN_DIR . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ ) ) . 'pro/';
			if (file_exists( $plugin_base . 'sm-privilege.php' )) {
				include_once ($plugin_base . 'sm-privilege.php');
				return;
			} else {
				$error_message = __( "A required Smart Manager file is missing. Can't continue. ", SM_TEXT_DOMAIN );
			}
		}
		
		function smart_add_privilege_page() {
	//		$page = add_submenu_page( 'options-general.php', 'Smart Manager', 'Smart Manager', 10, 'smart-manager-privilege', 'smart_show_privilege_page' );
			$page = add_submenu_page( 'options-general.php', 'Smart Manager', 'Smart Manager', 'activate_plugins', 'smart-manager-settings', 'smart_show_privilege_page' );
			
	                $sm_action = (isset($_GET['action']) ? $_GET['action'] : '');
	                if ($sm_action != 'sm-settings') { // not be include for settings page
	                        add_action( 'admin_print_scripts-' . $page, 'smart_admin_scripts' );
	                }
			add_action( 'admin_print_styles-' . $page, 'smart_admin_styles' );
		}
		if (file_exists( (dirname( __FILE__ )) . '/pro/sm.js' ))
			add_action( 'admin_menu', 'smart_add_privilege_page', 11 );

	}

	// function for removing the Help Tab
	function remove_help_tab(){

		//condition to remove the help tab only from SM pages
		if ( isset($_GET['page']) && ($_GET['page'] == "smart-manager-woo" || $_GET['page'] == "smart-manager-wpsc" || $_GET['page'] == "smart-manager-settings")) {
			$screen = get_current_screen();
	    	$screen->remove_help_tabs();
		}
	}

	function sm_admin_page() {

		if( !empty($_GET['landing-page']) ) {
			$GLOBALS['smart_manager_admin_welcome']->show_welcome_page();
		} else {
			if( (isset($_GET['sm_beta']) && $_GET['sm_beta'] == '1') || 
				(! is_plugin_active( 'woocommerce/woocommerce.php' ) && ! is_plugin_active( 'wp-e-commerce/wp-shopping-cart.php' )) ){

			    $GLOBALS['smart_manager_beta']->show_console_beta();
			} else {
				smart_show_console();			
			}	
		}
	}

	function smart_show_console() {

		define( 'PLUGINS_FILE_PATH', dirname( dirname( __FILE__ ) ) );
		define( 'SM_PLUGIN_DIRNAME', plugins_url( '', __FILE__ ) );
		define( 'IMG_URL', SM_PLUGIN_DIRNAME . '/images/' );
		define( 'SM_BETA_IMG_URL', SM_PLUGIN_DIRNAME . '/new/assets/images/' );
		
		if (WPSC_RUNNING === true) {
			$json_filename = (IS_WPSC37) ? 'json37' : 'json38';
		} else if (WOO_RUNNING === true) {
			$json_filename = 'woo-json';
		}
		// define( 'JSON_URL', SM_PLUGIN_DIRNAME . "/sm/$json_filename.php" );
		define( 'JSON_URL', $json_filename );
		define( 'ADMIN_URL', get_admin_url() ); //defining the admin url
		define( 'ABS_WPSC_URL', WP_PLUGIN_DIR . '/wp-e-commerce' );
		define( 'WPSC_NAME', 'wp-e-commerce' );
		
		$latest_version = smart_get_latest_version();
		$is_pro_updated = smart_is_pro_updated();
		

	//	if (isset( $_GET ['action'] ) && $_GET ['action'] == 'sm-settings') {
	//		smart_settings_page();
	//	} else {
			$base_path = WP_PLUGIN_DIR . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ ) ) . 'sm/';
			?>
	<div class="wrap">
	<div id="icon-smart-manager" class="icon32"><br />
	</div>
	<style>
	    div#TB_window {
	        background: lightgrey;
	    }
	</style>    
	<?php if ( SMPRO === true && function_exists( 'smart_manager_support_ticket_content' ) ) smart_manager_support_ticket_content();  ?>    
	    
	<h2 class = "sm-h2">
			<?php
	                echo 'Smart Manager ';
					echo (SMPRO === true) ? 'Pro' : 'Lite';
	                $before_plug_page = '';
	                $after_plug_page = '';
	                $plug_page = '';
			?>
			<span style="float:right; margin: -6px -21px -20px 0px;">
					<!-- <a href="http://www.storeapps.org/?buy-now=742&coupon=sm-festive-40-2013&utm_source=SM&utm_medium=Lite&utm_campaign=Festive2013" target="_blank"> <img src="<?php echo IMG_URL . '40perc-off-sm-thanks2013.png'?>" alt="40peroff"> </a> -->
					<a href="http://www.storeapps.org/sm-in-app-promo" target="_blank"> <img src="<?php echo IMG_URL . 'sm-in-app-promo.png' ?>" alt=""> </a>
			</span>
	   		<p class="wrap" style="font-size: 12px; margin: 18px -21px 0px 5px;"><span style="float: right; line-height: 17px;"> <?php
				if ( SMPRO === true && ! is_multisite() ) {
	                		$before_plug_page .= '<a href="admin.php?page=smart-manager-';
					$after_plug_page = '&action=sm-settings">Settings</a> | ';
					if (WPSC_RUNNING == true) {
						$plug_page = 'wpsc';
					} elseif (WOO_RUNNING == true) {
						$plug_page = 'woo';
					}
				} else {
					$before_plug_page = '';
					$after_plug_page = '';
					$plug_page = '';
				}
	    
				$sm_beta = '';

				if ( isset($_GET['page']) && ($_GET['page'] == "smart-manager-woo" || $_GET['page'] == "smart-manager-wpsc")) {

					if( empty($_GET['post_type']) ) {
						$_GET['post_type'] = ($_GET['page'] == "smart-manager-wpsc") ? 'wpsc-product' : 'product';
					}

					$sm_beta = '<a href="'. admin_url('edit.php?post_type='.$_GET['post_type'].'&page='.$_GET['page'].'&sm_beta=1') .'" title="'. __( 'Try out Smart Manager Beta', SM_TEXT_DOMAIN ) .'"> ' . __( 'Try out Smart Manager Beta', SM_TEXT_DOMAIN ) .'</a> <sup style="vertical-align: super;color:red;">New</sup> | ';
				}

                $before_plug_page = '<a href="'. esc_url( add_query_arg( array( 'landing-page' => 'sm-faqs' ) ) ) .'" title="Support" id="support_link">Need Help?</a> | ';

                if ( SMPRO === true ) {
                    if ( !wp_script_is( 'thickbox' ) ) {
                        if ( !function_exists( 'add_thickbox' ) ) {
                            require_once ABSPATH . 'wp-includes/general-template.php';
                        }
                        add_thickbox();
                    }
                    $before_plug_page = apply_filters( 'sm_before_plug_page', $before_plug_page );
                    if (is_super_admin()) {
                        $before_plug_page .= '<a href="options-general.php?page=smart-manager-settings">Settings</a> | ';
                    }
                    
                }


	//			printf ( __ ( '%1s%2s%3s<a href="%4s" target=_storeapps>Docs</a>' , SM_TEXT_DOMAIN), $before_plug_page, $plug_page, $after_plug_page, "http://www.storeapps.org/support/documentation/" );
				printf ( __ ( '%1s%2s<a href="%3s" target="_blank">Docs</a>' , SM_TEXT_DOMAIN) ,$sm_beta, $before_plug_page, "http://www.storeapps.org/support/documentation/smart-manager" );
				?>
				</span><?php
			_e( '10x productivity gains with store administration. Quickly find and update products, orders and customers', SM_TEXT_DOMAIN );
			?></p>
	</h2>
	<h6 align="right"><?php
			if (! $is_pro_updated) {
				$admin_url = ADMIN_URL . "plugins.php";
				$update_link = __( 'An upgrade for Smart Manager Pro', SM_TEXT_DOMAIN ) . " " . $latest_version . " " . __( 'is available.', SM_TEXT_DOMAIN ) . " " . "<a align='right' href=$admin_url>" . __( 'Click to upgrade.', SM_TEXT_DOMAIN ) . "</a>";
				smart_display_notice( $update_link );
			}
			?>

	</h6>
	<!--<h6 align="right"> 
	<?php
			if (SMPRO === true) {
				$sm_license_key = smart_get_license_key();
				if ($sm_license_key == '') {
					if (! is_multisite()) {
						if (WPSC_RUNNING == true) {
							$plug_page = 'wpsc';
						} elseif (WOO_RUNNING == true) {
							$plug_page = 'woo';
						}
						smart_display_notice( __( 'Please enter your license key for automatic upgrades and support to get activated.', SM_TEXT_DOMAIN ) . '<a href=admin.php?page=smart-manager-' . $plug_page . '&action=sm-settings>' . __( 'Enter License Key', SM_TEXT_DOMAIN ) . '</a>' );
					}
				}
			}
			?>
	</h6>-->
	</div>

	<?php
		if ( SMPRO === false && get_option('sm_dismiss_admin_notice') == '1') { ?>
				<div id="message" class="updated fade" style="display:block !important;">
					<p> <?php
						printf( ('<b>' . __( 'Important:', SM_TEXT_DOMAIN ) . '</b> ' . __( 'Upgrade to Pro to get features like \'<i>Batch Update</i>\' , \'<i>Export CSV</i>\' , \'<i>Duplicate Products</i>\' &amp; many more...', SM_TEXT_DOMAIN ) . " " . '<br /><a href="%1s" target=_storeapps>' . " " .__( 'Learn more about Pro version', SM_TEXT_DOMAIN ) . '</a> ' . __( 'or take a', SM_TEXT_DOMAIN ) . " " . '<a href="%2s" target=_livedemo>' . " " . __( 'Live Demo', SM_TEXT_DOMAIN ) . '</a>'), 'http://www.storeapps.org/product/smart-manager', 'http://demo.storeapps.org/?demo=sm-woo' );
						?>
					</p>
				</div>
			<?php
		} 
			$error_message = '';
			if ((file_exists( WP_PLUGIN_DIR . '/wp-e-commerce/wp-shopping-cart.php' )) && (file_exists( WP_PLUGIN_DIR . '/woocommerce/woocommerce.php' ))) {
				if (is_plugin_active( 'wp-e-commerce/wp-shopping-cart.php' )) {

	                            require_once (WPSC_FILE_PATH . '/wp-shopping-cart.php');
	                            // if (IS_WPSC37 || IS_WPSC38) {
	                            if  ( version_compare ( WPSC_VERSION, '3.8', '>=' )) {
	                                if (file_exists( $base_path . 'manager-console.php' )) {
	                                        include_once ($base_path . 'manager-console.php');
	                                        return;
	                                } else {
	                                        $error_message = __( "A required Smart Manager file is missing. Can't continue.", SM_TEXT_DOMAIN );
	                                }
	                            } else {
	                                $error_message = __( 'Smart Manager currently works only with WP e-Commerce 3.7 or above.', SM_TEXT_DOMAIN );
	                            }
				} else if (is_plugin_active( 'woocommerce/woocommerce.php' )) {
	                            if (IS_WOO13) {
	                                    $error_message = __( 'Smart Manager currently works only with WooCommerce 1.4 or above.', SM_TEXT_DOMAIN );
	                            } else {
	                                if (file_exists( $base_path . 'manager-console.php' )) {
	                                        include_once ($base_path . 'manager-console.php');
	                                        return;
	                                } else {
	                                        $error_message = __( "A required Smart Manager file is missing. Can't continue.", SM_TEXT_DOMAIN );
	                                }
	                            }
				}
	                        else {
	                            $error_message = "<b>" . __( 'Smart Manager', SM_TEXT_DOMAIN ) . "</b> " . __( 'add-on requires', SM_TEXT_DOMAIN ) . " " .'<a href="http://www.storeapps.org/wpec/">' . __( 'WP e-Commerce', SM_TEXT_DOMAIN ) . "</a>" . " " . __( 'plugin or', SM_TEXT_DOMAIN ) . " " . '<a href="http://www.storeapps.org/woocommerce/">' . __( 'WooCommerce', SM_TEXT_DOMAIN ) . "</a>" . " " . __( 'plugin. Please install and activate it.', SM_TEXT_DOMAIN );
	                        }
	                    } else if (file_exists( WP_PLUGIN_DIR . '/wp-e-commerce/wp-shopping-cart.php' )) {
	                        if (is_plugin_active( 'wp-e-commerce/wp-shopping-cart.php' )) {
	                            require_once (WPSC_FILE_PATH . '/wp-shopping-cart.php');
	                            if (IS_WPSC37 || IS_WPSC38) {
	                                if (file_exists( $base_path . 'manager-console.php' )) {
	                                        include_once ($base_path . 'manager-console.php');
	                                        return;
	                                } else {
	                                        $error_message = __( "A required Smart Manager file is missing. Can't continue.", SM_TEXT_DOMAIN );
	                                }
	                            } else {
	                                $error_message = __( 'Smart Manager currently works only with WP e-Commerce 3.7 or above.', SM_TEXT_DOMAIN );
	                            }
	                        } else {
	                                $error_message = __( 'WP e-Commerce plugin is not activated.', SM_TEXT_DOMAIN ) . "<br/><b>" . _e( 'Smart Manager', SM_TEXT_DOMAIN ) . "</b> " . _e( 'add-on requires WP e-Commerce plugin, please activate it.', SM_TEXT_DOMAIN );
	                        }
	                    } else if (file_exists( WP_PLUGIN_DIR . '/woocommerce/woocommerce.php' )) {
	                        if (is_plugin_active( 'woocommerce/woocommerce.php' )) {
	                            if (IS_WOO13) {
	                                    $error_message = __( 'Smart Manager currently works only with WooCommerce 1.4 or above.', SM_TEXT_DOMAIN );
	                            } else {
	                                if (file_exists( $base_path . 'manager-console.php' )) {
	                                    include_once ($base_path . 'manager-console.php');
	                                    return;
	                                } else {
	                                    $error_message = __( "A required Smart Manager file is missing. Can't continue.", SM_TEXT_DOMAIN );
	                                }
	                            }
	                        } else {
	                            $error_message = __( 'WooCommerce plugin is not activated.', SM_TEXT_DOMAIN ) . "<br/><b>" . __( 'Smart Manager', SM_TEXT_DOMAIN ) . "</b> " . __( 'add-on requires WooCommerce plugin, please activate it.', SM_TEXT_DOMAIN );
	                        }
	                    }
	                    else {
	                        $error_message = "<b>" . __( 'Smart Manager', SM_TEXT_DOMAIN ) . "</b> " . __( 'add-on requires', SM_TEXT_DOMAIN ) . " " .'<a href="http://www.storeapps.org/wpec/">' . __( 'WP e-Commerce', SM_TEXT_DOMAIN ) . "</a>" . " " . __( 'plugin or', SM_TEXT_DOMAIN ) . " " . '<a href="http://www.storeapps.org/woocommerce/">' . __( 'WooCommerce', SM_TEXT_DOMAIN ) . "</a>" . " " . __( 'plugin. Please install and activate it.', SM_TEXT_DOMAIN );
	                    }
			
			if ($error_message != '') {
				smart_display_err( $error_message );
				?>
	</p>
	</div>
	<?php
			}
	}

	function smart_update_notice() {
		if (! function_exists( 'sm_get_download_url_from_db' ))
			return;
		$download_details = sm_get_download_url_from_db();
		$link = $download_details ['results'] [0]->option_value; //$plugins->response [SM_PLUGIN_FILE]->package;
		

		if (! empty( $link )) {
			$current = get_site_transient( 'update_plugins' );
	//		$r1 = smart_plugin_reset_upgrade_link( $current, $link );
	//		set_site_transient( 'update_plugins', $r1 );
			echo $man_download_link = ' ' . __( 'Or', SM_TEXT_DOMAIN ) . ' ' . "<a href='$link'>" . __( 'click here to download the latest version.', SM_TEXT_DOMAIN ) . "</a>";
		}

	}

	function smart_display_err($error_message) {
		echo "<div id='notice' class='error'>";
		echo "<b>" . __( 'Error:', SM_TEXT_DOMAIN ) . "</b>" . $error_message;
		echo "</div>";
	}

	function smart_display_notice($notice) {
		echo "<div id='message' class='updated fade'>
	             <p>";
		echo _e( $notice, SM_TEXT_DOMAIN );
		echo "</p></div>";
	}

?>
