<?php declare(strict_types = 1);

namespace App\ProductModule\Factory;


class ProductFactory implements \Spameri\Elastic\Factory\IEntityFactory
{

	/**
	 * @var \Spameri\Elastic\Model\ServiceLocator
	 */
	private $serviceLocator;


	public function __construct(
		\Spameri\Elastic\Model\ServiceLocator $serviceLocator
	)
	{
		$this->serviceLocator = $serviceLocator;
	}


	public function create(\Spameri\Elastic\Entity\Collection\ResultCollection $collection) : \Generator
	{
		$productService = $this->serviceLocator->locateByEntityClass(\App\ProductModule\Entity\Product::class);

		foreach ($collection->rows() as $row) {
			yield new \App\ProductModule\Entity\Product(
				new \Spameri\Elastic\Entity\Property\ElasticId($row['id']),
				new \App\ProductModule\Entity\Product\IsPublic($row['isPublic']),
				new \App\ProductModule\Entity\Product\Name($row['name']),
				new \App\ProductModule\Entity\Product\Content($row['content']),
				new \App\ProductModule\Entity\Product\Details(
					new \App\ProductModule\Entity\Product\Details\TagCollection( ... $this->createTags($row['details']['tags'])),
					new \App\ProductModule\Entity\Product\Details\Accessories(
						$productService,
						$row['details']['accessories'] ?? NULL
					)
				),
				new \App\ProductModule\Entity\Product\Price($row['price']),
				new \App\ProductModule\Entity\Product\ParameterValuesCollection()
			);
		}
	}


	protected function createTags(
		array $tags
	): \Generator
	{
		foreach ($tags as $tag) {
			yield new \App\ProductModule\Entity\Product\Details\Tag($tag);
		}
	}

}
