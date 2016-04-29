<?php

use Filter\Filter;
use Filter\Rules\Compare;

class RuleTest extends PHPUnit_Framework_TestCase
{

	public function testCompare()
	{
		$filter = Filter::map(["key" => [Compare::create(">=", 6, null, "str_word_count")]]);
		
		$context = $filter->run(["key" => "Some sentence"]);
		
		$this->assertCount(1, $context->errors);
	}

}
