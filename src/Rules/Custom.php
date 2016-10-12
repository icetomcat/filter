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
	public $name;

	public function __construct($name, Closure $apply)
	{
		$this->name = $name;
		$this->fn = $apply->bindTo($this);
	}

	public function apply($value)
	{
		$fn = $this->fn;
		return $fn($value);
	}
	
	public function getTranslateId()
	{
		return "filter.rule." . $this->name;
	}


}
