<?php declare(strict_types = 1);

namespace App\WorkshopThreeModule\Presenter;

class WorkshopPresenter extends \App\Presenter\BasePresenter
{

	public function __construct(
		private readonly \App\WorkshopThreeModule\Model\WorkshopProductService $workshopProductService
	)
	{
	}


	public function renderDefault($queryString)
	{
		$elasticQuery = $this->buildQuery($queryString);

		$products = $this->workshopProductService->getAllBy($elasticQuery);
		$result = $this->workshopProductService->aggregate($elasticQuery);

		$this->getTemplate()->add(
			'products',
			$products
		);
		$this->getTemplate()->add(
			'aggregations',
			$result
		);

		$this->getTemplate()->add(
			'queryString',
			$queryString
		);
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
				':WorkshopThree:Workshop:default',
				[
					'queryString' => $form->getValues()->queryString,
				]
			);
		};

		return $form;
	}


	private function buildQuery($queryString): \Spameri\ElasticQuery\ElasticQuery
	{
		$elasticQuery = new \Spameri\ElasticQuery\ElasticQuery();

		$elasticQuery->addMustQuery(
			new \Spameri\ElasticQuery\Query\QueryCollection(
				shouldCollection: new \Spameri\ElasticQuery\Query\ShouldCollection(
					new \Spameri\ElasticQuery\Query\MultiMatch(
						[
							'name.dictionary',
							'name.edgeNgram',
							'name.wordJoin',
							'name.wordSplit',
							'name.synonym',
						],
						$queryString,
						3,
						new \Spameri\ElasticQuery\Query\Match\Fuzziness('AUTO'),
						\Spameri\ElasticQuery\Query\Match\MultiMatchType::BEST_FIELDS,
						operator: 'OR',
						analyzer:  'czechDictionary'
					),
					new \Spameri\ElasticQuery\Query\ElasticMatch(
						'content',
						$queryString,
						analyzer: 'czechDictionary'
					)
				)
			)
		);

		$elasticQuery->options()->sort()->add(
			new \Spameri\ElasticQuery\Options\Sort(
				'availability'
			)
		);

		$elasticQuery->options()->changeSize(100);

		$elasticQuery->addAggregation(
			new \Spameri\ElasticQuery\Aggregation\LeafAggregationCollection(
				'price',
				NULL,
				new \Spameri\ElasticQuery\Aggregation\Range(
					'price',
					TRUE,
					new \Spameri\ElasticQuery\Aggregation\RangeValueCollection(
						new \Spameri\ElasticQuery\Aggregation\RangeValue(
							'0 - 50 Kč',
							0,
							50
						),
						new \Spameri\ElasticQuery\Aggregation\RangeValue('50 - 100 Kč', 50, 100),
						new \Spameri\ElasticQuery\Aggregation\RangeValue('100 - 200 Kč', 100, 200),
						new \Spameri\ElasticQuery\Aggregation\RangeValue('200 - 500 Kč', 200, 500),
						new \Spameri\ElasticQuery\Aggregation\RangeValue('500 - 1000 Kč', 500, 1000)
					)
				)
			)
		);


		$elasticQuery->addAggregation(
			new \Spameri\ElasticQuery\Aggregation\LeafAggregationCollection(
				'categories',
				NULL,
				new \Spameri\ElasticQuery\Aggregation\Term(
					'categories',
					100
				)
			)
		);

		$elasticQuery->addAggregation(
			new \Spameri\ElasticQuery\Aggregation\LeafAggregationCollection(
				'brands',
				NULL,
				new \Spameri\ElasticQuery\Aggregation\Term(
					'brand',
					1000
				)
			)
		);


		$priceFrom = $this->getParameter('priceFrom');
		$priceTo = $this->getParameter('priceTo');
		if ($priceFrom !== NULL) {
			$elasticQuery->addMustQuery(
				new \Spameri\ElasticQuery\Query\Range(
					'price',
					$priceFrom,
					$priceTo,
				)
			);
		}

		return $elasticQuery;
	}

}
