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
        \Symfony\Component\Console\Output\OutputInterface $output,
    ): int
    {
        // 1. init output
        $output->writeln('Starting');
        $oldIndexName = false;

        $newIndexName = $this->recipeSettings->provide()->indexName() . '_' . \random_int(1000, 9999);
        // 2. Create index / delete and create index
        try {
            $document = new \Spameri\ElasticQuery\Document(
                index: $this->recipeSettings->provide()->indexName(),
            );

            $aliasDocument = new \Spameri\ElasticQuery\Document(
                index: $this->recipeSettings->provide()->indexName(),
                options:
                    [
                        'name' => $this->recipeSettings->provide()->indexName(),
                    ],
            );

            $response = $this->clientProvider->client()->indices()->existsAlias($aliasDocument->toArray());
            $oldIndexName = $response === false ? false : $response['hits']['hits'][0]['_index'];

            // 2a. Delete index
            $this->clientProvider->client()->indices()->delete($document->toArray());

        } catch (\Elasticsearch\Common\Exceptions\Missing404Exception $exception) {

            // 3. Initialize index
            $settingsDocument = new \Spameri\ElasticQuery\Document(
                index: $newIndexName,
                body: new \Spameri\ElasticQuery\Document\Body\Plain(
                    $this->recipeSettings->provide()->toArray(),
                ),
            );

            $this->clientProvider->client()->indices()->create(
                $settingsDocument->toArray(),
            );
        }

        // 4. Export data
        foreach ($this->createRecipes() as $recipe) {
            $output->writeln('Exporting recipe: ' . $recipe->title);

            $recipeDocument = new \Spameri\ElasticQuery\Document(
                $newIndexName,
                new \Spameri\ElasticQuery\Document\Body\Plain(
                    $recipe->entityVariables()
                )
            );

            $this->clientProvider->client()->index($recipeDocument->toArray());

            $output->writeln('Recipe done');
        }

        // 5. Alias switch
        $actions = [
            [
                'add' => [
                    'index' => $newIndexName,
                    'alias' => $this->recipeSettings->provide()->indexName(),
                ],
            ],
        ];

        if ($oldIndexName !== false) {
            $actions[] = [
                'remove' => [
                    'index' => $oldIndexName,
                    'alias' => $this->recipeSettings->provide()->indexName(),
                ],
            ];
        }

        $aliasDocument = new \Spameri\ElasticQuery\Document(
            null,
            body: new \Spameri\ElasticQuery\Document\Body\Plain(
                [
                    'actions' => $actions,
                ]
            ),
        );
        $this->clientProvider->client()->indices()->updateAliases($aliasDocument->toArray());

        // 6. Done
        $output->writeln('Done');

        return 0;
    }

    /**
     * @return \App\WorkshopFiveModule\Entity\Recipe[]
     */
    public function createRecipes(): array
    {
        $recipes = [];

        $titles = [
            'Knedlo vepřo zelo',
            'Svíčková na smetaně',
            'Guláš',
            'Smažený sýr',
            'Smažený hermelín',
            'Smažený květák',
            'Rajská omáčka',
            'Vepřová pečeně',
            'Kuřecí řízek',
            'Kuřecí kapsa',
            'Kuřecí steak',
            'Kuřecí prsa',
        ];

        $author = [
            'Petr Novák',
            'Jan Novák',
            'Petr Novotný',
            'Jan Novotný',
            'Petr Dvořák',
            'Jan Dvořák',
            'Petr Procházka',
            'Jan Procházka',
            'Petr Kučera',
            'Jan Kučera',
        ];

        $cookTime = [
            15,
            30,
            45,
            60,
            75,
            90,
        ];

        $categories = [
            'hlavní jídlo',
            'polévka',
            'dezert',
            'snídaně',
            'svačina',
            'předkrm',
            'omáčka',
            'salát',
            'nápoj',
            'příloha',
            'dort',
            'koláč',
            'knedlíky',
            'těstoviny',
            'pizza',
            'těsto',
            'pečivo',
            'sendvič',
            'knedlo vepřo zelo',
            'knedlo vepřo zelo recept',
            'knedlo vepřo zelo recepty',
        ];

        $ingredients = [
            'cukr',
            'mouka',
            'sůl',
            'pepř',
            'voda',
            'olej',
            'máslo',
            'vepřové',
            'hovězí',
            'kuřecí',
            'vejce',
            'cibule',
            'česnek',
            'paprika',
            'rajčata',
        ];

        $tags = [
            'Marinování',
            'Grilování',
            'Řecká kuchyně',
            'Náročné recepty',
            'Slané chutě',
            'Svačina',
            'Snídaně',
            'Dezert',
            'Polévka',
        ];

        for ($i = 1; $i < 1000; $i++) {
            $recipeCategories[] = $categories[\array_rand($categories)];
            $recipeCategories[] = $categories[\array_rand($categories)];
            $recipeCategories[] = $categories[\array_rand($categories)];

            $recipeIngredients[] = $ingredients[\array_rand($ingredients)];
            $recipeIngredients[] = $ingredients[\array_rand($ingredients)];
            $recipeIngredients[] = $ingredients[\array_rand($ingredients)];
            $recipeIngredients[] = $ingredients[\array_rand($ingredients)];
            $recipeIngredients[] = $ingredients[\array_rand($ingredients)];

            $recipeTags[] = $tags[\array_rand($tags)];
            $recipeTags[] = $tags[\array_rand($tags)];
            $recipeTags[] = $tags[\array_rand($tags)];
            $recipeTags[] = $tags[\array_rand($tags)];

            $recipes[] = new \App\WorkshopFiveModule\Entity\Recipe(
                id: \random_int(1, 1000000),
                title: $titles[\array_rand($titles)],
                categories: $recipeCategories,
                ingredients: $recipeIngredients,
                tags: $recipeTags,
                created: new \DateTime(\random_int(1, 1000) . ' days ago'),
                rating: (float) \random_int(1, 50) / 10,
                views: \random_int(1, 1000),
                cookTime: $cookTime[\array_rand($cookTime)],
                hasImage: \random_int(0, 1) === 1,
                hasVideo: \random_int(0, 1) === 1,
                hasCookbook: \random_int(0, 1) === 1,
                author: $author[\array_rand($author)],
            );

            $recipeCategories = [];
            $recipeIngredients = [];
            $recipeTags = [];
        }

        return $recipes;
    }
}