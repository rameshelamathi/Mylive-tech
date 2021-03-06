<?php

/**

 * The Template for displaying product archives, including the main shop page which is a post type archive.

 *

 * Override this template by copying it to yourtheme/woocommerce/archive-product.php

 *

 * @author 		WooThemes

 * @package 	WooCommerce/Templates

 * @version     2.0.0

 */



if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly



get_header( 'shop' ); ?>



<?php if(is_shop())  {?>

<style>

.et_pb_section {padding:0} .et_pb_column {padding-bottom:0}

</style>



<?php remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20); ?>



<div class="container">

<div style="margin-top:10px">

<?php 

    echo do_shortcode("[metaslider id=4673]"); 

?></div>



<div class="et_pb_column new-lt-shop et_pb_column_4_4" >

			<div class="et_pb_text et_pb_bg_layout_light et_pb_text_align_left">

			

<div class="one_third" style="background:#fff3e9;">

					<p style="text-align: center;margin:.5rem 0 0 0"><a title="Live-Tech Support Services" href="https://www.mylive-tech.com/product-category/live-tech-support-services/"><img width="32" height="33" alt="Live-Tech Support Services" src="https://www.mylive-tech.com/wp-content/uploads/Live-Tech-Support-Services.png" class="aligncenter size-full wp-image-2833"></a><br>Technical&nbsp;Support <br> Services</p>

				</div><div class="one_third" style="background:#eaf8fc;">

					<p style="text-align: center;margin:.5rem 0 0 0"><a title="Live-Tech Hosted Cloud Services" href="https://www.mylive-tech.com/product-category/live-tech-hosted-cloud-services/"><img width="32" height="33" alt="Live-Tech Hosted Cloud Services" src="https://www.mylive-tech.com/wp-content/uploads/Live-Tech-Hosted-Cloud-Services.png" class="aligncenter size-full wp-image-2834"></a><br>Live-Tech Hosted Cloud <br>Services</p>

				</div><div class="one_third" style="background:#f9f8f5;">

					<p style="text-align: center;margin:.5rem 0 0 0"><a title="Website Development &amp; Hosting" href="https://www.mylive-tech.com/product-category/website-development-and-hosting-services/"><img width="32" height="33" alt="Website Development &amp; Hosting" src="https://www.mylive-tech.com/wp-content/uploads/Website-Development-Hosting.png" class="aligncenter size-full wp-image-2835"></a><br>Website Development <br> &amp; Hosting</p>

				</div> <div class="one_third" style="background:#eef4e7;">

					<p style="text-align: center;margin:.5rem 0 0 0"><a title="Lynk VoIP Phone Service" href="https://www.mylive-tech.com/product-category/lynk-voip-phone-service/"><img width="32" height="33" alt="Lynk VoIP Phone Service" src="https://www.mylive-tech.com/wp-content/uploads/Lynk-VoIP-Phone-Service.png" class="aligncenter size-full wp-image-2836"></a><br>Lynk VoIP Phone <br> Service</p>

				</div><div class="one_third" style="background:#f3edf6;">

					<p style="text-align: center;margin:.5rem 0 0 0"><a title="Computers, Gadgets &amp; Hardware" href="https://www.mylive-tech.com/product-category/computers-gadgets-and-hardware/"><img width="32" height="33" alt="Computers, Gadgets & Hardware" src="https://www.mylive-tech.com/wp-content/uploads/Computers-Gadgets-Hardware.png" class="aligncenter size-full wp-image-2836"></a><br>Computers, Gadgets <br> &amp; Hardware</p>

				</div><div class="one_third" style="background:#f1f1f1;">

					<p style="text-align: center;margin:.5rem 0 0 0"><a title="Software &amp; Productivity Tools" href="https://www.mylive-tech.com/product-category/software/"><img width="32" height="33" alt="Software & Productivity Tools" src="https://www.mylive-tech.com/wp-content/uploads/Software-Productivity-Tools.png" class="aligncenter size-full wp-image-2836"></a><br>Software &amp; Productivity <br> Tools</p>

				</div>

<p>&nbsp;</p>



<div class="clearfix"> </div>



		</div> <!-- .et_pb_text -->

		</div>



</div>



<?php } ?>



	<?php

		/**

		 * woocommerce_before_main_content hook

		 *

		 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)

		 * @hooked woocommerce_breadcrumb - 20

		 */

		do_action( 'woocommerce_before_main_content' );

	

	?>

    

   <?php if(!is_shop())  {?>



		<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>



			<h1 class="page-title"><?php woocommerce_page_title(); ?></h1>



		<?php endif; ?>



<?php } ?>



		<?php do_action( 'woocommerce_archive_description' ); ?>

        <?php if ( have_posts() ) : ?>

        

        <?php woocommerce_product_loop_start(); ?>

        <?php woocommerce_product_subcategories(); ?>

        <?php endif; ?>

        <?php woocommerce_product_loop_end(); ?>

       



		<?php if ( have_posts() ) : ?>



<div class="lt-shop-before">

			<?php

				/**

				 * woocommerce_before_shop_loop hook

				 *

				 * @hooked woocommerce_result_count - 20

				 * @hooked woocommerce_catalog_ordering - 30

				 */

				do_action( 'woocommerce_before_shop_loop' );

			?>

            </div>



			<?php woocommerce_product_loop_start(); ?>



				<?php /** woocommerce_product_subcategories(); **/ ?>



				<?php while ( have_posts() ) : the_post(); ?>



					<?php wc_get_template_part( 'content', 'product' ); ?>



				<?php endwhile; // end of the loop. ?>



			<?php woocommerce_product_loop_end(); ?>



			<?php

				/**

				 * woocommerce_after_shop_loop hook

				 *

				 * @hooked woocommerce_pagination - 10

				 */

				do_action( 'woocommerce_after_shop_loop' );

			?>



		<?php elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>



			<?php wc_get_template( 'loop/no-products-found.php' ); ?>



		<?php endif; ?>



	<?php

		/**

		 * woocommerce_after_main_content hook

		 *

		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)

		 */

		do_action( 'woocommerce_after_main_content' );

	?>



	<?php

		/**

		 * woocommerce_sidebar hook

		 *

		 * @hooked woocommerce_get_sidebar - 10

		 */

		do_action( 'woocommerce_sidebar' );

	?>



<?php get_footer( 'shop' ); ?>