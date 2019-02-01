<?php

namespace Tribe\Events\ORM\Events;

use Tribe\Events\Test\Factories\Event;

class FetchByDateTest extends \Codeception\TestCase\WPTestCase {

	public function setUp() {
		// before
		parent::setUp();

		// your set up methods here
		$this->factory()->event = new Event();
	}

	/**
	 * It should allow getting events by all-day status
	 *
	 * @test
	 */
	public function should_allow_getting_events_by_all_day_status() {
		$all_day     = $this->factory()->event->create_many( 2, [ 'meta_input' => [ '_EventAllDay' => 'yes' ] ] );
		$not_all_day = $this->factory()->event->create_many( 3 );

		$this->assertEqualSets( $all_day, tribe_events()->where( 'all_day', true )->get_ids() );
		$this->assertEqualSets( $not_all_day, tribe_events()->where( 'all_day', false )->get_ids() );
		$this->assertCount( 5, tribe_events()->get_ids() );
	}

	/**
	 * It should allow filtering events by start date
	 *
	 * @test
	 */
	public function should_allow_filtering_events_by_start_date() {
		$site_timezone      = 'Europe/Paris';
		$ny_timezone_string = 'America/New_York';
		update_option( 'timezone_string', $site_timezone );
		$ny    = new \DateTimeZone( $ny_timezone_string );
		$paris = new \DateTimeZone( $site_timezone );
		$date  = new \DateTime( '2018-01-15 16:00:00', $ny );

		$starts_before_ends_before  = $this->factory()->event->starting_on( '2018-01-10 10:00:00' )
		                                                     ->with_timezone( $ny_timezone_string )
		                                                     ->lasting( 2 * HOUR_IN_SECONDS )
		                                                     ->create();
		$starts_before_ends_on_date = $this->factory()->event->starting_on( '2018-01-15 15:00:00' )
		                                                     ->with_timezone( $ny_timezone_string )
		                                                     ->lasting( HOUR_IN_SECONDS )
		                                                     ->create();
		$starts_before_ends_after   = $this->factory()->event->starting_on( '2018-01-15 15:00:00' )
		                                                     ->with_timezone( $ny_timezone_string )
		                                                     ->lasting( 2 * HOUR_IN_SECONDS )
		                                                     ->create();
		$starts_on_date_ends_after  = $this->factory()->event->starting_on( '2018-01-15 16:00:00' )
		                                                     ->with_timezone( $ny_timezone_string )
		                                                     ->lasting( 2 * HOUR_IN_SECONDS )
		                                                     ->create();
		$starts_after_ends_after    = $this->factory()->event->starting_on( '2018-01-17 15:00:00' )
		                                                     ->with_timezone( $ny_timezone_string )
		                                                     ->lasting( 2 * HOUR_IN_SECONDS )
		                                                     ->create();

		$this->assertEqualSets( [
			$starts_after_ends_after,
		], tribe_events()->where( 'starts_after', $date->format( 'Y-m-d H:i:s' ), $ny_timezone_string )->get_ids() );
		$this->assertEqualSets( [
			$starts_before_ends_on_date,
			$starts_before_ends_after,
			$starts_on_date_ends_after,
			$starts_after_ends_after,
		], tribe_events()->where( 'starts_after', $date->format( 'Y-m-d H:i:s' ), $paris )->get_ids() );
		$this->assertEqualSets( [
			$starts_after_ends_after,
		], tribe_events()->where( 'starts_after', $date, 'UTC' )->get_ids() );
		$this->assertEqualSets( [
			$starts_after_ends_after,
		], tribe_events()->where( 'starts_after', $date->getTimestamp() )->get_ids() );

		$this->assertEqualSets( [
			$starts_before_ends_before,
			$starts_before_ends_after,
			$starts_before_ends_on_date,
		], tribe_events()->where( 'starts_before', $date->format( 'Y-m-d H:i:s' ), $ny_timezone_string )->get_ids() );
		$this->assertEqualSets( [
			$starts_before_ends_before,
		], tribe_events()->where( 'starts_before', $date->format( 'Y-m-d H:i:s' ), $paris )->get_ids() );
		$this->assertEqualSets( [
			$starts_before_ends_before,
			$starts_before_ends_on_date,
			$starts_before_ends_after,
		], tribe_events()->where( 'starts_before', $date, 'UTC' )->get_ids() );
		$this->assertEqualSets( [
			$starts_before_ends_before,
			$starts_before_ends_on_date,
			$starts_before_ends_after,
		], tribe_events()->where( 'starts_before', $date->getTimestamp() )->get_ids() );

		$this->assertEqualSets( [
			$starts_on_date_ends_after,
		], tribe_events()->where( 'starts_between', $date->format( 'Y-m-d H:i:s' ), '2018-01-16 23:00:00', $ny_timezone_string )->get_ids() );
		$this->assertEqualSets( [
			$starts_before_ends_before,
			$starts_before_ends_on_date,
			$starts_before_ends_after,
			$starts_on_date_ends_after,
		], tribe_events()->where( 'starts_between', '2018-01-01 00:00:00', '2018-01-16 23:00:00', $paris )->get_ids() );
		$this->assertEqualSets( [
			$starts_on_date_ends_after,
		], tribe_events()->where( 'starts_between', '2018-01-15 16:00:00', '2018-01-15 17:00:00', 'America/New_York' )->get_ids() );
	}

	/**
	 * It should allow filtering events by ends after
	 *
	 * @test
	 */
	public function should_allow_filtering_events_by_ends_after() {
		$site_timezone      = 'Europe/Paris';
		$ny_timezone_string = 'America/New_York';
		update_option( 'timezone_string', $site_timezone );
		$ny    = new \DateTimeZone( $ny_timezone_string );
		$paris = new \DateTimeZone( $site_timezone );
		$date  = new \DateTime( '2018-01-15 16:00:00', $ny );

		$start_before_ends_before   = $this->factory()->event->starting_on( '2018-01-10 10:00:00' )
		                                                     ->with_timezone( $ny_timezone_string )
		                                                     ->lasting( 2 * HOUR_IN_SECONDS )
		                                                     ->create();
		$starts_before_ends_on_date = $this->factory()->event->starting_on( '2018-01-15 15:00:00' )
		                                                     ->with_timezone( $ny_timezone_string )
		                                                     ->lasting( HOUR_IN_SECONDS )
		                                                     ->create();
		$starts_before_ends_after   = $this->factory()->event->starting_on( '2018-01-15 15:00:00' )
		                                                     ->with_timezone( $ny_timezone_string )
		                                                     ->lasting( 2 * HOUR_IN_SECONDS )
		                                                     ->create();
		$starts_after_ends_after    = $this->factory()->event->starting_on( '2018-01-17 15:00:00' )
		                                                     ->with_timezone( $ny_timezone_string )
		                                                     ->lasting( 2 * HOUR_IN_SECONDS )
		                                                     ->create();

		$this->assertEqualSets( [
			$starts_before_ends_after,
			$starts_after_ends_after,
		], tribe_events()->where( 'ends_after', $date->format( 'Y-m-d H:i:s' ), $ny_timezone_string )->get_ids() );
		$this->assertEqualSets( [
			$starts_before_ends_on_date,
			$starts_before_ends_after,
			$starts_after_ends_after,
		], tribe_events()->where( 'ends_after', $date->format( 'Y-m-d H:i:s' ), $paris )->get_ids() );
		$this->assertEqualSets( [
			$starts_before_ends_after,
			$starts_after_ends_after,
		], tribe_events()->where( 'ends_after', $date, 'UTC' )->get_ids() );
		$this->assertEqualSets( [
			$starts_before_ends_after,
			$starts_after_ends_after,
		], tribe_events()->where( 'ends_after', $date->getTimestamp() )->get_ids() );

	}

	/**
	 * It should allow filtering events by ends before
	 *
	 * @test
	 */
	public function should_allow_filtering_events_by_ends_before() {
		$site_timezone      = 'Europe/Paris';
		$ny_timezone_string = 'America/New_York';
		update_option( 'timezone_string', $site_timezone );
		$ny    = new \DateTimeZone( $ny_timezone_string );
		$paris = new \DateTimeZone( $site_timezone );
		$date  = new \DateTime( '2018-01-15 16:00:00', $ny );

		$start_before_ends_before   = $this->factory()->event->starting_on( '2018-01-10 10:00:00' )
		                                                     ->with_timezone( $ny_timezone_string )
		                                                     ->lasting( 2 * HOUR_IN_SECONDS )
		                                                     ->create();
		$starts_before_ends_on_date = $this->factory()->event->starting_on( '2018-01-15 15:00:00' )
		                                                     ->with_timezone( $ny_timezone_string )
		                                                     ->lasting( HOUR_IN_SECONDS )
		                                                     ->create();
		$starts_before_ends_after   = $this->factory()->event->starting_on( '2018-01-15 15:00:00' )
		                                                     ->with_timezone( $ny_timezone_string )
		                                                     ->lasting( 2 * HOUR_IN_SECONDS )
		                                                     ->create();
		$starts_after_ends_after    = $this->factory()->event->starting_on( '2018-01-17 15:00:00' )
		                                                     ->with_timezone( $ny_timezone_string )
		                                                     ->lasting( 2 * HOUR_IN_SECONDS )
		                                                     ->create();

		$this->assertEqualSets( [
			$start_before_ends_before,
		], tribe_events()->where( 'ends_before', $date->format( 'Y-m-d H:i:s' ), $ny_timezone_string )->get_ids() );
		$this->assertEqualSets( [
			$start_before_ends_before,
		], tribe_events()->where( 'ends_before', $date->format( 'Y-m-d H:i:s' ), $paris )->get_ids() );
		$this->assertEqualSets( [
			$start_before_ends_before,
		], tribe_events()->where( 'ends_before', $date, 'UTC' )->get_ids() );
		$this->assertEqualSets( [
			$start_before_ends_before,
		], tribe_events()->where( 'ends_before', $date->getTimestamp() )->get_ids() );
	}

	/**
	 * It should allow filtering events by ends before
	 *
	 * @test
	 */
	public function should_allow_filtering_events_by_ends_between() {
		$site_timezone      = 'Europe/Paris';
		$ny_timezone_string = 'America/New_York';
		update_option( 'timezone_string', $site_timezone );
		$ny    = new \DateTimeZone( $ny_timezone_string );
		$paris = new \DateTimeZone( $site_timezone );
		$date  = new \DateTime( '2018-01-15 16:00:00', $ny );

		$start_before_ends_before   = $this->factory()->event->starting_on( '2018-01-10 10:00:00' )
		                                                     ->with_timezone( $ny_timezone_string )
		                                                     ->lasting( 2 * HOUR_IN_SECONDS )
		                                                     ->create();
		$starts_before_ends_on_date = $this->factory()->event->starting_on( '2018-01-15 15:00:00' )
		                                                     ->with_timezone( $ny_timezone_string )
		                                                     ->lasting( HOUR_IN_SECONDS )
		                                                     ->create();
		$starts_before_ends_after   = $this->factory()->event->starting_on( '2018-01-15 15:00:00' )
		                                                     ->with_timezone( $ny_timezone_string )
		                                                     ->lasting( 2 * HOUR_IN_SECONDS )
		                                                     ->create();
		$starts_after_ends_after    = $this->factory()->event->starting_on( '2018-01-17 15:00:00' )
		                                                     ->with_timezone( $ny_timezone_string )
		                                                     ->lasting( 2 * HOUR_IN_SECONDS )
		                                                     ->create();

		$this->assertEqualSets( [
			$starts_before_ends_on_date,
			$starts_before_ends_after,
		], tribe_events()->where( 'ends_between', $date->format( 'Y-m-d H:i:s' ), '2018-01-16 23:00:00', $ny_timezone_string )->get_ids() );
		$this->assertEqualSets( [
			$start_before_ends_before,
			$starts_before_ends_on_date,
			$starts_before_ends_after,
		], tribe_events()->where( 'ends_between', '2018-01-01 00:00:00', '2018-01-16 23:00:00', $paris )->get_ids() );
		$this->assertEqualSets( [
			$starts_before_ends_on_date,
			$starts_before_ends_after,
		], tribe_events()->where( 'ends_between', '2018-01-15 16:00:00', '2018-01-15 17:00:00', 'America/New_York' )->get_ids() );
	}

	/**
	 * It should allow filtering events by their multiday state
	 *
	 * @test
	 */
	public function should_allow_filtering_events_by_their_multiday_state() {
		tribe_update_option( 'multiDayCutoff', '00:00' );
		$same_day            = $this->factory()->event->starting_on( '2018-01-10 09:00:00' )
		                                              ->with_timezone( 'America/New_York' )
		                                              ->lasting( 5 * HOUR_IN_SECONDS )
		                                              ->create();
		$multi_day           = $this->factory()->event->starting_on( '2018-01-10 16:00:00' )
		                                              ->with_timezone( 'America/New_York' )
		                                              ->lasting( 20 * HOUR_IN_SECONDS )
		                                              ->create();
		$many_day_multi_day  = $this->factory()->event->starting_on( '2018-01-10 16:00:00' )
		                                              ->with_timezone( 'America/New_York' )
		                                              ->lasting( 3 * DAY_IN_SECONDS )
		                                              ->create();
		$not_multiday_in_utc = $this->factory()->event->starting_on( '2018-01-10 23:00:00' )
		                                              ->with_timezone( 'America/New_York' )
		                                              ->lasting( 2 * HOUR_IN_SECONDS )
		                                              ->create();

		$this->assertEqualSets( [
			$same_day,
			$multi_day,
			$many_day_multi_day,
			$not_multiday_in_utc,
		], tribe_events()->get_ids() );
		$this->assertEqualSets( [
			$multi_day,
			$many_day_multi_day,
			$not_multiday_in_utc,
		], tribe_events()->where( 'multiday', true )->get_ids() );
		$this->assertEqualSets( [ $same_day ], tribe_events()->where( 'multiday', false )->get_ids() );
	}

	/**
	 * It should handle multi-day with after midnight cutoff
	 *
	 * @test
	 */
	public function should_handle_multi_day_with_after_midnight_cutoff() {
		tribe_update_option( 'multiDayCutoff', '06:00' );
		$same_day                     = $this->factory()->event->starting_on( '2018-01-10 16:00:00' )
		                                                       ->with_timezone( 'America/New_York' )
		                                                       ->lasting( 4 * HOUR_IN_SECONDS )
		                                                       ->create();
		$cross_midnight_before_cutoff = $this->factory()->event->starting_on( '2018-01-10 23:00:00' )
		                                                       ->with_timezone( 'America/New_York' )
		                                                       ->lasting( 4 * HOUR_IN_SECONDS )
		                                                       ->create();
		$multi_day_till_cutoff        = $this->factory()->event->starting_on( '2018-01-10 23:00:00' )
		                                                       ->with_timezone( 'America/New_York' )
		                                                       ->lasting( 7 * HOUR_IN_SECONDS )
		                                                       ->create();
		$multi_day                    = $this->factory()->event->starting_on( '2018-01-10 23:00:00' )
		                                                       ->with_timezone( 'America/New_York' )
		                                                       ->lasting( 8 * HOUR_IN_SECONDS )
		                                                       ->create();

		$this->assertEqualSets( [
			$same_day,
			$cross_midnight_before_cutoff,
			$multi_day_till_cutoff,
			$multi_day,
		], tribe_events()->get_ids() );
		$this->assertEqualSets( [
			$multi_day_till_cutoff,
			$multi_day,
		], tribe_events()->where( 'multiday', true )->get_ids() );
		$this->assertEqualSets( [
			$same_day,
			$cross_midnight_before_cutoff,
		], tribe_events()->where( 'multiday', false )->get_ids() );
	}

	/**
	 * It should allow filtering events by calendar grid
	 *
	 * @test
	 */
	public function should_allow_filtering_events_by_calendar_grid() {
		extract( $this->create_events_from_dates( [
			'july_29th'                           => [ '2018-07-29 09:00:00', 2 * HOUR_IN_SECONDS ],
			'july_30th'                           => [ '2018-07-30 09:00:00', 2 * HOUR_IN_SECONDS ],
			'starts_in_july_ends_in_august'       => [ '2018-07-28 09:00:00', WEEK_IN_SECONDS ],
			'august_1st'                          => [ '2018-08-01 09:00:00', 2 * HOUR_IN_SECONDS ],
			'august_15th'                         => [ '2018-08-15 09:00:00', 2 * HOUR_IN_SECONDS ],
			'august_26th'                         => [ '2018-08-26 09:00:00', 2 * HOUR_IN_SECONDS ],
			'august_27th'                         => [ '2018-08-27 09:00:00', 2 * HOUR_IN_SECONDS ],
			'august_29th'                         => [ '2018-08-29 09:00:00', 2 * HOUR_IN_SECONDS ],
			'starts_in_august_ends_in_september'  => [ '2018-08-28 09:00:00', WEEK_IN_SECONDS ],
			'september_1st'                       => [ '2018-09-01 09:00:00', 2 * HOUR_IN_SECONDS ],
			'september_2nd'                       => [ '2018-09-02 09:00:00', 2 * HOUR_IN_SECONDS ],
			'september_3rd'                       => [ '2018-09-03 09:00:00', 2 * HOUR_IN_SECONDS ],
			'september_15th'                      => [ '2018-09-15 09:00:00', 2 * HOUR_IN_SECONDS ],
			'september_30th'                      => [ '2018-09-30 09:00:00', 2 * HOUR_IN_SECONDS ],
			'starts_in_september_ends_in_october' => [ '2018-09-28 09:00:00', WEEK_IN_SECONDS ],
			'october_1st'                         => [ '2018-10-01 09:00:00', 2 * HOUR_IN_SECONDS ],
		], 'America/New_York' ) );

		$events = tribe_events()->where( 'on_calendar_grid', 8, 2018 )->get_ids();
		$this->assertEqualSets( [
			$july_30th,
			$starts_in_july_ends_in_august,
			$august_1st,
			$august_15th,
			$august_26th,
			$august_27th,
			$august_29th,
			$starts_in_august_ends_in_september,
			$september_1st,
			$september_2nd,
		], $events );

		$events = tribe_events()->where( 'on_calendar_grid', 9, 2018 )->get_ids();
		$this->assertEqualSets( [
			$august_27th,
			$august_29th,
			$starts_in_august_ends_in_september,
			$september_1st,
			$september_2nd,
			$september_3rd,
			$september_15th,
			$september_30th,
			$starts_in_september_ends_in_october,
		], $events );
	}

	/**
	 * Creates a set of event from an array specifying the event names, start dates
	 * and durations.
	 *
	 * @param array $dates An array in the shape [ <var_name> => [ <start_date>, <duration> ] ].
	 * @param string The timezone string to use.
	 *
	 * @return array An array of event variable names mapped to theirs IDs, ready to be `extract`ed.
	 */
	protected function create_events_from_dates( $dates, $timezone = 'America/New_York' ) {
		return array_combine( array_keys( $dates ), array_map( function ( $payload ) use ( $timezone ) {
			list( $date, $duration ) = $payload;

			return $this->factory()->event->starting_on( $date )
			                              ->with_timezone( $timezone )
			                              ->lasting( $duration )
			                              ->create();
		}, $dates ) );
	}

	/**
	 * It should allow filtering events by timezone
	 *
	 * @test
	 */
	public function should_allow_filtering_events_by_timezone() {
		$utc   = new \DateTimeZone( 'UTC' );
		$ny    = new \DateTimeZone( 'America/New_York' );
		$paris = new \DateTimezone( 'Europe/Paris' );

		$utc_event    = $this->factory()->event->starting_on( '2018-01-10 10:00:00' )
		                                       ->with_timezone( 'UTC' )
		                                       ->lasting( 2 * HOUR_IN_SECONDS )
		                                       ->create();
		$ny_event     = $this->factory()->event->starting_on( '2018-01-10 09:00:00' )
		                                       ->with_timezone( 'America/New_York' )
		                                       ->lasting( 5 * HOUR_IN_SECONDS )
		                                       ->create();
		$offset_event = $this->factory()->event->create( [
			'meta_input' => [
				'_EventTimezone' => 'UTC+3'
			],
		] );

		$this->assertEquals( [ $utc_event ], tribe_events()->where( 'timezone', $utc )->get_ids() );
		$this->assertEquals( [ $utc_event ], tribe_events()->where( 'timezone', 'UTC' )->get_ids() );
		$this->assertEquals( [ $utc_event ], tribe_events()->where( 'timezone', 'UTC+0' )->get_ids() );
		$this->assertEquals( [ $utc_event ], tribe_events()->where( 'timezone', 'UTC-0' )->get_ids() );

		$this->assertEquals( [ $ny_event ], tribe_events()->where( 'timezone', $ny )->get_ids() );
		$this->assertEquals( [ $ny_event ], tribe_events()->where( 'timezone', 'America/New_York' )->get_ids() );

		$this->assertEquals( [ $offset_event ], tribe_events()->where( 'timezone', 'UTC+3' )->get_ids() );
	}

	/**
	 * It should allow filtering events by start and end date
	 *
	 * @test
	 */
	public function should_allow_filtering_events_by_start_and_end_date() {
		$ny_timezone_string    = 'America/New_York';
		$paris_timezone_string = 'Europe/Paris';
		$ny                    = new \DateTimeZone( $ny_timezone_string );
		$start                 = new \DateTime( '2018-01-10 16:00:00', $ny );
		$end                   = new \DateTime( '2018-01-17 16:00:00', $ny );
		extract( $this->create_events_from_dates( [
			'starts_before_ends_after_period' => [ '2018-01-01 10:00:00', 2 * MONTH_IN_SECONDS ],
			'starts_before_ends_before_start' => [ '2018-01-10 10:00:00', 2 * HOUR_IN_SECONDS ],
			'starts_before_ends_on_start'     => [ '2018-01-10 14:00:00', 2 * HOUR_IN_SECONDS ],
			'starts_on_start_ends_after'      => [ '2018-01-10 16:00:00', 2 * HOUR_IN_SECONDS ],
			'starts_before_ends_before_end'   => [ '2018-01-15 15:00:00', 2 * HOUR_IN_SECONDS ],
			'starts_before_ends_on_end'       => [ '2018-01-17 14:00:00', 2 * HOUR_IN_SECONDS ],
			'starts_before_ends_after_end'    => [ '2018-01-17 15:00:00', 2 * HOUR_IN_SECONDS ],
			'starts_on_end_ends_after_end'    => [ '2018-01-17 16:00:00', 2 * HOUR_IN_SECONDS ],
			'starts_after_ends_after_end'     => [ '2018-01-19 14:00:00', 2 * HOUR_IN_SECONDS ],
		], $ny_timezone_string ) );

		$this->assertEqualSets( [
			$starts_on_start_ends_after,
			$starts_before_ends_before_end,
			$starts_before_ends_on_end,
		], tribe_events()->where( 'starts_and_ends_between', $start, $end, $ny_timezone_string )->get_ids() );
	}

	/**
	 * It should allow fetching events by on date
	 *
	 * @test
	 */
	public function should_allow_fetching_events_by_on_date() {
		$ny_timezone_string    = 'America/New_York';
		extract( $this->create_events_from_dates( [
			'one'   => [ '2018-01-01 10:00:00', 2 * HOUR_IN_SECONDS ],
			'two'   => [ '2018-01-10 10:00:00', 2 * HOUR_IN_SECONDS ],
			'three' => [ '2018-01-10 14:00:00', 2 * HOUR_IN_SECONDS ],
			'four'  => [ '2018-01-15 15:00:00', 2 * HOUR_IN_SECONDS ],
			'five'  => [ '2018-01-17 14:00:00', 2 * HOUR_IN_SECONDS ],
			'six'   => [ '2018-01-19 14:00:00', 2 * HOUR_IN_SECONDS ],
			'seven' => [ '2018-01-09 23:00:00', 2 * HOUR_IN_SECONDS ],
		], $ny_timezone_string ) );
		$events = tribe_events()
			->per_page( - 1 )
			->order_by( 'event_date_utc', 'ASC' )
			->collect();
		codecept_debug( 'Event dates in ASC UTC date order: '
		                . implode( PHP_EOL, $events->pluck_meta( '_EventStartDateUTC' ) ) );

		$utc_matches = tribe_events()
			->where( 'on_date', '2018-01-10' )
			->order_by( 'event_date', 'DESC' )
			->collect();
		$this->assertEquals( [
			'2018-01-10 14:00:00',
			'2018-01-10 10:00:00',
			'2018-01-09 23:00:00',
		], $utc_matches->pluck_meta( '_EventStartDate' ) );

		$to_taipei_tz = function ( \WP_Post $p ) {
			$utc_start = get_post_meta( $p->ID, '_EventStartDateUTC', true );

			return ( new \DateTime( $utc_start, new \DateTimeZone( 'UTC' ) ) )
				->setTimezone( new \DateTimeZone( 'Asia/Taipei' ) )
				->format( 'Y-m-d H:i:s' );
		};

		codecept_debug( 'Event dates in ASC Asia/Taipei date order: '
		                . implode( PHP_EOL, $events->map( $to_taipei_tz ) ) );

		$taipei_matches = tribe_events()
			->where( 'on_date', '2018-01-10', 'Asia/Taipei' )
			->order_by( 'event_date', 'DESC' )
			->collect();
		$this->assertEquals( [
			'2018-01-10 10:00:00',
			'2018-01-09 23:00:00',
		], $taipei_matches->pluck_meta( '_EventStartDate' ) );

		codecept_debug( 'Event dates in ASC America/New_York date order: '
		                . implode( PHP_EOL, $events->pluck_meta( '_EventStartDate' ) ) );

		$ny_matches = tribe_events()
			->where( 'on_date', '2018-01-10', 'America/New_York' )
			->order_by( 'event_date', 'DESC' )
			->collect();
		$this->assertEquals( [
			'2018-01-10 14:00:00',
			'2018-01-10 10:00:00',
		], $ny_matches->pluck_meta( '_EventStartDate' ) );
	}
}
