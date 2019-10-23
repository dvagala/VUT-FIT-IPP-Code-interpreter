
class Argument:
	""" 
	Represents argument in IPPcode19
	"""		

	def __init__(self, type, name="",  order=0):
		if name == None:
			self.name = ""
		else:
			self.name = name
			
		self.type = type
		self.order = int(order)

	def __str__(self):
		if self.name:
			return self.name		
		else:
			if self.type:
				return f" <{self.type}>"
			else:
				return ""

	def __eq__(self, other):		
		""" 
		Smart argument type comparison for syntax check, e.g. "type" match not just with "type", 
		but also with "string", "int", "bool" 
		  
		Returns: 
		Bool: if same type
		"""		

		if self.type == other.type:
			return True
		elif "symb" == self.type:
			if other.type in ("var", "string", "int", "bool", "nil"):
				return True
		elif "symb" == other.type:
			if self.type in ("var", "string", "int", "bool", "nil"):
				return True
		elif "type" == self.type:
			if other.type in ("string", "int", "bool"):
				return True						
		elif "type" == other.type:
			if self.type in ("string", "int", "bool"):
				return True		
		return False


	def __ne__(self, other):
		return not __eq__(self, other)