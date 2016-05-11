<?php

/**
 * 
 * @license https://github.com/icetomcat/filter/blob/master/LICENSE MIT
 * @link https://github.com/filter/
 */

namespace Filter\Exceptions;

/**
 *
 * @author icetomcat <icetomcat@gmail.com>
 */
class Error extends \Exception
{

	protected $args = [];
	protected $name = null;
	
	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}

	public function getName()
	{
		return $this->name;
	}
	
	public function setArgs(array $args = [])
	{
		$this->args = $args;
		return $this;
	}

	public function getArgs()
	{
		return $this->args;
	}

}
