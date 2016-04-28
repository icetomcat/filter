<?php

/**
 * 
 * @license https://github.com/icetomcat/filter/blob/master/LICENSE MIT
 * @link https://github.com/filter/
 */

namespace Filter\Rules;

use Filter\Base\Rule;

/**
 *
 * @author icetomcat <icetomcat@gmail.com>
 */
class Equal extends Rule
{

	protected $expected;
	protected $strong;
	protected $from_context;

	/**
	 * 
	 * @param mixed $expected
	 * @param bool $strong
	 * @param mixed $from_context Key
	 */
	public function __construct($expected, $strong = false, $from_context = false)
	{
		$this->expected = $expected;
		$this->strong = $strong;
		$this->from_context = $from_context;
	}

	/**
	 * 
	 * @param mixed $expected
	 * @param bool $strong
	 * @param mixed $from_context Key
	 */
	public static function create(...$args)
	{
		return parent::create(...$args);
	}

	public function exec(\Filter\Context $context, $name)
	{
		if ($this->from_context)
		{
			$this->expected = isset($context->data[$this->from_context]) ? $context->data[$this->from_context] : null;
		}
		parent::exec($context, $name);
	}

	public function apply($actual)
	{
		return $this->strong ? ($actual === $this->expected) : ($actual == $this->expected);
	}

}
