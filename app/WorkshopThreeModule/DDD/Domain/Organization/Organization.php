<?php declare(strict_types = 1);

namespace DDD\Domain\Organization;

class Organization
{

	public function __construct(
		public readonly int $id,
		public readonly string $name,
		public readonly string $package,
	) {}

}
