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
		<span class="la la-th-large"></span>
	</a>
	<a class="action-btn ab-list" href="<?php echo add_query_arg('view', 'list'); ?>">
		<span class="la la-th-list"></span>
	</a>
	<a class="action-btn ab-map" href="<?php echo add_query_arg('view', 'map'); ?>">
		<span class="la la-map"></span>
	</a>
</div>