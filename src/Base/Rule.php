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
	protected $error_message = null;

	static public function create(...$args)
	{
		return new static(...$args);
	}

	protected function error(Context $context, $name)
	{
		throw (new Error($this->error_message ? $this->error_message : strtolower(str_replace("\\", ".", static::class))))->setArgs(["%name%" => $name, "%value%" => $context->getValue($name)]);
	}

	public function createError($message = null, $args = [])
	{
		throw (new Error(is_null($message) ? static::class : $message))->setArgs($args);
	}

	public function setErrorMessage($message)
	{
		$this->error_message = $message;
		return $this;
	}

	public function getErrorMessage($defalut = null)
	{
		return $this->error_message ? $this->error_message : $defalut;
	}

	public function exec(Context $context, $name)
	{
		$this->context = $context;
		$res = $this->apply($context->getValue($name));
		$this->context = null;
		if (!$res)
		{
			$this->error($context, $name);
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
