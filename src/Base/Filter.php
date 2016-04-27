<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Filter\Base;

use Filter\Context;
use Filter\Exceptions\Error;
use Filter\Exceptions\NextFilter;
use Filter\Interfaces\IFilter;

/**
 * Description of Filter
 *
 * @author icetomcat
 */
abstract class Filter implements IFilter
{

	/**
	 *
	 * @var Context
	 */
	protected $context = null;

	static public function create(...$args)
	{
		return new static(...$args);
	}

	public function error($message = null)
	{
		throw new Error(is_null($message) ? $message : static::class);
	}

	public function exec(Context $context, $name)
	{
		$this->context = $context;
		if ( isset($context->data[$name]) )
		{
			$context->data[$name] = $this->apply(isset($context->data[$name]) ? $context->data[$name] : null);
		}
		$this->context = null;
	}

	public function apply($value)
	{
		if ( is_null($value) )
		{
			throw new NextFilter();
		}
		return $value;
	}

}
