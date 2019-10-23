<?php

/**
 * Wrapper for Xmlwriter extension.
 */ 
class XmlGenerator{

	private static $instruction_count = 0;
	private static $argument_count = 0;

	private static $xml;

	/**
 	 * Add init xmlwriter and add xml start code
 	 */ 
	public static function init(){
		self::$xml = xmlwriter_open_memory();
		xmlwriter_set_indent(self::$xml, 1);
		$res = xmlwriter_set_indent_string(self::$xml, "   ");
		xmlwriter_start_document(self::$xml, '1.0', 'UTF-8');

		xmlwriter_start_element(self::$xml, 'program');
		xmlwriter_start_attribute(self::$xml, 'language');
		xmlwriter_text(self::$xml, 'IPPcode19');
		xmlwriter_end_attribute(self::$xml);
	}

	/**
 	 * Add xml ending code
 	 */ 
	public static function endProgram(){
		if(self::$xml)
			xmlwriter_end_document(self::$xml);
	}

	/**
 	 * Add instruction in xml code
 	 *
	 * @param String $instruction e.g., DEFVAR	 
 	 */ 
	public static function addInstruction($instruction){		

		if(self::$instruction_count != 0)
			xmlwriter_end_element(self::$xml); // end previous instruction

		self::$argument_count = 0;
		self::$instruction_count++;

		xmlwriter_start_element(self::$xml, 'instruction');
		
		xmlwriter_start_attribute(self::$xml, 'opcode');
		xmlwriter_text(self::$xml, $instruction);
		xmlwriter_end_attribute(self::$xml);

		xmlwriter_start_attribute(self::$xml, 'order');
		xmlwriter_text(self::$xml, self::$instruction_count);
		xmlwriter_end_attribute(self::$xml);
	}

	/**
 	 * Add argument to instruction in xml code
 	 *
	 * @param String $arg_type e.g., string, int 	
	 * @param String $arg_data e.g., hello, 8
 	 */ 
	public static function addArgument($arg_type, $arg_data){

		self::$argument_count++;

		xmlwriter_start_element(self::$xml, 'arg'.self::$argument_count);
		xmlwriter_start_attribute(self::$xml, 'type');
		xmlwriter_text(self::$xml, $arg_type);
		xmlwriter_end_attribute(self::$xml);
		xmlwriter_text(self::$xml, $arg_data);
				
		xmlwriter_end_element(self::$xml); // end argument
	}	

	/**
 	 * Print xml code t STDOUT.
 	 */ 
	public static function printXmlToStdout(){
		if(self::$xml)
			echo xmlwriter_output_memory(self::$xml);
	}
}

?>