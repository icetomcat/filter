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

	public function __construct(array $data, Context $parent = null)
	{
		$this->data = $data;
		$this->parent = $parent;
	}

}
