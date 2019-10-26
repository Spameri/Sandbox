<?php declare(strict_types = 1);

namespace App\ProductModule\Model\ExportToElastic;

class DataProvider implements \Spameri\Elastic\Import\DataProviderInterface
{

	/**
	 * @var \Dibi\Connection
	 */
	private $connection;


	public function __construct(
		\Dibi\Connection $connection
	)
	{
		$this->connection = $connection;
	}


	public function provide(\Spameri\Elastic\Import\Run\Options $options): \Generator
	{
		$hasResults = TRUE;

		$query = $this->connection
			->select(
				'product.id, product_text.name, product_text.content_description, main_alias.alias, main_library.library_id, '
				. 'product_item_price.amount, product_item_text.availability_id, product_item_text.isNew, product_item_text.isFreeTransport, '
				. 'product_item_text.isAction'
			)
		;
		$query->from('product');
		$query->join('product_text')->on('product.id = product_text.product_id');
		$query->join('main_library')->on('product.id = main_library.main_id')->and('main_library.sort < 2');
		$query->join('main_alias')->on('product.id = main_alias.main_id');
		$query->join('product_item')->on('product.id = product_item.product_id');
		$query->join('product_item_text')->on('product_item.id = product_item_text.item_id');
		$query->join('product_item_price')->on('product_item.id = product_item_price.item_id')
			->and('product_item_price.level_id = 1')
		;

		$limit = 100;
		$offset = 0;
		while ($hasResults) {
			$items = $query->fetchAll($offset, $limit);

			yield from $items;

			if ( ! \count($items)) {
				$hasResults = FALSE;

			} else {
				$offset += $limit;
			}
		}
	}


	public function count(\Spameri\Elastic\Import\Run\Options $options): int
	{
		return $this->connection
			->select('id')
			->from('product')
			->count()
		;
	}

}
