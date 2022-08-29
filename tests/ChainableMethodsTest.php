<?php

namespace Tests;

use Buglerv\LaravelHelpers\Traits\ChainableMethods;
use PHPUnit\Framework\TestCase;

class ChainableMethodsTest extends TestCase
{
	use ChainableMethods;
	
	/**
	 * @test
	 */
    public function chainableOr()
	{
		$this->assertEquals(2,$this->oneOrTwo(1));
		$this->assertEquals('g',$this->oneOrTwo('g'));
		$this->assertEquals(10,$this->oneOrTwo(null));
	}
	
	/**
	 * @test
	 */
    public function chainableAnd()
	{
		$this->assertEquals(1,$this->oneAndTwo(1));
		$this->assertFalse($this->oneAndTwo('g'));
		$this->assertFalse($this->oneAndTwo(null));
	}
	
	protected function one($var)
	{
		if(is_numeric($var)){
			return $var * 2;
		}
		
		return false;
	}
	
	protected function two($var)
	{
		return $var ?: 10;
	}
	
	protected function three()
	{
		
	}
}