<?php declare(strict_types = 1);

namespace App\ProductModule\Entity;

class SimpleProduct extends \Spameri\Elastic\Entity\AbstractImport implements \Spameri\Elastic\Entity\IElasticEntity
{

	/**
	 * @var \Spameri\Elastic\Entity\Property\IElasticId
	 */
	private $id;

	/**
	 * @var int
	 */
	private $databaseId;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string|null
	 */
	private $content;

	/**
	 * @var string
	 */
	private $alias;

	/**
	 * @var string
	 */
	private $image;

	/**
	 * @var float
	 */
	private $price;

	/**
	 * @var string
	 */
	private $availability;

	/**
	 * @var array<string>
	 */
	private $tags;

	/**
	 * @var array<string>
	 */
	private $categories;


	public function __construct(
		\Spameri\Elastic\Entity\Property\IElasticId $id,
		int $databaseId,
		string $name,
		?string $content,
		string $alias,
		string $image,
		float $price,
		string $availability,
		array $tags,
		array $categories
	)
	{
		$this->id = $id;
		$this->databaseId = $databaseId;
		$this->name = $name;
		$this->content = $content;
		$this->alias = $alias;
		$this->image = $image;
		$this->price = $price;
		$this->availability = $availability;
		$this->tags = $tags;
		$this->categories = $categories;
	}


	public function id(): \Spameri\Elastic\Entity\Property\IElasticId
	{
		return $this->id;
	}


	public function entityVariables(): array
	{
		return \get_object_vars($this);
	}


	public function getId(): \Spameri\Elastic\Entity\Property\IElasticId
	{
		return $this->id;
	}


	public function getDatabaseId(): int
	{
		return $this->databaseId;
	}


	public function getName(): string
	{
		return $this->name;
	}


	public function getContent(): ?string
	{
		return $this->content;
	}


	public function getAlias(): string
	{
		return $this->alias;
	}


	public function getImage(): string
	{
		return $this->image;
	}


	public function getPrice(): float
	{
		return $this->price;
	}


	public function getAvailability(): string
	{
		return $this->availability;
	}


	public function getTags(): array
	{
		return $this->tags;
	}


	public function getCategories(): array
	{
		return $this->categories;
	}

}
