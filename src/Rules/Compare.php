<?php

/**
 * 
 * @license https://github.com/icetomcat/filter/blob/master/LICENSE MIT
 * @link https://github.com/filter/
 */

namespace Filter\Rules;

use Closure;
use Exception;
use Filter\Base\Rule;
use Filter\Context;
use Filter\Exceptions\Error;

/**
 *
 * @author icetomcat <icetomcat@gmail.com>
 */
class Compare extends Rule
{

	protected $op;
	protected $expected;
	protected $with;
	protected $prepare;

	/**
	 * 
	 * @param Closure $op
	 * @param mixed $expected
	 * @param string $with
	 * @param mixed $prepare Function
	 */
	public function __construct($op = "?", $expected = null, $with = null, $prepare = null)
	{
		if ($op instanceof Closure)
		{
			$this->op = $op->bindTo($this);
		}
		else
		{
			$this->op = $op;
		}
		$this->expected = $expected;
		$this->with = $with;
		if ($prepare && !is_callable($prepare))
		{
			throw new Exception();
		}
		$this->prepare = $prepare;
	}

	/**
	 * 
	 * @param Closure $op
	 * @param mixed $expected
	 * @param string $with
	 * @param mixed $prepare Function
	 */
	public static function create(...$args)
	{
		return parent::create(...$args);
	}

	protected function error(Context $context, $name)
	{
		$expected = $this->expected;
		if (is_string($this->with))
		{
			$expected = $context->trans($this->with);
		}
		throw (new Error($this->getErrorMessage("filter.rules.compare.{$this->op}")))->setArgs([
			"%value%" => $context->getValue($name),
			"%name%" => $context->trans($name),
			"%expected%" => $expected
		]);
	}

	public function apply($actual)
	{
		$actual = $this->prepare ? call_user_func($this->prepare, $actual) : $actual;
		$expected = $this->expected;
		if (is_string($this->with))
		{
			$expected = $this->context->getValue($this->with);
		}
		if (is_callable($this->op))
		{
			return $this->op($actual, $this->expected);
		}
		else
		{
			switch ($this->op)
			{
				case "?":
					return $actual ? true : false;
				case "!":
					return !$actual;
				case "==":
					return $expected == $actual;
				case "===":
					return $expected === $actual;
				case "!=":
					return $expected != $actual;
				case "!==":
					return $expected !== $actual;
				case ">":
					return $actual > $expected;
				case ">=":
					return $actual >= $expected;
				case "<":
					return $actual < $expected;
				case "<=":
					return $actual <= $expected;
				case "&&":
					return $actual && $expected;
				case "||":
					return $actual || $expected;

				default:
					return false;
			}
		}
	}

}
