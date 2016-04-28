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
class FirstLower extends \Filter\Base\Filter
{

	public function apply($str)
	{
		return mb_strtolower(mb_substr($str, 0, 1)) . mb_substr($str, 1, mb_strlen($str));
	}

}
