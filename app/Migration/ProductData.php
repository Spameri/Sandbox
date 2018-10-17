<?php

$dirname = realpath(__DIR__ . '/../../app');
$container = include_once $dirname . '/bootstrap.php';

/** @var \App\ProductModule\Model\ProductService $productService*/
$productService = $container->getByType(\App\ProductModule\Model\ProductService::class);

$boolLottery = [TRUE, FALSE];
$names = [
	'Držák TV s náklonem pro TV 23" - 42" Fiber Novelty FN-44T',
	'TV držák s náklonem až 15° Northbayou C1T',
	'Otočný a sklopný držák s výsuvným ramenem Ma Clean MC-223',
	'Otočný a sklopný TV držák 503A',
	'Sklopný držák na TV PLAZMA LCD LED Northbayou  C2T',
	'Hvězda Nature',
	'Koule Cesmína bílá Crystal',
	'Koule Hvězdy Crystal',
	'Koule Struna Crystal',
	'Lampa Stolní Nature',
	'Les bílý Nature',
	'Les Nature',
	'Rampouchy mini Modrá 60 LED',
	'Držák na mobil do auta do větrací mřížky HS-1409',
	'Otočný a sklopný TV držák s výsuvným ramenem FN443',
	'Luxusní držák na TV nebo LCD monitor Exelium i-Flat i10',
	'Kvalitní stropní držák na projektor Brateck P1',
	'Špičkový držák na televizory Ma Clean MC20-443',
	'Kvalitní kloubový držák na LCD Fiber Novelty FN-101',
	'Univerzální podstavec pod TV FNP1',
	'Stropní držák projektoru Fiber Mounts T718-4 černý',
	'TV držák pro LCD LED PLASMA Brateck PLB-11',
	'Fixní držák pro LCD LED plazmové TV OMB SLIM 800',
	'Sklopný TV držák Ma Clean MC-33L',
	'Trampolína Marimex Premium 305 cm - II. jakost',
	'Kloubový televizní držák s výsuvným ramenem NS-W640',
	'Unikátní držák TV s teleskopickým ramenem držák TV EXELIUM SLIDE 20XL',
	'Držák TV Northbayou M400',
	'Profesionální kloubový TV držák CHIEF MWR',
	'Držák na Tv / monitory OMB OYSTER',
	'Levný profesionální stojan na Tv Fiber Mounts AVA1500',
	'Kvalitní nástěnný držák pro Tv Brateck 40 - l',
	'Držák pro zavěšení LCD pod nábytek OMB OYSTER',
	'Unikátní polohovatelný TV držák VP AX2AWL01',
	'Luxusní TV držák vysoké kvality Northbayou 787 - L400',
	'Rohový TV držák Fiber Novelty FN13',
	'Sklopný držák na LCD monitor Northbayou QT - 100',
	'Držák na projektor Northbayou T717 - M',
	'Držák TV Ma Clean MC - WA5',
	'Držák na 4 monitory EDBAK SV07',
	'Nástěnný TV držák MC - 742',
	'Otočný, sklopný, délkově a výškově stavitelný držák Tv Fiber Mounts AX784',
	'Televizní držák Brateck 1022 - F',
	'Držák na Tv OMB QUICK LEVEL',
	'Držák pro televizi ART AR - 18',
	'Fixní televizní držák EDBAK LWB1',
	'Televizní držák EDBAK PWB1',
];
$content = [
	'VESA standart 200x100 mm, 200x100 - 200x200 mm, 200x200 mm, 300x200 mm, 200x300 mm, 300x300 mm, 400x200 mm, 400x300 mm, 300x400 mm, 400x400 mm',
	'Stojánek na TV 32" - 52"',
	'Pevný nástěnný držák TV',
	'Špičkový držák moderního a nadčasového designu',
	'Designový držák s možností natočení TV do stran a náklonem',
	'Profesionální držák jedinečného designu',
	'Televizní držák se dvěma rameny, vhodný do rohu',
	'Nástěnný výklopný držák na LCD televize - profesionální kvalita',
	'Kvalitní fixní držák s minimální vzdáleností od zdi',
	'Vysoce kvalitní stropní držák na LCD Tv a monitory do 37"',
	'Stropní držák na středně velké LCD, plazma a LED Tv do 50 kg',
	'Stolní držák pro monitory - polohovatelný',
	'Držák do auta na zpětné zrcátko',
	'Nabíjecí kabel na telefon USB - univerzální',
	'Nabíjecí kabel pro mobilní telefon a jiné zařízení',
	'Moderní držák na mobil do auta',
	'Nosnost 8 kg, Úhlopříčka TV od od 13" do 24", VESA standart 100x100 mm, 75x75 mm',
	'Stropní držák na projektory a dataprojektory - skvělá kvalita a design',
	'Pojízdný televizní stojan pro LCD, LED a PLAZMA TV',
	'Nástěnná police na DVD, video, TV tunery atd. s nosností 25kg',
];
$tag = [
	'Sleva',
	'Novinka',
	'Doprava zdarma',
];

for ($i = 1; $i <= 100; $i++) {
	$productService->insert(
		new \App\ProductModule\Entity\Product(
			new \Spameri\Elastic\Entity\Property\EmptyElasticId(),
			new \App\ProductModule\Entity\Product\IsPublic(\array_rand($boolLottery)),
			new \App\ProductModule\Entity\Product\Name(\array_rand($names)),
			new \App\ProductModule\Entity\Product\Content(\array_rand($content)),
			new \App\ProductModule\Entity\Product\Details(
				new \App\ProductModule\Entity\Product\Details\TagCollection(\array_rand($tag)),
				new \App\ProductModule\Entity\Product\Details\Accessories($productService)
			),
			new \App\ProductModule\Entity\Product\Price(\random_int(100, 9000)),
			new \App\ProductModule\Entity\Product\ParameterValuesCollection()
		)
	);
}

