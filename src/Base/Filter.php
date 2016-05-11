<?php

/**
 * 
 * @license https://github.com/icetomcat/filter/blob/master/LICENSE MIT
 * @link https://github.com/filter/
 */

namespace Filter\Base;

use Filter\Context;
use Filter\Exceptions\Error;
use Filter\Exceptions\NextFilter;
use Filter\Interfaces\IFilter;

/**
 *
 * @author icetomcat <icetomcat@gmail.com>
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

	protected function error(Context $context, $name)
	{
		throw new Error();
	}

	public function exec(Context $context, $name)
	{
		$this->context = $context;
		if (isset($context->data[$name]))
		{
			$context->data[$name] = $this->apply($context->getValue($name));
		}
		$this->context = null;
	}

	public function apply($value)
	{
		if (is_null($value))
		{
			throw new NextFilter();
		}
		return $value;
	}
	
	static public function creatFromShortName($name, ...$args)
	{
		return static::create($name, ...$args);
	}

	static public function getShortNames()
	{
		return [];
	}

}
