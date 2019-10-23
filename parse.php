<?php

require_once "parse_src/Lexer.php";
require_once "parse_src/InstructionsHelper.php";
require_once "parse_src/XmlGenerator.php";

/**
 * Print help indeed
 */ 
function print_help(){
	echo "Usage: parser.php [--help]\n\n";
	echo "This php script get IPPcode19 source code from STDIN, do both lexical and syntactic analysis and print XML representation of program\n";
	echo "Error return codes:\n";
	echo "\t21 - missing or wrong header in IPPcode19 source code\n";
	echo "\t22 - missing or wrong operation code in IPPcode19 source code\n";
	echo "\t23 - other lexical or syntactical error in IPPcode19 source code\n";
}

/**
 * @return True if there is help argument in cli program aguments
 */ 
function is_there_help_argument(){
	global $argv;
	foreach ($argv as $key => $arg) {
		if(preg_match("/--help/", $arg, $array)){			
			return TRUE;
		}
	}
	return FALSE;
}

/**
 * Chcek if $token is header or is EOL and next token is header
 *
 * @param Token $token current readed token
 * @throws Exception("Invalid or missing header", 21)
 */ 
function check_header($token){
	
	// Source file must start with propper header
	if($token->type === "EOL"){
		$token = Lexer::nextToken();
		if($token->type === "header")
			return $token;			
	}else if($token->type === "header"){
		$token = Lexer::nextToken();
		if($token->type === "EOL" || $token->type === "EOF")
			return $token;	
	}

	throw new Exception("Invalid or missing header", 21);
}


/**
 * Check syntax on global level, call InstructionsHelper::check_instruction_syntax() to check indi-
 * vidual instructions syntax.
 *
 * @throws Exception("Wrong instruction", 22) 
 * @throws Exception("Other error", 23): rethrow from check_instruction_syntax()
 */ 
function check_syntax(){

	$token = new Token;
	$token = Lexer::nextToken();

	try {
	    $token = check_header($token);
	} catch (Throwable $t) {
		throw new Exception("Invalid or missing header", 21);
		return;
	}			

	while($token->type !== "EOF"){
		$token = Lexer::nextToken();
		if($token->type === "keywordOrLabel"){
			try {
			    $instruction_name = strtoupper($token->data);
			    InstructionsHelper::check_instruction_syntax($instruction_name);
			}catch (Throwable $t) {				
				throw new Exception($t->getMessage(), $t->getCode());
				return;
			}		
		}else if($token->type !== "EOF" && $token->type !== "EOL"){
			throw new Exception("Wrong instruction", 22);
			return;			
		}
	}
	XmlGenerator::endProgram();
}

function main(){

	if(is_there_help_argument()){
		print_help();
		exit(0);
	}
	
	XmlGenerator::init();

	try {
		check_syntax();
		XmlGenerator::printXmlToStdout();
	} catch (Throwable $t) {	
		fwrite(STDERR, "Parser Error: ".$t->getCode()."\n");
		exit($t->getCode());
	}	
}

main();

?>