<?php

/**
 * 
 * @license https://github.com/icetomcat/filter/blob/master/LICENSE MIT
 * @link https://github.com/filter/
 */

namespace Filter;

use Exception;
use Filter\Base\Rule;
use Filter\Exceptions\Error;
use Filter\Exceptions\NextField;
use Filter\Exceptions\NextFilter;
use Filter\Exceptions\Stop;
use Filter\Interfaces\IFilter;
use ReflectionClass;

/**
 *
 * @author icetomcat <icetomcat@gmail.com>
 */
class Filter
{

	/**
	 *
	 * @var array 
	 */
	protected $pipe;
	protected $short_names = [];
	protected $prefix = "";
	protected $postfix = "";
	protected $use_name = false;

	/**
	 * 
	 */
	public function __construct($prefix = "", $postfix = "", $use_name = false)
	{
		$this->prefix = $prefix;
		$this->postfix = $postfix;
		$this->use_name = $use_name;
		$this->pipe = new Pipe($this);
		$this->loadFiltersFromDir(__DIR__ . "/Rules", "\\Filter\\Rules\\");
		$this->loadFiltersFromDir(__DIR__ . "/Filters", "\\Filter\\Filters\\");
	}

	public function loadFiltersFromDir($dir, $namespace)
	{
		foreach (array_diff(scandir($dir), array('..', '.')) as $item)
		{
			if (pathinfo($item, PATHINFO_EXTENSION) == "php")
			{
				$class = $namespace . pathinfo($item, PATHINFO_FILENAME);
				if (class_exists($class) && count($short_names = $class::getShortNames()) > 0)
				{
					foreach ($short_names as $value)
					{
						$this->short_names[$value] = $class;
					}
				}
			}
		}
	}

	public function setPrefix($prefix)
	{
		$this->prefix = $prefix;
		return $this;
	}

	public function getPrefix()
	{
		return $this->prefix;
	}

	public function setPostfix($postfix)
	{
		$this->postfix = $postfix;
		return $this;
	}

	public function getPostfix()
	{
		return $this->translatePostfix;
	}

	public function setUseName($use_name)
	{
		$this->use_name = $use_name;
		return $this;
	}

	public function getUseName()
	{
		return $this->use_name;
	}

	/**
	 * 
	 * @param string $names
	 * @param mixed $filters
	 * @return \Filter\Filter
	 */
	public function addFilter($names, $filters)
	{
		if (is_int($names))
		{
			$names = [$names];
		}
		elseif (is_string($names))
		{
			preg_match("/\[(.*)\]/", $names, $matches);
			if (isset($matches[1]) && $matches[1])
			{
				$names = $matches[1];
				if (!$filters instanceof Filter)
				{
					$filters = [static::createFromFilter($this, $filters)];
				}
				else
				{
					$filters = [$filters];
				}
			}
			$names = str_getcsv($names);
		}
		elseif (!is_array($names))
		{
			throw new Exception;
		}

		foreach ($names as $name)
		{
			if (!is_string($name) && !is_int($name))
			{
				throw new Exception;
			}
			if (is_string($filters))
			{
				$filters = str_getcsv($filters, ";");
			}
			foreach ($filters as $key => $filter)
			{
				if ($filter instanceof IFilter || $filter instanceof Filter)
				{
					$this->pipe[] = [$name, $filter];
				}
				elseif (is_string($filter) || is_array($filter))
				{
					if (is_string($filter))
					{
						$filter = str_getcsv($filter);
					}
					$args = array_slice($filter, 1);
					$filter = $filter[0];
					$error_message = null;
					preg_match("/(.*)\((.*)\)/", $filter, $matches);
					if ($matches)
					{
						$filter = $matches[1];
						$error_message = $matches[2];
					}
					$reflect = null;
					if (isset($this->short_names[$filter]))
					{
						$class = $this->short_names[$filter];
						$filter = $class::creatFromShortName($filter, ...$args);
						if ($filter instanceof Rule)
						{
							$filter->setErrorMessage($error_message);
						}
						$this->pipe[] = [$name, $filter];
					}
					else
					{
						if (class_exists($filter))
						{
							$reflect = new ReflectionClass($filter);
						}
						elseif (class_exists($class = "\\Filter\\Filters\\" . $filter))
						{
							$reflect = new ReflectionClass($class);
						}
						elseif (class_exists($class = "\\Filter\\Rules\\" . $filter))
						{
							$reflect = new ReflectionClass($class);
						}
						if ($reflect && $reflect->implementsInterface(IFilter::class))
						{
							$class = $reflect->getName();
							$filter = $class::create(...$args);
							if ($filter instanceof Rule)
							{
								$filter->setErrorMessage($error_message);
							}
							$this->pipe[] = [$name, $filter];
						}
						else
						{
							throw new Exception($filter);
						}
					}
				}
			}
		}
		return $this;
	}

	static public function createFromFilter(Filter $filter, $map)
	{
		return (new static($filter->prefix, $filter->postfix, $filter->use_name))->__map($map);
	}

	/**
	 * 
	 * @param string|array $map
	 * @return Filter
	 * @throws Exception
	 */
	static public function map($map, ...$args)
	{
		return (new static(...$args))->__map($map);
	}

	protected function __map($map)
	{
		if (is_string($map))
		{
			$this->addFilter(0, $map);
		}
		elseif (is_array($map))
		{
			foreach ($map as $names => $filters)
			{
				if (is_int($names))
				{
					$this->addFilter(0, $map);
					break;
				}
				$this->addFilter($names, $filters);
			}
		}
		return $this;
	}

	protected function exec(Context $parent, $name = null)
	{
		if (!is_null($name) && isset($parent->data[$name]))
		{
			$context = $this->run(new Context($parent->data[$name], $this, $parent));
			$parent->data[$name] = $context->data;
			if ($context->errors)
			{
				$parent->errors[$name] = $context->errors;
			}
		}
	}

	public function trans($id, array $args = [])
	{
		$fid = ($this->prefix ? $this->prefix . "." : "") . $id . ($this->postfix ? "." . $this->postfix : "");

		return strtr($fid, $args);
	}

	/**
	 * 
	 * @param array $data
	 * @return \Filter\Context
	 */
	public function run($data)
	{

		if ($data instanceof Context)
		{
			$context = $data;
		}
		else
		{
			if (!is_array($data))
			{
				$data = [$data];
			}
			$context = new Context($data, $this);
		}

		try
		{
			foreach ($this->pipe as $filter)
			{
				try
				{
					if (!isset($context->errors[$filter[0]]))
					{
						$filter[1]->exec($context, $filter[0]);
					}
				}
				catch (NextFilter $exc)
				{
					continue;
				}
				catch (Error $exc)
				{
					$context->errors[$filter[0]] = $this->trans($exc->getMessage() . ($this->use_name ? "." . $exc->getName() : ""), $exc->getArgs());
				}
				catch (NextField $exc)
				{
					continue;
				}
			}
		}
		catch (Stop $exc)
		{
			
		}
		return $context;
	}

}
