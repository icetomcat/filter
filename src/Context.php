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
class Context
{

	public $errors = array();
	public $data = array();
	public $parent = null;
	protected $filter = null;

	public function __construct(array $data, Filter $filter, Context $parent = null)
	{
		$this->data = $data;
		$this->parent = $parent;
		$this->filter = $filter;
	}

	public function getValue($name)
	{
		return isset($this->data[$name]) ? $this->data[$name] : null;
	}
	
	public function getFilter()
	{
		return $this->filter;
	}
	
	public function trans($id, array $args = [])
	{
		return $this->filter->trans($id, $args);
	}
}
