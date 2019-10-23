
class Instruction:
	""" 
	Represents instruction in IPPcode19
	"""		

	def __init__(self, name, arguments,  order=None ):
		self.name = name.upper()
		self.arguments = arguments
		if order:
			self.order = int(order)
		else:
			self.order = None

	def __str__(self):
		""" 
		Just for friendly debug print
		"""				

		result = str(self.name)

		for argument in self.arguments:
			result += " " + argument.__str__()

		if self.order:		
			return result + " (" + str(self.order) + ")"
		else:
			return result

	def __repr__(self):
		return str(self)

	def __eq__(self, other):
		""" 
		Returns: 
		Bool: True if instruction has same name and same argument types
		"""				
		return self.name == other.name and self.arguments == other.arguments

	def __ne__(self, other):
		return not self.__eq__(self, other)