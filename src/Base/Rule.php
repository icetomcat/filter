<?php

/**
 * 
 * @license https://github.com/icetomcat/filter/blob/master/LICENSE MIT
 * @link https://github.com/filter/
 */

namespace Filter\Base;

use Exception;
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
	protected $name;

	public function __construct()
	{
		$class = explode("\\", static::class);
		$this->name = strtolower(end($class));
	}

	static public function create(...$args)
	{
		return new static(...$args);
	}

	protected function error(Context $context, $name)
	{
		throw (new Error($this->error_message ? $this->error_message : $this->getTranslateId()))->setName($name)->setArgs(["%name%" => $name, "%value%" => $context->getValue($name)]);
	}

	public function createError($message = null, $args = [])
	{
		throw (new Error(is_null($message) ? $this->getTranslateId() : $message))->setArgs($args);
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

	public function getTranslateId()
	{
		return "filter.rule." . $this->name;
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

	public function getContext()
	{
		return $this->context;
	}

	public function apply($value)
	{
		if (is_null($value))
		{
			throw new NextField();
		}
		return true;
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
