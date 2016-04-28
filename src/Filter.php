<?php

/**
 * 
 * @license https://github.com/icetomcat/filter/blob/master/LICENSE MIT
 * @link https://github.com/filter/
 */

namespace Filter;

use Exception;
use Filter\Exceptions\Error;
use Filter\Exceptions\NextField;
use Filter\Exceptions\NextFilter;
use Filter\Exceptions\Stop;
use Filter\Interfaces\IFilter;

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
	protected $pipe = array();

	/**
	 * 
	 */
	public function __construct()
	{
		
	}

	/**
	 * 
	 * @param string $names
	 * @param mixed $filters
	 * @return \Filter\Filter
	 */
	public function addFilter($names, $filters)
	{
		if (is_string($names))
		{
			preg_match("/\[(.*)\]/", $names, $matches);
			if (isset($matches[1]) && $matches[1])
			{
				$names = $matches[1];
				$filters = [static::map($filters)];
			}
			$names = str_getcsv($names);
		}
		elseif (!is_array($names))
		{
			throw new Exception;
		}
		foreach ($names as $name)
		{
			if (!is_string($name))
			{
				throw new Exception;
			}
			if (!is_array($filters))
			{
				$filters = [$filters];
			}
			foreach ($filters as $key => $filter)
			{
				if ($filter instanceof IFilter || $filter instanceof Filter)
				{
					$this->pipe[] = [$name, $filter];
				}
				elseif (is_string($filter))
				{
					$filter = str_getcsv($filter);
					$args = array_slice($filter, 1);
					$filter = $filter[0];
					$reflect = null;
					if (class_exists($filter))
					{
						$reflect = new \ReflectionClass($filter);
					}
					elseif (class_exists($class = "\\Filter\\Filters\\" . $filter))
					{
						$reflect = new \ReflectionClass($class);
					}
					elseif (class_exists($class = "\\Filter\\Rules\\" . $filter))
					{
						$reflect = new \ReflectionClass($class);
					}
					if ($reflect && $reflect->implementsInterface(IFilter::class))
					{
						$class = $reflect->getName();
						$this->pipe[] = [$name, $class::create(...$args)];
					}
					else
					{
						throw new Exception();
					}
				}
			}
		}
		return $this;
	}

	/**
	 * 
	 * @param array $map
	 * @return Filter
	 * @throws Exception
	 */
	static public function map(array $map)
	{
		$self = new static();
		foreach ($map as $names => $filters)
		{
			$self->addFilter($names, $filters);
		}
		return $self;
	}

	protected function exec(Context $parent, $name = null)
	{
		if (!is_null($name) && isset($parent->data[$name]))
		{
			$context = $this->run(new Context($parent->data[$name], $parent));
			$parent->data[$name] = $context->data;
			$parent->errors[$name] = $context->errors;
		}
	}

	/**
	 * 
	 * @param array $data
	 * @return \Filter\Context
	 */
	public function run($data)
	{
		if (is_array($data))
		{
			$context = new Context($data);
		}
		elseif ($data instanceof Context)
		{
			$context = $data;
		}
		try
		{
			foreach ($this->pipe as $filter)
			{
				try
				{
					$filter[1]->exec($context, $filter[0]);
				}
				catch (NextFilter $exc)
				{
					continue;
				}
				catch (Error $exc)
				{
					$context->errors[$filter[0]] = $exc->getMessage();
					throw new Stop;
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
