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
class LTrim extends Trim
{

	public function apply($value)
	{
		return ltrim($value, $this->character_mask);
	}

}
