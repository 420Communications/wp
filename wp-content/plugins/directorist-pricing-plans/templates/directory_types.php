<?php
/**
 * Multi directory navigation template.
 * Mostly used on listings archive view.
 *
 * @author  wpWax
 * @since   6.6
 * @version 7.0.5.3
 */

use \Directorist\Helper;

if ( ! defined( 'ABSPATH' ) ) exit;

$current_directory_type = ( ! empty( $_GET['directory_type'] ) ? $_GET['directory_type'] : '' );

?>
<div class="<?php Helper::directorist_container_fluid(); ?>">
	<div class="directorist-type-nav">
		<ul class="directorist-type-nav__list">

			<?php 
            foreach ( $directory_types as $term ) : 
                $general_config = get_term_meta( $term->term_id, 'general_config', true );
            ?>
				<li class="<?php echo ( ( $current_directory_type === $term->slug ) ? 'current': '' ); ?>">
					<a class="directorist-type-nav__link" href="<?php echo esc_url( directorist_get_directory_type_nav_url( $term->slug ) ); ?>">
                        <span class="<?php echo esc_attr( $general_config['icon'] ); ?>"></span>
                        <?php
                        echo esc_html( $term->name );?>
                    </a>
				</li>

			<?php endforeach; ?>

		</ul>
	</div>
</div>
