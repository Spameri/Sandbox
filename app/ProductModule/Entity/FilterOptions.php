<?php declare(strict_types=1);

namespace App\ProductModule\Entity;

class FilterOptions
{

	/**
	 * @var array
	 */
	private $values;

	/**
	 * @var array
	 */
	private $activeValues;


	public function __construct(
		array $values,
		array $activeValues
	)
	{
		$this->values = $values;
		$this->activeValues = $activeValues;
	}


	public function applyValue($parameterId, $parameterValue): array
	{
		$values = $this->activeValues;

		if (isset($values[$parameterId][$parameterValue])) {
			unset($values[$parameterId][$parameterValue]);

		} else {
			$values[$parameterId][$parameterValue] = $parameterValue;
		}

		return $values;
	}


	public function getValues(): array
	{
		return $this->values;
	}


	public function getActiveValues(): array
	{
		return $this->activeValues;
	}


	public function isActive($parameterId, $parameterValue): bool
	{
		return isset($this->activeValues[$parameterId][$parameterValue]);
	}


	public function getActiveCount($parameterId, $parameterValue)
	{
		return $this->values[$parameterId][$parameterValue]['count'] ?? 0;
	}


	public function setCount($parameterId, $parameterValue, $count)
	{
		$this->values[$parameterId][$parameterValue]['count'] = $count;
	}

}
