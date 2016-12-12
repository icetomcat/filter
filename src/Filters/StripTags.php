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
class StripTags extends \Filter\Base\Filter
{
	
	protected $allowable_tags;
	
	public function __construct($allowable_tags = null)
	{
		$this->allowable_tags = $allowable_tags;
	}

	public function apply($value)
	{
		return strip_tags($value, $this->allowable_tags);
	}

	public static function getShortNames()
	{
		return ["strip tags"];
	}

}
