<?php declare(strict_types = 1);

namespace DDD\Infrastructure\Addon;

class OrganizationFormRepository implements \DDD\Domain\Organization\OrganizationRepository
{

	public function getByAddonId(int $id): \DDD\Domain\Organization\Organization
	{
		$data = $this->netteFormData;

		return new \DDD\Domain\Organization\Organization(
			$data['id'],
			$data['name'],
			$data['package'],
		);
	}

}
