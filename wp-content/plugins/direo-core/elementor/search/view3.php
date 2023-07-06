<?php
/**
 * Description.
 *
 * @package WordPress
 * @author  WpWax
 * @since   1.0
 * @version 1.0
 */

$text_field       = $settings['text_field'];
$text_field_label = $settings['text_field_label'];
$text_field_ph    = $settings['text_field_ph'];
$search_btn_ph    = $settings['search_btn_ph'];

$require_text        = get_directorist_option( 'require_search_text' ) ? 'required' : '';
$search_placeholder  = $text_field_ph ? $text_field_ph : get_directorist_option( 'search_placeholder', esc_attr_x( 'What are you looking for?', 'placeholder', 'direo-core' ) );
$search_listing_text = $search_btn_ph ? $search_btn_ph : get_directorist_option( 'search_listing_text', esc_html__( 'Search', 'direo-core' ) );
?>

<?php do_action( 'atbdp_before_search_form' ); ?>

<div class="row atbdp-search-form atbdp-search-form--three">
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

			if ( $top_categories ) { ?>
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
			<?php } ?>
		</div>
	<?php } ?>

	<div class="atbd_submit_btn"> <button type="submit" class="btn-gradient btn-gradient-two"> <?php echo esc_attr( $search_listing_text ); ?> </button></div>
</div>
