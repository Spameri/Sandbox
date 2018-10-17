<?php declare(strict_types = 1);

namespace App\ProductModule\Presenter;


class ProductListPresenter extends \Nette\Application\UI\Presenter
{

	/**
	 * @var \App\ProductModule\Model\ProductService
	 */
	private $productService;


	public function __construct(
		\App\ProductModule\Model\ProductService $productService
	)
	{
		$this->productService = $productService;
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
	}

}
