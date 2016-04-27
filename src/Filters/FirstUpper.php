<?php

namespace Filter\Filters;

class FirstUpper extends \Filter\Base\Filter
{

	public function apply($str)
	{
		return mb_strtoupper(mb_substr($str, 0, 1)) . mb_substr($str, 1, mb_strlen($str));
	}

}
