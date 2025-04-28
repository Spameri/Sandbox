<?php declare(strict_types = 1);

namespace App\WorkshopSixModule\Presenter;

class WorkshopPresenter extends \App\Presenter\BasePresenter
{

    public function __construct(
        private \Elasticsearch\ClientBuilder $clientBuilder,



        private \App\WorkshopSixModule\Entity\ProductSettings $productSettings,
        private \Spameri\ElasticQuery\Response\ResultMapper $resultMapper,
    )
    {
    }

    public function renderDefault(string $queryString = '')
    {
        $elasticQuery = new \Spameri\ElasticQuery\ElasticQuery();

        $multiMatch = new \Spameri\ElasticQuery\Query\MultiMatch(
            fields: [
                'SisStiNameSearch^30',
                'SisStiNameSearch.cestina^25',
                'SisStiName^30',
                'SisStiSLName^20',
                'SisStiCodeLike.raw^20',
                'SisStiPartNoLike.raw^20',
                'SisStiName.cestina^15',
                'SisStiNameWeb^10',
                'SisStiNameWeb.cestina^9',
                'SisStiNameGen^9',
                'SisStiNameGen.cestina^8',
                'SisStiKeywords^8',
                'SisStiKeywords.cestina^8',
                'SisStiManName^6',
                'SisStiSpecType^5',
                'SisStiCodeLike.cestina_suggest',
                'SisStiPartNoLike.cestina_suggest',
                'SisStiStoCode1',
                'SisStiStoCode2',
                'SisStiStoCode3',
                'SisStiStoCode4',
                'SisStiStoCode5',
                'SisStiStoCode6',
            ],
            query: 'notebook dell',
            fuzziness: new \Spameri\ElasticQuery\Query\Match\Fuzziness(
                \Spameri\ElasticQuery\Query\Match\Fuzziness::AUTO,
            ),
            type: \Spameri\ElasticQuery\Query\Match\MultiMatchType::BEST_FIELDS,
            operator: \Spameri\ElasticQuery\Query\Match\Operator::OR,
            analyzer: 'cestina',
        );
        $elasticQuery->addMustQuery($multiMatch);

        $match = new \Spameri\ElasticQuery\Query\ElasticMatch(
            field: 'SisStiSpecType',
            query: 'notebook dell',
            fuzziness: new \Spameri\ElasticQuery\Query\Match\Fuzziness(
                \Spameri\ElasticQuery\Query\Match\Fuzziness::AUTO,
            ),
            operator: \Spameri\ElasticQuery\Query\Match\Operator::OR,
            analyzer: 'cestina',
        );
        $elasticQuery->addShouldQuery($match);

        $subQuery = new \Spameri\ElasticQuery\Query\QueryCollection();
        $subQuery->addMustNotQuery(
            new \Spameri\ElasticQuery\Query\Term(
                field: 'StiHideIList',
                query: '100mega',
            ),
        );
        $subQuery->addMustNotQuery(
            new \Spameri\ElasticQuery\Query\Term(
                field: 'SisHideManufacturerComId',
                query: -1,
            ),
        );
        $subQuery->addMustNotQuery(
            new \Spameri\ElasticQuery\Query\Terms(
                field: 'SisHideComDiscGroup',
                query: [
                    -1,
                ],
            ),
        );
        $subQuery->addMustQuery(
            new \Spameri\ElasticQuery\Query\Range(
                field: 'SisStiPrice2',
                gte: 0,
                lte: \PHP_INT_MAX,
            ),
        );
        $subQuery->addMustQuery(
            new \Spameri\ElasticQuery\Query\Term(
                field: 'SisComId',
                query: 0,
            ),
        );
        $subQuery->addMustQuery(
            new \Spameri\ElasticQuery\Query\Term(
                field: 'SisStiHideI',
                query: 0,
            ),
        );
        $subQuery->addMustQuery(
            new \Spameri\ElasticQuery\Query\Term(
                field: 'SisStiHide',
                query: 0,
            ),
        );
        $subQuery->addMustQuery(
            new \Spameri\ElasticQuery\Query\Term(
                field: 'SisNotSearchable',
                query: 0,
            ),
        );
        $subQuery->addMustQuery(
            new \Spameri\ElasticQuery\Query\Terms(
                field: 'SisNotSearchable',
                query: [
                    0,
                ],
            ),
        );
        $elasticQuery->addFilter($subQuery);

        $elasticQuery->addAggregation(
            new \Spameri\ElasticQuery\Aggregation\LeafAggregationCollection(
                name: 'max_price',
                filter: null,
                aggregations: new \Spameri\ElasticQuery\Aggregation\Max(
                    'SisStiPrice2',
                ),
            ),
        );
        $elasticQuery->addAggregation(
            new \Spameri\ElasticQuery\Aggregation\LeafAggregationCollection(
                name: 'min_price',
                filter: null,
                aggregations: new \Spameri\ElasticQuery\Aggregation\Min(
                    'SisStiPrice2',
                ),
            ),
        );
        $elasticQuery->addAggregation(
            new \Spameri\ElasticQuery\Aggregation\LeafAggregationCollection(
                name: 'categorie',
                filter: null,
                aggregations: new \Spameri\ElasticQuery\Aggregation\Term(
                    'SisTree',
                    10,
                ),
            ),
        );
        $elasticQuery->addAggregation(
            new \Spameri\ElasticQuery\Aggregation\LeafAggregationCollection(
                name: 'manName',
                filter: null,
                aggregations: new \Spameri\ElasticQuery\Aggregation\Term(
                    'SisStiManName',
                    10,
                ),
            ),
        );
        $elasticQuery->options()->changeSize(10);

//        $elasticQuery->functionScore()?->function()->add(
//            new \Spameri\ElasticQuery\FunctionScore\ScoreFunction\RandomScore()
//        );

        $elasticQuery->addShouldQuery(
            new \Spameri\ElasticQuery\Query\Term(
                'SisStiRank',
                1,
                boost: 1,
            ),
        );
        $elasticQuery->addShouldQuery(
            new \Spameri\ElasticQuery\Query\Term(
                'SisStiRank',
                3,
                boost: 3,
            ),
        );
        $elasticQuery->addShouldQuery(
            new \Spameri\ElasticQuery\Query\Term(
                'SisStiRank',
                5,
                boost: 30,
            ),
        );
        $elasticQuery->addShouldQuery(
            new \Spameri\ElasticQuery\Query\Term(
                'SisStiRank',
                10,
                boost: 99,
            ),
        );

        $elasticQuery->addShouldQuery(
            new \Spameri\ElasticQuery\Query\QueryCollection(
                mustCollection: new \Spameri\ElasticQuery\Query\MustCollection(
                    new \Spameri\ElasticQuery\Query\QueryCollection(
                        mustCollection: new \Spameri\ElasticQuery\Query\MustCollection(
                            new \Spameri\ElasticQuery\Query\Range(
                                field: 'SisQtyFree',
                                gte: 1,
                                boost: 3,
                            ),
                        ),
                        shouldCollection: new \Spameri\ElasticQuery\Query\ShouldCollection(
                            new \Spameri\ElasticQuery\Query\Term(
                                'SisStiStatus',
                                24,
                                boost: 3,
                            ),
                            new \Spameri\ElasticQuery\Query\Term(
                                'SisStiStatus',
                                27,
                                boost: 3,
                            ),
                            new \Spameri\ElasticQuery\Query\Term(
                                'SisStiStatus',
                                1,
                                boost: 2,
                            ),
                            new \Spameri\ElasticQuery\Query\Range(
                                'SisStiWAvgNew',
                                gte: 10,
                                boost: 2,
                            ),
                        ),
                    ),
                ),
            ),
        );

        $document = new \Spameri\ElasticQuery\Document(
            index: 'products',
            body: new \Spameri\ElasticQuery\Document\Body\Plain(
                $elasticQuery->toArray(),
            ),
        );
        $response = $this->clientBuilder->build()->search(
            $document->toArray(),
        );

        /** @var \Spameri\ElasticQuery\Response\ResultSearch $mappedResult */
        $mappedResult = $this->resultMapper->map($response);

        $mappedResult->aggregations()->getAggregation('max_price');

        /** @var \Spameri\ElasticQuery\Response\Result\Hit $hit */
        foreach ($mappedResult->hits() as $hit) {
            $rank = $hit->getIntegerValue('SisStiRank');
            $cestina = $hit->getSubValue('SisStiNameSearch.cestina');

            $product = new Product(
                $hit->id(),
                $hit->getStringValue('SisStiName'),
            );
        }




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