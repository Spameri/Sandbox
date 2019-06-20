<?php declare(strict_types = 1);

namespace App\ProductModule\Entity\Product;


class Price implements \Spameri\Elastic\Entity\IValue
{

	/**
	 * @var int
	 */
	private $value;


	public function __construct(
		int $value
	)
	{
		$this->value = $value;
	}


	public function value() : int
	{
		return $this->value;
	}

}
