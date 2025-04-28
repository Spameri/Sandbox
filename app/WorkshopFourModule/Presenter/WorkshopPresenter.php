<?php declare(strict_types = 1);

namespace App\WorkshopFourModule\Presenter;

class WorkshopPresenter extends \App\Presenter\BasePresenter
{

    public function __construct(
        private \Spameri\Elastic\ClientProvider $clientProvider,
        private \App\WorkshopFourModule\Entity\ProductConfig $productConfig,
        private \Spameri\ElasticQuery\Response\ResultMapper $resultMapper,
    ) {}

    public function renderDefault(string $queryString = '')
    {
        // SEARCH
        $elasticQuery = new \Spameri\ElasticQuery\ElasticQuery();
        $elasticQuery->options()->changeSize(1000);
//        $elasticQuery->options()->changeFrom(100); pro stránkování

        // vyhledávání v bool fieldu
//        $elasticQuery->addMustQuery(
//            new \Spameri\ElasticQuery\Query\Term(
//                \App\WorkshopFourModule\Entity\Product::PUBLIC,
//                true
//            )
//        );

        $elasticQuery->addMustQuery(
            new \Spameri\ElasticQuery\Query\Range(
                field: \App\WorkshopFourModule\Entity\Product::STOCK,
                gte: 1,
            )
        );

        $elasticQuery->addMustQuery(
            new \Spameri\ElasticQuery\Query\Exists(
                \App\WorkshopFourModule\Entity\Product::IMAGE
            )
        );

        // řazení podle abecedy
//        $elasticQuery->options()->sort()
//            ->add(
//                new \Spameri\ElasticQuery\Options\Sort(
//                    field: \App\WorkshopFourModule\Entity\Product::CATEGORY,
//                    type: \Spameri\ElasticQuery\Options\Sort::DESC,
//                    missing: \Spameri\ElasticQuery\Options\Sort::MISSING_FIRST,
//                )
//            );
// řazení podle ceny
//        $elasticQuery->options()->sort()
//            ->add(
//                new \Spameri\ElasticQuery\Options\Sort(
//                    field: \App\WorkshopFourModule\Entity\Product::PRICE,
//                    type: \Spameri\ElasticQuery\Options\Sort::ASC,
//                    missing: \Spameri\ElasticQuery\Options\Sort::MISSING_FIRST,
//                )
//            );

        $elasticQuery->addShouldQuery(
            new \Spameri\ElasticQuery\Query\Term(
                \App\WorkshopFourModule\Entity\Product::STICKERS,
                'NOVINKA',
                10
            )
        );

        $elasticQuery->addShouldQuery(
            new \Spameri\ElasticQuery\Query\ElasticMatch(
                field: \App\WorkshopFourModule\Entity\Product::DESCRIPTION,
                query: $queryString,
                boost: 5,
                analyzer: \Spameri\ElasticQuery\Mapping\Analyzer\Custom\CzechDictionary::NAME
            )
        );

        $elasticQuery->addMustQuery(
            new \Spameri\ElasticQuery\Query\MultiMatch(
                fields: [
                    'title.czechDictionary',
                    'title.slovakDictionary',
                    'title.hungarianDictionary',
                    'title.wordJoin',
                    'title.wordSplit',
                    'title.synonym',
                    'title.edgeNgram',
                ],
                query: $queryString,
                boost: 10,
                fuzziness: new \Spameri\ElasticQuery\Query\Match\Fuzziness('2'),
                type: \Spameri\ElasticQuery\Query\Match\MultiMatchType::BEST_FIELDS,
                operator: \Spameri\ElasticQuery\Query\Match\Operator::OR,
                analyzer: \Spameri\ElasticQuery\Mapping\Analyzer\Custom\CzechDictionary::NAME,
            )
        );

        if ($this->getParameter('color')) {
            $elasticQuery->addMustQuery(
                new \Spameri\ElasticQuery\Query\Term(
                    \App\WorkshopFourModule\Entity\Product::COLOR,
                    $this->getParameter('color'),
                )
            );
        }

        if ($this->getParameter('priceFrom')) {
            $elasticQuery->addMustQuery(
                new \Spameri\ElasticQuery\Query\Range(
                    field: \App\WorkshopFourModule\Entity\Product::PRICE,
                    gte: $this->getParameter('priceFrom'),
                    lte: $this->getParameter('priceTo'),
                )
            );
        }

        if ($this->getParameter('sticker')) {
            $elasticQuery->addMustQuery(
                new \Spameri\ElasticQuery\Query\Term(
                    \App\WorkshopFourModule\Entity\Product::STICKERS,
                    $this->getParameter('sticker'),
                )
            );
        }


        $response = $this->clientProvider->client()->search(
            (
            new \Spameri\ElasticQuery\Document(
                $this->productConfig->provide()->indexName(),
                new \Spameri\ElasticQuery\Document\Body\Plain(
                    $elasticQuery->toArray()
                )
            )
            )->toArray()
        );

        // AGGREGATIONS
        $elasticQuery->addAggregation(
            new \Spameri\ElasticQuery\Aggregation\LeafAggregationCollection(
                name: 'price',
                filter: $this->buildFilter('price'),
                aggregations: new \Spameri\ElasticQuery\Aggregation\Range(
                    field: \App\WorkshopFourModule\Entity\Product::PRICE,
                    keyed: true,
                    rangeValueCollection: new \Spameri\ElasticQuery\Aggregation\RangeValueCollection(
                        new \Spameri\ElasticQuery\Aggregation\RangeValue(
                            key: '1000 - 2000 Kč',
                            from: 1000,
                            to: 2000,
                        ),
                        new \Spameri\ElasticQuery\Aggregation\RangeValue(
                            key: '2000 - 5000 Kč',
                            from: 2000,
                            to: 5000,
                        ),
                        new \Spameri\ElasticQuery\Aggregation\RangeValue(
                            key: '5000 - 10000 Kč',
                            from: 5000,
                            to: 10000,
                        ),
                        new \Spameri\ElasticQuery\Aggregation\RangeValue(
                            key: '10000 Kč',
                            from: 10000,
                            to: null,
                        ),
                    )
                )
            )
        );

        $elasticQuery->addAggregation(
            new \Spameri\ElasticQuery\Aggregation\LeafAggregationCollection(
                name: 'color',
                filter: $this->buildFilter('color'),
                aggregations: new \Spameri\ElasticQuery\Aggregation\Term(
                    field: \App\WorkshopFourModule\Entity\Product::COLOR
                )
            )
        );

        $elasticQuery->addAggregation(
            new \Spameri\ElasticQuery\Aggregation\LeafAggregationCollection(
                name: 'stickers',
                filter: $this->buildFilter('stickers'),
                aggregations: new \Spameri\ElasticQuery\Aggregation\Term(
                    field: \App\WorkshopFourModule\Entity\Product::STICKERS
                )
            )
        );

        $elasticQuery->query()->must()->remove(
            'term_' . \App\WorkshopFourModule\Entity\Product::COLOR
            . '_' . $this->getParameter('color')
        );
        $elasticQuery->query()->must()->remove(
            'term_' . \App\WorkshopFourModule\Entity\Product::STICKERS
            . '_' . $this->getParameter('sticker')
        );
        $elasticQuery->query()->must()->remove(
            'range_' . \App\WorkshopFourModule\Entity\Product::PRICE
            . '_' . $this->getParameter('priceFrom')
            . '_' . $this->getParameter('priceTo')
        );

        // RESPONSE
        $responseAggs = $this->clientProvider->client()->search(
            (
                new \Spameri\ElasticQuery\Document(
                    $this->productConfig->provide()->indexName(),
                    new \Spameri\ElasticQuery\Document\Body\Plain(
                        $elasticQuery->toArray()
                    )
                )
            )->toArray()
        );

        $mappedResponse = $this->resultMapper->mapSearchResults($response);
        $mappedResponseAggs = $this->resultMapper->mapSearchResults($responseAggs);

        // FACTORY
        $products = $this->createProducts($mappedResponse);

        $this->template->products = $products;
        $this->template->queryString = $queryString;
        $this->template->aggregations = $mappedResponseAggs;
        $this->template->color = $this->getParameter('color');
        $this->template->priceTo = $this->getParameter('priceTo');
        $this->template->priceFrom = $this->getParameter('priceFrom');
        $this->template->stickerParam = $this->getParameter('sticker');
    }


    public function buildFilter($type): \Spameri\ElasticQuery\Filter\FilterCollection
    {
        $filters = new \Spameri\ElasticQuery\Filter\FilterCollection();

        if ($type === 'price') {
            $filters->must()->add(
                new \Spameri\ElasticQuery\Query\Term(
                    \App\WorkshopFourModule\Entity\Product::COLOR,
                    $this->getParameter('color'),
                )
            );
            $filters->must()->add(
                new \Spameri\ElasticQuery\Query\Term(
                    \App\WorkshopFourModule\Entity\Product::STICKERS,
                    $this->getParameter('sticker'),
                )
            );
        }

        if ($type === 'color') {
            $filters->must()->add(
                new \Spameri\ElasticQuery\Query\Range(
                    field: \App\WorkshopFourModule\Entity\Product::PRICE,
                    gte: $this->getParameter('priceFrom'),
                    lte: $this->getParameter('priceTo'),
                )
            );
            $filters->must()->add(
                new \Spameri\ElasticQuery\Query\Term(
                    \App\WorkshopFourModule\Entity\Product::STICKERS,
                    $this->getParameter('sticker'),
                )
            );
        }


        if ($type === 'stickers') {
            $filters->must()->add(
                new \Spameri\ElasticQuery\Query\Term(
                    \App\WorkshopFourModule\Entity\Product::COLOR,
                    $this->getParameter('color'),
                )
            );
            $filters->must()->add(
                new \Spameri\ElasticQuery\Query\Range(
                    field: \App\WorkshopFourModule\Entity\Product::PRICE,
                    gte: $this->getParameter('priceFrom'),
                    lte: $this->getParameter('priceTo'),
                )
            );
        }

        return $filters;
    }

    private function createProducts(\Spameri\ElasticQuery\Response\ResultSearch $mappedResponse): array
    {
        $products = [];
        /** @var \Spameri\ElasticQuery\Response\Result\Hit $hit */

        foreach ($mappedResponse->hits() as $hit) {
            $products[] = new \App\WorkshopFourModule\Entity\Product(
                id: new \Spameri\Elastic\Entity\Property\ElasticId($hit->id()),
                title: $hit->getValue(\App\WorkshopFourModule\Entity\Product::TITLE),
                description: $hit->getValue(\App\WorkshopFourModule\Entity\Product::DESCRIPTION),
                price: $hit->getValue(\App\WorkshopFourModule\Entity\Product::PRICE),
                priceDiscount: $hit->getValue(\App\WorkshopFourModule\Entity\Product::PRICE_DISCOUNT),
                image: $hit->getValue(\App\WorkshopFourModule\Entity\Product::IMAGE),
                color: $hit->getValue(\App\WorkshopFourModule\Entity\Product::COLOR),
                size: $hit->getValue(\App\WorkshopFourModule\Entity\Product::SIZE),
                cut: $hit->getValue(\App\WorkshopFourModule\Entity\Product::CUT),
                material: $hit->getValue(\App\WorkshopFourModule\Entity\Product::MATERIAL),
                stickers: $hit->getValue(\App\WorkshopFourModule\Entity\Product::STICKERS),
                category: $hit->getValue(\App\WorkshopFourModule\Entity\Product::CATEGORY),
                published: new \DateTime(
                    $hit->getValue(\App\WorkshopFourModule\Entity\Product::PUBLISHED)
                ),
                stock: $hit->getValue(\App\WorkshopFourModule\Entity\Product::STOCK),
            );
        }

        return $products;
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
                ':WorkshopFour:Workshop:default',
                [
                    'queryString' => $form->getValues()->queryString,
                ]
            );
        };

        return $form;
    }

}
