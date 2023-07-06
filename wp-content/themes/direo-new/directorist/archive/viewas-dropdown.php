<?php
/**
 * @author  wpWax
 * @since   6.6
 * @version 6.7
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="view-mode">
	<a class="action-btn ab-grid" href="<?php echo add_query_arg('view', 'grid'); ?>">
		<?php directorist_icon( 'la la-th-large' ); ?>
	</a>
	<a class="action-btn ab-list" href="<?php echo add_query_arg('view', 'list'); ?>">
		<?php directorist_icon( 'la la-th-list' ); ?>
	</a>
	<a class="action-btn ab-map" href="<?php echo add_query_arg('view', 'map'); ?>">
		<?php directorist_icon( 'la la-map' ); ?>
	</a>
</div>