<?php
	const provided			=  array
	(
		'import'			=>  array
		(
			'long'				=>  array
			(
				[ '8cb390cc-db43-11e7-b71b-0800276fa7d4',					true,	'Long format v1 UUID validates'],	// uuid -v1
				[ '409b254f-4650-3d62-a0ff-e81907798621',					true,	'Long format v3 UUID validates'],	// uuid -v3 ns:DNS http://boondoc.com/
				[ 'cd8dfeec-d2cd-4ac1-aaa0-5b43804011e7',					true,	'Long format v4 UUID validates'],	// uuid -v4
				[ '486f8cc0-cd92-5e76-80a6-e4b401d1498a',					true,	'Long format v5 UUID validates'],	// uuid -v5 ns:DNS http://boondoc.com/
				[ '00000000-0000-0000-0000-000000000000',					true,	'Long format nil UUID validates'],	// special case!
				[ 'ffffffff-ffff-ffff-ffff-ffffffffffff',					false,	'Long format full UUID should not validate'],
				[ 'ffffffff-ffff-5fff-bfff-ffffffffffff',					true,	'Long format capped UUID validates'],
				['{a3ecf7f6-db43-11e7-b538-0800276fa7d4}',					true,	'Long format braced UUID validates'],
				[ 'a3ecf7f6-db43-11e7-b53-80800276fa7d4',					false,	'Invalid long format should not validate'],
				[ 'a3ecf7f6-db43-11e7-b538-0800276fa7d40',					false,	'Invalid long format length should not validate'],
				[ 'a3ecf7f6-db43-11e7-b538-0800276fa7dg',					false,	'Invalid long format character should not validate'],
			),

			'short' 			=>  array
			(
				['-2zbvttHEeeyJAgAJ2-n1A',							true,	'Short format v1 UUID validates'],	// uuid -v1
				['4aNj-06EO1K8cRRVTnNMug',							true,	'Short format v3 UUID validates'],	// uuid -v3 ns:URL http://boondoc.com/
				['1Pyr8GgtQIWQw1iO0ozcQA',							true,	'Short format v4 UUID validates'],	// uuid -v4
				['1NvHLocsUry4tat1LpBi0A',							true,	'Short format v5 UUID validates'],	// uuid -v5 ns:URL http://boondoc.com/
				['AAAAAAAAAAAAAAAAAAAAAA',							true,	'Short format nil UUID validates'],
				['0000000000000000000000',							false,	'Short format zero UUID should not validate'],
				['00000000U020000000000w',							true,	'Short format mostly-zero UUID validates'],
				['gggggggggggggggggggggg',							false,	'Short format \'g\' UUID should not validate'],
				['wwwwwwwwwwwwwwwwwwwwww',							false,	'Short format \'w\' UUID should not validate'],
				['--------X------------g',							true,	'Short format capped-dashes UUID validates'],
				['________X_-__________w',							true,	'Short format capped-scores UUID validates'],
				['++++++++X++++++++++++g',							true,	'Short format capped-pluses UUID validates'],
				['////////X/+//////////w',							true,	'Short format capped-slashes UUID validates'],
				['CrJua/B0TkSdpgEgX6Dpdg==',							true,	'Short format non-url-safe UUID validates'],
				['CrJua_B0TkSdpgEgX6Dpd$',							false,	'Invalid short format character should not validate'],
				['CrJua_B0TkSdpgEgX6DpdgJ',							false,	'Invalid short format length should not validate'],
			),

			'binary'			=>  array
			(
				["\x6B\xE9\x6E\x10\xDB\x49\x11\xE7\xB7\x16\x08\x00\x27\x6F\xA7\xD4",		true,	'Binary format v1 UUID validates'],	// uuid -v1
				["\xE3\xC2\x87\x28\x44\xC8\x33\x4C\xA4\x71\x03\xAA\x2D\x36\xBC\x0F",		true,	'Binary format v3 UUID validates'],	// uuid -v3 ns:OID http://boondoc.com/
				["\x6B\x31\x90\xD8\x5C\x24\x48\x62\xAB\x9A\xCE\x3B\x37\x92\x40\xE8",		true,	'Binary format v4 UUID validates'],	// uuid -v4
				["\x38\x14\x3D\x96\xEE\xA5\x59\x9B\x8B\x8E\xD4\x54\xED\x77\x85\x23",		true,	'Binary format v5 UUID validates'],	// uuid -v5 ns:OID http://boondoc.com/
				["\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00",		true,	'Binary format nil UUID validates'],
				["\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF",		false,	'Binary format full UUID should not validate'],
				["\xFF\xFF\xFF\xFF\xFF\xFF\x5F\xFF\xBF\xFF\xFF\xFF\xFF\xFF\xFF\xFF",		true,	'Binary format capped UUID validates'],
				['0123456789ABCDEF',								false,  'Invalid binary format printable string should not validate'],
				['FEDCBA9876543210',								false,  'Invalid binary format printable string should not validate'],
				["\xA3\xEC\xF7\xF6\xDB\x43\x11\xE7\xB5\x38\x08\x00\x27\x6F\xA7\xD4\x67",	false,	'Invalid binary format length should not validate'],
				["\xA3\xEC\xF7\xF6\xDB\x43\x11\xE7\xB5\x38\x08\x00\x27\x6F\xA7",		false,	'Invalid binary format length should not validate'],
			),
		),

		'reversible'			=>  array
		(
			['00000000-0000-0000-0000-000000000000'],
			['ffffffff-ffff-1fff-bfff-ffffffffffff'],
			['fedcba98-7654-3210-89ab-cdef01234567'],
			['AAAAAAAAAAAAAAAAAAAAAA'],
			['SASASASASASASASASASASA'],
			['VUTSRQPONMK6JIHGFEDCBA'],
			["\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00"],
			["\xFF\xFF\xFF\xFF\xFF\xFF\x1F\xFF\xBF\xFF\xFF\xFF\xFF\xFF\xFF\xFF"],
			["\xFE\xDC\xBA\x98\x76\x54\x32\x10\x89\xAB\xCD\xEF\x01\x23\x45\x67"],
		),

		'binary'			=>  array
		(
			["\x98\x78\x0D\x76\xE3\xAD\x61\xE7\xBB\xC4\x08\x00\x27\x6F\xA7\xD4"],
			["\x98\x78\x0D\x76\xE3\xAD\x11\xE7\xCB\xC4\x08\x00\x27\x6F\xA7\xD4"],
		),

		'namespaced'			=>  array
		(
			'version3' 			=>  array
			(
				['00000000-0000-0000-0000-000000000000', '',		'4ae71336-e44b-39bf-b9d2-752e234818a5', 'SucTNuRLOb-50nUuI0gYpQ'],
				['00000000-0000-0000-0000-000000000000', 'test',	'96e17d7a-ac89-38cf-95e1-bf5098da34e1', 'luF9eqyJOM-V4b9QmNo04Q'],
				['00000000-0000-0000-0000-000000000000', 'Test',	'7a8bf5d2-2e33-34ec-8af5-d3636b55e1fe', 'eov10i4zNOyK9dNja1Xh_g'],
				['6ba7b810-9dad-11d1-80b4-00c04fd430c8', 'Gilgamesh',	'25649678-b6e6-3368-be5a-a7d730e6fc6c', 'JWSWeLbmM2i-WqfXMOb8bA'],
				['6ba7b811-9dad-11d1-80b4-00c04fd430c8', 'Agamemnon',	'25a2d5de-8fb3-384c-99d1-9c9e35f5c095', 'JaLV3o-zOEyZ0ZyeNfXAlQ'],
				['6ba7b812-9dad-11d1-80b4-00c04fd430c8', 'Voldemort',	'cd71cf15-6e09-357e-8b93-f3d82e2558bf', 'zXHPFW4JNX6Lk_PYLiVYvw'],
				['6ba7b814-9dad-11d1-80b4-00c04fd430c8', 'Полудница',	'83cfd5c8-b234-3d38-8da1-1ce51730cdf0', 'g8_VyLI0PTiNoRzlFzDN8A'],
				['AAAAAAAAAAAAAAAAAAAAAA',		 '',		'4ae71336-e44b-39bf-b9d2-752e234818a5', 'SucTNuRLOb-50nUuI0gYpQ'],
				['AAAAAAAAAAAAAAAAAAAAAA',		 'test',	'96e17d7a-ac89-38cf-95e1-bf5098da34e1', 'luF9eqyJOM-V4b9QmNo04Q'],
				['AAAAAAAAAAAAAAAAAAAAAA',		 'Test',	'7a8bf5d2-2e33-34ec-8af5-d3636b55e1fe', 'eov10i4zNOyK9dNja1Xh_g'],
				['OQOQOQOQOQOQOQOQOQOQOQ',		 '',		'7cd698a4-6082-3cb1-b651-b66b99e42c4f', 'fNaYpGCCPLG2UbZrmeQsTw'],
				['OQOQOQOQOQOQOQOQOQOQOQ',		 'test',	'b4637c99-88c4-3cfb-b977-3ee099cc792b', 'tGN8mYjEPPu5dz7gmcx5Kw'],
				['OQOQOQOQOQOQOQOQOQOQOQ',		 'Test',	'99bd2e2d-0326-3359-95ee-4217a5b3747e', 'mb0uLQMmM1mV7kIXpbN0fg'],
				['SASASASASASASASASASASA',		 '',		'dab83559-9adc-3433-8b75-88385e7adecc', '2rg1WZrcNDOLdYg4XnrezA'],
				['SASASASASASASASASASASA',		 'test',	'acaef368-5178-388a-8bbb-f3c7623a1109', 'rK7zaFF4OIqLu_PHYjoRCQ'],
				['SASASASASASASASASASASA',		 'Test',	'28d4452b-3b72-3f44-bd38-4cc850bded38', 'KNRFKztyP0S9OEzIUL3tOA'],
				['a6e4EJ2tEdGAtADAT9QwyA',		 'Leonidas', 	'a3f45b6f-e19a-3a6c-8b30-c94a4507ed5a', 'o_Rbb-GaOmyLMMlKRQftWg'],
				['a6e4EZ2tEdGAtADAT9QwyA',		 'Boudicca', 	'd6a53f5d-0e27-3402-b002-c1791b170d95', '1qU_XQ4nNAKwAsF5GxcNlQ'],
				['a6e4Ep2tEdGAtADAT9QwyA',		 'Skeletor', 	'99bf31e3-0ee2-3103-942d-b118ee8f6470', 'mb8x4w7iMQOULbEY7o9kcA'],
				['a6e4FJ2tEdGAtADAT9QwyA',		 'Hermóður', 	'03ebe33b-4427-3b4f-9eaa-aeb4f8384266', 'A-vjO0QnO0-eqq60-DhCZg'],
			),

			'version5' 			=>  array
			(
				['00000000-0000-0000-0000-000000000000', '',		'e129f27c-5103-5c5c-844b-cdf0a15e160d', '4SnyfFEDXFyES83woV4WDQ'],
				['00000000-0000-0000-0000-000000000000', 'test',	'e8b764da-5fe5-51ed-8af8-c5c6eca28d7a', '6Ldk2l_lUe2K-MXG7KKNeg'],
				['00000000-0000-0000-0000-000000000000', 'Test',	'5b23436d-8e7c-51cf-8162-5cd5fd379ecf', 'WyNDbY58Uc-BYlzV_Teezw'],
				['6ba7b810-9dad-11d1-80b4-00c04fd430c8', 'Gilgamesh',	'bda75f17-89b5-5d7d-aa8e-5a6238a21530', 'vadfF4m1XX2qjlpiOKIVMA'],
				['6ba7b811-9dad-11d1-80b4-00c04fd430c8', 'Agamemnon',	'e4fadfc7-a223-5517-9ed4-56219d0316c7', '5Prfx6IjVRee1FYhnQMWxw'],
				['6ba7b812-9dad-11d1-80b4-00c04fd430c8', 'Voldemort',	'edcb7a23-3f05-550b-9c9c-a63a573457ea', '7ct6Iz8FVQucnKY6VzRX6g'],
				['6ba7b814-9dad-11d1-80b4-00c04fd430c8', 'Полудница',	'25b14b0f-2d85-5005-9910-edb5d22d33f4', 'JbFLDy2FUAWZEO210i0z9A'],
				['AAAAAAAAAAAAAAAAAAAAAA',		 '',		'e129f27c-5103-5c5c-844b-cdf0a15e160d', '4SnyfFEDXFyES83woV4WDQ'],
				['AAAAAAAAAAAAAAAAAAAAAA',		 'test',	'e8b764da-5fe5-51ed-8af8-c5c6eca28d7a', '6Ldk2l_lUe2K-MXG7KKNeg'],
				['AAAAAAAAAAAAAAAAAAAAAA',		 'Test',	'5b23436d-8e7c-51cf-8162-5cd5fd379ecf', 'WyNDbY58Uc-BYlzV_Teezw'],
				['OQOQOQOQOQOQOQOQOQOQOQ',		 '',		'90f8025d-4d66-5e2b-bfe3-e6a0dd34b9cc', 'kPgCXU1mXiu_4-ag3TS5zA'],
				['OQOQOQOQOQOQOQOQOQOQOQ',		 'test',	'e701bfad-da84-5e22-868d-eb9056f2449f', '5wG_rdqEXiKGjeuQVvJEnw'],
				['OQOQOQOQOQOQOQOQOQOQOQ',		 'Test',	'ebbeac90-c0bf-50f3-b761-9d90e87735a1', '676skMC_UPO3YZ2Q6Hc1oQ'],
				['SASASASASASASASASASASA',		 '',		'3469116a-751e-5e64-b21b-4b0bf43cf46c', 'NGkRanUeXmSyG0sL9Dz0bA'],
				['SASASASASASASASASASASA',		 'test',	'a39bf6fd-ef1a-5ade-9161-50a0dfbca09d', 'o5v2_e8aWt6RYVCg37ygnQ'],
				['SASASASASASASASASASASA',		 'Test',	'653941d5-9085-5450-9344-7b81ebd34553', 'ZTlB1ZCFVFCTRHuB69NFUw'],
				['a6e4EJ2tEdGAtADAT9QwyA',		 'Leonidas', 	'3ea25879-3c25-52cc-931e-8335974e1207', 'PqJYeTwlUsyTHoM1l04SBw'],
				['a6e4EZ2tEdGAtADAT9QwyA',		 'Boudicca', 	'f7738bee-3eac-5ce0-a4e3-5fc903f2d3c8', '93OL7j6sXOCk41_JA_LTyA'],
				['a6e4Ep2tEdGAtADAT9QwyA',		 'Skeletor', 	'72d5d126-5b44-5c91-b25f-32c91e19b256', 'ctXRJltEXJGyXzLJHhmyVg'],
				['a6e4FJ2tEdGAtADAT9QwyA',		 'Hermóður', 	'd2838152-56b9-5008-80c1-d2605904ef4d', '0oOBUla5UAiAwdJgWQTvTQ'],
			),

			'invalid'			=>  array
			(
				['00000000-0000-0000-0000-00000000000', 	'test'],	// Too short
				['00000000-0000-0000-000-0000000000000',	'test'],	// Valid length, and charset, but dash in the wrong spot
				['00000000-0000-0000-0000-0000000000000',	'test'],	// Too long
				['000000000000000000000',			'test'],	// Too short
				['0000000000000000000000',			'test'],	// Valid length and charset, but final character not commutative
				['00000000000000000000000',			'test'],	// Too long
				['----------------------',			'test'],	// Valid length and charset, but final character not commutative
				['______________________',			'test'],	// Valid length and charset, but final character not commutative
				['garbage',					'test'],	// What it says on the tin
				["\xFF\x35\x99\x89",				'test'],	// Binary rubbish
			),

			'uuids' 			=>  array
			(
				['00000000-0000-0000-0000-000000000000', '00000000-0000-0000-0000-000000000000', '9c11b015-f43b-5972-b792-b9ca3f1188f3', 'nBGwFfQ7WXK3krnKPxGI8w'],
				['266421d7-5d4b-42ca-a0c8-43f95943be0b', 'f28712c0-55b9-4160-bdd7-0d02b7e8c03e', '60d4747d-d4b5-551f-b391-fe7109168ea5', 'YNR0fdS1VR-zkf5xCRaOpQ'],
				['2gV3MezRTfacorHbLpddzA',		 'fd435fea-e325-11e7-b5f9-0800276fa7d4', 'a77505d7-5e7a-56b4-a756-6f334000e406', 'p3UF1156VrSnVm8zQADkBg'],
				['fd435fea-e325-11e7-b5f9-0800276fa7d4', '2gV3MezRTfacorHbLpddzA',		 '246a6b39-3eec-59bc-baa7-ff2fa51cfc35', 'JGprOT7sWby6p_8vpRz8NQ'],
				['OQOQOQOQOQOQOQOQOQOQOQ',		 'SASASASASASASASASASASA',		 'b0af89ca-ec43-5c20-9f44-3238d6468798', 'sK-JyuxDXCCfRDI41kaHmA'],
			),
		),

		'addresses'			=>  array
		(
			'valid' 			=>  array
			(
				[ null ],							// NULL doesn't actually get passed to ::setMACAddress, it just tells the test to test the default
				['00-00-00-00-00-00'],
				['01:23:45:67:89:AB'],
				['DEAFDEADBEA7'],
			),

			'invalid'			=>  array
			(
				[ null ],							// NOW we pass NULL to ::setMACAddress and see what it does!
				['01:23-45:67-89:AB'],						// Mixing-and-matching separators is technically legal, but we don't allow it
				['ABCDEFGHIJKL'],						// Invalid characters
			),
		),
	);
