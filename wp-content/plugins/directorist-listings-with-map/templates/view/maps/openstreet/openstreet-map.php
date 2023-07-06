<?php global $bdmv_listings;
$bdmv_listings->data['listings']->load_inline_openstreet_map([
    'zoom' => ! empty( $_POST['map_zoom_level'] ) ? $_POST['map_zoom_level'] : $bdmv_listings->get_data('map_zoom_level')
]);