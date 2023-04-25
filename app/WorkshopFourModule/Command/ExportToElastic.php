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

        $productDocument = new \Spameri\ElasticQuery\Document(
            index: $this->productConfig->provide()->indexName(),
            body: new \Spameri\ElasticQuery\Document\Body\Plain(
                (
                    new \App\WorkshopFourModule\Entity\Product(
                        id: new \Spameri\Elastic\Entity\Property\ElasticId('123465'),
                        title: 'TRIČKO GANT ORIGINAL V-NECK SS T-SHIRT',
                        description: 'Dámské tričko s výstřihem do V a krátkým rukávem. Klasický design v rovném střihu doplňuje tonální logo Gant vyšité na hrudi. 100% bavlna prémiové kvality zaručuje dokonalou prodyšnost, maximální komfort při nošení a jednoduchou údržbu. Velmi praktický kousek pro volný čas, který skvěle doladí každý Váš sportovně ležérní outfit.',
                        price: 1199.0,
                        priceDiscount: 1499.0,
                        image: 'https://eshop-cdn.vermont.eu/colored_products/50000/50000/50929/jpg/product-detail-small2007000018958_1KoeJ6f7RS.jpg',
                        color: 'PUMPKIN ORANGE',
                        size: [
                            'XS',
                            'S',
                            'M',
                            'L',
                            'XL',
                        ],
                        cut: 'relaxed',
                        material: [
                            'BAVLNA 100 %'
                        ],
                        stickers: [
                            'DOPRAVA ZDARMA',
                            'NOVINKA',
                        ],
                        category: 'Dámské polokošile a trička',
                        published: new \DateTime(),
                        stock: 10,
                    )
                )->entityVariables()
            ),
        );

        $this->clientProvider->client()->index($productDocument->toArray());

        // 5. Alias switch

        $output->writeln('Done');

        return 0;
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
