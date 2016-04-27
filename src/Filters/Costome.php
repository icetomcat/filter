<?php

namespace Filter\Filters;

use Filter\Base\Filter;

class Costome extends Filter
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
