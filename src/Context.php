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

	public function getNormalizedErrors($path_glue = "/")
	{
		return static::normalize($this->errors, "", $path_glue);
	}

	static protected function __normalize($array, &$result = [], $path = "", $path_glue = "/")
	{
		foreach ($array as $key => $value)
		{
			if (is_array($value))
			{
				static::__normalize($value, $result, $path . $path_glue . $key, $path_glue);
			}
			else
			{
				$result[$path . $path_glue . $key] = [
					"parameter" => $key,
					"message" => $value
				];
			}
		}
	}

	static public function normalize(array $array, $start_path = "", $path_glue = "/")
	{
		static::__normalize($array, $result, $start_path, $path_glue);
		return $result;
	}

}
