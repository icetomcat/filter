<?php

use Filter\Filter;

class RecursionTest extends PHPUnit_Framework_TestCase
{

	public function testUser()
	{
		$filter = Filter::map([
					"[user]" => [
						"login,password,confirm" => "Trim,\r\n\t\0\x0B",
						"password" => "Compare,===,,confirm"
					],
					"[profile]" => [
						"first_name,last_name,middle_name" => ["Trim", "Lower", "FirstUpper"]
					]
		]);

		$context = $filter->run([
			"user" => ["login" => "\tlogin\r\n", "password" => "pass\r\n", "confirm" => "pass"],
			"profile" => ["first_name" => "иван", "last_name" => "иванов ", "middle_name" => " иванович "]
		]);

		$this->assertCount(0, $context->errors);

		$this->assertEquals("Иван", $context->data["profile"]["first_name"]);
		$this->assertEquals("Иванов", $context->data["profile"]["last_name"]);
		$this->assertEquals("Иванович", $context->data["profile"]["middle_name"]);

		$this->assertEquals("login", $context->data["user"]["login"]);
		$this->assertEquals("pass", $context->data["user"]["password"]);
	}

}
