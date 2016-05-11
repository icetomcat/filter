<?php

/**
 * 
 * @license https://github.com/icetomcat/filter/blob/master/LICENSE MIT
 * @link https://github.com/filter/
 */

namespace Filter\Filters;

/**
 *
 * @author icetomcat <icetomcat@gmail.com>
 */
class ToInteger extends \Filter\Base\Filter
{

	public function apply($value)
	{
		return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
	}

	public static function getShortNames()
	{
		return ["to int", "to integer"];
	}

}
