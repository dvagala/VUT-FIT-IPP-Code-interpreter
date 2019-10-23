<?php
require_once "Token.php";
require_once "XmlGenerator.php";
require_once "Instruction.php";

/**
 * Bridge between global syntax analysis in parse.php and Instrucion objects
 */ 
class InstructionsHelper{
	
	private static $instr_rules = array();
	
	/**
 	 * Get Instruction object from array $instr_rules by instruction name.
 	 *
 	 * @param String $instruction_name
 	 * @return Instruction object or NULL if instruction not found
 	 */ 
	private static function get_instruction($instruction_name){
		
		foreach (self::$instr_rules as $instr_rule) {
			if($instr_rule->instruction === $instruction_name){
				return new Instruction($instr_rule->instruction, $instr_rule->arg1, 
					$instr_rule->arg2, $instr_rule->arg3);
			}
		}
		return NULL;
	}

	/**
 	 * Fill array $instr_rules with instructions syntax rules according to IPPcode19 specification.
 	 */ 
	public static function fill_instructions_rules(){	
		array_push(self::$instr_rules, new Instruction("MOVE", "var", "symb"));
		array_push(self::$instr_rules, new Instruction("CREATEFRAME"));
		array_push(self::$instr_rules, new Instruction("PUSHFRAME"));
		array_push(self::$instr_rules, new Instruction("POPFRAME"));
		array_push(self::$instr_rules, new Instruction("DEFVAR", "var"));
		array_push(self::$instr_rules, new Instruction("CALL", "label"));
		array_push(self::$instr_rules, new Instruction("RETURN"));
		array_push(self::$instr_rules, new Instruction("PUSHS", "symb"));	
		array_push(self::$instr_rules, new Instruction("POPS", "var"));		
		array_push(self::$instr_rules, new Instruction("ADD", "var", "symb", "symb"));	
		array_push(self::$instr_rules, new Instruction("SUB", "var", "symb", "symb"));
		array_push(self::$instr_rules, new Instruction("MUL", "var", "symb", "symb"));
		array_push(self::$instr_rules, new Instruction("IDIV", "var", "symb", "symb"));
		array_push(self::$instr_rules, new Instruction("LT", "var", "symb", "symb"));
		array_push(self::$instr_rules, new Instruction("GT", "var", "symb", "symb"));
		array_push(self::$instr_rules, new Instruction("EQ", "var", "symb", "symb"));
		array_push(self::$instr_rules, new Instruction("AND", "var", "symb", "symb"));
		array_push(self::$instr_rules, new Instruction("OR", "var", "symb", "symb"));
		array_push(self::$instr_rules, new Instruction("NOT", "var", "symb"));
		array_push(self::$instr_rules, new Instruction("INT2CHAR", "var", "symb"));
		array_push(self::$instr_rules, new Instruction("STRI2INT", "var", "symb", "symb"));
		array_push(self::$instr_rules, new Instruction("READ", "var", "type"));	
		array_push(self::$instr_rules, new Instruction("WRITE", "symb"));
		array_push(self::$instr_rules, new Instruction("CONCAT", "var", "symb", "symb"));
		array_push(self::$instr_rules, new Instruction("STRLEN", "var", "symb"));
		array_push(self::$instr_rules, new Instruction("GETCHAR", "var", "symb", "symb"));
		array_push(self::$instr_rules, new Instruction("SETCHAR", "var", "symb", "symb"));
		array_push(self::$instr_rules, new Instruction("TYPE", "var", "symb"));
		array_push(self::$instr_rules, new Instruction("LABEL", "label"));
		array_push(self::$instr_rules, new Instruction("JUMP", "label"));
		array_push(self::$instr_rules, new Instruction("JUMPIFEQ", "label", "symb", "symb"));
		array_push(self::$instr_rules, new Instruction("JUMPIFNEQ", "label", "symb", "symb"));
		array_push(self::$instr_rules, new Instruction("EXIT", "symb"));
		array_push(self::$instr_rules, new Instruction("DPRINT", "symb"));
		array_push(self::$instr_rules, new Instruction("BREAK"));
	}

	/**
 	 * Basically just extended string comparison, so we can compare non terminal with terminal
 	 *
 	 * @param String $non_terminal, String $terminal
 	 * @return True if parameters types matched
 	 */ 
	private static function type_match($non_terminal, $terminal){
		if($non_terminal === $terminal){
			return TRUE;
		}else if($non_terminal === "symb"){
			if($terminal === "var" || $terminal === "string" || $terminal === "int"
				|| $terminal === "bool" || $terminal === "nil")
				return TRUE;									
		}else if($non_terminal === "type"){
			if($terminal === "string" || $terminal === "int"
				|| $terminal === "bool")
				return TRUE;									
		}
		return FALSE;
	}

	/**
 	 * Get current instruction name and gradually call Lexer::nextToken() to check if syntax is correct.
 	 * Also check if after every instruction is EOL.
 	 *
 	 * @param String $instruct_name
 	 * @return True if parameters types matched
 	 * @throws Exception("Wrong instruction", 22)
 	 * @throws Exception("Other error", 23)
 	 */ 
	public static function check_instruction_syntax($instruct_name){
		
		// First time
		if(empty(self::$instr_rules)){			
			InstructionsHelper::fill_instructions_rules();
		}

		XmlGenerator::addInstruction($instruct_name);

		$instr_rule = self::get_instruction($instruct_name);

		if(!$instr_rule){
			throw new Exception("Wrong instruction", 22);
			return;
		}

		$token = new Token;

		foreach ($instr_rule->get_args() as $arg) {
			$token = Lexer::nextToken();	

			// To keep things working		
			if($token->type === "keywordOrLabel"){
				if($instruct_name === "READ"){
					$token->type = "type";	
					if($token->data !== "string" && $token->data !== "int"
						&& $token->data !== "bool"){
						throw new Exception("Other error", 23);
						return;
					}
				}			
				else
					$token->type = "label";
			}
			if(!self::type_match($arg, $token->type)){
				throw new Exception("Other error", 23);
				return;
			}
			XmlGenerator::addArgument($token->type, $token->data);	
		}

		// Every line with instruction have to ends with END or EOF
		$token = Lexer::nextToken();
		if($token->type !== "EOL" && $token->type !== "EOF" )
			throw new Exception("Other error", 23);				
	}		
}
?>