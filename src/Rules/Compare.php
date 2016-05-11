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
use Filter\Filter;

/**
 *
 * @author icetomcat <icetomcat@gmail.com>
 */
class Compare extends Rule
{

	protected $op;
	protected $expected;
	protected $with;
	protected $filter;

	/**
	 * 
	 * @param Closure $op
	 * @param mixed $expected
	 * @param string $with
	 * @param mixed $filter
	 */
	public function __construct($op = "?", $expected = null, $with = null, $filter = null)
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
		if ($filter instanceof Filter)
		{
			$this->filter = $filter;
		}
		elseif (is_string($filter))
		{
			$this->filter = Filter::map($filter);
		}
		elseif (is_array($filter))
		{
			$this->filter = Filter::map($filter);
		}
		else
		{
			$this->filter = null;
		}
	}

	/**
	 * 
	 * @param Closure $op
	 * @param mixed $expected
	 * @param string $with
	 * @param mixed $filter
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
		throw (new Error($this->getErrorMessage("filter.rule.compare.{$this->op}")))->setArgs([
			"%value%" => $context->getValue($name),
			"%name%" => $context->trans($name),
			"%expected%" => $expected
		])->setName(is_string($this->with) ? $name . "." . $this->with : $name);
	}

	public function apply($actual)
	{
		$actual = $this->filter ? $this->filter->run([$actual])->data[0] : $actual;
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

	static public function creatFromShortName($name, ...$args)
	{
		if (in_array($name, static::getShortNames()))
		{
			return static::create($name, ...$args);
		}
		else
		{
			throw new Exception($name);
		}
	}

	static public function getShortNames()
	{
		return ["?", "!", "==", "===", "!=", "!==", ">", ">=", "<", "<=", "&&", "||"];
	}

}
