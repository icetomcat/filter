<?php

use Filter\Filter;
use Filter\Rules\Equal;

class RuleTest extends PHPUnit_Framework_TestCase
{

	public function testEqual()
	{
		$filter = Filter::map(["key" => [Equal::create("value")]]);

		$this->assertCount(0, $filter->run(["key" => "value"])->errors);
		$this->assertCount(1, $filter->run([])->errors);
		$this->assertCount(1, $filter->run(["key" => "other"])->errors);

		
		$filter = Filter::map(["key" => [Equal::create(0)]]);
		
		$this->assertCount(0, $filter->run(["key" => "0"])->errors);
		$this->assertCount(0, $filter->run(["key" => 0])->errors);
		$this->assertCount(0, $filter->run(["key" => false])->errors);
		$this->assertCount(0, $filter->run(["key" => 0.0])->errors);
		$this->assertCount(1, $filter->run(["key" => []])->errors);
		$this->assertCount(0, $filter->run(["key" => null])->errors);
		$this->assertCount(0, $filter->run([])->errors);

		
		$filter = Filter::map(["key" => [Equal::create("1")]]);
		
		$this->assertCount(0, $filter->run(["key" => "1"])->errors);
		$this->assertCount(0, $filter->run(["key" => 1])->errors);
		$this->assertCount(0, $filter->run(["key" => true])->errors);
		$this->assertCount(0, $filter->run(["key" => 1.0])->errors);
		$this->assertCount(1, $filter->run(["key" => [1]])->errors);

		
		$filter = Filter::map(["key" => [Equal::create("1", true)]]);
		
		$this->assertCount(0, $filter->run(["key" => "1"])->errors);
		$this->assertCount(1, $filter->run(["key" => 1])->errors);
		$this->assertCount(1, $filter->run(["key" => true])->errors);
		$this->assertCount(1, $filter->run(["key" => 1.0])->errors);
		$this->assertCount(1, $filter->run(["key" => [1]])->errors);

		
		$filter = Filter::map(["password" => [Equal::create(null, true, "confirm")]]);
		
		$this->assertCount(0, $filter->run(["password" => "123", "confirm" => "123"])->errors);
		$this->assertCount(1, $filter->run(["password" => "123", "confirm" => "321"])->errors);
		$this->assertCount(1, $filter->run(["password" => "123"])->errors);
		$this->assertCount(0, $filter->run([])->errors);
		$this->assertCount(1, $filter->run(["confirm" => "321"])->errors);
	}

}
