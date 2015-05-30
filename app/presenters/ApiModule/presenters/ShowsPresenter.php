<?php

/**
 * This file is part of the Kdyby (http://www.kdyby.org)
 * Copyright (c) 2008 Filip Procházka (filip@prochazka.su)
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace App\ApiModule\Presenters;

use Nette;



/**
 * @author Filip Procházka <filip@prochazka.su>
 */
class ShowsPresenter extends BasePresenter
{

	protected function startup()
	{
		parent::startup();

		if (!$this->user->isLoggedIn()) {
			$this->error("Unauthorized", Nette\Http\IResponse::S401_UNAUTHORIZED);
		}
	}



	public function actionReadAll()
	{
		$this->success();
	}



	public function actionRead($id)
	{
		$this->payload->show = ['id' => $id, 'name' => 'Agents of Shield'];
		$this->success();
	}



	public function actionCreate()
	{
		$this->payload->pingBack = $this->request->getPost();
		$this->success();
	}

}
