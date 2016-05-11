<?php

use Filter\Filter;
use Filter\Rules\Compare;

class RuleTest extends PHPUnit_Framework_TestCase
{

	public function testCompare()
	{
		$filter = Filter::map([Compare::create(">=", 6, null, "Trim;Words")]);
		$context = $filter->run("Some sentence");
		$this->assertCount(1, $context->errors);
		
		$filter = Filter::map([Compare::create(">=", 6, null, "Trim;Length")]);
		$context = $filter->run("Some sentence");
		$this->assertCount(0, $context->errors);
		
		$filter = Filter::map([[">=", 6, null, "Trim;Length"]]);
		$context = $filter->run("            ");
		$this->assertCount(1, $context->errors);
		
		$filter = Filter::map([[">", 0, null, "Trim;Length"], ["<", 5, null, "Trim;Length"]]);
		$context = $filter->run("      1      ");
		$this->assertCount(0, $context->errors);

		$context = $filter->run("      123456      ");
		$this->assertCount(1, $context->errors);
	}

}
