<?php
/**
 * @author  wpWax
 * @since   6.6
 * @version 7.3.1
 */

if ( ! defined( 'ABSPATH' ) ) exit;
$i = 0;
?>

<div class="directorist-listing-category-top">
	<ul>

		<?php foreach ( $top_categories as $key => $cat ): 
			$i = ( 4 == $i ) ? 0 : $i; $i++;
			?>

			<li>
				<a href="<?php echo esc_url( ATBDP_Permalink::atbdp_get_category_page( $cat ) ); ?>">
					<span class="<?php echo esc_attr( $searchform->category_icon_class( $cat ) ) . ' color' . $i; ?> "></span>
					<p><?php echo esc_html( $cat->name ); ?></p>
				</a>
			</li>
			
		<?php endforeach; ?>

	</ul>
</div>