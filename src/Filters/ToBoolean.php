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
class ToBoolean extends \Filter\Base\Filter
{

	public function apply($value)
	{
		return filter_var($value, FILTER_VALIDATE_BOOLEAN);
	}
	
	public static function getShortNames()
	{
		return ["to bool", "to boolean"];
	}


}
