<?php
/**
 * WooCommerce First Data
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce First Data to newer
 * versions in the future. If you wish to customize WooCommerce First Data for your
 * needs please refer to http://docs.woothemes.com/document/firstdata/
 *
 * @package     WC-First-Data/Templates
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2016, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

/**
 * The My Account - My Cards
 *
 * @param array $tokens optional array of credit card tokens
 * @param WC_Gateway_FirstData $wc_gateway_firstdata firstdata gateway object
 *
 * @version 3.6.0
 * @since 3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?> <h2 id="woocommerce-firstdata-my-payment-methods" style="margin-top:40px;"><?php esc_html_e( 'My Saved Cards', 'woocommerce-gateway-firstdata' ); ?></h2><?php

if ( ! empty( $tokens ) ) :
	?>
	<a name="woocommerce-firstdata-my-payment-methods"></a>
	<table class="shop_table my-account-woocommerce-firstdata-payment-methods">

		<thead>
		<tr>
			<th class="woocommerce-firstdata-payment-method-type"><span class="nobr"><?php esc_html_e( 'Card Type', 'woocommerce-gateway-firstdata' ); ?></span></th>
			<th class="woocommerce-firstdata-payment-method-account"><span class="nobr"><?php esc_html_e( 'Last Four', 'woocommerce-gateway-firstdata' ); ?></span></th>
			<th class="woocommerce-firstdata-payment-method-exp-date"><span class="nobr"><?php esc_html_e( 'Expires', 'woocommerce-gateway-firstdata' ); ?></span></th>
			<th class="woocommerce-firstdata-payment-method-status"><span class="nobr"><?php esc_html_e( 'Status', 'woocommerce-gateway-firstdata' ); ?></span></th>
			<th class="woocommerce-firstdata-payment-method-actions"><span class="nobr"><?php esc_html_e( 'Actions', 'woocommerce-gateway-firstdata' ); ?></span></th>
		</tr>
		</thead>

		<tbody>
			<?php foreach ( $tokens as $token => $payment ) :
				$delete_url      = wp_nonce_url( add_query_arg( array( 'wc-firstdata-token' => $token, 'wc-firstdata-action' => 'delete' ) ), 'wc-firstdata-token-action' );
				$make_active_url = wp_nonce_url( add_query_arg( array( 'wc-firstdata-token' => $token, 'wc-firstdata-action' => 'make-active' ) ), 'wc-firstdata-token-action' );
				$card_image_url  = $wc_gateway_firstdata->get_card_image( $payment['type'] );
				?>
				<tr class="wc-firstdata-payment-method-label">
					<td class="wc-firstdata-payment-method-card-type">
						<?php if ( $card_image_url ) : ?>
							<img src="<?php echo esc_url( $card_image_url ); ?>" alt="<?php esc_attr_e( $payment['type'] ); ?>" title="<?php esc_attr_e( $payment['type'] ); ?>" style="vertical-align:middle;" />
						<?php else: ?>
							<?php echo esc_html( $payment['type'] ); ?>
						<?php endif; ?>
					</td>
					<td class="wc-firstdata-payment-method-account-number">
						<?php echo esc_html( $payment['last_four'] ); ?>
					</td>
					<td class="wc-firstdata-payment-method-exp-date">
						<?php echo esc_html( substr( $payment['exp_date'], 0, 2 ) . '/' . substr( $payment['exp_date'], -2 ) ); ?>
					</td>
					<td class="wc-firstdata-payment-method-status">
						<?php echo ( $payment['active'] ) ? esc_html__( 'Default', 'woocommerce-gateway-firstdata' ) : '<a href="' . esc_url( $make_active_url ) . '">' . esc_html__( 'Make Default', 'woocommerce-gateway-firstdata' ) . '</a>'; ?>
					</td>
					<td class="wc-firstdata-payment-method-actions" style="width: 1%; text-align: center;">
						<a href="<?php echo esc_url( $delete_url ); ?>" title="<?php esc_attr_e( 'Delete payment method', 'woocommerce-gateway-firstdata' ); ?>" class="wc-firstdata-delete-payment-method"></a>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>

	</table>
<?php

else :

	?><p><?php esc_html_e( 'You do not have any saved payment methods.', 'woocommerce-gateway-firstdata' ); ?></p><?php

endif;
