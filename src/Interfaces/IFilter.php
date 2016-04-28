<?php

/**
 * 
 * @license https://github.com/icetomcat/filter/blob/master/LICENSE MIT
 * @link https://github.com/filter/
 */

namespace Filter\Interfaces;

/**
 *
 * @author icetomcat <icetomcat@gmail.com>
 */
interface IFilter
{

	public function exec(\Filter\Context $context, $name);

	public function apply($value);
}
