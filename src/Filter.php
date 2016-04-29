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
use ReflectionClass;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Translator;

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
	protected $translator = null;
	protected $short_names = [];

	/**
	 * 
	 */
	public function __construct($translator = null)
	{
		$this->translator = $translator;
		
		$this->short_names["cmp"] = Rules\Compare::class;
	}

	public function setTranslator($translator)
	{
		$this->translator = $translator;
		foreach (array_diff(scandir($dir = __DIR__ . "/I18n"), array('..', '.')) as $item)
		{
			if(file_exists($file_name = ($dir . "/$item/messages.{$item}.yaml")))
			{
				$this->translator->addResource("yaml", $file_name, $item);
			}
		}
		return $this;
	}

	public function getTranslator()
	{
		return $this->translator;
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
				$filters = [(new static($this->translator))->__map($filters)];
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
					$error_message = null;
					preg_match("/(.*)\((.*)\)/", $filter, $matches);
					if ($matches)
					{
						$filter = $matches[1];
						$error_message = $matches[2];
					}
					$reflect = null;
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
						if ($filter instanceof Base\Rule)
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
		return (new static())->__map($map);
	}

	protected function __map(array $map)
	{
		foreach ($map as $names => $filters)
		{
			$this->addFilter($names, $filters);
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
		if ($this->translator)
		{
			return $this->translator->trans($id, $args);
		}
		else
		{
			return strtr($id, $args);
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
			$context = new Context($data, $this);
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
					$context->errors[$filter[0]] = $this->trans($exc->getMessage(), $exc->getArgs());
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
