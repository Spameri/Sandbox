<?php declare(strict_types = 1);

namespace DDD\Infrastructure\Addon;

class OrganizationNodeRepository implements \DDD\Domain\Organization\OrganizationRepository
{

	public function getByAddonId(int $id): \DDD\Domain\Organization\Organization
	{
		$data = $this->nodeMagicApi->fetchbyAddonId($id);

		return new \DDD\Domain\Organization\Organization(
			$data['id'],
			$data['name'],
			$data['package'],
		);
	}

}
