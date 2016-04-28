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
class Lower extends \Filter\Base\Filter
{

	public function apply($str)
	{
		return mb_strtolower($str);
	}

}
