<?php declare(strict_types = 1);

namespace App\WorkshopFourModule\Command;

class ExportToElastic extends \Symfony\Component\Console\Command\Command
{

    public function __construct(
        private \Spameri\Elastic\ClientProvider $clientProvider,
        private \App\WorkshopFourModule\Entity\ProductConfig $productConfig,
        private \App\WorkshopFourModule\Elastic\Create $create,
        string $name = null
    )
    {
        parent::__construct($name);
    }

    public function configure(): void
    {
        $this->setName('workshopFour:exportToElastic');
    }


    public function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ): int
    {
        $forceDelete = true;
        // 1. init output
        $output->writeln('Starting');

        try {
            $document = new \Spameri\ElasticQuery\Document(
                index: $this->productConfig->provide()->indexName(),
            );

            if ($forceDelete) {
                // 2a. Delete index
                $this->clientProvider->client()->indices()->delete($document->toArray());
            }

            // 2. check if index exists
            $response = $this->clientProvider->client()->search($document->toArray());

            \var_dump($response);

        } catch (\Elasticsearch\Common\Exceptions\Missing404Exception $exception) {

            // 3. Setup index
            $settingsDocument = new \Spameri\ElasticQuery\Document(
                index: $this->productConfig->provide()->indexName(),
                body: new \Spameri\ElasticQuery\Document\Body\Plain(
                    $this->productConfig->provide()->toArray()
                )
            );

            // 3a. Create index
            $this->clientProvider->client()->indices()->create(
                $settingsDocument->toArray()
            );

//            $this->create->execute(
//                index: $this->productConfig->provide()->indexName(),
//                parameters: $this->productConfig->provide()->toArray(),
//            );
        }

        // 4. Initialize data, iterate

        /** @var \App\WorkshopFourModule\Entity\Product $product */
        foreach ($this->prepareProducts() as $product) {
            $productDocument = new \Spameri\ElasticQuery\Document(
                index: $this->productConfig->provide()->indexName(),
                body: new \Spameri\ElasticQuery\Document\Body\Plain(
                    $product->entityVariables()
                ),
                id: $product->id->value(),
            );

            $this->clientProvider->client()->index($productDocument->toArray());
        }


        // 5. Alias switch

        $output->writeln('Done');

        return 0;
    }


    public function prepareProducts()
    {
        $products = [];
        $sizeData = [
            'XS',
            'S',
            'M',
            'L',
            'XL',
            'XXL',
            'XXXL',
            '36',
            '37',
            '40',
        ];
        $materialData = [
            'PRYŽ 100 %',
            'SEMIŠ 100 %',
            'TELECÍ KŮŽE 100 %',
            'BAVLNA 47 %',
            'POLYESTER 53 %',
            'RECYKLOVANÝ POLYESTER 100 %',
            'BAVLNA 100 %',
            'BAVLNA 100 %',
            'BAVLNA 100 %',
        ];
        $stickerData = [
            'DOPRAVA ZDARMA',
            'NOVINKA',
            'Výprodej',
            'sleva 50%',
        ];
        $titles = [
            'TRIČKO GANT ORIGINAL V-NECK SS T-SHIRT',
            'BUNDA GANT WIND PARKA',
            'BUNDA GANT UNLINED COTTON JACKET',
            'VESTA GANT LIGHT DOWN GILET',
            'BUNDA GANT D2. QUILTED JACKET',
            'BUNDA GANT D2. LONG DOWN JACKET',
            'BUNDA GANT D2. WINTER PARKA',
            'POUZDRO NA PLATEBNÍ KARTY GANT UNISEX. LEATHER CARD HOLDER',
            'KOŠILE GANT REG BANKER DOT BD',
            'TENISKY GANT LAWILL',
            'MOKASÍNY GANT GRACELYN',
        ];
        $description = [
            'Dámské tričko s výstřihem do V a krátkým rukávem. Klasický design v rovném střihu doplňuje tonální logo Gant vyšité na hrudi. 100% bavlna prémiové kvality zaručuje dokonalou prodyšnost, maximální komfort při nošení a jednoduchou údržbu. Velmi praktický kousek pro volný čas, který skvěle doladí každý Váš sportovně ležérní outfit.',
            'Dámské kožené mokasíny na nízkém podpatku. Svrchní materiál je tvořen broušenou usní prémiové kvality, která vyniká svou příjemnou měkkostí a mimořádnou odolností. Vytlačený branding na boční straně boty, elegantní prošívání. Kožená stélka spolu s lehkou a při chůzi dokonale poddajnou podešví, představují záruku vysoce komfortního pocitu. Moderní a velmi šmrncovní zpracování nadčasové klasiky, která bude perfektně vypadat v outfitu s formálními kalhotami a sakem.',
            'Dámské tenisky z broušené hovězí usně prémiové kvality. Svrchní materiál vyniká svou odolností, mimořádnou pevností, avšak příjemnou měkkostí a jedinečnou prodyšností. Branding vytlačený na vnější straně boty, ikonický label na jazyku, Gant logo na kontrastně zesílené patě. Textilní podšití, dokonale přizpůsobivá kožená stélka, pryžová podrážka. Praktický, všestranně kombinovatelný artikl, který se stane nepostradatelnou součástí Vašich sportovně-ležérních outfitů.',
            'Pánská dlouhorukávová košile rovného střihu s drobným vzorem. Ikonický button down límec se třemi knoflíky pro držení jeho lepší stability, případně snazší vedení kravaty. Náprsní kapsa s vyšitým Gant logem. Lehká tkanina z bavlněného vlákna prémiové kvality je krásně vzdušná a dokonale prodyšná. Kvalitní materiálové složení je na omak příjemně měkké a velmi komfortní v průběhu nošení. Vkusný artikl, který podtrhne šmrncovní vzhled Vašich ležérně-elegantních outfitů.',
            'Pouzdro na karty z hladké usně prémiové kvality, rozměry 7,5x10 cm. Vytlačený branding na přední straně, elegantní šití v tonálním barevném provedení. Pouzdro obsahuje čtyři přihrádky na karty a otevřenou vrchní kapsu ve středu. Materiálové složení je tvořeno hovězí kůží, která vyniká svou extrémní pevností a odolností, avšak příjemnou měkkostí. Velmi šmrncovní doplněk, jenž je důkazem vytříbeného vkusu.',
            'Dámská zimní parka rovného střihu v délce nad kolena. Propracovaný design s nastavitelnou kapucí a odnímatelnou syntetickou kožešinou doplňuje vnitřní stahování v pase, léga se zapínáním na knoflíky, pružné náplety na rukávech, dvě náprsní a dvě boční kapsy s patkou a knoflíkem. Nechybí diamantový brand našitý na rukávu. Pevná bavlněná tkanina má povrchovou úpravu odolnou vůči vodě, větru a oděru. Kvalitní výplň z kachního peří v poměru 90/10 zajišťuje maximální tepelný komfort. Dokonale funkční a zároveň stylový kousek, který Vás zahřeje i v těch nejchladnějších dnech.',
            'Dámská péřová bunda s délkou pod boky, která byla vyrobena z částečně recyklovaných syntetických vláken. Propracovaný design s fixní kapucí a šňůrkami na stažení doplňuje dvojitý zip překrytý légou s tonálním lemováním a zapínáním na knoflíky, vnitřní stahování v pase a ve spodním lemu, boční kapsy s patkou a knoflíkem a vnitřní kapsička. Nechybí elastické stažení rukávů a našitý brand. Materiálové složení s kvalitní výplní z kachního peří v poměru 90/10 zaručuje dokonalou hřejivost, prodyšnost a příjemný pocit při nošení. Díky propracovanému střihu, který jemně zdůrazňuje ženské křivky, je bunda maximálně praktická a stylově doladí každý Váš zimní outfit.',
            'Dámská lehká péřová vesta, jejíž povrchový materiál a podšívka jsou tvořeny 100% recyklovaným syntetickým vláknem. Rovný střih s dvojitým zipem překrytým légou s kovovými druky doplňují boční kapsy se zapínáním a tonální brand potisk na hrudi. Materiálové složení zaručuje dokonalou lehkost a maximální hřejivost díky kvalitní výplni z kachního peří v poměru 90/10. Velmi praktický a skvěle propracovaný kousek vhodný pro celoroční využití.',
        ];
        $image = [
            'https://eshop-cdn.vermont.eu/colored_products/50000/50000/50929/jpg/product-detail-small2007000018958_1KoeJ6f7RS.jpg',
            'https://eshop-cdn.vermont.eu/colored_products/40000/48000/48734/webp/product-detail-regular4056734677762_1ejGLPhDtn.webp',
            'https://eshop-cdn.vermont.eu/colored_products/40000/48000/48692/webp/product-detail-regular4056734690310_1LSy9AvK7z.webp',
            'https://eshop-cdn.vermont.eu/colored_products/50000/50000/50336/webp/product-detail-regular2007000011546_1xCiR9Qn23.webp',
            'https://eshop-cdn.vermont.eu/colored_products/50000/50000/50750/webp/product-detail-regular7325707070495_1pauH18os0.webp',
            'https://eshop-cdn.vermont.eu/colored_products/40000/41000/41769/webp/product-detail-regular4700245-34_1922mAsNEn.webp',
            'https://eshop-cdn.vermont.eu/colored_products/40000/41000/41771/webp/product-detail-regular4700246-19_1VqSqmnG6b.webp',
            'https://eshop-cdn.vermont.eu/colored_products/50000/50000/50949/webp/product-detail-regular4700210-433_nCphAveNww.webp',
        ];
        $color = [
            'orange',
            'red',
            'blue',
            'yellow',
            'green',
            'pink',
            'brown',
            'gray',
            'black',
        ];
        $cut = [
            'relaxed',
            'mokasíny',
            'tenisky',
            'regular',
            'city',
            'slim',
        ];
        $category = [
            'Dámské polokošile a trička',
            'GANT',
            'ŽENY',
            'BUNDY A VESTY',
            'BUNDY',
            'DOPLŇKY',
            'MUŽI',
            'KOŠILE',
            'BOTY',
        ];


        for ($i = 1; $i < 1000; $i++) {
            $priceDiscount = 0.0;
            if ($i % 3) {
                $priceDiscount = \random_int(2000, 20000);
            }

            $yMax = \random_int(1, 6);
            $sizes = [];
            for ($y = 0; $y < $yMax; $y++) {
                $sizes[] = $sizeData[\array_rand($sizeData)];
            }

            $zMax = \random_int(1, 3);
            $material = [];
            for ($z = 0; $z < $zMax; $z++) {
                $material[] = $materialData[\array_rand($materialData)];
            }
            $stickers = [];
            $stickers[] = $stickerData[\array_rand($stickerData)];
            $stickers[] = $stickerData[\array_rand($stickerData)];

            $products[] = new \App\WorkshopFourModule\Entity\Product(
                id: new \Spameri\Elastic\Entity\Property\ElasticId((string) \random_int(1, 1000000)),
                title: $titles[\array_rand($titles)],
                description: $description[\array_rand($description)],
                price: (float) \random_int(1000, 20000),
                priceDiscount: $priceDiscount,
                image: $image[\array_rand($image)],
                color: $color[\array_rand($color)],
                size: $sizes,
                cut: $cut[\array_rand($cut)],
                material: $material,
                stickers: $stickers,
                category: $category[\array_rand($category)],
                published: new \DateTime(),
                stock: \random_int(0, 20),
            );
        }

        return $products;
    }


    /*
     * ClientProvider

    public function init(): void
    {
        $settings = $this->settingsProvider->provide();
        $this->clientBuilder->setHosts(
            [
                $settings->host() . ':' . $settings->port(),
            ]
        );
        $this->clientBuilder->setConnectionParams(
            [
                'client' => [
                    'headers' => $settings->headers(),
                ],
            ]
        );
    }

    public function client(): \Elasticsearch\Client
    {
        if ( ! ($this->client instanceof \Elasticsearch\Client)) {
            $this->client = $this->clientBuilder->build();
        }

        return $this->client;
    }

    */
}
