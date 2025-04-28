<?php declare(strict_types = 1);

namespace App\WorkshopFiveModule\Presenter;

class WorkshopPresenter extends \App\Presenter\BasePresenter
{

    public function __construct(
        private \Spameri\Elastic\ClientProvider $clientProvider,
        private \App\WorkshopFiveModule\Entity\RecipeSettings $recipeSettings,
        private \Spameri\ElasticQuery\Response\ResultMapper $resultMapper,
    )
    {
    }

    public function renderSuggest(string $queryString = '')
    {
        $elasticQuery = new \Spameri\ElasticQuery\ElasticQuery();
        $elasticQuery->options()->changeSize(3);

        $elasticQuery->addMustQuery(
            new \Spameri\ElasticQuery\Query\MultiMatch(
                fields: [
                    \App\WorkshopFiveModule\Entity\Recipe::TITLE .
                    \Spameri\ElasticQuery\Mapping\Settings\Mapping\FieldSeparator::FIELD_SEPARATOR .
                    \App\WorkshopFiveModule\Entity\Recipe::CZECH_DICTIONARY,
                    \App\WorkshopFiveModule\Entity\Recipe::TITLE . '.edgeNgram',
                ],
                query: $queryString,
                boost: 1,
                fuzziness: new \Spameri\ElasticQuery\Query\Match\Fuzziness(
                    \Spameri\ElasticQuery\Query\Match\Fuzziness::AUTO,
                ),
                type: \Spameri\ElasticQuery\Query\Match\MultiMatchType::BEST_FIELDS,
                minimumShouldMatch: 1,
                operator: \Spameri\ElasticQuery\Query\Match\Operator::OR,
                analyzer: 'czechDictionary',
            ),
        );


        $response = $this->clientProvider->client()->search(
            (
            new \Spameri\ElasticQuery\Document(
                $this->recipeSettings->provide()->indexName(),
                new \Spameri\ElasticQuery\Document\Body\Plain(
                    $elasticQuery->toArray(),
                ),
            )
            )->toArray(),
        );

        \Tracy\Debugger::barDump($response);
    }

    public function renderDefault(string $queryString = '')
    {
        $elasticQuery = new \Spameri\ElasticQuery\ElasticQuery();
        $elasticQuery->options()->changeSize(20);

        $elasticQuery->addMustQuery(
            new \Spameri\ElasticQuery\Query\Term(
                field: \App\WorkshopFiveModule\Entity\Recipe::HAS_IMAGE,
                query: true,
            ),
        );

        // TODO pro sladění s našeptáváním bude potřeba poladit jak se pracuje s edgeNgramy
//        $elasticQuery->addShouldQuery(
//            new \Spameri\ElasticQuery\Query\ElasticMatch(
//                field: \App\WorkshopFiveModule\Entity\Recipe::TITLE . '.asciiEdgeNgram',
//                query: $queryString,
//                boost: 0.5,
//                analyzer: 'onlyLowercase',
//            ),
//        );

        $elasticQuery->addShouldQuery(
            new \Spameri\ElasticQuery\Query\ElasticMatch(
                field: \App\WorkshopFiveModule\Entity\Recipe::TITLE . '.edgeNgram',
                query: $queryString,
                boost: 0.5,
                analyzer: 'customLowercase',
            ),
        );

        $elasticQuery->addShouldQuery(
            new \Spameri\ElasticQuery\Query\MultiMatch(
                fields: [
                    \App\WorkshopFiveModule\Entity\Recipe::TITLE .
                    \Spameri\ElasticQuery\Mapping\Settings\Mapping\FieldSeparator::FIELD_SEPARATOR .
                    \App\WorkshopFiveModule\Entity\Recipe::CZECH_DICTIONARY,
                    \App\WorkshopFiveModule\Entity\Recipe::TITLE . '.commonGrams',
                    \App\WorkshopFiveModule\Entity\Recipe::TITLE . '.wordDelimiter',
                    \App\WorkshopFiveModule\Entity\Recipe::TITLE . '.synonym',
                ],
                query: $queryString,
                boost: 5,
                fuzziness: new \Spameri\ElasticQuery\Query\Match\Fuzziness(
                    \Spameri\ElasticQuery\Query\Match\Fuzziness::AUTO,
                ),
                type: \Spameri\ElasticQuery\Query\Match\MultiMatchType::BEST_FIELDS,
                minimumShouldMatch: 1,
                operator: \Spameri\ElasticQuery\Query\Match\Operator::OR,
                analyzer: 'czechDictionary',
            ),
        );

        $elasticQuery->addMustQuery(
            new \Spameri\ElasticQuery\Query\Range(
                field: \App\WorkshopFiveModule\Entity\Recipe::COOK_TIME,
                lte: 15,
            ),
        );

        $elasticQuery->addMustNotQuery(
            new \Spameri\ElasticQuery\Query\ElasticMatch(
                field: \App\WorkshopFiveModule\Entity\Recipe::INGREDIENTS,
                query: 'máslo',
                fuzziness: new \Spameri\ElasticQuery\Query\Match\Fuzziness(
                    \Spameri\ElasticQuery\Query\Match\Fuzziness::AUTO,
                ),
            ),
        );
        $elasticQuery->addMustNotQuery(
            new \Spameri\ElasticQuery\Query\Terms(
                field: \App\WorkshopFiveModule\Entity\Recipe::INGREDIENTS,
                query: [
                    'mouka',
                ],
            ),
        );

        $elasticQuery->addShouldQuery(
            new \Spameri\ElasticQuery\Query\Term(
                field: \App\WorkshopFiveModule\Entity\Recipe::HAS_IMAGE,
                query: true,
                boost: 1,
            ),
        );

        $elasticQuery->addShouldQuery(
            new \Spameri\ElasticQuery\Query\Exists(
                'cookedRecipe',
            ),
        );

        $elasticQuery->options()->sort()->add(
            new \Spameri\ElasticQuery\Options\Sort(
                field: \App\WorkshopFiveModule\Entity\Recipe::RATING,
                type: \Spameri\ElasticQuery\Options\Sort::DESC,
            ),
        );

        ////////////////////////

        $response = $this->clientProvider->client()->search(
            (
            new \Spameri\ElasticQuery\Document(
                $this->recipeSettings->provide()->indexName(),
                new \Spameri\ElasticQuery\Document\Body\Plain(
                    $elasticQuery->toArray(),
                ),
            )
            )->toArray(),
        );

        $mappedResult = $this->resultMapper->map($response);

        $this->template->queryString = $queryString;
        $this->template->aggregations = [];
        $this->template->products = $this->mapRecipes($mappedResult);
    }

    public function createComponentSearchForm() :\Nette\Application\UI\Form
    {
        $form = new \Nette\Application\UI\Form();
        $form->addText('queryString', 'query')
            ->setAttribute('class', 'inp-text suggest')
        ;

        $form->addSubmit('search', 'Search');

        $form->onSuccess[] = function () use ($form) {
            $this->redirect(
                ':WorkshopFive:Workshop:default',
                [
                    'queryString' => $form->getValues()->queryString,
                ],
            );
        };

        return $form;
    }

    private function mapRecipes(\Spameri\ElasticQuery\Response\ResultInterface $mappedResult)
    {
        $recipes = [];
        /** @var \Spameri\ElasticQuery\Response\Result\Hit $hit */
        foreach ($mappedResult->hits() as $hit) {
            $recipes[] = new \App\WorkshopFiveModule\Entity\Recipe(
                id: $hit->getIntegerValue(\App\WorkshopFiveModule\Entity\Recipe::ID),
                title: $hit->getStringValue(\App\WorkshopFiveModule\Entity\Recipe::TITLE),
                categories: $hit->getArrayValue(\App\WorkshopFiveModule\Entity\Recipe::CATEGORIES),
                ingredients: $hit->getArrayValue(\App\WorkshopFiveModule\Entity\Recipe::INGREDIENTS),
                tags: $hit->getArrayValue(\App\WorkshopFiveModule\Entity\Recipe::TAGS),
                created: new \DateTime($hit->getStringValue(\App\WorkshopFiveModule\Entity\Recipe::CREATED)),
                rating: $hit->getValue(\App\WorkshopFiveModule\Entity\Recipe::RATING),
                views: $hit->getIntegerValue(\App\WorkshopFiveModule\Entity\Recipe::VIEWS),
                cookTime: $hit->getIntegerValue(\App\WorkshopFiveModule\Entity\Recipe::COOK_TIME),
                hasImage: $hit->getBoolValue(\App\WorkshopFiveModule\Entity\Recipe::HAS_IMAGE),
                hasVideo: $hit->getBoolValue(\App\WorkshopFiveModule\Entity\Recipe::HAS_VIDEO),
                hasCookbook: $hit->getBoolValue(\App\WorkshopFiveModule\Entity\Recipe::HAS_COOKBOOK),
                author: $hit->getStringValue(\App\WorkshopFiveModule\Entity\Recipe::AUTHOR),
            );
        }

        return $recipes;
    }
}