<?php

/**
 * Represents token with type and data information.
 */ 
class Token{

	// header, keywordOrLabel, var, string, int, bool, nil, error, EOL, EOF
	public $type;	

	// ADD, AND, BREAK, CALL, CONCAT, CREATEFRAME, DEFVAR, DPRINT, EXIT, GETCHAR, GT, LT, EQ, IDIV, INT2CHAR, JUMP, JUMPIFEQ, JUMPIFNEQ, LABEL, MOVE, MUL, NOT, OR, POPFRAME, POPS, PUSHFRAME, PUSHS, READ, RETURN, SETCHAR, STRI2INT, STRLEN, SUB, TYPE, WRITE
	// label8
	// GF@counter
	// adam
	// -9
	// true
	public $data;
}

?>