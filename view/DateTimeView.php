<?php

class DateTimeView {
	private static $TIMEZONE = "Europe/Paris";

	public function show() {
		date_default_timezone_set(self::$TIMEZONE);
		$timeString = date("l") . ', the ' . date("d") . 'th of ' . $this->getMonthAsText() . ' ' . date("Y") . ', The time is ' . date("H:i:s");

		return '<p>' . $timeString . '</p>';
	}

	/**
	 * F stands for the format. mktime creates the timestamp. Output is the month as text.
	 *
	 * @return string $monthAsText
	 */
	private function getMonthAsText () {
		$monthAsText = date("F", mktime(null, null, null, $this->getMonthAsNumber()));

		return $monthAsText;
	}

	/**
	 * Converts the integer to a string and trims the leading 0. Then converts back to integer.
	 *
	 * @return int $monthAsNumber
	 */
	private function getMonthAsNumber () {
		$monthAsNumber = intval(ltrim(date("m"), '0'));

		return $monthAsNumber;
	}
}