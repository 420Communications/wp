<?php
$field_data = !empty($args['field_data']) ? $args['field_data'] : '';
?>

<div class="<?php echo esc_attr( $field_data['class'] ); ?>">
    <div class="directorist-claimed--badge">
        <span>
            <i class="<?php atbdp_icon_type( true ); ?>-check"></i>
        </span>
        <?php echo esc_attr( $field_data['verified_text'] ); ?>
    </div> 
    <span class="directorist-claimed--tooltip">
        <?php echo esc_attr( $field_data['hover_text'] ); ?>
    </span>
</div>
