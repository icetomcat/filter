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
class Words extends \Filter\Base\Filter
{

	public function apply($str)
	{
		return str_word_count($str);
	}

}
