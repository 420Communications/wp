<?php
/**
 * @author  WpWax
 * @since   1.10.05
 * @version 1.0
 */

if ( post_password_required() ) {
	return;
}

$left_icon  = class_exists( 'Directorist_Base' ) ? directorist_icon( 'la la-long-arrow-left', false ) : '';
$right_icon = class_exists( 'Directorist_Base' ) ? directorist_icon( 'la la-long-arrow-right', false ) : '';

if ( have_comments() ) { ?>
	<div class="comments-area m-top-50 m-bottom-60" id="comments">
		<div class="comment-title">
			<h3> <?php echo get_comments_number() . esc_html__( ' Comments', 'direo' ); ?> </h3>
		</div>

		<?php if ( get_comments_number() >= 1 ) { ?>
			<div class="comment-lists">
				<ul class="media-list list-unstyled">
					<?php
					wp_list_comments(
						array(
							'callback' => 'direo_comment',
						)
					)
					?>
				</ul>
			</div>
		<?php } ?>

		<div class="m-top-50">
			<nav class="navigation pagination d-flex justify-content-center" role="navigation">
				<div class="nav-links">
					<?php
					paginate_comments_links(
						array(
							'prev_text' => $left_icon,
							'next_text' => $right_icon,
						)
					);
					?>
				</div>
			</nav>
		</div>
	</div>
	<?php
}

if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) { ?>
	<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'direo' ); ?></p>
	<?php
}

comment_form();
