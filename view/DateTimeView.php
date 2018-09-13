<?php

class DateTimeView {
	private static $TIMEZONE = "Europe/Paris";

	public function show() {
		date_default_timezone_set(self::$TIMEZONE);// Considered side effect? Maybe put it in the constructor?
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
	 * Converts the integer (currentMonth) to a string and trims the leading 0. Then converts back to integer.
	 *
	 * @return int $monthAsNumber
	 */
	private function getMonthAsNumber () {
		$currentMonth = date("m");
		$monthAsNumber = intval(ltrim($currentMonth, '0'));

		return $monthAsNumber;
	}
}