<?php declare(strict_types = 1);

namespace App\ProductModule\Presenter;

class WorkshopPresenter extends \App\Presenter\BasePresenter
{

	/**
	 * @var \App\ProductModule\Model\WorkshopProductService
	 */
	private $productService;


	public function __construct(
		\App\ProductModule\Model\WorkshopProductService $productService
	)
	{
		parent::__construct();
		$this->productService = $productService;
	}


	public function renderDefault($queryString): void
	{
		$query = $this->buildQuery($queryString);

		try {
			$products = $this->productService->getAllBy($query);

		} catch (\Spameri\Elastic\Exception\ElasticSearchException $exception) {
			$products = [];
		}

		$parameterArray = [];
		foreach ($products->aggregations()->getAggregation('parameter_value_ids')->buckets() as $bucket) {
			[$parameterId, $parameterValue] = \explode('_', $bucket->key());
			$parameterArray[$parameterId][$parameterValue]['value'] = $parameterValue;
		}

		// Iterate over $_GET parameters
		$elasticQuery = new \Spameri\ElasticQuery\ElasticQuery();

		foreach (['p1'] as $parameterId) {
			$filterFilledByP2 = new \Spameri\ElasticQuery\Filter\FilterCollection();
			$elasticQuery->addAggregation(
				new \Spameri\ElasticQuery\Aggregation\LeafAggregationCollection(
					'parameter_' . $parameterId,
					$filterFilledByP2,
					new \Spameri\ElasticQuery\Aggregation\Term(
						'parameters.uid'
					)
				)
			);
		}

		$activeParameters = [];
		$filterOptions = new \App\ProductModule\Entity\FilterOptions($parameterArray, $activeParameters);


		$response = $this->productService->getAllBy($elasticQuery);
		/** @var \Spameri\ElasticQuery\Response\Result\Aggregation $aggregation */
		foreach ($response->aggregations() as $aggregation) {
			/** @var \Spameri\ElasticQuery\Response\Result\Aggregation\Bucket $bucket */
			foreach ($aggregation->buckets() as $bucket) {
				\Tracy\Debugger::barDump($bucket->key());
				[$parameterId, $parameterValue] = \explode('_', $bucket->key());
				$filterOptions->setCount($parameterId, $parameterValue, $bucket->docCount());
			}
		}

		\Tracy\Debugger::barDump($filterOptions->getValues());




		$this->getTemplate()->add(
			'products',
			$products
		);
		$this->getTemplate()->add(
			'filterOptions',
			$filterOptions
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
				301,
				':Product:Workshop:default',
				[
					'queryString' => $form->getValues()->queryString,
				]
			);
		};

		return $form;
	}


	public function buildQuery(?string $queryString): \Spameri\ElasticQuery\ElasticQuery
	{
		$query = new \Spameri\ElasticQuery\ElasticQuery();
		$shouldCollection = new \Spameri\ElasticQuery\Query\ShouldCollection();
		$mustCollection = new \Spameri\ElasticQuery\Query\MustCollection();
		$mustNotCollection = new \Spameri\ElasticQuery\Query\MustNotCollection();

		$mustCollection->add(
			new \Spameri\ElasticQuery\Query\MultiMatch(
				[
					'name.czechDictionary',
					'name.edgeNgram',
					'name.wordSplit',
					'name.wordJoin',
					'name.czechSynonym',
				],
				$queryString,
				3,
				\Spameri\ElasticQuery\Query\Match\MultiMatchType::BEST_FIELDS,
				\Spameri\ElasticQuery\Query\Match\Operator::OR,
				new \Spameri\ElasticQuery\Query\Match\Fuzziness(
					\Spameri\ElasticQuery\Query\Match\Fuzziness::AUTO
				),
				'czechDictionary'
			)
		);

		// ===============


		$query->addMustQuery(
			new \Spameri\ElasticQuery\Query\QueryCollection(
				$mustCollection,
				$shouldCollection,
				$mustNotCollection
			)
		);

		$query->options()->changeSize(20);


		// =========== AGGS

		$query->addAggregation(
			new \Spameri\ElasticQuery\Aggregation\LeafAggregationCollection(
				'parameter_ids',
				NULL,
				new \Spameri\ElasticQuery\Aggregation\Term(
					'parameters.parameter.id',
					10000
				)
			)
		);

		$query->addAggregation(
			new \Spameri\ElasticQuery\Aggregation\LeafAggregationCollection(
				'parameter_value_ids',
				NULL,
				new \Spameri\ElasticQuery\Aggregation\Term(
					'parameters.uid',
					10000
				)
			)
		);

		return $query;
	}

}
