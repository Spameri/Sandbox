<?php declare(strict_types = 1);

namespace DDD\Domain\Addon;

class Addon
{

	public function __construct(
		public readonly int $id,
		public readonly \DDD\Domain\Organization\Organization $organization,
	) {}

}
