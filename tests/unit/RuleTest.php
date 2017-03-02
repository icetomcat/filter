<?php

use Filter\Filter;
use Filter\Rules\IsBoolean;
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

	public function testIsBoolean()
	{
		$filter = Filter::map([IsBoolean::create()]);
		$this->assertCount(0, $filter->run("yes")->errors);
		$this->assertCount(0, $filter->run("true")->errors);
		$this->assertCount(0, $filter->run("1")->errors);
		$this->assertCount(0, $filter->run("on")->errors);
		$this->assertCount(0, $filter->run("no")->errors);
		$this->assertCount(0, $filter->run("false")->errors);
		$this->assertCount(0, $filter->run("0")->errors);
		$this->assertCount(0, $filter->run("off")->errors);
		$this->assertCount(0, $filter->run("")->errors);
		
		$this->assertCount(1, $filter->run("oops")->errors);
	}

}
