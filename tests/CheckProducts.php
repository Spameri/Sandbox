<?php declare(strict_types = 1);

namespace Tests;

require_once __DIR__ . '/../vendor/autoload.php';

class CheckProducts
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


	public function check(): void
	{
		foreach (\Tests\ExpectedResults::SEARCH_QUERIES as $queryString => $expectedProducts) {
			$query = new \Spameri\ElasticQuery\ElasticQuery();
			$subQuery = new \Spameri\ElasticQuery\Query\QueryCollection();
			$subQuery->addShouldQuery(
				new \Spameri\ElasticQuery\Query\MultiMatch(
					[
						'name.czechDictionary',
						'name.edgeNgram',
						'name.wordSplit',
						'name.wordJoin',
					],
					$queryString,
					3,
					\Spameri\ElasticQuery\Query\Match\MultiMatchType::BEST_FIELDS,
					\Spameri\ElasticQuery\Query\Match\Operator::OR,
					new \Spameri\ElasticQuery\Query\Match\Fuzziness(\Spameri\ElasticQuery\Query\Match\Fuzziness::AUTO)
				)
			);
			$subQuery->addShouldQuery(
				new \Spameri\ElasticQuery\Query\Match(
					'content',
					$queryString,
					1,
					\Spameri\ElasticQuery\Query\Match\Operator::OR,
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
			$query->options()->changeSize(4);

			try {
				$products = $this->productService->getAllBy($query);
				$expected = \array_flip($expectedProducts);
				/** @var \App\ProductModule\Entity\SimpleProduct $product */
				foreach ($products as $product) {
					if (isset($expected[$product->getDatabaseId()])) {
						unset($expected[$product->getDatabaseId()]);
						echo "For query `\033[0;33m" . $queryString . "\033[0m`"
							 . " was found `\033[0;32m" . $product->getDatabaseId() . ':' . $product->getName() . "\033[0m`";
						echo "\n";

					} elseif (\count($expected) === 0) {
						break;

					} else {
 						echo "For query `\033[0;33m" . $queryString . "\033[0m` was expected product with id " . \key($expected)
							 . " but found `\033[0;31m" . $product->getDatabaseId() . ':' . $product->getName() . "\033[0m`";
 						echo "\n\n";
					}
				}

			} catch (\Spameri\Elastic\Exception\ElasticSearchException $exception) {

			}
		}
	}

}

$clientProvider = new \Spameri\Elastic\ClientProvider(
	new \Elasticsearch\ClientBuilder(),
	new \Spameri\Elastic\Settings\NeonSettingsProvider('localhost', 9200)
);
$resultMapper = new \Spameri\ElasticQuery\Response\ResultMapper();

(
	new CheckProducts(
		new \App\ProductModule\Model\ProductService(
			'spameri_simple_product',
			new \App\ProductModule\Factory\SimpleProductFactory(),
			new \App\ProductModule\Factory\SimpleProductCollectionFactory(),
			$clientProvider,
			new \Spameri\Elastic\Model\Insert(
				new \Spameri\Elastic\Model\Insert\PrepareEntityArray(
					new \Spameri\Elastic\Model\Insert\ApplyTimestamp(
						new \Spameri\Elastic\Model\NetteUserProvider(
							new \Nette\Security\User(
								new \Nette\Http\UserStorage(
									new \Nette\Http\Session(
										new \Nette\Http\Request(
											new \Nette\Http\UrlScript()
										),
										new \Nette\Http\Response()
									)
								)
							)
						),
						new \Spameri\Elastic\Provider\DateTimeProvider(new \DateTimeImmutable())
					),
					new \Spameri\Elastic\Model\ServiceLocator(new \Nette\DI\Container())
				),
				$clientProvider
			),
			new \Spameri\Elastic\Model\Get(
				$clientProvider,
				$resultMapper
			),
			new \Spameri\Elastic\Model\GetBy(
				$clientProvider,
				$resultMapper
			),
			new \Spameri\Elastic\Model\GetAllBy(
				$clientProvider,
				$resultMapper
			),
			new \Spameri\Elastic\Model\Delete(
				$clientProvider
			),
			new \Spameri\Elastic\Model\Aggregate(
				$clientProvider,
				$resultMapper
			)
		)
	)
)->check();
