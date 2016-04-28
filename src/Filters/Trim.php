<?php

/**
 * 
 * @license https://github.com/icetomcat/filter/blob/master/LICENSE MIT
 * @link https://github.com/filter/
 */

namespace Filter\Filters;

/**
 *
 * @author icetomcat <icetomcat@gmail.com>
 */
class Trim extends \Filter\Base\Filter
{

	protected $character_mask;

	/**
	 * @param string $character_mask [optional] <p>
	 * Optionally, the stripped characters can also be specified using
	 * the <i>character_mask</i> parameter.
	 * Simply list all characters that you want to be stripped. With
	 * .. you can specify a range of characters.
	 * </p>
	 * @param string $character_mask
	 */
	public function __construct($character_mask = " \t\n\r\0\x0B")
	{
		$this->character_mask = $character_mask;
	}

	/**
	 * 
	 * @param string $character_mask [optional] <p>
	 * Optionally, the stripped characters can also be specified using
	 * the <i>character_mask</i> parameter.
	 * Simply list all characters that you want to be stripped. With
	 * .. you can specify a range of characters.
	 * </p>
	 * @return Trim
	 */
	public static function create(...$args)
	{
		return parent::create(...$args);
	}

	public function apply($value)
	{
		return trim($value, $this->character_mask);
	}

}
