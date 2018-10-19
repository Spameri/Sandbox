<?php declare(strict_types = 1);

namespace App\ProductModule\Presenter;


class ProductListPresenter extends \App\Presenters\BasePresenter
{

	/**
	 * @var \App\ProductModule\Model\ProductService
	 */
	private $productService;
	/**
	 * @var \Spameri\Elastic\Model\Search
	 */
	private $search;


	public function __construct(
		\App\ProductModule\Model\ProductService $productService
		, \Spameri\Elastic\Model\Search $search
	)
	{
		$this->productService = $productService;
		$this->search = $search;
	}


	public function renderDefault(): void
	{
		$this->getTemplate()->add(
			'products',
			$this->productService->getAllBy(
				new \Spameri\ElasticQuery\ElasticQuery(
					new \Spameri\ElasticQuery\Query\QueryCollection(
						new \Spameri\ElasticQuery\Query\MustCollection(
							new \Spameri\ElasticQuery\Query\Term('isPublic', TRUE)
						)
					)
				)
			)
		);

		$this->getTemplate()->add(
			'search',
			$this->search->execute(
				new \Spameri\ElasticQuery\ElasticQuery(
					NULL,
					NULL,
					NULL,
					new \Spameri\ElasticQuery\Aggregation\AggregationCollection(
						NULL,

						new \Spameri\ElasticQuery\Aggregation\LeafAggregationCollection(
							'priceHistogram',
							NULL,
								new \Spameri\ElasticQuery\Aggregation\Histogram(
									'price',
									1000
								),
								new \Spameri\ElasticQuery\Aggregation\LeafAggregationCollection(
									'priceRange',
									NULL,
									new \Spameri\ElasticQuery\Aggregation\Range(
										'price',
										TRUE,
										new \Spameri\ElasticQuery\Aggregation\RangeValueCollection(
											new \Spameri\ElasticQuery\Aggregation\RangeValue('100-1000', 100, 1000),
											new \Spameri\ElasticQuery\Aggregation\RangeValue('1000-2000', 1000, 2000)
										)
									)
								)
						),

						new \Spameri\ElasticQuery\Aggregation\LeafAggregationCollection(
							'priceTerm',
							NULL,
							new \Spameri\ElasticQuery\Aggregation\Term(
								'price',
								5
							)
						)
					),
					0,
					0
				),
				'workshop_product'
			)
		);

	}

}
