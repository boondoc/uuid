<?php
	namespace	Boondoc\Tests\Units; #TODO: HOW CAN I CHANGE THIS TO SOMETHING LESS ICK?

	use		atoum;
	use		Boondoc\uuid\InvalidString;
	use		Boondoc\uuid\InvalidBinary;
	use		Boondoc\uuid\InvalidNamespace;
	use		Boondoc\uuid\InvalidAddress;


	class uuid extends atoum
	{
		public function testImportLong ($string_given, $should_validate, $message)
		{
			$maybe					= $should_validate ? 'isIdenticalTo' : 'isNotIdenticalTo';
			$uuid					= $this->newTestedInstance ($string_given);
			$string_clean 				=  trim ($string_given, '{}');

			$this	->assert			($message)

				->object			($uuid)
				->isTestedInstance		()
				->isInstanceOf			($this->getTestedClassName ())

				->string			($string_clean)
				->$maybe			($uuid->long)

				->castToString			($uuid)
				->$maybe			($string_clean);
		}

		public function testImportShort ($string_given, $should_validate, $message)
		{
			$maybe					= $should_validate ? 'isIdenticalTo' : 'isNotIdenticalTo';
			$uuid					= $this->newTestedInstance ($string_given);
			$string_clean				=  str_replace (['+', '/', '='], ['-', '_', ''], $string_given);

			$this	->assert			($message)

				->object			($uuid)
				->isTestedInstance		()
				->isInstanceOf			($this->getTestedClassName ())

				->string			($string_clean)
				->$maybe			($uuid->short);
		}

		public function testImportBinary ($string_given, $should_validate, $message)
		{
			$maybe					= $should_validate ? 'isIdenticalTo' : 'isNotIdenticalTo';
			$uuid					= $this->newTestedInstance ($string_given);

			$this	->object			($uuid)
				->isTestedInstance		()
				->isInstanceOf			($this->getTestedClassName ());

			$this	->string			($string_given)
				->$maybe			($uuid->binary);
		}

		public function testImportInvalidString ($string)
		{
			$this	->exception			( function () use ($string) { $this->newTestedInstance ($string, false); })
				->isInstanceOf			( InvalidString::class);
		}

		public function testImportInvalidBinary ($string)
		{
			$this	->exception			( function () use ($string) { $this->newTestedInstance ($string, false); })
				->isInstanceOf			( InvalidBinary::class);
		}


		public function testGenerated ()
		{
			$class					= $this->getTestedClassName ();
			$name					= 'test';
			$namespace				= $this->newTestedInstance ();
			$uuid_nsdefault 			= $this->newTestedInstance ($name);
			$uuid_nscustom				= $this->newTestedInstance ($name, $namespace);

			$this	->assert			('UUID v4 generation returns a valid long format')
				->boolean			($class::isValidLong		($namespace     ->long))
				->isTrue

				->assert			('UUID v4 generation returns a valid short format')
				->boolean			($class::isValidShort		($namespace     ->short))
				->isTrue

				->assert			('UUID v4 generation returns a valid binary format')
				->boolean			($class::isValidBinary		($namespace     ->binary))
				->isTrue

				->assert			('UUID v4 generation returns a valid toString')
				->boolean			($class::isValid	(strval ($namespace)))
				->isTrue


				->assert			('UUID v5 generation with default namespace returns a valid long format')
				->boolean			($class::isValidLong		($uuid_nsdefault->long))
				->isTrue

				->assert			('UUID v5 generation with default namespace returns a valid short format')
				->boolean			($class::isValidShort		($uuid_nsdefault->short))
				->isTrue

				->assert			('UUID v5 generation with default namespace returns a valid binary format')
				->boolean			($class::isValidBinary		($uuid_nsdefault->binary))
				->isTrue

				->assert			('UUID v5 generation with default namespace returns a valid toString')
				->boolean			($class::isValid	(strval ($uuid_nsdefault)))
				->isTrue

				->assert			('UUID v5 generation with custom namespace returns a valid long format')
				->boolean			($class::isValidLong		($uuid_nscustom ->long))
				->isTrue

				->assert			('UUID v5 generation with custom namespace returns a valid short format')
				->boolean			($class::isValidShort		($uuid_nscustom ->short))
				->isTrue

				->assert			('UUID v5 generation with custom namespace returns a valid binary format')
				->boolean			($class::isValidBinary		($uuid_nscustom ->binary))
				->isTrue

				->assert			('UUID v5 generation with custom namespace returns a valid toString')
				->boolean			($class::isValid	(strval ($uuid_nscustom)))
				->isTrue;
		}

		public function testReversible ($string)
		{
			$class					= $this->getTestedClassName ();
			$uuid1					= $this->newTestedInstance ($string);
			$uuid2					= $this->newTestedInstance ($uuid1->short);
			$uuid3					= $this->newTestedInstance ($uuid1->binary);

			    if ($class::isValidLong	($string))
				$this	->string			($string)
					->isIdenticalTo			($uuid1->long);
			elseif ($class::isValidShort	($string))
				$this	->string			($string)
					->isIdenticalTo			($uuid1->short);
			elseif ($class::isValidBinary	($string))
				$this	->string			($string)
					->isIdenticalTo			($uuid1->binary);
			elseif ($class::isValid		($string))
				// This assertion is actually an error message: we *want* this to fail!
				$this	->assert ("Valid UUID '{$string}' fails long, short and binary form criteria")
					->boolean			( false)
					->isTrue			();
			else	// This assertion is actually an error message: we *want* this to fail!
				$this	->assert ("Invalid UUID {$string}")
					->boolean			( false)
					->isTrue			();

			$this	->string			($uuid1->long)
				->isIdenticalTo			($uuid2->long)
				->isIdenticalTo			($uuid3->long)

				->string			($uuid1->short)
				->isIdenticalTo			($uuid2->short)
				->isIdenticalTo			($uuid3->short)

				->string			($uuid1->binary)
				->isIdenticalTo			($uuid2->binary)
				->isIdenticalTo			($uuid3->binary);
		}


		public function testV1Different ()
		{
			$class					= $this->getTestedClassName ();

			$this	->assert			('Same UUID v1 should not generate twice.')
				->string			( strval ($class::v1 ()))
				->isNotIdenticalTo		( strval ($class::v1 ()));
		}

		public function testV4Different ()
		{
			$class					= $this->getTestedClassName ();

			$this	->assert			('Same UUID v4 should not generate twice')
				->string			( strval ($this->newTestedInstance ()))
				->isNotIdenticalTo		( strval ($this->newTestedInstance ()))
				->isNotIdenticalTo		( strval ($class::v4 ()));
		}

		public function testV3Identical ()
		{
			$class					= $this->getTestedClassName ();
			$namespace				= $this->newTestedInstance ();
			$name					= 'test';

			$this	->assert			('Same UUID v3 inputs generated different results')

				->string			( strval ($class::v3 ($name)))
				->isIdenticalTo			( strval ($class::v3 ($name)))

				->string			( strval ($class::v3 ($name, $namespace)))
				->isIdenticalTo			( strval ($class::v3 ($name, $namespace)));
		}

		public function testV5Identical ()
		{
			$class					= $this->getTestedClassName ();
			$namespace				= $this->newTestedInstance ();
			$name					= 'test';

			$this	->assert			('Same UUID v5 inputs generated different results')

				->string			( strval ($class::v5			($name)))
				->isIdenticalTo			( strval ($class::v5			($name)))
				->isIdenticalTo			( strval ($this->newTestedInstance	($name)))

				->string			( strval ($class::v5			($name, $namespace)))
				->isIdenticalTo			( strval ($class::v5			($name, $namespace)))
				->isIdenticalTo			( strval ($this->newTestedInstance	($name, $namespace)));
		}


		public function testInstanceNamespacesValid ($namespace, $name, $long, $short)
		{
			$class					= $this->getTestedClassName ();
			$uuid					= $this->newTestedInstance ($name, $namespace);

			$this	->assert			('Namespace validates')
				->boolean			($class::isValid ($namespace))

				->assert			('Generated the expected long form')
				->string			($long)
				->isIdenticalTo			($uuid->long)

				->assert			('Generated the expected short form')
				->string			($short)
				->isIdenticalTo			($uuid->short);
		}

		public function testInstanceNamespacesInvalid ($namespace, $name)
		{
			$this	->exception			( function () use ($name, $namespace) { $this->newTestedInstance ($name, $namespace); })
				->IsInstanceOf			( InvalidNamespace::class);
		}

		public function testInstanceNamespacesObjects ($namespace, $name, $long, $short)
		{
			$uuid					= $this->newTestedInstance ($this->newTestedInstance ($name),
											$this->newTestedInstance ($namespace));

			$this	->assert			('Generated the expected long form')
				->string			($long)
				->isIdenticalTo			($uuid->long)

				->assert			('Generated the expected short form')
				->string			($short)
				->isIdenticalTo			($uuid->short);
		}

		public function testInstanceNamespacesDefault ($namespace, $name, $long, $short)
		{
			$class					= $this->getTestedClassName ();

			$class::setDefaultNamespace		($namespace);

			$uuid					= $this->newTestedInstance ($name, true);

			$this	->assert			('Generated the expected long form')
				->string			($long)
				->isIdenticalTo			($uuid->long)

				->assert			('Generated the expected short form')
				->string			($short)
				->isIdenticalTo			($uuid->short);
		}

		public function testFactoryNamespacesVersion3 ($namespace, $name, $long, $short)
		{
			$class					= $this->getTestedClassName ();

			$uuid					= $class::v3 ($name, $namespace);

			$this	->assert			('Namespace validates')
				->boolean			($class::isValid ($namespace))

				->assert			('Generated the expected long form')
				->string			($long)
				->isIdenticalTo			($uuid->long)

				->assert			('Generated the expected short form')
				->string			($short)
				->isIdenticalTo			($uuid->short);
		}

		public function testFactoryNamespacesVersion5 ($namespace, $name, $long, $short)
		{
			$class					= $this->getTestedClassName ();

			$uuid					= $class::v5 ($name, $namespace);

			$this	->assert			('Namespace validates')
				->boolean			($class::isValid ($namespace))

				->assert			('Generated the expected long form')
				->string			($long)
				->isIdenticalTo			($uuid->long)

				->assert			('Generated the expected short form')
				->string			($short)
				->isIdenticalTo			($uuid->short);
		}

		public function testFactoryNamespacesInvalid ($namespace, $name)
		{
			$class					= $this->getTestedClassName ();

			$this	->exception			( function () use ($name, $namespace, $class) { $class::v5 ($name, $namespace); })
				->IsInstanceOf			( InvalidNamespace::class);
		}

		public function testFactoryNamespacesObjects ($namespace, $name, $long, $short)
		{
			$class					= $this->getTestedClassName ();

			$uuid					= $class::v5 ($this->newTestedInstance ($name), $namespace);

			$this	->assert			('Generated the expected long form')
				->string			($long)
				->isIdenticalTo			($uuid->long)

				->assert			('Generated the expected short form')
				->string			($short)
				->isIdenticalTo			($uuid->short);
		}

		public function testFactoryNamespacesDefault ($namespace, $name, $long, $short)
		{
			$class					= $this->getTestedClassName ();

			$class::setDefaultNamespace		($namespace);

			$uuid					= $class::v5 ($name);

			$this	->assert			('Generated the expected long form')
				->string			($long)
				->isIdenticalTo			($uuid->long)

				->assert			('Generated the expected short form')
				->string			($short)
				->isIdenticalTo			($uuid->short);
		}

		public function testAddressesValid ($mac_declared)
		{
			$class					= $this->getTestedClassName ();

			if (is_null ($mac_declared))
			{
				$mac_stored		=  str_replace ( ':',       '',  $class::getMACAddress ());
				$mac_uuid		= $class::v1 ()->long;

				$this	->assert			('Encoded MAC address matches stored')
					->string			($mac_uuid)
					->contains			($mac_stored);
			}
			else
			{
				$class::setMACAddress ($mac_declared);

				$mac_declared		=  strtolower (str_replace ([':', '-'], '', $mac_declared));
				$mac_stored		=  strtolower (str_replace ( ':',       '', $class::getMACAddress ()));
				$mac_uuid		= $class::v1 ()->long;

				$this	->assert			('Stored MAC address matches declared')
					->string			($mac_declared)
					->contains			($mac_stored)

					->assert			('Encoded MAC address matches stored')
					->string			($mac_uuid)
					->contains			($mac_stored)

					->assert			('Encoded MAC address matches declared')
					->string			($mac_uuid)
					->contains			($mac_declared);
			}
		}

		public function testAddressesInvalid ($mac_declared)
		{
			$class					= $this->getTestedClassName ();

			$this	->exception			( function () use ($class, $mac_declared) { $class::setMACAddress ($mac_declared); })
				->isInstanceOf			( InvalidAddress::class);
		}


		protected function testImportLongDataProvider			() { return provided['import']['long']; }
		protected function testImportShortDataProvider			() { return provided['import']['short']; }
		protected function testImportBinaryDataProvider			() { return provided['import']['binary']; }
		protected function testImportInvalidStringDataProvider		() { return array_filter (array_unique (array_column (provided['namespaced']['version5'], 1))); }
		protected function testImportInvalidBinaryDataProvider		() { return provided['binary']; }
		protected function testInstanceNamespacesValidDataProvider	() { return provided['namespaced']['version5']; }
		protected function testInstanceNamespacesInvalidDataProvider	() { return provided['namespaced']['invalid']; }
		protected function testInstanceNamespacesObjectsDataProvider	() { return provided['namespaced']['uuids']; }
		protected function testInstanceNamespacesDefaultDataProvider	() { return provided['namespaced']['version5']; }
		protected function testFactoryNamespacesVersion3DataProvider	() { return provided['namespaced']['version3']; }
		protected function testFactoryNamespacesVersion5DataProvider	() { return provided['namespaced']['version5']; }
		protected function testFactoryNamespacesInvalidDataProvider	() { return provided['namespaced']['invalid']; }
		protected function testFactoryNamespacesObjectsDataProvider	() { return provided['namespaced']['uuids']; }
		protected function testFactoryNamespacesDefaultDataProvider	() { return provided['namespaced']['version5']; }
		protected function testReversibleDataProvider			() { return provided['reversible']; }
		protected function testAddressesValidDataProvider		() { return provided['addresses']['valid']; }
		protected function testAddressesInvalidDataProvider		() { return provided['addresses']['invalid']; }
	}

