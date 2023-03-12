<?php
	use		mageekguy\atoum\test\generator;

	$runner	->setTestGenerator		((new generator ())
			->setTestedClassNamespace	('Boondoc')
			// ->setTestClassNamespace		('Boondoc\Test')
			->setTestClassesDirectory	(__DIR__))
		->addTestsFromDirectory		(__DIR__);

