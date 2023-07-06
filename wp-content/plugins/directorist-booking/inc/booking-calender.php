<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;
/**
 * Directorist_Rent_Calendar class
 */
class Directorist_Rent_Calendar {

	private $weekDayName                = array("Mon","Tue","Wed","Thu","Fri","Sat","Sun");
	private $currentDay                 = 0;
	private $currentMonth 				= 0;
	private $currentYear 				= 0;
	private $currentMonthStart 			= null;
	private $currentMonthDaysLength 	= null;
	public $listing_id;

	function __construct() {

		$this->currentYear 				= date( "Y", time() );
		$this->currentMonth 			= date( "m", time() );

		$this->currentMonthStart 		= $this->currentYear . '-' . $this->currentMonth . '-01';
		$this->currentMonthDaysLength 	= date( 't', strtotime( $this->currentMonthStart ) );

		add_action( 'wp_ajax_directorist_rent_calendar', array( $this, 'getCalendarAJAX' ) );
		add_action( 'wp_ajax_nopriv_directorist_rent_calendar', array( $this, 'getCalendarAJAX') );

		add_action( 'wp_ajax_dashboard_rent_calendar', array( $this, 'dashCalendarAJAX' ) );

		add_action( 'save_post_at_biz_dir', array( $this, 'after_listing_inserted' ), 10, 3 );

	}

	public function after_listing_inserted( $listing_id = 0, $post = null ) {



			$booking_type				= get_post_meta( $listing_id, '_bdb_booking_type', true );
			$calender_unavailable       = get_post_meta( $listing_id, '_bdb_calender_unavailable', true );
			$calender_price             = get_post_meta( $listing_id, '_bdb_calender_price', true );

			if( ! empty( $booking_type ) && 'rent' == $booking_type ) {

				if( ! empty( $calender_unavailable ) ) {
					$dates = array_filter( explode( "|", $calender_unavailable ) );
					BD_Booking()->bdb_booking_database->update_reservations( $listing_id, $dates );
				}

				if( ! empty( $calender_price ) ) {
					$prices = json_decode( $calender_price, true );
					BD_Booking()->bdb_booking_database->update_special_prices( $listing_id, $prices );
				}

			}


	}

	function getCalendarAJAX(){

		if ( ! empty ( $_POST ['year'] ) ) {
			$this->currentYear = $_POST ['year'];
		}
		if ( ! empty ( $_POST ['month'] ) ) {
			$this->currentMonth = $_POST ['month'];
		}
		$this->currentMonthStart 		= $this->currentYear . '-' . $this->currentMonth . '-01';
		$this->currentMonthDaysLength 	= date( 't', strtotime( $this->currentMonthStart ) );

		$result['type'] = 'success';
		$result['response'] = $this->getCalendarHTML();
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
	      $result = json_encode($result);
	      echo $result;
	   }
	   else {
	      header('Location: '.$_SERVER['HTTP_REFERER']);
	   }
	   die();
	}

	function dashCalendarAJAX(){

		if ( ! empty ( $_POST ['year'] ) ) {
			$this->currentYear = $_POST ['year'];
		}
		if ( ! empty ( $_POST ['month'] ) ) {
			$this->currentMonth = ( 1 == strlen( $_POST ['month'] ) ) ? '0' . $_POST ['month'] : $_POST ['month'];
		}
		$this->currentMonthStart 		= $this->currentYear . '-' . $this->currentMonth . '-01';
		$this->currentMonthDaysLength 	= date( 't', strtotime( $this->currentMonthStart ) );

		$result['type'] = 'success';
		$result['response'] = $this->dashboard_calendar();
		$result['pricing']  = dashboardPrice();
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
	      $result = json_encode($result);
	      echo $result;
	   }
	   else {
	      header('Location: '.$_SERVER['HTTP_REFERER']);
	   }
	   die();
	}

	function dashboard_pricing() {
		$listing_id = ! empty( $_POST['listingId'] ) ? esc_attr( $_POST['listingId'] ) : '';
	}

	function getCalendarHTML() {

		$calendarHTML = '<div id="directorist-calendar-outer-container">';
		$calendarHTML .= '<table id="directorist-calendar-outer">';
		$calendarHTML .= '<thead><tr><th class="directorist-calendar-nav" colspan="7">' . $this->getCalendarNavigation() . '</th></tr>';
		$calendarHTML .= '<tr class="directorist-week-name-title">' . $this->getWeekDayName () . '</tr></thead>';
		$calendarHTML .= '<tbody class="directorist-week-day-cell">' . $this->getWeekDays () . '</tbody>';
		$calendarHTML .= '</table>';
		$calendarHTML .= '</div>';
		return $calendarHTML;
	}

	function dashboard_calendar() {
		$calendarHTML = '<div class="directorist-calendar-nav">' . $this->dashCalendarNavigation() . '</div>';
		$calendarHTML .= '<div class="directorist-calendar-outer-container-des" id="directorist-calendar-outer-container">';
		$calendarHTML .= '<table id="directorist-calendar-outer">';
		$calendarHTML .= '<thead><tr class="directorist-week-name-title">' . $this->getWeekDayName () . '</tr></thead>';
		$calendarHTML .= '<tbody class="directorist-week-day-cell">' . $this->dashboard_week_days () . '</tbody>';
		$calendarHTML .= '</table>';
		$calendarHTML .= '</div>';
		return $calendarHTML;
	}

	function getCalendarNavigation() {

		$prevMonthYear = date ( 'm,Y', strtotime ( $this->currentMonthStart. ' -1 Month'  ) );
		$prevMonthYearArray = explode(",",$prevMonthYear);
		$nextMonthYear = date ( 'm,Y', strtotime ( $this->currentMonthStart . ' +1 Month'  ) );
		$nextMonthYearArray = explode(",",$nextMonthYear);

		$navigationHTML = '<div class="directorist-calendar-nav__wrapper"><div class="directorist-prev" data-prev-month="' . $prevMonthYearArray[0] . '" data-prev-year = " ' . $prevMonthYearArray[1]. '"><i class="las la-angle-left"></i></div>';
		$navigationHTML .= '<div class="directorist-content"><span id="currentMonth">' . date ( 'M ', strtotime ( $this->currentMonthStart ) ) . '</span>';
		$navigationHTML .= '<span contenteditable="true" id="currentYear">'. date ( 'Y', strtotime ( $this->currentMonthStart ) ) . ' ' . '</span></div>';
		$navigationHTML .= '<div class="directorist-next" data-next-month="' . $nextMonthYearArray[0] . '" data-next-year = "' . $nextMonthYearArray[1]. '"><i class="las la-angle-right"></i></div></div>';
		return $navigationHTML;
	}

	function dashCalendarNavigation() {

		$prevMonthYear = date ( 'm,Y', strtotime ( $this->currentMonthStart. ' -1 Month'  ) );
		$prevMonthYearArray = explode(",",$prevMonthYear);
		$nextMonthYear = date ( 'm,Y', strtotime ( $this->currentMonthStart . ' +1 Month'  ) );
		$nextMonthYearArray = explode(",",$nextMonthYear);

		$navigationHTML = '<div class="directorist-calendar-nav__wrapper"><div class="directorist-dash-prev" data-prev-month="' . $prevMonthYearArray[0] . '" data-prev-year = "' . $prevMonthYearArray[1]. '"><i class="las la-angle-left"></i></div>';
		$navigationHTML .= '<div class="directorist-content"><span id="currentMonth">' . date ( 'M ', strtotime ( $this->currentMonthStart ) ) . ' ' . '</span>';
		$navigationHTML .= '<span contenteditable="true" id="currentYear">'. date ( 'Y', strtotime ( $this->currentMonthStart ) ) . '</span></div>';
		$navigationHTML .= '<div class="directorist-dash-next" data-next-month="' . $nextMonthYearArray[0] . '" data-next-year = "' . $nextMonthYearArray[1]. '"><i class="las la-angle-right"></i></div></div>';
		return $navigationHTML;
	}

	function getWeekDayName() {
		$WeekDayName= '';
		foreach ( $this->weekDayName as $dayname ) {
			$WeekDayName.= '<th><div class="directorist-week-name-title-item">' . $dayname . '</th></div>';
		}
		return $WeekDayName;
	}

	function getWeekDays() {
		$weekLength 		= $this->getWeekLengthByMonth();
		$firstDayOfTheWeek 	= date( 'N', strtotime( $this->currentMonthStart ) );

		$date 				= strtotime(date("Y-m-d"));
		$today 				= date('d', $date);
		$weekDays 			= "";

		for($i = 0; $i < $weekLength; $i ++) {
			$weekDays .= '<tr>';
			for($j = 1; $j <= 7; $j ++) {
				$cellIndex = $i * 7 + $j;

				$cellValue = null;

				if ($cellIndex == $firstDayOfTheWeek) {
					$this->currentDay = 1;

				}
				if (! empty ( $this->currentDay ) && $this->currentDay <= $this->currentMonthDaysLength) {
					$cellValue = $this->currentDay;
					$this->currentDay ++;
				}

				if($cellValue){

					$weekDays .= '<td class="directorist-calendar-day ';
					if($cellValue == $today){
						$weekDays .= 'directorist-todays_date ';
					}
					if($j == 6 || $j == 7 ){
						$weekDays .= ' directorist-weekend';
					}
					$weekDays .= '"data-timestamp="'.strtotime("$cellValue.$this->currentMonth.$this->currentYear").'"';
					$weekDays .= 'data-date="'.$cellValue.'-'.$this->currentMonth.'-'.$this->currentYear.'">';
					// $weekDays .= '<div class="calendar-day-date-wrapper"><span class="calendar-day-date-name">'.$this->weekDayName[$j-1].'</span>';
					$weekDays .= '<div class="directorist-calendar-day-date-wrapper">';
					$weekDays .= '<span class="directorist-calendar-day-date">'.$cellValue.'</span></div>';
					// $weekDays .= '<div class="calendar-price">
					// 				<span>'.esc_html__('Price for day','directorist-booking').'</span>
					// 				<button  type="button">'.esc_html__('Set price','directorist-booking').'</button>
					// 			</div>';
					$currency = get_directorist_option( 'g_currency', 'USD' );
					$currency_symbol = atbdp_currency_symbol( $currency );
					$weekDays .= '<div class="directorist-calendar-price">
									<div class="directorist-calendar-price-money">
										<label class="directorist-calendar-price-money__currency">' . $currency_symbol . '
											<input type="number" placeholder="0">
										</label>
									</div>
								</div>';
					$weekDays .= '</td>';
				} else {
					$weekDays .= '<td class="directorist-empty-calendar-day"></td>';
				}
			}
			$weekDays .= '</tr>';
		}
		// $weekDays .= '<div class="directorist-coupon">
		// <div class="directorist-select ">
		// 	<select name="Select coupon" >
		// 		<option value="volvo">Select coupon</option>
		// 		<option value="saab">Select coupon</option>
		// 	</select>
		// </div>
		// <h6 class="directorist-coupon__no-found">No coupon available.</h6>
		// <p class="directorist-coupon__dialog">(Create your coupon in the Coupons section of the Dashboard).</p>
		// </div>';
		return $weekDays;

	}

	function dashboard_week_days() {

		$weekLength 		= $this->getWeekLengthByMonth();
		$firstDayOfTheWeek 	= date( 'N', strtotime( $this->currentMonthStart ) );

		$date 				= strtotime(date("Y-m-d"));
		$today 				= date('d', $date);
		$weekDays 			= "";

		for($i = 0; $i < $weekLength; $i ++) {
			$weekDays .= '<tr class="directorist-calendar-day-row">';
			for($j = 1; $j <= 7; $j ++) {
				$cellIndex = $i * 7 + $j;

				$cellValue = null;

				if ($cellIndex == $firstDayOfTheWeek) {
					$this->currentDay = 1;

				}
				if (! empty ( $this->currentDay ) && $this->currentDay <= $this->currentMonthDaysLength) {
					$cellValue = $this->currentDay;
					$this->currentDay ++;
				}

				if($cellValue){
					$day_number 	= ( strlen( $cellValue ) < 2 ) ? '0' . $cellValue : $cellValue;
					$present_date 	= $this->currentYear . '-' . $this->currentMonth . '-' . $day_number;
					$calender_data 	= searchForId( $present_date);
					$booking_class  = ! empty( $calender_data['class_name'] ) ? $calender_data['class_name'] : 'day_available';
					$calender_data_name  = ! empty( $calender_data['name'] ) ? $calender_data['name'] : '';
					$currency = get_directorist_option( 'g_currency', 'USD' );
					$currency_symbol = atbdp_currency_symbol( $currency );
					
					// $weekDays .= '<td class="directorist-calendar-day directorist-calendar-day__available ';
					$weekDays .= '<td class="directorist-calendar-day directorist-calendar-' . $booking_class . '';

					if($j == 6 || $j == 7 ){
						$weekDays .= ' directorist-weekend"';
					} else {
						$weekDays .= '"';
					}

					$weekDays .= ' data-date="'.$cellValue.'-'.$this->currentMonth.'-'.$this->currentYear.'"> <div class="directorist-calendar-day-contents">';

					$weekDays .= '<div class="directorist-calendar-day-date-wrapper">';
					$weekDays .= '<span class="directorist-calendar-day-date">'.$cellValue.'</span></div>';

						$weekDays .= '<div class="directorist-calendar-day-events">
										<div class="directorist--calendar-event-harness">
											<a class="directorist-calendar-block-event">
												<div class="directorist-calendar-event-main">
													<div class="directorist-calendar-event-main-frame">
														<div class="directorist-calendar-event-title-container">';
						if( ! empty( $calender_data['img_src'] ) ) {
							$weekDays .=									'<img class="directorist-calendar-event-img" src="'. $calender_data['img_src'].'" alt="Italian Trulli">';
						}
						$weekDays .=									'<div class="directorist-calendar-event-title">' . $calender_data_name . '</div>
														</div>
													</div>
												</div>
												<div
											</a>
										</div>
										</div>
									</div>';

					$weekDays .= '<span class="directorist-calendar-price">
									<span class="directorist-calendar-price-money">
										<span class="directorist-calendar-price-money__currency">' . $currency_symbol . '
											<span>0</span>
										</span>
									</span>
								</span>';
					$weekDays .= '</div></td>';
				} else {
					$weekDays .= '<td class="directorist-empty-calendar-day"><div class="directorist-calendar-day-contents"></div></td>';
				}
			}
			$weekDays .= '</tr>';
		}

		return $weekDays;
	}

	function getWeekLengthByMonth() {
		$weekLength =  intval ( $this->currentMonthDaysLength / 7 );
		if( $this->currentMonthDaysLength % 7 > 0 ) {
			$weekLength++;
		}
		$monthStartDay	= date( 'N', strtotime( $this->currentMonthStart ) );
		$monthEndingDay	= date( 'N', strtotime( $this->currentYear . '-' . $this->currentMonth . '-' . $this->currentMonthDaysLength ) );
		if ($monthEndingDay < $monthStartDay) {
			$weekLength++;
		}

		return $weekLength;
	}

}