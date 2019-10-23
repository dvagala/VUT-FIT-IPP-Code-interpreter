<?php

require_once "Token.php";

/**
 * Provides lexical analysis.
 */ 
class Lexer{

	// ASCII values
	private const space = 32;
	private const newLineUnix = 10;
	private const newLineWin = 13;
	private const horizTab = 9;
	private const hashtag = 35;
	private const EOF = 0;

	private static $c = "init_char";

	private static $foundEOF = FALSE;
	private static $foundEOL = FALSE;

	/**
     * Read and discard comments from STDIN EOL or EOF are found
     */ 
	private static function discard_comments(){

		// Here no need to chceck windows line ending, just wait for \n
		// #Comment\r\n
		while(ord(self::$c) != self::newLineUnix && ord(self::$c) != self::EOF){		
			self::$c = fgetc(STDIN);
		}

		if(ord(self::$c) == self::EOF)
			self::$foundEOF = TRUE;		
		else if(ord(self::$c) == self::newLineUnix)
			self::$foundEOL = TRUE;
	}

	/**
     * Discard whitespaces and comments from STDIN, stop at the beginning of code or header
     */ 
	private static function discardUselessStuffFromStdin(){
			
		if(ord(self::$c) == self::EOF){			
			self::$foundEOF = TRUE;
			return;
		}

		while(ord(self::$c) == self::space || ord(self::$c) == self::newLineUnix || ord(self::$c) == self::newLineWin || ord(self::$c) == self::horizTab || ord(self::$c) == self::hashtag || self::$c === "init_char"){

			// Strip whitespaces
			while(ord(self::$c) == self::space || ord(self::$c) == self::newLineUnix || ord(self::$c) == self::newLineWin || ord(self::$c) == self::horizTab || self::$c === "init_char"){

				if(ord(self::$c) == self::newLineUnix)
					self::$foundEOL = TRUE;

				self::$c = fgetc(STDIN);
			}
	
			if(ord(self::$c) == self::hashtag){		
				self::discard_comments();
			}
		}
	}

	/**
     * Read symbols, stop at whitespace or comment.
     *
     * @return String $newString readed string
     */ 
	public static function readString(){

		$newString = "";

		// Read the newString
		while(ord(self::$c) != self::space && ord(self::$c) != self::newLineUnix && ord(self::$c) != self::newLineWin && ord(self::$c) != self::horizTab && ord(self::$c) != self::EOF && ord(self::$c) != self::hashtag){
			$newString .= self::$c;
			self::$c = fgetc(STDIN);

			self::$foundEOL = ord(self::$c) == self::newLineUnix || ord(self::$c) == self::newLineWin;
		}

		return $newString;
	}

	/**
     * With regexes determine what type of code was there, store the information in the token object and  
     * return to caller.
     *
     * @return Token $token new founded token
     */ 
	public static function nextToken(){
		$token = new Token;

		self::discardUselessStuffFromStdin();	

		if(self::$foundEOL){
			self::$foundEOL = FALSE;
			$token->type = "EOL";
			return $token;
		} else if(self::$foundEOF){
			self::$foundEOF = FALSE;
			$token->type = "EOF";
			return $token;
		}

		$newString = self::readString();

		if(strtoupper($newString) === ".IPPCODE19"){
			$token->type = "header";
		} else if(preg_match("/^(GF|LF|TF)@([a-z]|[A-Z]|[\_\-\$\&\%\*\?\!])(\w|[\_\-\$\&\%\*\?\!])*$/", $newString, $array)){
			$token->type = "var";
			$token->data = $array[0];		
		} else if(preg_match("/^string@(([^\s\#\\\\]|\\\\[0-9]{3})*$)/", $newString, $array)) {
				$token->type = "string";
				$token->data = $array[1];	
		} else if(preg_match("/^int@([-\+]?[0-9]+$)/", $newString, $array)) {
				$token->type = "int";
				$token->data = $array[1];
		} else if(preg_match("/^bool@(false|true)$/", $newString, $array)) {
				$token->type = "bool";				
				$token->data = $array[1];	
		} else if(preg_match("/^nil@nil$/", $newString, $array)) {
				$token->type = "nil";				
				$token->data = "nil";
		} else if(preg_match("/^([a-z]|[A-Z]|[\_\-\$\&\%\*\?\!])(\w|[\_\-\$\&\%\*\?\!])*$/", $newString, $array)){
			$token->type = "keywordOrLabel";
			$token->data = $array[0];		
		} else {
			$token->type = "error";	
		}

		return $token;
	}

}

?>