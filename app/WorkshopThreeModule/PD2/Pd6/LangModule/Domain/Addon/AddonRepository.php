<?php declare(strict_types = 1);

namespace DDD\Domain\Organization;

interface AddonRepository
{

	public function availableAddonByTier(int $tier): \DDD\Domain\Addon\Addon;

}
