<?php

/**
 * 
 * @license https://github.com/icetomcat/filter/blob/master/LICENSE MIT
 * @link https://github.com/filter/
 */

namespace Filter\Filters;

use Filter\Base\Filter;

/**
 *
 * @author icetomcat <icetomcat@gmail.com>
 */
class Custom extends Filter
{

	public $fn;

	public function __construct(\Closure $apply)
	{
		$this->fn = $apply->bindTo($this);
	}

	public function apply($value)
	{
		return $this->fn($value);
	}

}
