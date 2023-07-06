<?php
/**
 * @author  wpWax
 * @since   6.7
 * @version 6.7
 */

namespace Directorist;

if ( ! defined( 'ABSPATH' ) ) exit;

$listing = Directorist_Single_Listing::instance();

$args = array(
	'listing'    => $listing,
	'has_slider' => true,
	'data'       => $listing->get_slider_data(),
);
?>

<div class="directorist-card directorist-card-general-section <?php echo esc_attr($section_data['widget_name']); ?>">

	<div class="directorist-card__header">

		<h4 class="directorist-card__header--title">
			<?php directorist_icon( $section_data['icon'] ); ?>
			<?php echo esc_attr($section_data['label']); ?>
		</h4>

	</div>

	<div class="directorist-card__body">

		<div class="directorist-details-info-wrap">

			<?php Helper::get_template('single/slider', $args ); ?>

		</div>

	</div>

</div>
