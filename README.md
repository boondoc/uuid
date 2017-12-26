# Boondoc/uuid

Boondoc/uuid is a PHP5.6+ class for handling and generating [RFC 4122][rfc4122] versions 1, 3, 4 and 5 (variant 1) Universally Unique Identifiers (UUIDs).

Three string output formats are provided:
*	“Long” format is a standard 36-character string (lower case, segmented with dashes, no enclosing braces)
*	“Short” format is a 22-character base64-encoded string using URL-safe characters and no padding
*	“Binary” format is a 16-character octet blob (big-endian encoding)

UUID variants 0 and 2 are not generated, or accepted as valid input.

Once generated, UUIDs are stored and treated as strings, with no internal structure — timestamp, clock sequence, etc. are ignored. Analysis and rearrangement of component fields for sorting is *not* supported.


## Installation

**With [Composer](https://getcomposer.org):**

```bash
composer require boondoc/uuid
```

**Manual Installation:**

Download the file `uuid.php` and include it in your code with a `require` statement.


## Usage

Import the class:

```php
use Boondoc\uuid;
```

A `uuid` object is assigned its value at creation. Once created, the objectʼs value cannot be altered, although it can be retrieved in multiple formats.

```php
$u = new uuid ([ mixed $uuid = '' [, mixed $namespace = null ] ] );
```

**Import value:**

Passing a valid UUID as the `$uuid` parameter will attempt to assign that value to the new `uuid` object.
```php
$u = new uuid ('fae0b286-007f-3824-a616-16d7332b7a09'); // Imports the specified string
```

Note that another `uuid` object instance is considered a valid UUID for this purpose:
```php
$u1 = new uuid ('fae0b286-007f-3824-a616-16d7332b7a09');
$u2 = new uuid ($u1); // Imports the value of $u1 – essentially $u2 is now a clone of $u1
```

**Generate random value:**

Passing no arguments will result in a new version 4 UUID being generated and assigned to the new `uuid` object.
```php
$u = new uuid (); // Generates a new UUID v4
```

**Generate namespaced value:**

If the `$uuid` parameter is not empty, but is *not* a valid UUID, a new version 5 UUID will be generated using the `$uuid` parameter as its “name” input, and assigned to the new `uuid` object.

If the `$namespace` parameter is a valid UUID, it will be used as the “namespace” argument of the version 5 generation.
```php
$u1 = new uuid ('String to be hashed', '87e1d1be-df8d-11e7-b804-0800276fa7d4'); // Generates a new UUID v5 using '87e1…' as the namespace
$u2 = new uuid ('String to be hashed', $u1); // Generates a new UUID v5 using $u1 as the namespace
```

If the `$namespace` parameter is omitted, or is boolean `true`, then a default “namespace” argument is used instead. At the start of script execution, the default namespace is initialised to the “nil” UUID, but it can be subsequently set to any valid UUID.
```php
echo new uuid (uuid::getDefaultNamespace ()); // '00000000-0000-0000-0000-000000000000', aka the “nil” UUID

$u1 = new uuid ('String to be hashed'); // Generates a new UUID v5 using '0000…' as the namespace

uuid::setDefaultNamespace ('87e1d1be-df8d-11e7-b804-0800276fa7d4');

$u2 = new uuid ('String to be hashed'); // Generates a new UUID v5 using '87e1…' as the namespace
```

**Suppressing namespaced generation:**

If the `$namespace` parameter is boolean `false`, then namespaced generation is suppressed.

In this case, an empty `$uuid` parameter still results in generation of a version 4 UUID.
```php
$u = new uuid ('', false); // Generates a new UUID v4
```

Otherwise, the `$uuid` parameter is treated strictly as a value to be imported. In the event that `$uuid` is not empty, but also not a valid UUID, and `$namespace` is boolean `false`, either an `InvalidString` or an `InvalidBinary` exception is thrown (see below).
```php
try
{
	$u = new uuid ('String to be hashed', false);
}
catch (InvalidString $e)
{
	echo $e->getMessage () . PHP_EOL;
}
```

**Factory notation:**

An alternate, factory-style notation is also available:
```php
$u1 = uuid::v4 (); // Generates a new UUID v4
$u2 = uuid::v5 ('String to be hashed'); // Generates a new UUID v5 using the default namespace
$u3 = uuid::v5 ('String to be hashed', '87e1d1be-df8d-11e7-b804-0800276fa7d4'); // Generates a new UUID v5 using '87e1…' as the namespace
```

Factory-style notation also allows access to version 1 and 3 UUIDs; these are *not* available through direct object instantiation.
```php
$u1 = uuid::v1 (); // Generates a new UUID v1
$u2 = uuid::v3 ('String to be hashed'); // Generates a new UUID v3 using the default namespace
$u3 = uuid::v3 ('String to be hashed', '87e1d1be-df8d-11e7-b804-0800276fa7d4'); // Generates a new UUID v3 using '87e1…' as the namespace
```

Since version 1 UUIDs rely on a MAC address, and PHP has no reliable, platform-agnostic method of obtaining the MAC address of the server, the MAC address must be defined manually. At the start of script execution, the MAC address is initialised to a “nil” value, but it can be subsequently set to any valid MAC address.
```php
echo bin2hex (uuid::getMACAddress ()); // '000000000000'

uuid::setMACAddress ('01:23:45:67:89:AB');
```

**Output formats:**

The three output formats are all available as read-only properties. The `__toString` magic method returns the Long format.

```php
echo $u->long			. PHP_EOL;	// fae0b286-007f-3824-a616-16d7332b7a09
echo $u->short			. PHP_EOL;	// -uCyhgB_OCSmFhbXMyt6CQ
echo bin2hex ($u->binary)	. PHP_EOL;	// fae0b286007f3824a61616d7332b7a09
echo $u				. PHP_EOL;	// fae0b286-007f-3824-a616-16d7332b7a09
```


## Exceptions

During normal operation, the following exceptions may be thrown:

*	`\Boondoc\uuid\InvalidBinary`: The specified `$uuid` could not be validated as a UUID, and `$namespace` was `false`. Automatically passes `$name` through `bin2hex ()` for printing.
*	`\Boondoc\uuid\InvalidString`: The specified `$uuid` could not be validated as a UUID, and `$namespace` was `false`. Catch to encompass `InvalidBinary` as well.
*	`\Boondoc\uuid\InvalidNamespace`: The specified `$namespace` could not be validated as a UUID.
*	`\Boondoc\uuid\InvalidAddress`: When calling ::setMACAddress, the specified `$macaddress` could not be validated as a MAC address.
*	`\Boondoc\uuid\Exception`: Abstract type, never thrown explicitly; catch to encompass all of the above.

These exceptions are for catching only, and should never need to be thrown explicitly.

All of the above exceptions feature a new method, `getValue ()`, which returns the offending value without the rest of the message string.


## Examples

```php
<?php
	require ('imports/autoload.php');		// Or 'vendor/autoload.php' if youʼre a traditionalist…

	use Boondoc\uuid;

	uuid::setDefaultNamespace ('0f1abe4a-df91-11e7-86fc-0800276fa7d4'); // See Note 1

	// Import; these all produce objects with identical internal values
	$uuid_long			= new uuid ('fae0b286-007f-3824-a616-16d7332b7a09');
	$uuid_caps			= new uuid ('FAE0B286-007F-3824-A616-16D7332B7A09');
	$uuid_guid			= new uuid ('{fae0b286-007f-3824-a616-16d7332b7a09}');
	$uuid_short			= new uuid ('-uCyhgB_OCSmFhbXMyt6CQ');
	$uuid_base64			= new uuid ('+uCyhgB/OCSmFhbXMyt6CQ==');
	$uuid_binary			= new uuid ("\xFA\xE0\xB2\x86\x00\x7F\x38\x24\xA6\x16\x16\xD7\x33\x2B\x7A\x09");
	$uuid_suppress_namespace	= new uuid ('fae0b286-007f-3824-a616-16d7332b7a09', false);

	// Instance generation
	$uuid_v4			= new uuid ();
	$uuid_v5_global_namespace	= new uuid ('String to be hashed'); // Namespace is '0f1a…'
	$uuid_v5_string_namespace	= new uuid ('String to be hashed', '87e1d1be-df8d-11e7-b804-0800276fa7d4');
	$uuid_v5_object_namespace	= new uuid ('String to be hashed', $uuid_v4);
	$uuid_v5_constant_namespace	= new uuid ('String to be hashed',  UUID_NAMESPACE_OID); // See Note 2

	// Factory generation
	$uuid_v1			= uuid::v1 ();
	$uuid_v3_global_namespace	= uuid::v3 ('String to be hashed'); // Namespace is '0f1a…'
	$uuid_v3_string_namespace	= uuid::v3 ('String to be hashed', '87e1d1be-df8d-11e7-b804-0800276fa7d4');
	$uuid_v3_object_namespace	= uuid::v3 ('String to be hashed', $uuid_v1);
	$uuid_v3_constant_namespace	= uuid::v3 ('String to be hashed',  UUID_NAMESPACE_OID); // See Note 2

	$uuid_v4			= uuid::v4 ();
	$uuid_v5_global_namespace	= uuid::v5 ('String to be hashed'); // Namespace is '0f1a…'
	$uuid_v5_string_namespace	= uuid::v5 ('String to be hashed', '87e1d1be-df8d-11e7-b804-0800276fa7d4');
	$uuid_v5_object_namespace	= uuid::v5 ('String to be hashed', $uuid_v4);
	$uuid_v5_constant_namespace	= uuid::v5 ('String to be hashed',  UUID_NAMESPACE_OID); // See Note 2

	// Output formats
	echo $uuid_long->long				. PHP_EOL;	// fae0b286-007f-3824-a616-16d7332b7a09
	echo $uuid_long->short				. PHP_EOL;	// -uCyhgB_OCSmFhbXMyt6CQ
	echo bin2hex ($uuid_long->binary)		. PHP_EOL;	// fae0b286007f3824a61616d7332b7a09
	echo $uuid_long 				. PHP_EOL;	// fae0b286-007f-3824-a616-16d7332b7a09

	// Validation
	uuid::isValidLong ('fae0b286-007f-3824-a616-16d7332b7a09');	// true
	uuid::isValidLong ('00000000-0000-0000-0000-000000000000');	// true: nil UUID
	uuid::isValidLong ('FAE0B286-007F-3824-A616-16D7332B7A09');	// true: uppercase is permitted on input
	uuid::isValidLong ('{fae0b286007f3824a61616d7332b7a09}');	// true: dashes and end-braces are both optional
	uuid::isValidLong ('fae0b286-007f-e824-a616-16d7332b7a09');	// false: there is no UUID version 'E'
	uuid::isValidLong ('fae0b286-007f-3824-c616-16d7332b7a09');	// false: variant 2 is not permitted (character 'c')
	uuid::isValidLong ('fae0b286-007f-j824-a616-16d7332b7a09');	// false: character 'j' is not permitted
	uuid::isValidLong ('fae0b286-007f-3824-a61616d7-332b7a09');	// false: dash in the wrong place
	uuid::isValidLong ('fae0b286-007f-3824-a616-16d7332b7a0936');	// false: wrong length

	uuid::isValidShort ('-uCyhgB_OCSmFhbXMyt6CQ');			// true
	uuid::isValidShort ('AAAAAAAAAAAAAAAAAAAAAA');			// true: nil UUID
	uuid::isValidShort ('+uCyhgB/OCSmFhbXMyt6CQ==');		// true: standard base64 charset is permitted on input, padding is optional
	uuid::isValidShort ('-uCyhgB_OCSmFhbXMyt6Cj');			// false: final character must be one of [g, w, A, Q]
	uuid::isValidShort ('-uCyhgB_OCSmFhbXMyt6CQm'); 		// false: wrong length

	uuid::isValidBinary ("\xFA\xE0\xB2\x86\x00\x7F\x38\x24\xA6\x16\x16\xD7\x33\x2B\x7A\x09");	// true
	uuid::isValidBinary ("\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00");	// true: nil UUID
	uuid::isValidBinary ("\xFA\xE0\xB2\x86\x00\x7F\xE8\x24\xA6\x16\x16\xD7\x33\x2B\x7A\x09");	// false: there is no UUID version 'E'
	uuid::isValidBinary ("\xFA\xE0\xB2\x86\x00\x7F\xE8\x24\xA6\x16\x16\xD7\x33\x2B\x7A\x09\x36");	// false: wrong length

	uuid::isValid ('fae0b286-007f-3824-a616-16d7332b7a09');		// true		// See Note 3
	uuid::isValid ($uuid_v4);					// true
```

**Notes:**
1.	If the default namespace is never set, it defaults to the “nil” UUID `'00000000-0000-0000-0000-000000000000'`.
1.	[RFC 4122][rfc4122] Appendix C suggests some predefined namespaces. Constants defined by this library are:
		* `UUID_NAMESPACE_NIL`  ≈ `'00000000-0000-0000-0000-000000000000'`
		* `UUID_NAMESPACE_DNS`  ≈ `'6ba7b810-9dad-11d1-80b4-00c04fd430c8'`
		* `UUID_NAMESPACE_URL`  ≈ `'6ba7b811-9dad-11d1-80b4-00c04fd430c8'`
		* `UUID_NAMESPACE_OID`  ≈ `'6ba7b812-9dad-11d1-80b4-00c04fd430c8'`
		* `UUID_NAMESPACE_X500` ≈ `'6ba7b814-9dad-11d1-80b4-00c04fd430c8'`
	Note that these constants are actually defined as **Binary** strings, and will produce unexpected results when passed as the `$uuid` parameter of a *namespaced* UUID.
1.	`uuid::isValid ()` returns `true` if the argument is a `uuid` object, or if any of `::isValidLong ()`, `::isValidShort ()` or `::isValidBinary ()` would return true.
