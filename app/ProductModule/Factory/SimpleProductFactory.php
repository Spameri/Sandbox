<?php declare(strict_types = 1);

namespace App\ProductModule\Factory;


class SimpleProductFactory implements \Spameri\Elastic\Factory\IEntityFactory
{

	public function create(\Spameri\ElasticQuery\Response\Result\Hit $hit) : \Generator
	{
		yield new \App\ProductModule\Entity\SimpleProduct(
			new \Spameri\Elastic\Entity\Property\ElasticId($hit->id()),
			$hit->getValue('databaseId'),
			$hit->getValue('name'),
			$hit->getValue('content'),
			$hit->getValue('alias'),
			$hit->getValue('image'),
			$hit->getValue('price'),
			$hit->getValue('availability'),
			$hit->getValue('tags'),
			$hit->getValue('categories'),
			$hit->getValue('purpose'),
			$hit->getValue('venality'),
			$hit->getValue('brand')
		);
	}

}
