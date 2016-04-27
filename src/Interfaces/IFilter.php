<?php

namespace Filter\Interfaces;

interface IFilter
{
	public function exec(\Filter\Context $context, $name);
	public function apply($value);
}