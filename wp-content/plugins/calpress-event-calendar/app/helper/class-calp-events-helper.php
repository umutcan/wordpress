<?php
//
//  class-calp-events-helper.php
//  This file was originally created as part of the all-in-one-event-calendar it has been modified by the Calpress team
//
//  Created by The Seed Studio on 2011-07-13.
//  Updated by the Calpress Team on 2012-03-01.
//

/**
 * Calp_Events_Helper class
 *
 * @package Helpers
 * @author Calpress (Modified from original work by time.ly)
 **/
class Calp_Events_Helper {
	/**
	 * _instance class variable
	 *
	 * Class instance
	 *
	 * @var null | object
	 **/
	private static $_instance = NULL;

	/**
	 * Constructor
	 *
	 * Default constructor
	 **/
	private function __construct() { }

	/**
	 * get_instance function
	 *
	 * Return singleton instance
	 *
	 * @return object
	 **/
	static function get_instance() {
		if( self::$_instance === NULL ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * get_event function
	 *
	 * Fetches the event object with the given post ID. Uses the WP cache to
	 * make this more efficient if possible.
	 *
	 * @param int $post_id  The ID of the post associated with the event
	 *
	 * @return Calp_Event  The associated event object
	 **/
	static function get_event( $post_id )
	{
		$event = wp_cache_get( $post_id, CALP_POST_TYPE );
		if( $event === false ) {
			// try to get the event instance id, if it is not set get the post id
			$instance_id = isset( $_REQUEST["instance_id"] ) ? (int) $_REQUEST["instance_id"] : false;
			$event = new Calp_Event( $post_id, $instance_id );

			if( ! $event->post_id )
				throw new Calp_Event_Not_Found( "Event with ID '$post_id' could not be retrieved from the database." );

			// Cache the event data
			wp_cache_add( $post_id, $event, CALP_POST_TYPE );
		}
		return $event;
	}

	/**
	 * get_matching_event function
	 *
	 * Return event ID by iCalendar UID, feed url, start time and whether the
	 * event has recurrence rules (to differentiate between an event with a UID
	 * defining the recurrence pattern, and other events with with the same UID,
	 * which are just RECURRENCE-IDs).
	 *
	 * @param int $uid iCalendar UID property
	 * @param string $feed Feed URL
	 * @param int $start Start timestamp (GMT)
	 * @param bool $has_recurrence Whether the event has recurrence rules
	 * @param int|null $exclude_post_id Do not match against this post ID
	 *
	 * @return object|null Matching event's post ID, or null if no match
	 **/
	function get_matching_event_id( $uid, $feed, $start, $has_recurrence = false, $exclude_post_id = null ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'calp_events';
		$query = "SELECT post_id FROM {$table_name} " .
			"WHERE ical_feed_url = %s " .
			"AND ical_uid = %s " .
			"AND start = FROM_UNIXTIME( %d ) " .
			( $has_recurrence ? 'AND NOT ' : 'AND ' ) .
			"( recurrence_rules IS NULL OR recurrence_rules = '' )";
		$args = array( $feed, $uid, $start );
		if( ! is_null( $exclude_post_id ) ) {
			$query .= 'AND post_id <> %d';
			$args[] = $exclude_post_id;
		}

		return $wpdb->get_var( $wpdb->prepare( $query, $args ) );
	}

	/**
	 * delete_event_cache function
	 *
	 * Delete cache of event
	 *
	 * @param int $pid Event post ID
	 *
	 * @return void
	 **/
	function delete_event_cache( $pid ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'calp_event_instances';
		$wpdb->query( $wpdb->prepare( "DELETE FROM $table_name WHERE post_id = %d", $pid ) );
	}

	/**
	 * cache_event function
	 *
	 * Creates a new entry in the cache table for each date that the event appears
	 * (and does not already have an explicit RECURRENCE-ID instance, given its
	 * iCalendar UID).
	 *
	 * @param object $event Event to generate cache table for
	 *
	 * @return void
	 **/
	function cache_event( &$event ) {
		global $wpdb;

		// Convert event's timestamps to local for correct calculations of
		// recurrence. Need to also remove PHP timezone offset for each date for
		// SG_iCal to calculate correct recurring instances.
        $event->start = $this->gmt_to_local( $event->start ) - date( 'Z', $event->start );
		$event->end = $this->gmt_to_local( $event->end ) - date( 'Z', $event->end );

		$evs = array();
		$e	 = array(
			'post_id' => $event->post_id,
			'start' 	=> $event->start,
			'end'   	=> $event->end,
		);
		$duration = $event->getDuration();

		// Always cache initial instance
		$evs[] = $e;

		if( $event->recurrence_rules )
		{
			$count 	= 0;
			$start  = $event->start;
			$freq 	= $event->getFrequency();

			$freq->firstOccurrence();
			while( ( $next = $freq->nextOccurrence( $start ) ) > 0 &&
						 $count < 1000 )
			{
				$count++;
				$start      = $next;
				$e['start'] = $start;
				$e['end'] 	= $start + $duration;

				$evs[] = $e;
			}
		}

		// Make entries unique (sometimes recurrence generator creates duplicates?)
		$evs_unique = array();
		foreach( $evs as $ev ) {
			$evs_unique[ md5( serialize( $ev ) ) ] = $ev;
		}

		foreach( $evs_unique as $e )
		{
			// Find out if this event instance is already accounted for by an
			// overriding 'RECURRENCE-ID' of the same iCalendar feed (by comparing the
			// UID, start date, recurrence). If so, then do not create duplicate
			// instance of event.
			$matching_event_id = $event->ical_uid ?
					$this->get_matching_event_id(
						$event->ical_uid,
						$event->ical_feed_url,
						$start = $this->local_to_gmt( $e['start'] ) - date( 'Z', $e['start'] ),
						false,	// Only search events that don't define recurrence (i.e. only search for RECURRENCE-ID events)
						$event->post_id
					)
				: null;

			// If no other instance was found
			if( is_null( $matching_event_id ) )
			{
				$start = getdate( $e['start'] );
				$end = getdate( $e['end'] );

				/*
				// Commented out for now
				// If event spans a day and end time is not midnight, or spans more than
				// a day, then create instance for each spanning day
				if( ( $start['mday'] != $end['mday'] &&
							( $end['hours'] || $end['minutes'] || $end['seconds'] ) )
						|| $e['end'] - $e['start'] > 60 * 60 * 24 ) {
					$this->create_cache_table_entries( $e );
				// Else cache single instance of event
				} else {
					$this->insert_event_in_cache_table( $e );
				}
				*/
				$this->insert_event_in_cache_table( $e );
			}
		}
	}

	/**
	 * insert_event_in_cache_table function
	 *
	 * Inserts a new record in the cache table
	 *
	 * @param array $event Event array
	 *
	 * @return void
	 **/
	 function insert_event_in_cache_table( $event ) {
		 global $wpdb;

		 // Return the start/end times to GMT zone
		 $event['start'] = $this->local_to_gmt( $event['start'] ) + date( 'Z', $event['start'] );
		 $event['end']   = $this->local_to_gmt( $event['end'] )   + date( 'Z', $event['end'] );

		 $wpdb->query(
			 $wpdb->prepare(
				 "INSERT INTO {$wpdb->prefix}calp_event_instances " .
				 "       ( post_id,  start,               end                 ) " .
				 "VALUES ( %d,       FROM_UNIXTIME( %d ), FROM_UNIXTIME( %d ) )",
				 $event
			 )
		 );
	 }

	 /**
		* create_cache_table_entries function
		*
		* Create a new entry for each day that the event spans.
		*
		* @param array $e Event array
		*
		* @return void
		**/
		function create_cache_table_entries( $e )
		{
			global $calp_events_helper;

			// Decompose start dates into components
			$start_bits = getdate( $e['start'] );

			// ============================================
			// = Calculate the time for event's first day =
			// ============================================
			// Start time is event's original start time
			$event_start = $e['start'];
			// End time is beginning of next day
			$event_end = mktime(
				0,                       // hour
				0,                       // minute
				0,                       // second
				$start_bits['mon'],      // month
				$start_bits['mday'] + 1, // day
				$start_bits['year']      // year
			);
			// Cache first day
			$this->insert_event_in_cache_table( array( 'post_id' => $e['post_id'], 'start' => $event_start, 'end' => $event_end ) );

			// ====================================================
			// = Calculate the time for event's intermediate days =
			// ====================================================
			// Start time is previous end time
			$event_start = $event_end;
			// End time one day ahead
			$event_end += 60 * 60 * 24;
			// Cache intermediate days
			while( $event_end < $e['end'] ) {
				$this->insert_event_in_cache_table( array( 'post_id' => $e['post_id'], 'start' => $event_start, 'end' => $event_end ) );
				$event_start  = $event_end;    // Start time is previous end time
				$event_end    += 24 * 60 * 60; // Increment end time by 1 day
			}

			// ===========================================
			// = Calculate the time for event's last day =
			// ===========================================
			// Start time is already correct (previous end time)
			// End time is event end time
			// Only insert if the last event instance if span is > 0
			$event_end = $e['end'];
			if( $event_end > $event_start )
				// Cache last day
				$this->insert_event_in_cache_table( array( 'post_id' => $e['post_id'], 'start' => $event_start, 'end' => $event_end ) );
		}

	/**
	 * Returns the various preset recurrence options available (e.g.,
	 * 'DAILY', 'WEEKENDS', etc.).
	 *
	 * @return string        An associative array of pattern names to English
	 *                       equivalents
	 */
	function get_repeat_patterns() {
		// Calling functions when creating an array does not seem to work when
		// the assigned to variable is static. This is a workaround.
		static $options;
		if( !isset( $options ) ) {
			$temp = array(
				' ' => __( 'No repeat', CALP_PLUGIN_NAME ),
				'1' => __( 'Every day', CALP_PLUGIN_NAME ),
				'2' => __( 'Every week', CALP_PLUGIN_NAME ),
				'3' => __( 'Every month', CALP_PLUGIN_NAME ),
				'4' => __( 'Every year', CALP_PLUGIN_NAME ),
				'5' => '-----------',
				'6' => __( 'Custom...', CALP_PLUGIN_NAME ),
			);
			$options = $temp;
		}
		return $options;
	}

	/**
	 * Generates and returns repeat dropdown
	 *
	 * @param Integer|NULL $selected Selected option
	 *
	 * @return String Repeat dropdown
	 */
	function create_repeat_dropdown( $selected = null ) {
		$options = array(
			' ' => __( 'No repeat', CALP_PLUGIN_NAME ),
			1   => __( 'Every day', CALP_PLUGIN_NAME ),
			2   => __( 'Every week', CALP_PLUGIN_NAME ),
			3   => __( 'Every month', CALP_PLUGIN_NAME ),
			4   => __( 'Every year', CALP_PLUGIN_NAME ),
			5   => '-----------',
			6   => __( 'Custom...', CALP_PLUGIN_NAME ),
		);
		return $this->create_select_element( 'calp_repeat', $options, $selected, array( 5 ) );
	}

	/**
	 * Returns an associative array containing the following information:
	 *   string 'repeat' => pattern of repetition ('DAILY', 'WEEKENDS', etc.)
	 *   int    'count'  => end after 'count' times
	 *   int    'until'  => repeat until date (as UNIX timestamp)
	 * Elements are null if no such recurrence information is available.
	 *
	 * @param  Calp_Event  Event object to parse recurrence rules of
	 * @return array        Array structured as described above
	 **/
	function parse_recurrence_rules( &$event )
	{
		$repeat   = null;
		$count    = null;
		$until    = null;
		$end      = 0;
		if( ! is_null( $event ) ) {
			if( strlen( $event->recurrence_rules ) > 0 ) {
				$line = new SG_iCal_Line( $event->recurrence_rules );
				$rec = new SG_iCal_Recurrence( $line );
				switch( $rec->req ) {
					case 'DAILY':
						$by_day = $rec->getByDay();
						if( empty( $by_day ) ) {
							$repeat = 'DAILY';
						} elseif( $by_day[0] == 'SA+SU' ) {
							$repeat = 'WEEKENDS';
						} elseif( count( $by_day ) == 5 ) {
							$repeat = 'WEEKDAYS';
						} else {
							foreach( $by_day as $d ) {
								$repeat .= $d . '+';
							}
							$repeat = substr( $repeat, 0, -1 );
						}
						break;
					case 'WEEKLY':
						$repeat = 'WEEKLY';
						break;
					case 'MONTHLY':
						$repeat = 'MONTHLY';
						break;
					case 'YEARLY':
						$repeat = 'YEARLY';
						break;
				}
				$count = $rec->getCount();
				$until = $rec->getUntil();
				if( $until ) {
					$until = strtotime( $rec->getUntil() );
					$until += date( 'Z', $until ); // Add timezone offset
					$end = 2;
				} elseif( $count )
					$end = 1;
				else
					$end = 0;
			}
		}
		return array(
			'repeat'  => $repeat,
			'count'   => $count,
			'until'   => $until,
			'end'     => $end
		);
	}

	/**
	 * Generates and returns "End after X times" input
	 *
	 * @param Integer|NULL $count Initial value of range input
	 *
	 * @return String Repeat dropdown
	 */
	function create_count_input( $name, $count = 100, $max = 365 ) {
		ob_start();

		if( ! $count ) $count = 100;
		?>
			<input type="range" name="<?php echo $name ?>" id="<?php echo $name ?>" min="1" max="<?php echo $max ?>"
				<?php if( $count ) echo 'value="' . $count . '"' ?> />
		<?php
		return ob_get_clean();
	}

	/**
	 * create_select_element function
	 *
	 *
	 *
	 * @return void
	 **/
	function create_select_element( $name, $options = array(), $selected = false, $disabled_keys = array() ) {
		ob_start();
		?>
		<select name="<?php echo $name ?>" id="<?php echo $name ?>">
			<?php foreach( $options as $key => $val ): ?>
				<option value="<?php echo $key ?>" <?php echo $key === $selected ? 'selected="selected"' : '' ?><?php echo in_array( $key, $disabled_keys ) ? 'disabled="disabled"' : '' ?>>
					<?php echo $val ?>
				</option>
			<?php endforeach ?>
		</select>
		<?php
		return ob_get_clean();
	}

	/**
	 * create_on_the_select function
	 *
	 *
	 *
	 * @return void
	 **/
	function create_on_the_select( $f_selected = false, $s_selected = false ) {
		$ret = "";

		$first_options = array(
			'0' => __( 'first', CALP_PLUGIN_NAME ),
			'1' => __( 'second', CALP_PLUGIN_NAME ),
			'2' => __( 'third', CALP_PLUGIN_NAME ),
			'3' => __( 'fourth', CALP_PLUGIN_NAME ),
			'4' => '------',
			'5' => __( 'last', CALP_PLUGIN_NAME )
		);
		$ret = $this->create_select_element( 'calp_monthly_each_select', $first_options, $f_selected, array( 4 ) );

		$second_options = array(
			'0'   => __( 'Sunday', CALP_PLUGIN_NAME ),
			'1'   => __( 'Monday', CALP_PLUGIN_NAME ),
			'2'   => __( 'Tuesday', CALP_PLUGIN_NAME ),
			'3'   => __( 'Wednesday', CALP_PLUGIN_NAME ),
			'4'   => __( 'Thursday', CALP_PLUGIN_NAME ),
			'5'   => __( 'Friday', CALP_PLUGIN_NAME ),
			'6'   => __( 'Saturday', CALP_PLUGIN_NAME ),
			'7'   => '--------',
			'8'   => __( 'day', CALP_PLUGIN_NAME ),
			'9'   => __( 'weekday', CALP_PLUGIN_NAME ),
			'10'  => __( 'weekend day', CALP_PLUGIN_NAME )
		);

		return $ret . $this->create_select_element( 'calp_monthly_on_the_select', $second_options, $s_selected, array( 7 ) );
	}

	/**
	 * undocumented function
	 *
	 *
	 *
	 * @return void
	 **/
	function create_list_element( $name, $options = array(), $selected = array() ) {
		ob_start();
		?>
		<ul class="calp_date_select <?php echo $name?>" id="<?php echo $name?>">
			<?php foreach( $options as $key => $val ): ?>
				<li<?php echo in_array( $key, $selected ) ? 'class="calp_selected"' : '' ?>>
					<?php echo $val ?>
					<input type="hidden" name="<?php echo $name . '_' . $key ?>" value="<?php echo $key ?>" />
				</li>
			<?php endforeach ?>
		</ul>
		<input type="hidden" name="<?php echo $name ?>" value="<?php echo implode( ',', $selected ) ?>" />
		<?php
		return ob_get_clean();
	}

	/**
	 * create_montly_date_select function
	 *
	 *
	 *
	 * @return void
	 **/
	function create_montly_date_select( $selected = array() ) {
		$options = array();

		for( $i = 1; $i <= 31; ++$i )
			$options[$i] = $i;

		return $this->create_list_element( 'calp_montly_date_select', $options, $selected );
	}

	/**
	 * create_yearly_date_select function
	 *
	 *
	 *
	 * @return void
	 **/
	function create_yearly_date_select( $selected = array() ) {
		global $wp_locale;
		$options = array();

		for( $i = 1; $i <= 12; ++$i ) {
			$x = $i < 10 ? 0 . $i : $i;
			$options[$i] = $wp_locale->month_abbrev[$wp_locale->month[$x]];
		}

		return $this->create_list_element( 'calp_yearly_date_select', $options, $selected );
	}

	function get_frequency( $index ) {
		$frequency = array(
			0 => __( 'Daily', CALP_PLUGIN_NAME ),
			1 => __( 'Weekly', CALP_PLUGIN_NAME ),
			2 => __( 'Monthly', CALP_PLUGIN_NAME ),
			3 => __( 'Yearly', CALP_PLUGIN_NAME ),
		);
		return $frequency[$index];
	}

	/**
	 * row_frequency function
	 *
	 *
	 *
	 * @return void
	 **/
	function row_frequency( $visible = false, $selected = false ) {
		global $calp_view_helper;

		$frequency = array(
			0 => __( 'Daily', CALP_PLUGIN_NAME ),
			1 => __( 'Weekly', CALP_PLUGIN_NAME ),
			2 => __( 'Monthly', CALP_PLUGIN_NAME ),
			3 => __( 'Yearly', CALP_PLUGIN_NAME ),
		);

		$args = array(
		 'visible'    => $visible,
		 'frequency'  => $this->create_select_element( 'calp_frequency', $frequency, $selected )
		);
		return $calp_view_helper->get_view( 'row_frequency.php', $args );
	}

	/**
	 * row_daily function
	 *
	 * Returns daily selector
	 *
	 * @return void
	 **/
	function row_daily( $visible = false, $selected = 1 ) {
		global $calp_view_helper;

		$args = array(
		 'visible'  => $visible,
		 'count'    => $this->create_count_input( 'calp_daily_count', $selected, 365 ) . __( 'day(s)', CALP_PLUGIN_NAME )
		);
		return $calp_view_helper->get_view( 'row_daily.php', $args );
	}

	/**
	 * row_weekly function
	 *
	 * Returns weekly selector
	 *
	 * @return void
	 **/
	function row_weekly( $visible = false, $count = 1, $selected = array() ) {
		global $calp_view_helper, $wp_locale;
		$start_of_week = get_option( 'start_of_week', 1 );

		$options = array();
		// get days from start_of_week until the last day
		for( $i = $start_of_week; $i <= 6; ++$i )
			$options[$this->get_weekday_by_id( $i )] = $wp_locale->weekday_initial[$wp_locale->weekday[$i]];

		// get days from 0 until start_of_week
		if( $start_of_week > 0 ) {
			for( $i = 0; $i < $start_of_week; $i++ )
				$options[$this->get_weekday_by_id( $i )] = $wp_locale->weekday_initial[$wp_locale->weekday[$i]];
		}

		$args = array(
		 'visible'    => $visible,
		 'count'      => $this->create_count_input( 'calp_weekly_count', $count, 52 ) . __( 'week(s)', CALP_PLUGIN_NAME ),
		 'week_days'  => $this->create_list_element( 'calp_weekly_date_select', $options, $selected )
		);
		return $calp_view_helper->get_view( 'row_weekly.php', $args );
	}

	/**
	 * get_weekday_by_id function
	 *
	 * Returns weekday name in English
	 *
	 * @param int $day_id Day ID
	 *
	 * @return string
	 **/
	function get_weekday_by_id( $day_id, $by_value = false ) {
		// do not translate this !!!
		$week_days = array(
		 0 => 'SU',
		 1 => 'MO',
		 2 => 'TU',
		 3 => 'WE',
		 4 => 'TH',
		 5 => 'FR',
		 6 => 'SA'
		);

		if( $by_value ) {
			while( $_name = current( $week_days ) ) {
					if( $_name == $day_id ) {
							return key( $week_days );
					}
					next( $week_days );
			}
			return false;
		}
		else
			return $week_days[$day_id];
	}

	/**
	 * row_monthly function
	 *
	 * Returns monthly selector
	 *
	 * @return void
	 **/
	function row_monthly( $visible = false, $count = 1, $calp_monthly_each = 0, $calp_monthly_on_the = 0, $month = array(), $first = false, $second = false ) {
		global $calp_view_helper;

		$args = array(
		 'visible'              => $visible,
		 'count'                => $this->create_count_input( 'calp_monthly_count', $count, 12 ) . __( 'month(s)', CALP_PLUGIN_NAME ),
		 'calp_monthly_each'   => $calp_monthly_each,
		 'calp_monthly_on_the' => $calp_monthly_on_the,
		 'month'                => $this->create_montly_date_select( $month ),
		 'on_the_select'        => $this->create_on_the_select( $first, $second )
		);
		return $calp_view_helper->get_view( 'row_monthly.php', $args );
	}

	/**
	 * row_yearly function
	 *
	 * Returns yearly selector
	 *
	 * @return void
	 **/
	function row_yearly( $visible = false, $count = 1, $year = array(), $first = false, $second = false ) {
		global $calp_view_helper;

		$args = array(
		 'visible'              => $visible,
		 'count'                => $this->create_count_input( 'calp_yearly_count', $count, 10 ) . __( 'year(s)', CALP_PLUGIN_NAME ),
		 'year'                 => $this->create_yearly_date_select( $year ),
		 'on_the_select'        => $this->create_on_the_select( $first, $second )
		);
		return $calp_view_helper->get_view( 'row_yearly.php', $args );
	}

	/**
	 * get_all_matching_posts function
	 *
	 * Gets existing event posts that are between the interval
	 *
	 * @param int $s_time Start time
	 * @param int $e_time End time
	 *
	 * @return Array of matching event posts
	 **/
	function get_all_matching_posts( $s_time, $e_time ) {
		global $calp_calendar_helper;
		return $calp_calendar_helper->get_events_between( $s_time, $e_time );
	}

	/**
	 * get_matching_events function
	 *
	 * Get events that match with the arguments provided.
	 *
	 * @param int | bool          $start      Events start before this (GMT) time
	 * @param int | bool          $end        Events end before this (GMT) time
	 * @param array $filter       Array of filters for the events returned.
	 *                            ['cat_ids']   => non-associatative array of category IDs
	 *                            ['tag_ids']   => non-associatative array of tag IDs
	 *                            ['post_ids']  => non-associatative array of post IDs
	 *
	 * @return array Matching events
	 **/
	function get_matching_events( $start = false, $end = false, $filter = array() ) {
		global $wpdb, $calp_calendar_helper;

		// holds event_categories sql
		$c_sql = '';
		$c_where_sql = '';
		// holds event_tags sql
		$t_sql = '';
		$t_where_sql ='';
		// holds posts sql
		$p_where_sql = '';
		// holds start sql
		$start_where_sql = '';
		// holds end sql
		$end_where_sql = '';
		// hold escape values
		$args = array();

		// =============================
		// = Generating start date sql =
		// =============================
		if( $start !== false ) {
			$start_where_sql = "AND (e.start >= FROM_UNIXTIME( %d ) OR e.recurrence_rules != '')";
			$args[] = $start;
		}

		// ===========================
		// = Generating end date sql =
		// ===========================
		if( $end !== false ) {
			$end_where_sql = "AND (e.end <= FROM_UNIXTIME( %d ) OR e.recurrence_rules != '')";
			$args[] = $end;
		}

		// Get the Join (filter_join) and Where (filter_where) statements based on $filter elements specified
		$calp_calendar_helper->_get_filter_sql( $filter );

		$query = $wpdb->prepare(
			"SELECT *, e.post_id, UNIX_TIMESTAMP( e.start ) as start, UNIX_TIMESTAMP( e.end ) as end, e.allday, e.recurrence_rules, e.exception_rules,
				e.recurrence_dates, e.exception_dates, e.venue, e.country, e.address, e.city, e.province, e.postal_code,
				e.show_map, e.contact_name, e.contact_phone, e.contact_email, e.cost, e.ical_feed_url, e.ical_source_url,
				e.ical_organizer, e.ical_contact, e.ical_uid " .
			"FROM $wpdb->posts " .
				"INNER JOIN {$wpdb->prefix}calp_events AS e ON e.post_id = ID " .
				$filter['filter_join'] .
			"WHERE post_type = '" . CALP_POST_TYPE . "' " .
				"AND post_status = 'publish' " .
				$filter['filter_where'] .
				$start_where_sql .
				$end_where_sql,
			$args );

		$events = $wpdb->get_results( $query, ARRAY_A );

		foreach( $events as &$event ) {
			try{
				$event = new Calp_Event( $event );
			} catch( Calp_Event_Not_Found $n ) {
				unset( $event );
				// The event is not found, continue to the next event
				continue;
			}

			// if there are recurrence rules, include the event, else...
			if( empty( $event->recurrence_rules ) ) {
				// if start time is set, and event start time is before the range
				// it, continue to the next event
				if( $start !== false && $event->start < $start ) {
					unset( $event );
					continue;
				}
				// if end time is set, and event end time is after
				// it, continue to the next event
				if( $end !== false && $ev->end < $end ) {
					unset( $event );
					continue;
				}
			}
		}

		return $events;
	}

	/**
	 * fuzzy_string_compare function
	 *
	 * Compares string A to string B using fuzzy comparison algorithm
	 *
	 * @param String $a String to compare
	 * @param String $b String to compare
	 *
	 * @return boolean True if the two strings match, false otherwise
	 **/
	function fuzzy_string_compare( $a, $b ) {
		$percent = 0;
		similar_text( $a, $b, $percent );
		return ( $percent > 50 );
	}

	/**
	 * get_short_time function
	 *
	 * Format a short-form time for use in compressed (e.g. month) views;
	 * this is also converted to the local timezone.
	 *
	 * @param int $timestamp
	 * @param bool $convert_from_gmt Whether to convert from GMT time to local
	 *
	 * @return string
	 **/
	function get_short_time( $timestamp, $convert_from_gmt = true ) {
		$time_format = get_option( 'time_format', 'g:ia' );
		if( $convert_from_gmt )
			$timestamp = $this->gmt_to_local( $timestamp );
		return date_i18n( $time_format, $timestamp, true );
	}

	/**
	 * get_short_date function
	 *
	 * Format a short-form date for use in compressed (e.g. month) views;
	 * this is also converted to the local timezone.
	 *
	 * @param int $timestamp
	 * @param bool $convert_from_gmt Whether to convert from GMT time to local
	 *
	 * @return string
	 **/
	function get_short_date( $timestamp, $convert_from_gmt = true ) {
		if( $convert_from_gmt )
			$timestamp = $this->gmt_to_local( $timestamp );
		return date_i18n( 'M j', $timestamp, true );
	}

	/**
	 * get_medium_time function
	 *
	 * Format a medium-length time for use in other views (e.g., Agenda);
	 * this is also converted to the local timezone.
	 *
	 * @param int $timestamp
	 *
	 * @return string
	 **/
	function get_medium_time( $timestamp, $convert_from_gmt = true ) {
		$time_format = get_option( 'time_format', 'g:ia' );
		if( $convert_from_gmt )
			$timestamp = $this->gmt_to_local( $timestamp );
		return date_i18n( $time_format, $timestamp, true );
	}

	/**
	 * get_long_time function
	 *
	 * Format a long-length time for use in other views (e.g., single event);
	 * this is also converted to the local timezone.
	 *
	 * @param int $timestamp
	 * @param bool $convert_from_gmt Whether to convert from GMT time to local
	 *
	 * @return string
	 **/
	function get_long_time( $timestamp, $convert_from_gmt = true ) {
		$date_format = get_option( 'date_format', 'D, F j' );
		$time_format = get_option( 'time_format', 'g:i' );
		if( $convert_from_gmt )
			$timestamp = $this->gmt_to_local( $timestamp );
		return date_i18n( $date_format, $timestamp, true ) . ' @ ' . date_i18n( $time_format, $timestamp, true );
	}

	/**
	 * get_long_date function
	 *
	 * Format a long-length date for use in other views (e.g., single event);
	 * this is also converted to the local timezone if desired.
	 *
	 * @param int $timestamp
	 * @param bool $convert_from_gmt Whether to convert from GMT time to local
	 *
	 * @return string
	 **/
	function get_long_date( $timestamp, $convert_from_gmt = true ) {
		$date_format = get_option( 'date_format', 'D, F j' );
		if( $convert_from_gmt )
			$timestamp = $this->gmt_to_local( $timestamp );
		return date_i18n( $date_format, $timestamp, true );
	}

	/**
	 * gmt_to_local function
	 *
	 * Returns the UNIX timestamp adjusted to the local timezone.
	 *
	 * @param int $timestamp
	 *
	 * @return int
	 **/
	function gmt_to_local( $timestamp ) {
		$offset = get_option( 'gmt_offset' );
		$tz     = get_option( 'timezone_string', 'America/Los_Angeles' );

		$offset = $this->get_timezone_offset( 'UTC', $tz, $timestamp );

		if( ! $offset )
			$offset = get_option( 'gmt_offset' ) * 3600;

		return $timestamp + $offset;
	}

	/**
	 * local_to_gmt function
	 *
	 * Returns the UNIX timestamp adjusted from the local timezone to GMT.
	 *
	 * @param int $timestamp
	 *
	 * @return int
	 **/
	function local_to_gmt( $timestamp ) {
		$offset = get_option( 'gmt_offset' );
		$tz     = get_option( 'timezone_string', 'America/Los_Angeles' );

		$offset = $this->get_timezone_offset( 'UTC', $tz, $timestamp );

		if( ! $offset )
			$offset = get_option( 'gmt_offset' ) * 3600;

		return $timestamp - $offset;
	}

	/**
	 * get_timezone_offset function
	 *
	 * Returns the offset from the origin timezone to the remote timezone, in seconds.
	 *
	 * @param string $remote_tz Remote TimeZone
	 * @param string $origin_tz Origin TimeZone
	 * @param string/int $timestamp Unix Timestamp or 'now'
	 *
	 * @return int
	 **/
	/**
	 * get_timezone_offset function
	 *
	 * Returns the offset from the origin timezone to the remote timezone, in seconds.
	 *
	 * @param string $remote_tz Remote TimeZone
	 * @param string $origin_tz Origin TimeZone
	 * @param string/int $timestamp Unix Timestamp or 'now'
	 *
	 * @return int
	 **/
	function get_timezone_offset( $remote_tz, $origin_tz = null, $timestamp = false ) {
		// set timestamp to time now
		if( $timestamp == false ) {
			$timestamp = gmmktime();
		}

		if( $origin_tz === null ) {
			if( ! is_string( $origin_tz = date_default_timezone_get() ) ) {
				return false; // A UTC timestamp was returned -- bail out!
			}
		}

		try {
			$origin_dtz = new DateTimeZone( $origin_tz );
			$remote_dtz = new DateTimeZone( $remote_tz );

			// if DateTimeZone fails, throw exception
			if( $origin_dtz == false || $remote_dtz == false )
				throw new Exception( 'DateTimeZone class failed' );

			$origin_dt  = new DateTime( gmdate( 'Y-m-d H:i:s', $timestamp ), $origin_dtz );
			$remote_dt  = new DateTime( gmdate( 'Y-m-d H:i:s', $timestamp ), $remote_dtz );

			// if DateTime fails, throw exception
			if( $origin_dt == false || $remote_dt == false )
				throw new Exception( 'DateTime class failed' );

			$offset = $origin_dtz->getOffset( $origin_dt ) - $remote_dtz->getOffset( $remote_dt );
		} catch( Exception $e ) {
			return false;
		}

		return $offset;
	}

	/**
	 * A GMT-version of PHP getdate().
	 *
	 * @param int $timestamp  UNIX timestamp
	 * @return array          Same result as getdate(), but based in GMT time.
	 **/
	function gmgetdate( $timestamp = null ) {
		if( ! $timestamp ) $timestamp = time();
		$bits = explode( ',', gmdate( 's,i,G,j,w,n,Y,z,l,F,U', $timestamp ) );
		$bits = array_combine(
			array( 'seconds', 'minutes', 'hours', 'mday', 'wday', 'mon', 'year', 'yday', 'weekday', 'month', 0 ),
			$bits
		);
		return $bits;
	}

	/**
	 * time_to_gmt function
	 *
	 * Converts time to GMT
	 *
	 * @param int $timestamp
	 *
	 * @return int
	 **/
	function time_to_gmt( $timestamp ) {
		return strtotime( gmdate( 'M d Y H:i:s', $timestamp ) );
	}

	/**
	 * get_gmap_url function
	 *
	 * Returns the URL to the Google Map for the given event object.
	 *
	 * @param Calp_Event $event  The event object to display a map for
	 *
	 * @return string
	 **/
	function get_gmap_url( &$event ) {
		$location_arg = urlencode( $event->address );
		$lang         = $this->get_lang();

		return "http://www.google.com/maps?f=q&hl=" . $lang . "&source=embed&q=" . $location_arg;
	}

	/**
	 * get_lang function
	 *
	 * Returns the ISO-639 part of the configured locale. The default
	 * language is English (en).
	 *
	 * @return string
	 **/
	function get_lang() {
		$locale = explode( '_', get_locale() );

		return ( isset( $locale[0] ) && $locale[0] != '' ) ? $locale[0] : 'en';
	}

	/**
	 * get_region function
	 *
	 * Returns the ISO-3166 part of the configured locale as a ccTLD.
	 * Used for region biasing in the geo autocomplete plugin.
	 *
	 * @return string
	 **/
	function get_region() {
		$locale = explode( '_', get_locale() );

		$region = ( isset( $locale[1] ) && $locale[1] != '' ) ? strtolower( $locale[1] ) : '';

		// Primary ccTLD for United Kingdom is uk.
		return ( $region == 'gb' ) ? 'uk' : $region;
	}

	/**
	 * trim_excerpt function
	 *
	 * Generates an excerpt from the given content string. Adapted from
	 * WordPress's wp_trim_excerpt function that is not useful for applying
	 * to custom content.
	 *
	 * @param string $text The content to trim.
	 *
	 * @return string      The excerpt.
	 **/
	function trim_excerpt( $text )
	{
		$raw_excerpt = $text;

		$text = strip_shortcodes( $text );

		$text = str_replace(']]>', ']]&gt;', $text);
		$text = strip_tags($text);
		$excerpt_length = apply_filters('excerpt_length', 55);
		$excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');
		$words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
		if ( count($words) > $excerpt_length ) {
			array_pop($words);
			$text = implode(' ', $words);
			$text = $text . $excerpt_more;
		} else {
			$text = implode(' ', $words);
		}
		return apply_filters('wp_trim_excerpt', $text, $raw_excerpt);
	}

	/**
	 * filter_by_terms function
	 *
	 * Returns a subset of post IDs from the given set of post IDs that have any
	 * of the given taxonomy term IDs. This is actually useful for all posts and
	 * taxonomies in general, not just event posts and event-specific taxonomies.
	 *
	 * @param array|string $post_ids  Post IDs as an array of ints or
	 *                                comma-separated string
	 * @param array|string $term_ids  Term IDs as an array of ints or
	 *                                comma-separated string
	 *
	 * @return array                  Filtered post IDs as an array of ints
	 */
	function filter_by_terms( $post_ids, $term_ids )
	{
		global $wpdb;

		// ===============================================
		// = Sanitize provided IDs against SQL injection =
		// ===============================================
		if( ! is_array( $post_ids ) )
			$post_ids = explode( ',', $post_ids );
		foreach( $post_ids as &$post_id ) {
			$post_id = intval( $post_id );
		}
		$post_ids = join( ',', $post_ids );

		if( ! is_array( $term_ids ) )
			$term_ids = explode( ',', $term_ids );
		foreach( $term_ids as &$term_id ) {
			$term_id = intval( $term_id );
		}
		$term_ids = join( ',', $term_ids );

		$query =
			"SELECT DISTINCT p.ID " .
			"FROM $wpdb->posts p " .
				"INNER JOIN $wpdb->term_relationships tr ON p.ID = tr.object_id " .
				"INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id " .
			"WHERE p.ID IN ( " . $post_ids . " ) " .
				"AND tt.term_id IN ( " . $term_ids . " )";

		return $wpdb->get_col( $query );
	}

	/**
	 * get_category_color function
	 *
	 * Returns the color of the Event Category having the given term ID.
	 *
	 * @param int $term_id The ID of the Event Category
	 * @return string
	 */
	function get_category_color( $term_id ) {
		global $wpdb;

		$term_id = (int) $term_id;
		$table_name = $wpdb->prefix . 'calp_event_category_colors';
		$color = $wpdb->get_var( "SELECT term_color FROM {$table_name} WHERE term_id = {$term_id}" );
		return $color;
	}

	/**
	 * get_category_color_square function
	 *
	 * Returns the HTML markup for the category color square of the given Event
	 * Category term ID.
	 *
	 * @param int $term_id The Event Category's term ID
	 * @return string
	 **/
	function get_category_color_square( $term_id ) {
		$color = $this->get_category_color( $term_id );
		$cat = get_term( $term_id, 'events_categories' );
		if( ! is_null( $color ) && ! empty( $color ) )
			return '<div class="calp-category-color" style="background:' . $color . '" title="' . esc_attr( $cat->name ) . '"></div>';

		return '';
	}

	/**
	 * get_event_category_color_style function
	 *
	 * Returns the style attribute assigning the category color style to an event.
	 *
	 * @param int $term_id The Event Category's term ID
	 * @param bool $allday Whether the event is all-day
	 * @return string
	 **/
	function get_event_category_color_style( $term_id, $allday = false ) {
		$color = $this->get_category_color( $term_id );
		if( ! is_null( $color ) && ! empty( $color ) ) {
            return $color;
		}

		return '';
	}

	/**
	 * get_event_category_faded_color function
	 *
	 * Returns a faded version of the event's category color in hex format.
	 *
	 * @param int $term_id The Event Category's term ID
	 * @return string
	 **/
	function get_event_category_faded_color( $term_id ) {
		$color = $this->get_category_color( $term_id );
		if( ! is_null( $color ) && ! empty( $color ) ) {

			$color1 = substr( $color, 1 );
			$color2 = 'ffffff';

			$c1_p1 = hexdec( substr( $color1, 0, 2 ) );
			$c1_p2 = hexdec( substr( $color1, 2, 2 ) );
			$c1_p3 = hexdec( substr( $color1, 4, 2 ) );

			$c2_p1 = hexdec( substr( $color2, 0, 2 ) );
			$c2_p2 = hexdec( substr( $color2, 2, 2 ) );
			$c2_p3 = hexdec( substr( $color2, 4, 2 ) );

			$m_p1 = dechex( round( $c1_p1 * 0.3 + $c2_p1 * 0.7 ) );
			$m_p2 = dechex( round( $c1_p2 * 0.3 + $c2_p2 * 0.7 ) );
			$m_p3 = dechex( round( $c1_p3 * 0.3 + $c2_p3 * 0.7 ) );

			return '#' . $m_p1 . $m_p2 . $m_p3;
		}

		return '';
	}

	/**
	 * get_event_category_colors function
	 *
	 * Returns category color squares for the list of Event Category objects.
	 *
	 * @param array $cats The Event Category objects as returned by get_terms()
	 * @return string
	 **/
	function get_event_category_colors( $cats ) {
		$sqrs = '';

		foreach( $cats as $cat ) {
			$tmp = $this->get_category_color_square( $cat->term_id );
			if( ! empty( $tmp ) )
				$sqrs .= $tmp;
		}

		return $sqrs;
	}

	/**
	 * create_end_dropdown function
	 *
	 * Outputs the dropdown list for the recurrence end option.
	 *
	 * @param int $selected The index of the selected option, if any
	 * @return void
	 **/
	function create_end_dropdown( $selected = null ) {
		ob_start();

		$options = array(
			0 => __( 'Never', CALP_PLUGIN_NAME ),
			1 => __( 'After', CALP_PLUGIN_NAME ),
			2 => __( 'On date', CALP_PLUGIN_NAME )
		);

		?>
		<select name="calp_end" id="calp_end">
			<?php foreach( $options as $key => $val ): ?>
				<option value="<?php echo $key ?>" <?php if( $key === $selected ) echo 'selected="selected"' ?>>
					<?php echo $val ?>
				</option>
			<?php endforeach ?>
		</select>
		<?php

		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}

	/**
	 * rrule_to_text function
	 *
	 *
	 *
	 * @return void
	 **/
	function rrule_to_text( $rrule = '') {
		$txt = '';
		$rc = new SG_iCal_Recurrence( new SG_iCal_Line( 'RRULE:' . $rrule ) );
		switch( $rc->getFreq() ) {
			case 'DAILY':
				$this->_get_interval( $txt, 'daily', $rc->getInterval() );
				$this->_ending_sentence( $txt, $rc );
				break;
			case 'WEEKLY':
				$this->_get_interval( $txt, 'weekly', $rc->getInterval() );
				$this->_get_sentence_by( $txt, 'weekly', $rc );
				$this->_ending_sentence( $txt, $rc );
				break;
			case 'MONTHLY':
				$this->_get_interval( $txt, 'monthly', $rc->getInterval() );
				$this->_get_sentence_by( $txt, 'monthly', $rc );
				$this->_ending_sentence( $txt, $rc );
				break;
			case 'YEARLY':
				$this->_get_interval( $txt, 'yearly', $rc->getInterval() );
				$this->_get_sentence_by( $txt, 'yearly', $rc );
				$this->_ending_sentence( $txt, $rc );
				break;
			default:
				$txt = $rrule;
		}
		return $txt;
	}

	/**
	 * _get_sentence_by function
	 *
	 * @internal
	 *
	 * @return void
	 **/
	function _get_sentence_by( &$txt, $freq, $rc ) {
		global $wp_locale;

		switch( $freq ) {
			case 'weekly':
				if( $rc->getByDay() ) {
					if( count( $rc->getByDay() ) > 1 ) {
						// if there are more than 3 days
						// use days's abbr
						if( count( $rc->getByDay() ) > 2 ) {
							$_days = '';
							foreach( $rc->getByDay() as $d ) {
								$day = $this->get_weekday_by_id( $d, true );
								$_days .= ' ' . $wp_locale->weekday_abbrev[$wp_locale->weekday[$day]] . ',';
							}
							// remove the last ' and'
							$_days = substr( $_days, 0, -1 );
							$txt .= ' ' . _x( 'on', 'Recurrence editor - weekly tab', CALP_PLUGIN_NAME ) . $_days;
						} else {
							$_days = '';
							foreach( $rc->getByDay() as $d ) {
								$day = $this->get_weekday_by_id( $d, true );
								$_days .= ' ' . $wp_locale->weekday[$day] . ' ' . __( 'and', CALP_PLUGIN_NAME );
							}
							// remove the last ' and'
							$_days = substr( $_days, 0, -4 );
							$txt .= ' ' . _x( 'on', 'Recurrence editor - weekly tab', CALP_PLUGIN_NAME ) . $_days;
						}
					} else {
						$_days = '';
						foreach( $rc->getByDay() as $d ) {
							$day = $this->get_weekday_by_id( $d, true );
							$_days .= ' ' . $wp_locale->weekday[$day];
						}
						$txt .= ' ' . _x( 'on', 'Recurrence editor - weekly tab', CALP_PLUGIN_NAME ) . $_days;
					}
				}
				break;
			case 'monthly':
				if( $rc->getByMonthDay() ) {
					// if there are more than 2 days
					if( count( $rc->getByMonthDay() ) > 2 ) {
						$_days = '';
						foreach( $rc->getByMonthDay() as $m_day ) {
							$_days .= ' ' . $this->_ordinal( $m_day ) . ',';
						}
						$_days = substr( $_days, 0, -1 );
						$txt .= ' ' . _x( 'on', 'Recurrence editor - monthly tab', CALP_PLUGIN_NAME ) . $_days . ' ' . __( 'of the month', _PLUGIN_NAME );
					} else if( count( $rc->getByMonthDay() ) > 1 ) {
						$_days = '';
						foreach( $rc->getByMonthDay() as $m_day ) {
							$_days .= ' ' . $this->_ordinal( $m_day ) . ' ' . __( 'and', CALP_PLUGIN_NAME );
						}
						$_days = substr( $_days, 0, -4 );
						$txt .= ' ' . _x( 'on', 'Recurrence editor - monthly tab', CALP_PLUGIN_NAME ) . $_days . ' ' . __( 'of the month', _PLUGIN_NAME );
					} else {
						$_days = '';
						foreach( $rc->getByMonthDay() as $m_day ) {
							$_days .= ' ' . $this->_ordinal( $m_day );
						}
						$txt .= ' ' . _x( 'on', 'Recurrence editor - monthly tab', CALP_PLUGIN_NAME ) . $_days . ' ' . __( 'of the month', _PLUGIN_NAME );
					}
				}
				break;
			case 'yearly':
				if( $rc->getByMonth() ) {
					// if there are more than 2 months
					if( count( $rc->getByMonth() ) > 2  ) {
						$_months = '';
						foreach( $rc->getByMonth() as $_m ) {
							$_m = $_m < 10 ? 0 . $_m : $_m;
							$_months .= ' ' . $wp_locale->month_abbrev[$wp_locale->month[$_m]] . ',';
						}
						$_months = substr( $_months, 0, -1 );
						$txt .= ' ' . _x( 'on', 'Recurrence editor - yearly tab', CALP_PLUGIN_NAME ) . $_months;
					} else if( count( $rc->getByMonth() ) > 1 ) {
						$_months = '';
						foreach( $rc->getByMonth() as $_m ) {
							$_m = $_m < 10 ? 0 . $_m : $_m;
							$_months .= ' ' . $wp_locale->month[$_m] . ' ' . __( 'and', CALP_PLUGIN_NAME );
						}
						$_months = substr( $_months, 0, -4 );
						$txt .= ' ' . _x( 'on', 'Recurrence editor - yearly tab', CALP_PLUGIN_NAME ) . $_months;
					} else {
						$_months = '';
						foreach( $rc->getByMonth() as $_m ) {
							$_m = $_m < 10 ? 0 . $_m : $_m;
							$_months .= ' ' . $wp_locale->month[$_m];
						}
						$txt .= ' ' . _x( 'on', 'Recurrence editor - yearly tab', CALP_PLUGIN_NAME ) . $_months;
					}
				}
				break;
		}
	}

	/**
	 * _ordinal function
	 *
	 * @internal
	 *
	 * @return void
	 **/
	function _ordinal( $cdnl ) {
		$locale = explode( '_', get_locale() );

		if( isset( $locale[0] ) && $locale[0] != 'en' )
			return $cdnl;

		$test_c = abs($cdnl) % 10;
		$ext = ( ( abs( $cdnl ) % 100 < 21 && abs( $cdnl ) % 100 > 4 ) ? 'th'
							: ( ( $test_c < 4 ) ? ( $test_c < 3 ) ? ( $test_c < 2 ) ? ( $test_c < 1 )
							? 'th' : 'st' : 'nd' : 'rd' : 'th' ) );
		return $cdnl.$ext;
	}

	/**
	 * _get_interval function
	 *
	 * @internal
	 *
	 * @return void
	 **/
	function _get_interval( &$txt, $freq, $interval ) {
		switch( $freq ) {
			case 'daily':
				// check if interval is set
				if( ! $interval || $interval == 1 ) {
					$txt = __( 'Daily', CALP_PLUGIN_NAME );
				} else {
					if( $interval == 2 ) {
						$txt = __( 'Every other day', CALP_PLUGIN_NAME );
					} else {
						$txt = sprintf( __( 'Every %d days', CALP_PLUGIN_NAME ), $interval );
					}
				}
				break;
			case 'weekly':
				// check if interval is set
				if( ! $interval || $interval == 1 ) {
					$txt = __( 'Weekly', CALP_PLUGIN_NAME );
				} else {
					if( $interval == 2 ) {
						$txt = __( 'Every other week', CALP_PLUGIN_NAME );
					} else {
						$txt = sprintf( __( 'Every %d weeks', CALP_PLUGIN_NAME ), $interval );
					}
				}
				break;
			case 'monthly':
				// check if interval is set
				if( ! $interval || $interval == 1 ) {
					$txt = __( 'Monthly', CALP_PLUGIN_NAME );
				} else {
					if( $interval == 2 ) {
						$txt = __( 'Every other month', CALP_PLUGIN_NAME );
					} else {
						$txt = sprintf( __( 'Every %d months', CALP_PLUGIN_NAME ), $interval );
					}
				}
				break;
			case 'yearly':
				// check if interval is set
				if( ! $interval || $interval == 1 ) {
					$txt = __( 'Yearly', CALP_PLUGIN_NAME );
				} else {
					if( $interval == 2 ) {
						$txt = __( 'Every other year', CALP_PLUGIN_NAME );
					} else {
						$txt = sprintf( __( 'Every %d years', CALP_PLUGIN_NAME ), $interval );
					}
				}
				break;
		}
	}

	/**
	 * _ending_sentence function
	 *
	 * Ends rrule to text sentence
	 *
	 * @internal
	 *
	 * @return void
	 **/
	function _ending_sentence( &$txt, &$rc ) {
		if( $until = $rc->getUntil() ) {
			if( ! is_int( $until ) )
				$until = strtotime( $until );
			$txt .= ' ' . sprintf( __( 'until %s', CALP_PLUGIN_NAME ), date_i18n( get_option( 'date_format' ), $until ) );
		}
		else if( $count = $rc->getCount() )
			$txt .= ' ' . sprintf( __( 'for %d occurrences', CALP_PLUGIN_NAME ), $count );
		else
			$txt .= ' - ' . __( 'forever', CALP_PLUGIN_NAME );
	}

	/**
	 * undocumented function
	 *
	 *
	 *
	 * @return void
	 **/
	function convert_rrule_to_text() {
		$error = false;
		// check to see if RRULE is set
		if( isset( $_REQUEST["rrule"] ) ) {

			// check to see if rrule is empty
			if( empty( $_REQUEST["rrule"] ) ) {
				$error = true;
				$message = 'Recurrence rule cannot be empty!';
			} else {
				// convert rrule to text
				$message = $this->rrule_to_text( $_REQUEST["rrule"] );
			}

		} else {
			$error = true;
			$message = 'Recurrence rule is not provided!';
		}

		$output = array(
			"error" 	=> $error,
			"message"	=> stripslashes( $message )
		);

		echo json_encode( $output );
		exit();
	}

	/**
	 * post_type_link function
	 *
	 *
	 *
	 * @return void
	 **/
	function post_type_link( $permalink, $post, $leavename ) {
		global $calp_app_helper, $calp_settings, $calp_calendar_helper;
		if( $post->post_type == CALP_POST_TYPE ) {
            $page_url = get_permalink( $calp_settings->calendar_page_id );
            
            $event = $calp_calendar_helper->get_event_by_postid( $post->ID );
            $instance = isset($event->instance_id)?$event->instance_id:0;
            
			return $page_url .  '#action=calp_agenda&calp_item_id='. $instance;
		}

		return $permalink;
	}

	/**
	 * get_week_start_day_offset function
	 *
	 * Returns the day offset of the first day of the week given a weekday in
	 * question.
	 *
	 * @param int $wday      The weekday to get information about
	 * @return int           A value between -6 and 0 indicating the week start
	 *                       day relative to the given weekday.
	 */
	function get_week_start_day_offset( $wday ) {
		global $calp_settings;

		return - ( 7 - ( $calp_settings->week_start_day - $wday ) ) % 7;
	}
      
	/**
	 * get_month_weeks function
	 *
	 * Returns the month offset.
	 *
	 * @param $timestamp     timestamp (GMT)
	 * @param $offset        Current day offset
	 * @return array         An array of month days with their offset
	 *                       day relative to the given weekday.
	 */
	function get_month_weeks( $timestamp, $offset ) {
		global $calp_settings, $calp_events_helper;
        // days in month
        $first_timestamp = $calp_events_helper->gmgetdate( gmmktime( 0, 0, 0, gmdate( 'm', $timestamp ), 1, gmdate( 'Y', $timestamp ) ) );
        $last_day = gmdate( 't', $timestamp );
        $current_date = gmdate( 'd', $timestamp );
       // Figure out index of first table cell
		$first_cell_index = $first_timestamp['wday'];
		// Modify weekday based on start of week setting
		$first_cell_index = ( 7 + $first_cell_index - $calp_settings->week_start_day ) % 7;
        // Get the last day of the month
		$last_day = gmdate( 't', $timestamp );
        
        $weeks = array();
        $week = 0;
        $weeks[$week] = array();
        
        // Insert any needed blank cells into first week
		for( $i = 0; $i < $first_cell_index; $i++ ) {
			$weeks[$week][] = array( 'date' => null );
		}
        
        // Insert each month's day with offset
		for( $i = 1; $i <= $last_day; $i++ ) {
			$weeks[$week][] = array(
				'date'   => $i,
				'today'  => $i == $current_date,
                'offset' => $i - $current_date + $offset
			);
			// If reached the end of the week, increment week
			if( count( $weeks[$week] ) == 7 )
				$week++;
		}
        
        return $weeks;
	}
    
    /**
	  * check_event_update function
	  * @param object 	$event  	   			New event
	  * @param int 	 	$recurrence_start  	   	Start time of recurring event from the seria
	  * @param bool   	$ics  	   				IS ICS import or not
	  *
	  * @return void
	**/
    public function remove_recurring_instance($event, $recurrence_start)
    {
    	global $wpdb, $calp_settings;

		$table_name = $wpdb->prefix . 'calp_events';
        $query = "SELECT post_id FROM {$table_name} " .
			"WHERE ical_feed_url = %s " .
			"AND ical_uid = %s " .
			"AND ( recurrence_rules IS NOT NULL AND recurrence_rules != '' )";
		$args = array( $event->ical_feed_url, $event->ical_uid );
		$post_id = $wpdb->get_var( $wpdb->prepare( $query, $args ) );
		if ( !empty($post_id) ) {
		    $table_name = $wpdb->prefix . 'calp_event_instances';
			$query = "DELETE FROM {$table_name} " .
			"WHERE post_id = %d " .
			"AND start = FROM_UNIXTIME( %d ) ";
			$args = array( $post_id, $recurrence_start );
			$wpdb->query($wpdb->prepare( $query, $args ));
		}
		
    }
    
}
// END class
