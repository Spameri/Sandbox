<?php

namespace App\Presenter;


final class HomepagePresenter extends \App\Presenter\BasePresenter
{
	public function renderDefault()
	{
		$this->template->anyVariable = 'any value';
	}
}
