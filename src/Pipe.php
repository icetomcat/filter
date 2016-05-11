<?php

/**
 * 
 * @license https://github.com/icetomcat/filter/blob/master/LICENSE MIT
 * @link https://github.com/filter/
 */

namespace Filter;

/**
 *
 * @author icetomcat <icetomcat@gmail.com>
 */
class Pipe implements \ArrayAccess, \Iterator
{

	protected $pipe = [];
	protected $filter;

	public function __construct(Filter $filter)
	{
		$this->filter = $filter;
	}
	
	public function __set($name, $value)
	{
		$this->filter->addFilter($name, $value);
	}
	
	public function addFilter($name, $value)
	{
		$this->filter->addFilter($name, $value);
		return $this;
	}

	public function offsetExists($offset)
	{
		return isset($this->pipe[$offset]);
	}

	public function offsetGet($offset)
	{
		return $this->pipe[$offset];
	}

	public function offsetSet($offset, $value)
	{
		if (is_null($offset))
		{
			$this->pipe[] = $value;
		}
		else
		{
			$this->pipe[$offset] = $value;
		}
	}

	public function offsetUnset($offset)
	{
		unset($this->pipe[$offset]);
	}

	public function current()
	{
		return current($this->pipe);
	}

	public function key()
	{
		return key($this->pipe);
	}

	public function next()
	{
		next($this->pipe);
	}

	public function rewind()
	{
		reset($this->pipe);
	}

	public function valid()
	{
		return key($this->pipe) !== null;
	}

}
