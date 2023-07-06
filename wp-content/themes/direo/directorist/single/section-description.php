<?php
/**
 * @author  wpWax
 * @since   6.7
 * @version 6.7
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$content = $listing->get_contents();

if ( ! $content ) return;
?>
<div class="directorist-card directorist-single-listing-header">

	<div class="directorist-card__header directorist-flex directorist-align-center directorist-justify-content-between">
		<h4 class="directorist-card__header--title"><?php directorist_icon( $icon );?></i><?php echo esc_html( $label );?></h4>
	</div>

	<div class="directorist-card__body">
		<div class="directorist-listing-details">
 
			<?php if ( $content ): ?>
				<div class="directorist-listing-details__text">
					<?php echo wp_kses_post( $content ); ?>
				</div>
			<?php endif; ?>	
		</div>
	</div>
</div>