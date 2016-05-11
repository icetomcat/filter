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
class URL extends \Filter\Base\Rule
{
	public function apply($actual)
	{
		return filter_var($actual, FILTER_VALIDATE_EMAIL) !== false;
	}
	
	public static function getShortNames()
	{
		return ["url"];
	}
}
