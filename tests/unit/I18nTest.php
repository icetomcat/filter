<?php

use Filter\Filter;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Translator;

class I18nTestTest extends PHPUnit_Framework_TestCase
{

	protected $translator;

	protected function setUp()
	{
		Locale::setDefault("ru_RU");
		$this->translator = new Translator("ru_RU", new MessageSelector());
		$this->translator->addLoader("yaml", new YamlFileLoader());
		$this->translator->addResource("yaml", __DIR__ . "/../I18n/" . "ru_RU" . "/fields." . "ru_RU" . ".yaml", "ru_RU");

		$this->translator->addResource("yaml", __DIR__ . "/../I18n/en_EN/fields.en_EN.yaml", "en_EN");
	}

	public function testI18nEqual()
	{
		$filter = Filter::map([
					"password" => "Compare(my.rule.compare.===),===,,confirm"
				])->setTranslator($this->translator);

		$context = $filter->run(["password" => "pass", "confirm" => "wrong"]);
		$this->assertEquals("пароли не совпадают", $context->errors["password"]);

		$this->translator->setLocale("en_EN");
		$context = $filter->run(["password" => "pass", "confirm" => "wrong"]);
		$this->assertEquals("passwords is not equal", $context->errors["password"]);

		$this->translator->setLocale("ru_RU");
		$filter = Filter::map(["num" => "Compare,>=,5"])->setTranslator($this->translator);
		$context = $filter->run(["num" => "4"]);
		$this->assertEquals("число должен быть больше либо равен 5", $context->errors["num"]);
	}

}
