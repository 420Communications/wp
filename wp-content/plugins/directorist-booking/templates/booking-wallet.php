

	<?php do_action( 'wallet_dashboard_before_content' ); ?>

	<div class="directorist-wallet-wrap">
		<div class="directorist-row">
			<div class="directorist-col-12">
				<?php
				$html = sprintf( '<h2 class="directorist-wallet-title">%s</h2>', __( 'Wallet', 'directorist - booking' ) );
				echo apply_filters( 'wallet_dashboard_content_area_title', $html );
				?>
				<div class="directorist-wallet-card-list">
					<div class="directorist-card directorist-card-wallet directorist-wallet-balance">
						<span class="directorist-card-wallet__amount">
						<?php
						echo ! empty( $total_available_amount ) ? $total_available_amount : 0;
						?>
						</span>
						<span class="directorist-card-wallet__type"><?php _e( 'Available Balance', 'directorist-booking' ); ?></span>
					</div>
					<div class="directorist-card directorist-card-wallet directorist-wallet-earning">
						<span class="directorist-card-wallet__amount">
						<?php
						echo ! empty( $total_earning ) ? $total_earning : 0;
						?>
						</span>
						<span class="directorist-card-wallet__type"><?php _e( 'Total Earnings', 'directorist-booking' ); ?></span>
					</div>
					<div class="directorist-card directorist-card-wallet directorist-wallet-orders">
						<span class="directorist-card-wallet__amount">
						<?php
						echo ! empty( $total_orders ) ? $total_orders : 0;
						?>
						</span>
						<span class="directorist-card-wallet__type"><?php _e( 'Total Orders', 'directorist-booking' ); ?></span>
					</div>
				</div><!-- ends: .directorist-wallet-card-list -->

				<div class="directorist-wallet-table">
					<div class="directorist-wallet-table__top">
						<h3><?php _e( 'Earnings', 'directorist-booking' ); ?></h3><span><?php _e( 'Fee', 'directorist-booking' ); ?>: <strong><?php echo ! empty( $commission_rate ) ? $commission_rate : 0; ?>%</strong></span>
					</div>
					<div class="directorist-table-responsive">
						<table>
							<thead>
								<tr>
									<th><?php _e( 'Items', 'directorist-booking' ); ?></th>
									<th><?php _e( 'Order ID', 'directorist-booking' ); ?></th>
									<th><?php _e( 'Date', 'directorist-booking' ); ?></th>
									<th><?php _e( 'Price', 'directorist-booking' ); ?></th>
									<th><?php _e( 'Fee', 'directorist-booking' ); ?></th>
									<th><?php _e( 'Earnings', 'directorist-booking' ); ?></th>
									<th><?php _e( 'Status', 'directorist-booking' ); ?></th>
								</tr>
							</thead>
							<tbody>
							<?php
							if ( $all_earnings ) {
								foreach ( array_reverse( $all_earnings ) as $earning ) {
									?>
									<tr>
										<td><?php echo ! empty( $earning['listing_name'] ) ? $earning['listing_name'] : ''; ?></td>
										<td>#<?php echo ! empty( $earning['order_id'] ) ? $earning['order_id'] : ''; ?></td>
										<td><span class="directorist-wallet-table-date"><?php echo ! empty( $earning['date'] ) ? $earning['date'] : ''; ?></span></td>
										<td><?php echo ! empty( $earning['amount'] ) ? atbdp_display_price( $earning['amount'], false, '', '', '', false ) : ''; ?></td>
										<td><?php echo ! empty( $earning['site_fee'] ) ? atbdp_display_price( $earning['site_fee'], false, '', '', '', false ) : ''; ?></td>
										<td><?php echo ! empty( $earning['earning'] ) ? atbdp_display_price( $earning['earning'], false, '', '', '', false ) : ''; ?></td>
										<td><?php echo ! empty( $earning['status'] ) ? $earning['status'] : ''; ?></td>
									</tr>
									<?php
								}
							}
							?>
							</tbody>
						</table>
					</div>
					<div class="directorist-wallet-table__pagination">
						<!-- Pagination markup goes here -->
					</div>
				</div><!-- ends: .directorist-wallet-table -->
			</div>
			<div class="directorist-col-lg-6">
				<div class="directorist-wallet-table directorist-wallet-table-payout-history">
					<div class="directorist-wallet-table__top">
						<h3><?php _e( 'Payout History', 'directorist-booking' ); ?></h3>
					</div>
					<div class="directorist-table-responsive">
						<table>
							<thead>
								<tr>
									<th><?php _e( 'Amount', 'directorist-booking' ); ?></th>
									<th><?php _e( 'Payout Method', 'directorist-booking' ); ?></th>
									<th><?php _e( 'Date', 'directorist-booking' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
								if ( $payouts->have_posts() ) {
									while ( $payouts->have_posts() ) :
										$payouts->the_post();
										$username = get_post_meta( get_the_ID(), '_username', true );
										if ( $author_name == $username ) {
											$amount         = get_post_meta( get_the_ID(), '_total_balance_pay', true );
											$payment_method = get_post_meta( get_the_ID(), '_payment_method', true );
											if ( 'paypal' == $payment_method ) {
												$payment_method = 'PayPal';
											} elseif ( 'bank_transfer' == $payment_method ) {
												$payment_method = 'Bank Transfer';
											}
											$t           = get_the_time( 'U' );
											$date_format = get_option( 'date_format' );
											$date        = date_i18n( $date_format, $t );
											?>
										<tr>
											<td><?php echo ! empty( $amount ) ? atbdp_display_price( $amount, false, '', '', '', false ) : ''; ?></td>
											<td><?php echo ! empty( $payment_method ) ? esc_html( $payment_method ) : 'No Payment Method'; ?></td>
											<td><span class="directorist-wallet-table-date"><?php echo ! empty( $date ) ? esc_html( $date ) : ''; ?></span></td>
										</tr>
											<?php
										}
								  endwhile;
									wp_reset_query();
								}
								?>
							</tbody>
						</table>
					</div>
					<div class="directorist-wallet-table__pagination">
						<!-- Pagination markup goes here -->
					</div>
				</div><!-- ends: .directorist-wallet-table -->
			</div>
			<?php
				$current_user          = wp_get_current_user();
				$payment_method        = get_user_meta( $current_user->ID, 'bdb_payment_method', true );
				$paypal_checked        = ( ! empty( $payment_method ) && 'paypal' == $payment_method ) ? 'checked' : '';
				$bank_transfer_checked = ( ! empty( $payment_method ) && 'bank_transfer' == $payment_method ) ? 'checked' : '';
				$bdb_other_checked     = ( ! empty( $payment_method ) && 'bdb_other' == $payment_method ) ? 'checked' : '';
				$paypal_email          = get_user_meta( $current_user->ID, 'bdb_paypal_email', true );
				$bank_details          = get_user_meta( $current_user->ID, 'bdb_bank_details', true );
				$other_details         = get_user_meta( $current_user->ID, 'bdb_other_details', true );
			?>
			<div class="directorist-col-lg-6">
				<div class="directorist-card directorist-wallet-payment-method">
					<div class="directorist-card__header directorist-wallet-payment-method__title">
						<h3><?php _e( 'Payout Method', 'directorist-booking' ); ?></h3>
					</div>
					<div class="directorist-card__body">
						<form id="bdb-commission-payment-method" method="post" enctype="multipart/form-data" action="<?php echo esc_url( self_admin_url( 'admin-ajax.php' ) ); ?>">
							<div class="directorist-wallet-payment-method__card">
								<div class="directorist-wallet-payment-method__input">
									<div class="directorist-radio directorist-radio-circle">
										<input type="radio" name="bdb_payment_method" class="bdb_payment_method" id="paypal2" value="paypal" <?php echo $paypal_checked; ?>>
										<label for="paypal2" class="directorist-radio__label"><?php _e( 'Paypal', 'directorist-booking' ); ?></label>
									</div>
								</div>
								<div class="directorist-wallet-payment-method__fields">
									<div class="directorist-form-group">
										<label for="bdb-paypal-email"><?php _e( 'Paypal Email', 'directorist-booking' ); ?></label>
										<input type="text" id="bdb-paypal-email" class="directorist-form-element" placeholder="paypal@gmail.com" value="<?php echo ! empty( $paypal_email ) ? esc_attr( $paypal_email ) : ''; ?>">
									</div>
								</div>
							</div><!-- ends: .directorist-wallet-payment-method__card -->
							<div class="directorist-wallet-payment-method__card">
								<div class="directorist-wallet-payment-method__input">
									<div class="directorist-radio directorist-radio-circle">
										<input type="radio" name="bdb_payment_method" class="bdb_payment_method" id="bank-transfer" value='bank_transfer' <?php echo $bank_transfer_checked; ?>>
										<label for="bank-transfer" class="directorist-radio__label"><?php _e( 'Bank Transfer', 'directorist-booking' ); ?></label>
									</div>
								</div>
								<div class="directorist-wallet-payment-method__fields">
									<div class="directorist-form-group">
										<label for="bdb-bank-details"><?php _e( 'Bank Details', 'directorist-booking' ); ?></label>
										<textarea id="bdb-bank-details" class="directorist-form-element"><?php echo ! empty( $bank_details ) ? esc_attr( $bank_details ) : ''; ?></textarea>
									</div>
								</div>
							</div><!-- ends: .directorist-wallet-payment-method__card -->
							<div class="directorist-wallet-payment-method__card">
								<div class="directorist-wallet-payment-method__input">
									<div class="directorist-radio directorist-radio-circle">
										<input type="radio" name="bdb_payment_method" class="bdb_payment_method" id="bdb_other" value='bdb_other' <?php echo $bdb_other_checked; ?>>
										<label for="bdb_other" class="directorist-radio__label"><?php _e( 'Other', 'directorist-booking' ); ?></label>
									</div>
								</div>
								<div class="directorist-wallet-payment-method__fields">
									<div class="directorist-form-group">
										<label for="bdb_other"><?php _e( 'Details', 'directorist-booking' ); ?></label>
										<textarea id="bdb-other-details" class="directorist-form-element"><?php echo ! empty( $other_details ) ? esc_attr( $other_details ) : ''; ?></textarea>
									</div>
								</div>
							</div><!-- ends: .directorist-wallet-payment-method__card -->
							<p id="directorist-booking-payment-notice"></p>
							<input type="hidden" name='bdb_payment_method_nonce' id='bdb_payment_method_nonce' value='<?php echo wp_create_nonce( '_bdb_payment_method_nonce' ); ?>' />
							<button class="directorist-btn directorist-btn-md directorist-btn-primary directorist-booking-btn-save-card" id="bdb_submit_payment_method" type="submit"><?php _e( 'Save Card', 'directorist-booking' ); ?></button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div><!-- ends: .wallet-wrapper -->
	<?php do_action( 'wallet_dashboard_after_content' ); ?>
