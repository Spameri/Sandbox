<?php declare(strict_types = 1);

namespace App\WorkshopFiveModule\Command;

class ExportToElastic extends \Symfony\Component\Console\Command\Command
{

    public function __construct(
        string $name = null,
        private \Spameri\Elastic\ClientProvider $clientProvider,
        private readonly \App\WorkshopFiveModule\Entity\RecipeSettings $recipeSettings,
    )
    {
        parent::__construct($name);
    }

    public function configure(): void
    {
        $this->setName('workshopFive:exportToElastic');
    }


    public function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ): int
    {
        // 1. init output
        $output->writeln('Starting');

        // 2. Create index / delete and create index
        try {
            $document = new \Spameri\ElasticQuery\Document(
                index: $this->recipeSettings->provide()->indexName(),
            );

            // 2a. Delete index
            $this->clientProvider->client()->indices()->delete($document->toArray());

        } catch (\Elasticsearch\Common\Exceptions\Missing404Exception $exception) {

            // 3. Setup index
            $settingsDocument = new \Spameri\ElasticQuery\Document(
                index: $this->recipeSettings->provide()->indexName(),
                body: new \Spameri\ElasticQuery\Document\Body\Plain(
                    $this->recipeSettings->provide()->toArray()
                )
            );

            $this->clientProvider->client()->indices()->create(
                $settingsDocument->toArray()
            );
        }


        // 3. Initialize index

        // 4. Export data

        // 5. Alias switch

        // 6. Done
        $output->writeln('Done');

        return 0;
    }
}