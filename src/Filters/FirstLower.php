<?php

namespace Filter\Filters;

class FirstLower extends \Filter\Base\Filter
{
	public function apply($str)
	{
		return mb_strtolower(mb_substr($str, 0, 1)) . mb_substr($str, 1, mb_strlen($str));
	}

}
