<?php

use Filter\Filter;
use Filter\Filters\FirstLower;
use Filter\Filters\FirstUpper;
use Filter\Filters\Lower;
use Filter\Filters\Trim;
use Filter\Filters\Upper;

class FilterTest extends PHPUnit_Framework_TestCase
{

	public function testTrimFilter()
	{
		$filter = Filter::map(["key" => [Trim::create()]]);

		$this->assertEquals("value", $filter->run(["key" => "value"])->data["key"]);
		$this->assertEquals("value", $filter->run(["key" => " value"])->data["key"]);
		$this->assertEquals("value", $filter->run(["key" => "value "])->data["key"]);
		$this->assertEquals("value", $filter->run(["key" => " value "])->data["key"]);
		$this->assertEquals("value", $filter->run(["key" => "\t\t value\r\n"])->data["key"]);

		$filter = Filter::map(["key" => [Trim::create(" ")]]);

		$this->assertEquals("value", $filter->run(["key" => " value  "])->data["key"]);
		$this->assertEquals("\t\tvalue\n", $filter->run(["key" => "\t\tvalue\n "])->data["key"]);
	}

	public function testStringCaseFilters()
	{
		$filter = Filter::map([
					"to_upper" => [Upper::create()],
					"to_lower" => [Lower::create()],
					"first_up" => [FirstUpper::create()],
					"first_low" => [FirstLower::create()],
		]);

		$context = $filter->run([
			"to_upper" => "Τάχιστη αλώπηξ βαφής ψημένη γη, δρασκελίζει υπέρ νωθρού κυνός",
			"to_lower" => "ΤΆΧΙΣΤΗ ΑΛΏΠΗΞ ΒΑΦΉΣ ΨΗΜΈΝΗ ΓΗ, ΔΡΑΣΚΕΛΊΖΕΙ ΥΠΈΡ ΝΩΘΡΟΎ ΚΥΝΌΣ",
			"first_up" => "τάχιστη",
			"first_low" => "Τάχιστη",
		]);

		$this->assertEquals("ΤΆΧΙΣΤΗ ΑΛΏΠΗΞ ΒΑΦΉΣ ΨΗΜΈΝΗ ΓΗ, ΔΡΑΣΚΕΛΊΖΕΙ ΥΠΈΡ ΝΩΘΡΟΎ ΚΥΝΌΣ", $context->data["to_upper"]);
		$this->assertEquals("τάχιστη αλώπηξ βαφήσ ψημένη γη, δρασκελίζει υπέρ νωθρού κυνόσ", $context->data["to_lower"]);
		$this->assertEquals("Τάχιστη", $context->data["first_up"]);
		$this->assertEquals("τάχιστη", $context->data["first_low"]);
	}

}
