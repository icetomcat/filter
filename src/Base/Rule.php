<?php

/**
 * 
 * @license https://github.com/icetomcat/filter/blob/master/LICENSE MIT
 * @link https://github.com/filter/
 */

namespace Filter\Base;

use Filter\Context;
use Filter\Exceptions\Error;
use Filter\Exceptions\NextField;
use Filter\Interfaces\IFilter;

/**
 *
 * @author icetomcat <icetomcat@gmail.com>
 */
abstract class Rule implements IFilter
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
		throw new Error(is_null($message) ? static::class : $message);
	}

	public function exec(Context $context, $name)
	{
		$this->context = $context;
		$res = $this->apply(isset($context->data[$name]) ? $context->data[$name] : null);
		$this->context = null;
		if (!$res)
		{
			$this->error();
		}
	}

	public function apply($value)
	{
		if (is_null($value))
		{
			throw new NextField();
		}
		return true;
	}

}
