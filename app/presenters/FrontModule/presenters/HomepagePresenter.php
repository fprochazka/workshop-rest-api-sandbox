<?php

namespace App\FrontModule\Presenters;

use Nette;


class HomepagePresenter extends Nette\Application\UI\Presenter
{

	protected function startup()
	{
		parent::startup();

		if ($this->session->exists()) {
			$this->session->start();
		}
	}



	public function renderDefault()
	{
	}

}
