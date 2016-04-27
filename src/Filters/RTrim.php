<?php

namespace Filter\Filters;

class RTrim extends Trim
{

	public function apply($value)
	{
		return rtrim($value, $this->character_mask);
	}

}
