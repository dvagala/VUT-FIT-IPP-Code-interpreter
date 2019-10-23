
class Error:
	""" 
	Represents one error type according to project specs.

	Attributes
	description (str) : friendly description of error type that will be printed to stderr
	code (int) : exit code
	"""			
	def __init__(self, description, code):
		self.description = description
		self.code = code

	def __str__(self):
		return self.description

wrongParameters = Error("Missing parameter or illegal combination", 10)
cannotOpenSourceFiles = Error("Cannot open source files", 11)
cannotOpenOutputFiles = Error("Cannot open files to write", 12)
internalError = Error("Internal error", 99)
xmlNotWellFormated = Error("Wrong XML syntax, not well formatted", 31)
xmlStructureSyntaxLex = Error("Wrong XML structure or syntactic/lexical error", 32)
semantics = Error("semantics error in XML, e.g. not defined label or redefinition of label", 52)
wrongOperandType = Error("Wrong operand type", 53)
variableNotDefined = Error("Accessing not defined variable, frame exists", 54)
frameNotExists = Error("Frame not exists", 55)
missingValue = Error("Missing value in variable or stack", 56)
wrongOperandValue = Error("Wrong operand value, e.g. division by zero", 57)
wrongStringManipulation = Error("Wrong string manipulation", 58)
