<?php

namespace Filter;

use Exception;
use Filter\Exceptions\Error;
use Filter\Exceptions\NextField;
use Filter\Exceptions\NextFilter;
use Filter\Exceptions\Stop;
use Filter\Interfaces\IFilter;

/**
 * 
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
	 * @param string $name
	 * @param IFilter $filter
	 * @return \Filter\Filter
	 */
	public function addFilter($name, IFilter $filter)
	{
		$this->pipe[$name][] = $filter;
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
		foreach ( $map as $name => $filters )
		{
			if ( $filters instanceof IFilter )
			{
				$filters = array($filters);
			}
			foreach ( $filters as $filter )
			{
				if ( $filter instanceof IFilter )
				{
					$self->addFilter($name, $filter);
				}
				else
				{
					throw new Exception;
				}
			}
		}
		return $self;
	}

	/**
	 * 
	 * @param array $data
	 * @return \Filter\Context
	 */
	public function run($data)
	{
		$context = new Context($data);
		try
		{
			foreach ( $this->pipe as $name => $filters )
			{
				try
				{
					foreach ( $filters as $filter )
					{
						try
						{
							$filter->exec($context, $name);
						}
						catch ( NextFilter $exc )
						{
							continue;
						}
					}
				}
				catch ( Error $exc )
				{
					$context->errors[$name] = $exc->getMessage();
					throw new Stop;
				}
				catch ( NextField $exc )
				{
					continue;
				}
			}
		}
		catch ( Stop $exc )
		{
			
		}
		return $context;
	}

}
