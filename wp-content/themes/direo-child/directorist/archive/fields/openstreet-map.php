<?php
/**
 * @author  wpWax
 * @since   6.6
 * @version 7.3.1.2
 */

$listingID 	 	   = get_the_ID();
$authorID  	 	   = get_post_field( 'post_author', $listingID );
$profilePic  	   = get_user_meta( $authorID, 'pro_pic', true );
$authorImage 	   = wp_get_attachment_image_src( $profilePic );

if(!empty($authorImage)) {
	$imgURL = $authorImage[0];
} else {
	$avatarURL  = get_avatar_url( $authorID );
	$imgURL 	= $avatarURL;
}

?>
<div class='atbdp-body atbdp-map embed-responsive embed-responsive-16by9 atbdp-margin-bottom'>
	<?php if ( ! empty( $display_image_map ) ) { ?>
		<div class='media-left'>
			<?php if ( ! $disable_single_listing ) { ?>
				<a href='<?php echo esc_url( get_the_permalink() ); ?>'>
				<?php
			}

			if ( !empty($imgURL) ) { ?>
				<img src='<?php echo esc_url( $imgURL ); ?>' alt='<?php echo esc_attr( get_the_title() ); ?>'>
				<?php
			}

			if ( ! $disable_single_listing ) { ?>
				</a>
				<?php
			}
			?>
		</div>
		<?php
	}
	?>

	<div class='media-body'>
		<div class='atbdp-listings-title-block'>
			<h3 class='atbdp-no-margin'>
				<a href='<?php echo esc_url( get_the_permalink() ); ?>'><?php echo get_the_title($listingID); ?></a>
			</h3>
		</div>
		<?php
		if ( ! empty( $ls_data['address'] ) ) {
			if ( ! empty( $display_address_map ) ) { ?>
				<div class='osm-iw-location'>
					<span class='<?php atbdp_icon_type( true ); ?>-map-marker'></span>
					<a href='./' class='map-info-link'><?php echo esc_html( $ls_data['address'] ); ?></a>
				</div>
				<?php
			}

			if ( ! empty( $display_direction_map ) ) { ?>
				<div class='osm-iw-get-location'>
					<a href='http://www.google.com/maps?daddr=<?php echo esc_attr( $ls_data['manual_lat'] ) . ',' . esc_attr( $ls_data['manual_lng'] ); ?>' target='_blank'><?php esc_html_e( 'Get Directions', 'directorist' );?></a>
					<span class='<?php atbdp_icon_type( true ); ?>-arrow-right'></span>
				</div>
				<?php
			}
		}
		?>
	</div>
</div>