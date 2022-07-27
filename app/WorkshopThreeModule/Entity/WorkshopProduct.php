<?php declare(strict_types=1);

namespace App\WorkshopThreeModule\Entity;

class WorkshopProduct extends \Spameri\Elastic\Entity\AbstractImport implements \Spameri\Elastic\Entity\ElasticEntityInterface
{

	public function __construct(
		public readonly \Spameri\Elastic\Entity\Property\ElasticIdInterface $id,
		public readonly int $databaseId,
		public readonly string $name,
		public readonly ?string $content,
		public readonly string $alias,
		public readonly string $image,
		public readonly float $price,
		public readonly string $availability,
		public readonly array $tags,
		public readonly array $categories,
		public readonly array $purpose,
		public readonly int $venality,
		public readonly string $brand,
	)
	{
		parent::__construct($id->value());
	}


	public function id(): \Spameri\Elastic\Entity\Property\ElasticIdInterface
	{
		return $this->id;
	}

	/**
	 * @return array<mixed>
	 */
	public function entityVariables(): array
	{
		$vars = \get_object_vars($this);
		unset($vars['key']);

		return $vars;
	}

}
