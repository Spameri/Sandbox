<?php declare(strict_types = 1);

namespace App\ProductModule\Presenter;

class SimpleProductListPresenter extends \App\Presenter\BasePresenter
{

	/**
	 * @var \App\ProductModule\Model\SimpleProductService
	 */
	private $productService;


	public function __construct(
		\App\ProductModule\Model\SimpleProductService $productService
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

		$this->getTemplate()->add(
			'products',
			$products
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
			->setAttribute('class', 'inp-text suggest');

		$form->addSubmit('search', 'Search');

		$form->onSuccess[] = function () use ($form) {
			$this->redirect(
				301,
				':Product:SimpleProductList:default',
				[
					'queryString' => $form->getValues()->queryString,
				]
			);
		};

		return $form;
	}


	public function buildQuery(string $queryString): \Spameri\ElasticQuery\ElasticQuery
	{
		$query = new \Spameri\ElasticQuery\ElasticQuery();
		$subQuery = new \Spameri\ElasticQuery\Query\QueryCollection();
		$subQuery->addShouldQuery(
			new \Spameri\ElasticQuery\Query\Match(
				'name',
				$queryString,
				3,
				\Spameri\ElasticQuery\Query\Match\Operator:: OR,
				new \Spameri\ElasticQuery\Query\Match\Fuzziness(\Spameri\ElasticQuery\Query\Match\Fuzziness::AUTO)
			)
		);
		$subQuery->addShouldQuery(
			new \Spameri\ElasticQuery\Query\WildCard(
				'name',
				$queryString . '*',
				2
			)
		);
		$subQuery->addShouldQuery(
			new \Spameri\ElasticQuery\Query\MatchPhrase(
				'name',
				$queryString,
				2
			)
		);
		$subQuery->addShouldQuery(
			new \Spameri\ElasticQuery\Query\Match(
				'content',
				$queryString,
				1,
				\Spameri\ElasticQuery\Query\Match\Operator:: OR,
				new \Spameri\ElasticQuery\Query\Match\Fuzziness(\Spameri\ElasticQuery\Query\Match\Fuzziness::AUTO)
			)
		);

		$query->addMustQuery($subQuery);
		$query->addShouldQuery(
			new \Spameri\ElasticQuery\Query\Match(
				'availability',
				'Skladem',
				10
			)
		);
		$query->options()->changeSize(20);

		return $query;
	}

}
