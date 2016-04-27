<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Filter;

/**
 * Description of Context
 *
 * @author icetomcat
 */
class Context
{
	public $errors = array();
	public $data = array();
	
	public function __construct(array $data)
	{
		$this->data = $data;
	}
}
