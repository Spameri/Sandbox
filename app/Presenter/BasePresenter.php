<?php

namespace App\Presenters;

use Nette;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{

	/**
	 * @return Nette\Application\UI\ITemplate|Nette\Bridges\ApplicationLatte\Template
	 */
	public function getTemplate()
	{
		return parent::getTemplate();
	}

}
