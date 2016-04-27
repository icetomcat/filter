<?php

namespace Filter\Filters;

class RTrim extends Trim
{

	public function apply($value)
	{
		return ltrim($value, $this->character_mask);
	}

}
