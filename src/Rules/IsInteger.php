<?php

/**
 * 
 * @license https://github.com/icetomcat/filter/blob/master/LICENSE MIT
 * @link https://github.com/filter/
 */

namespace Filter\Rules;

/**
 *
 * @author icetomcat <icetomcat@gmail.com>
 */
class Integer extends \Filter\Base\Rule
{
	public function apply($actual)
	{
		return filter_var($actual, FILTER_VALIDATE_INT) !== false;
	}
	
	public static function getShortNames()
	{
		return ["int", "integer"];
	}
}
