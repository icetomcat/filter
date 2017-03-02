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
class IsFloat extends \Filter\Base\Rule
{
	public function apply($actual)
	{
		return filter_var($actual, FILTER_VALIDATE_FLOAT) !== false;
	}
	
	public static function getShortNames()
	{
		return ["float", "double", "is float"];
	}
}
