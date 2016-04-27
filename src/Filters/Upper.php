<?php

namespace Filter\Filters;

class Upper extends \Filter\Base\Filter
{
	public function apply($str)
	{
		return mb_strtoupper($str);
	}

}
