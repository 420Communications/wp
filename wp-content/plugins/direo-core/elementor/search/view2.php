<?php
/**
 * Description.
 *
 * @package WordPress
 * @author  WpWax
 * @since   1.0
 * @version 1.0
 */

$text_field           = $settings['text_field'];
$location_field       = $settings['location_field'];
$text_field_label     = $settings['text_field_label'];
$text_field_ph        = $settings['text_field_ph'];
$location_field_label = $settings['location_field_label'];
$location_field_ph    = $settings['location_field_ph'];
$search_btn_ph        = $settings['search_btn_ph'];

$require_text                = get_directorist_option( 'require_search_text' ) ? 'required' : '';
$require_loc                 = get_directorist_option( 'require_search_location' ) ? 'required' : '';
$search_location_address     = get_directorist_option( 'search_location_address', 'address' );
$search_placeholder          = $text_field_ph ? $text_field_ph : get_directorist_option( 'search_placeholder', esc_attr_x( 'Ex: shopping, restaurant...', 'placeholder', 'direo-core' ) );
$search_location_placeholder = $location_field_ph ? $location_field_ph : get_directorist_option( 'search_location_placeholder', esc_html__( 'Your city...', 'direo-core' ) );
$search_listing_text         = $search_btn_ph ? $search_btn_ph : get_directorist_option( 'search_listing_text', esc_html__( 'Search', 'direo-core' ) );

$query_args = array(
	'parent'             => 0,
	'term_id'            => 0,
	'hide_empty'         => 0,
	'orderby'            => 'name',
	'order'              => 'asc',
	'show_count'         => 0,
	'single_only'        => 0,
	'pad_counts'         => true,
	'immediate_category' => 0,
	'active_term_id'     => 0,
	'ancestors'          => array(),
);

do_action( 'atbdp_before_search_form' ); ?>

<div class="row atbdp-search-form atbdp-search-form--two">
	<?php if ( $text_field ) { ?>
		<div class="single_search_field search_query">
			<span class="search_query__label"><?php echo esc_attr( $text_field_label ); ?></span>
			<input class="form-control search_fields" type="text" name="q" <?php echo esc_attr( $require_text ); ?> autocomplete="off" placeholder="<?php echo esc_html( $search_placeholder ); ?>">

			<?php
			$args = array(
				'type'          => ATBDP_POST_TYPE,
				'parent'        => 0,
				'orderby'       => 'count',
				'order'         => 'desc',
				'hide_empty'    => 1,
				'number'        => 5,
				'taxonomy'      => ATBDP_CATEGORY,
				'no_found_rows' => true,

			);
			$top_categories = get_categories( $args );

			if ( $top_categories ) {
				?>
				<div class="directory_home_category_area">
					<ul>
						<?php
						$i = 1;
						foreach ( $top_categories as $cat ) {
							$icon      = get_cat_icon( $cat->term_id );
							$icon_type = substr( $icon, 0, 2 );
							$icon      = 'la' === $icon_type ? $icon_type . ' ' . $icon : 'fa ' . $icon;
							$url       = class_exists( 'Directorist_Base' ) ? ATBDP_Permalink::atbdp_get_category_page( $cat ) : '';
							$bg_color  = 'color-' . $i;

							echo sprintf( '<li><a href="%s"><span class="sc-icon %s %s" aria-hidden="true"></span> <span>%s</span></a></li>', esc_url( $url ), esc_attr( $icon ), esc_attr( $bg_color ), esc_attr( $cat->name ) );
							$i++;
							$i = $i > 6 ? $i = 1 : $i;
						}
						?>
					</ul>
				</div>
				<?php
			}
			?>

		</div>
		<?php
	}

	if ( $location_field ) {
		if ( 'listing_location' === $search_location_address ) {
			?>
			<div class="single_search_field search_location">
				<select <?php echo esc_attr( $require_loc ); ?> name="in_loc" class="search_fields form-control" id="at_biz_dir-location">
					<option value=""><?php echo esc_html( $search_location_placeholder ); ?></option>
					<?php echo search_category_location_filter( $query_args, ATBDP_LOCATION ); ?>
				</select>
			</div>
			<?php
		} else {
			wp_enqueue_script( 'atbdp-geolocation' );
			$address            = ! empty( $_GET['address'] ) ? $_GET['address'] : '';
			$select_listing_map = get_directorist_option( 'select_listing_map', 'google' );
			?>
			<div class="single_search_field atbdp_map_address_field">
				<div class="atbdp_get_address_field">
					<span class="search_query__label"><?php echo esc_attr( $location_field_label ); ?></span>
					<input type="text" id="address" name="address" autocomplete="off" value="<?php echo esc_attr( $address ); ?>" placeholder="<?php echo esc_html( $search_location_placeholder ); ?>" <?php echo esc_attr( $require_loc ); ?> class="form-control location-name">
					<!-- <span class="atbd_get_loc la la-crosshairs"></span> -->
				</div>
				<?php echo 'google' !== $select_listing_map ? wp_kses_post( '<div class="address_result"></div>' ) : ''; ?>
				<input type="hidden" id="cityLat" name="cityLat" value="" />
				<input type="hidden" id="cityLng" name="cityLng" value="" />
			</div>
			<?php
		}
	}
	?>
	<div class="atbd_submit_btn"><button type="submit" class="btn-gradient btn-gradient-two"><?php echo esc_attr( $search_listing_text ); ?> </button></div>

</div>
