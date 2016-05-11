<?php

/**
 * 
 * @license https://github.com/icetomcat/filter/blob/master/LICENSE MIT
 * @link https://github.com/filter/
 */

namespace Filter\Rules;

use Filter\Base\Rule;

/**
 *
 * @author icetomcat <icetomcat@gmail.com>
 */
class Boolean extends Rule
{
	public function apply($actual)
	{
		return filter_var($actual, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null;
	}
	
	public static function getShortNames()
	{
		return ["bool", "boolean"];
	}

}
