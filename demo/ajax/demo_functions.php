<?php

/**
 * Converts a date string of given timezone (considering DST) and format to 14-digit UTC timestamp (YYYYMMDDHHMMSS)
 *
 * DateTime::createFromFormat requires PHP >= 5.3
 *
 * <li><b>Note about strtotime</b>: Dates in the m/d/y or d-m-y formats are disambiguated by looking at the separator between the various components:
 * if the separator is a slash (/), then the American m/d/y is assumed;
 * whereas if the separator is a dash (-) or a dot (.), then the European d-m-y format is assumed.
 *
 * To avoid potential ambiguity, it's best to use ISO 8601 (YYYY-MM-DD) dates or DateTime::createFromFormat() when possible.
 *
 * @param $str_user_datetime
 *
 * <li><var>$str_user_timezone</var> and <var>$str_user_dateformat</var> must match. Otherwise error occurs.
 *
 * <li>If <var>$str_server_dateformat</var> is longer than <var>$str_user_dateformat</var>,
 * the missing time digits filled with zero, but if all times digits are missing current time is returned.
 *
 * <li>Other values (invalid datetime strings) throw an error. Milliseconds are not supported.
 *
 * @param $str_user_timezone
 * @param $str_user_dateformat
 * @return string
 *
 * @link http://www.php.net/manual/en/function.strtotime.php
 * @link http://stackoverflow.com/questions/4163641/php-using-strtotime-with-a-uk-date-format-dd-mm-yy
 * @link http://derickrethans.nl/british-date-format-parsing.html
 */
function date_encode($str_user_timezone,
					 $str_user_dateformat,
					 $str_user_datetime) {

	$str_server_timezone = 'UTC';
	$str_server_dateformat = 'YmdHis';
	$str_safe_dateformat_strtotime = 'Y-m-d H:i:s';

	// set timezone to user timezone
	date_default_timezone_set($str_user_timezone);

	// create date object using any given format
	if($str_user_datetime == 'now' || !$str_user_datetime) {
		$date = new DateTime('', new DateTimeZone($str_user_timezone));
	} else {
		$date = DateTime::createFromFormat($str_user_dateformat, $str_user_datetime, new DateTimeZone($str_user_timezone));
		if($date === false) {
			trigger_error('date_encode: Invalid date', E_USER_ERROR);
		}
	}

	// convert given datetime to safe format for strtotime
	$str_user_datetime = $date->format($str_safe_dateformat_strtotime);

	// convert to UTC
	$str_server_datetime = gmdate($str_server_dateformat, strtotime($str_user_datetime));

	// return timezone to server default
	date_default_timezone_set($str_server_timezone);

	return $str_server_datetime;
}