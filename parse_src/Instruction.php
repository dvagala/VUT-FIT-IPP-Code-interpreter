<?php

/**
 * Represent one instruction syntax, store information about instruction name and it’s parameters. 
 * E.g., STRI2INT <var> <symb> <symb>.
 */ 
class Instruction{
	public $instruction = 0;
	public $arg1 = NULL;
	public $arg2 = NULL;
	public $arg3 = NULL;

	/** 	 
 	 * Set instruction arguments
 	 */ 
	function __construct($instruction, $arg1 = NULL, $arg2 = NULL, $arg3 = NULL){
		$this->instruction = $instruction;
		$this->arg1 = $arg1;
		$this->arg2 = $arg2;
		$this->arg3 = $arg3;
	}	

	/** 	 
 	 * @return instruction arguments if contains some
 	 */ 
	public function get_args(){
		$args = array();
		if($this->arg1)
			array_push($args, $this->arg1);
		if($this->arg2)
			array_push($args, $this->arg2);
		if($this->arg3)
			array_push($args, $this->arg3);

		return $args;
	}
}

?>