<?php

namespace Filter\Filters;

class Lower extends \Filter\Base\Filter
{
	public function apply($str)
	{
		return mb_strtolower($str);
	}

}
