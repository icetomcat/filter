<?php

/**
 * 
 * @license https://github.com/icetomcat/filter/blob/master/LICENSE MIT
 * @link https://github.com/filter/
 */

namespace Filter\Rules;

use Closure;

/**
 *
 * @author icetomcat <icetomcat@gmail.com>
 */
class Custom extends \Filter\Base\Rule
{

	public $fn;

	public function __construct(Closure $apply)
	{
		$this->fn = $apply->bindTo($this);
	}

	public function apply($value)
	{
		return $this->fn($value);
	}

}
