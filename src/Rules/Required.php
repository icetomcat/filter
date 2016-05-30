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
class Required extends Rule
{
	public function apply($actual)
	{
		return !is_null($actual) && ($actual !== []) && ($actual !== "");
	}
	
	public static function getShortNames()
	{
		return ["required"];
	}

}
