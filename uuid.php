<?php
	namespace	Boondoc;

	use		Boondoc\uuid\InvalidString;
	use		Boondoc\uuid\InvalidBinary;
	use		Boondoc\uuid\InvalidNamespace;
	use		Boondoc\uuid\InvalidAddress;


	define ('UUID_NAMESPACE_NIL',	"\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00");
	define ('UUID_NAMESPACE_DNS',	"\x6B\xA7\xB8\x10\x9D\xAD\x11\xD1\x80\xB4\x00\xC0\x4F\xD4\x30\xC8");
	define ('UUID_NAMESPACE_URL',	"\x6B\xA7\xB8\x11\x9D\xAD\x11\xD1\x80\xB4\x00\xC0\x4F\xD4\x30\xC8");
	define ('UUID_NAMESPACE_OID',	"\x6B\xA7\xB8\x12\x9D\xAD\x11\xD1\x80\xB4\x00\xC0\x4F\xD4\x30\xC8");
	define ('UUID_NAMESPACE_X500',	"\x6B\xA7\xB8\x14\x9D\xAD\x11\xD1\x80\xB4\x00\xC0\x4F\xD4\x30\xC8");


	class uuid
	{
		private 			$long;
		private 			$short;
		private 			$binary;

		private static			$MACADDRESS		= "\x00\x00\x00\x00\x00\x00";
		private static			$NAMESPACE		=  UUID_NAMESPACE_NIL;

		// Long:	Rigid RFC compliance is enforced (version and variant); case is not checked, braces are optional;
		//		dashes are also optional, but if present, must be correctly-placed.
		// Short:	RFC compliance impossible to enforce; both Base64 character sets permitted, but mix-matching is not;
		//		end-padding is optional
		const				 RX_MAC 		= '#^(?:[0-9a-f]{2}([-:]?))(?:[0-9a-f]{2}\1){4}[0-9a-f]{2}$#i';
		const				 RX_LONG		= '#^\{?(?:0{8}(-?)(?:0{4}\1){3}(?:0{12})|(?:[0-9a-f]{8}(-?))(?:[0-9a-f]{4}\2)(?:[1345][0-9a-f]{3}\2)(?:[89ab][0-9a-f]{3}\2)(?:[0-9a-f]{12}))\}?$#i';
		const				 RX_SHORT		= '#^(?:[0-9a-zA-Z_-]{21}|[0-9a-zA-Z+/]{21})[gwAQ](?:==)?$#';
		const				 VERSIONS		= [ 1, 3, 4, 5 ];


		public		function	 __construct		($uuid = '', $namespace = '')
		{
			$string 				=  strval ($uuid);

			if (empty ($namespace))
			{
				if (empty ($uuid))
					$this->binary				=  static::version4 ();
				elseif ($uuid instanceof static)
					$this->binary				= $uuid->binary;
				elseif (static::isValidLong   ($string))
					$this->binary				=  static::import_long ($string);
				elseif (static::isValidShort  ($string))
					$this->binary				=  static::import_short ($string);
				elseif (static::isValidBinary ($string))
					$this->binary				= $string;
				elseif ($namespace === null)
					throw new InvalidNamespace ($string);
				elseif ($namespace === false)
				{
					if (mb_detect_encoding ($string) === false)
						throw new InvalidBinary ($string);
					else	throw new InvalidString ($string);
				}
			}

			if (empty ($this->binary))
			{
				if (empty ($namespace) or $namespace === true)
					$namespace				=      static::$NAMESPACE;
				else	$namespace				= (new static ($namespace, null))->binary;


				// Command-line utilities don't bother converting UUID subjects to binary first — all v3/5 input
				// is treated as raw binary. To match expected behaviour, object literals should be converted
				// to their long format before processing.
				$this->binary                           =  static::version5 ($namespace, strval ($uuid));
			}


			$this->long				=  static::binary_to_long  ($this->binary);
			$this->short				=  static::binary_to_short ($this->binary);
		}

		public		function	 __toString		()		{ return $this->long; }
		public		function	 __get			($name)
		{
			if (in_array ($name, ['binary', 'long', 'short']))
				return $this->$name;

			if ($name === 'sql')
				return '0x' . strtoupper (static::long_raw ($this->long));

			if ($name === 'version')
				return ord ($this->binary[6]) >> 4;

			if ($name === 'variant')
			{
				$variant				=  ord ($this->binary[8]);

				if (($variant & 0x80) === 0x00)	return 0;	// 0xx
				if (($variant & 0xC0) === 0x80)	return 1;	// 10x
				if (($variant & 0xE0) === 0xC0)	return 3;	// 110
								return 5;	// 111
			}

			if ($name === 'version')
				return ord ($this->binary[6]) >> 4;

			if ($name === 'variant')
			{
				$variant				=  ord ($this->binary[8]);

				if (($variant & 0x80) === 0x00)	return 0;	// 0xx
				if (($variant & 0xC0) === 0x80)	return 1;	// 10x
				if (($variant & 0xE0) === 0xC0)	return 3;	// 110
								return 5;	// 111
			}

			throw new \BadMethodCallException (sprintf ('Undefined property: %s::$%s', __CLASS__, $name));
		}


		public	static	function	   isValid		($string)	{ return ($string instanceof static) ? : static::isValidBinary ($string) || static::isValidLong ($string) || static::isValidShort ($string); }
		private static	function	   isValidRx		($string, $rx)	{ return   preg_match ($rx, $string) === 1; }
		public	static	function	   isValidLong		($string)	{ return   static::isValidRx ($string, static::RX_LONG); }
		public	static	function	   isValidShort 	($string)	{ return   static::isValidRx ($string, static::RX_SHORT) && static::isValidBinary (static::import_short ($string)); }
		public	static	function	   isValidBinary	($string)	{ return   static::isValidBinaryNil ($string) || static::isValidBinaryReal ($string); }
		private static	function	   isValidBinaryNil	($string)	{ return  $string === UUID_NAMESPACE_NIL; }
		private static	function	   isValidBinaryReal	($string)	{ return   strlen (bin2hex ($string)) === 32 && in_array (ord ($string[6]) >> 4, static::VERSIONS) && (ord ($string[8]) & 0xC0) === 0x80; }

		private static	function	   import_long		($string)	{ return   static::long_to_binary                   ($string); }
		private static	function	   import_short 	($string)	{ return   static::short_to_binary (static::short_url ($string)); }

		private static	function	   short_b64		($string)	{ return  str_replace (['_', '-'],	['/', '+'],		 $string) . '=='; }
		private static	function	   short_url		($string)	{ return  str_replace (['/', '+', '='],	['_', '-', ''], 	 $string); }
		private static	function	   long_raw		($string)	{ return  str_replace (['{', '-', '}'],	 '',			 $string); }
		private static	function	   long_sep		($string)	{ return preg_replace ('#^(.{8})(.{4})(.{4})(.{4})(.{12})$#',
																'$1-$2-$3-$4-$5',	 $string); }
		private static	function	   long_to_binary	($uuid) 	{ return  hex2bin			(static::long_raw 	($uuid)); }
		private static	function	   short_to_binary	($uuid) 	{ return  base64_decode 		(static::short_b64	($uuid)); }
		private static	function	   binary_to_long	($uuid) 	{ return  static::long_sep		(bin2hex		($uuid)); }
		private static	function	   binary_to_short	($uuid) 	{ return  static::short_url		(base64_encode		($uuid)); }


		private static	function	   hex			($dec, $l = 16) { return  hex2bin (str_pad ($dec, intval ($l), '0', STR_PAD_LEFT)); }

		private static	function	   random		($bytes   = 16)
		{
			if (function_exists ('random_bytes'))
				return  random_bytes			($bytes);
				return  openssl_random_pseudo_bytes	($bytes);
		}

		private static	function	   increment		($bytes)
		{
			$length 				=  strlen                   ($bytes);
			$value					=  dechex  (hexdec (bin2hex ($bytes))        + 1);
			$bytes					=  static::hex                ($value, $length * 4);

			return substr ($bytes, -$length);
		}

		private static	function	   version		($hash, $ver)
		{
			$hash[6]				=  chr (ord ($hash[6]) & 0x0F | $ver << 4);
			$hash[8]				=  chr (ord ($hash[8]) & 0x3F |  0x80); 	// Variant 1

			return $hash;
		}

		private static	function	   namespaced		($namespace, $name, $crypto)
		{
			return substr (hash ($crypto, $namespace . $name, true), 0, 16);
		}


		private static	function	   version3		($space, $name) { return static::version (static::namespaced ($space, $name,  'md5'), 3); }
		private static	function	   version5		($space, $name)	{ return static::version (static::namespaced ($space, $name, 'sha1'), 5); }
		private static	function	   version4		()		{ return static::version (static::random (),                          4); }

		private static	function	   version1		()
		{
			static $sequence, $last;

			$time					=  microtime (true) * 10000000 + 0x01B21DD213814000;

			if (empty ($sequence) or ($time - $last) > 10000)
				$sequence				=  static::random    ( 2);
			else	$sequence				=  static::increment ($sequence);

			$last					= $time;

			// $time = number of 100-ns intervals since 1582-10-15 00:00:00 UTC
			// It's a 64-bit float, so we can't use just blindly use dechex in case we're on a 32-bit system,
			// because that would truncate the timestamp to 32-bits as well.
			$hash					=  static::hex (dechex  ($time & 0xFFFFFFFF),                 8)
								.  static::hex (dechex  ($time / 0xFFFFFFFF        & 0xFFFF), 4)
								.  static::hex (dechex (($time / 0xFFFFFFFF >> 16) & 0xFFFF), 4)
								. $sequence
								.  static::$MACADDRESS;

			return static::version ($hash, 1);
		}


		public	static	function	   getDefaultNamespace	()		{ return new static (static::$NAMESPACE, false); }
		public	static	function	   setDefaultNamespace	($namespace)
		{
			static::$NAMESPACE			= $namespace instanceof static
								? $namespace->binary
								: (new static ($namespace, null))->binary;
		}

		public	static	function	   getMACAddress 	()		{ return preg_replace ('#^(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})$#',
																'$1:$2:$3:$4:$5:$6',
																 bin2hex (static::$MACADDRESS)); }
		public	static	function	   setMACAddress 	($macaddress)
		{
			if (!static::isValidRx ($macaddress, static::RX_MAC))
				throw new InvalidAddress ($macaddress);

			static::$MACADDRESS			=  hex2bin (str_replace ([':', '-'], '', $macaddress));
		}


		public	static	function	   v1			()		{ return new static (static::version1 (), false); }
		public	static	function	   v4			()		{ return new static (static::version4 (), false); }

		public	static	function	   v3			($uuid = '', $namespace = '')
		{
			return new static (static::version3 ($namespace ? (new static ($namespace, null))->binary : static::$NAMESPACE, strval ($uuid)), false);
		}

		public	static	function	   v5			($uuid = '', $namespace = '')
		{
			return new static (static::version5 ($namespace ? (new static ($namespace, null))->binary : static::$NAMESPACE, strval ($uuid)), false);
		}
	}



	namespace Boondoc\uuid;


	abstract class Exception extends \UnexpectedValueException
	{
		protected $value;

		public		function	  getValue	()
		{
			return $this->value;
		}

		public		function	__construct	($message, $value, $code = 0, Exception $previous = null)
		{
			$this->value				= $value;

			parent::__construct (sprintf ($message, $this->value), $code, $previous);
		}
	};


	class InvalidString extends Exception
	{
		public		function	__construct	($value, $code = 0, Exception $previous = null)
		{
			Exception::__construct ('Invalid UUID string %s', $value, $code, $previous);
		}
	};

	class InvalidBinary extends InvalidString
	{
		public		function	__construct	($value, $code = 0, Exception $previous = null)
		{
			Exception::__construct ('Invalid UUID binary string %s', bin2hex ($value), $code, $previous);
		}
	};

	class InvalidNamespace extends Exception
	{
		public		function	__construct	($value, $code = 0, Exception $previous = null)
		{
			Exception::__construct ('Invalid namespace UUID %s', $value, $code, $previous);
		}
	};

	class InvalidAddress extends Exception
	{
		public		function	__construct	($value, $code = 0, Exception $previous = null)
		{
			Exception::__construct ('Invalid MAC address %s', $value, $code, $previous);
		}
	};

