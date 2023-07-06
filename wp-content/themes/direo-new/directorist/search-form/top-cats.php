<?php
/**
 * @author  wpWax
 * @since   6.6
 * @version 7.4.0
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
					<?php directorist_icon( get_cat_icon( $cat->term_id ), true, 'color' . $i ); ?>
					<p><?php echo esc_html( $cat->name ); ?></p>
				</a>
			</li>
			
		<?php endforeach; ?>

	</ul>
</div>