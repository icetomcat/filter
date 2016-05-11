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
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\TranslatorInterface;

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

	/**
	 *
	 * @var Translator
	 */
	protected $translator = null;
	protected $short_names = [];
	protected $translate_prefix = "";
	protected $translate_postfix = "";
	protected $translate_use_name = false;

	/**
	 * 
	 */
	public function __construct(Translator $translator = null, $translate_prefix = "", $translate_postfix = "", $translate_use_name = false)
	{
		if ($translator)
		{
			$this->setTranslator($translator);
		}
		$this->translate_prefix = $translate_prefix;
		$this->translate_postfix = $translate_postfix;
		$this->translate_use_name = $translate_use_name;
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

	public function setTranslatePrefix($translate_prefix)
	{
		$this->translate_prefix = $translate_prefix;
		return $this;
	}

	public function getTranslatePrefix()
	{
		return $this->translate_prefix;
	}

	public function setTranslatePostfix($translate_postfix)
	{
		$this->translate_postfix = $translate_postfix;
		return $this;
	}

	public function getTranslatePostfix()
	{
		return $this->translatePostfix;
	}

	public function setTranslateUseName($translate_use_name)
	{
		$this->translate_use_name = $translate_use_name;
		return $this;
	}

	public function getTranslatUseName()
	{
		return $this->translate_use_name;
	}

	public function setTranslator(TranslatorInterface $translator)
	{
		$this->translator = $translator;
		foreach (array_diff(scandir($dir = __DIR__ . "/I18n"), array('..', '.')) as $item)
		{
			if (file_exists($file_name = ($dir . "/$item/messages.{$item}.yaml")))
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

	/**
	 * 
	 * @param string|array $map
	 * @return Filter
	 * @throws Exception
	 */
	static public function map($map)
	{
		return (new static())->__map($map);
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

	public function trans($id, array $args = [], $domain = null, $locale = null)
	{
		$fid = ($this->translate_prefix ? $this->translate_prefix . "." : "") . $id . ($this->translate_postfix ? "." . $this->translate_postfix : "");
		if ($this->translator)
		{
			if ($this->translator->getCatalogue()->has($fid))
			{
				return $this->translator->trans($fid, $args, $domain, $locale);
			}
			else
			{
				return $this->translator->trans($id, $args, $domain, $locale);
			}
		}
		else
		{
			return strtr($fid, $args);
		}
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
					$filter[1]->exec($context, $filter[0]);
				}
				catch (NextFilter $exc)
				{
					continue;
				}
				catch (Error $exc)
				{
					$context->errors[$filter[0]] = $this->trans($exc->getMessage() . ($this->translate_use_name ? "." . $exc->getName() : ""), $exc->getArgs());
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
