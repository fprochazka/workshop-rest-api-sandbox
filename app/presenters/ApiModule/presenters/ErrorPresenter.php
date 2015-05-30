<?php

/**
 * This file is part of the Kdyby (http://www.kdyby.org)
 * Copyright (c) 2008 Filip Procházka (filip@prochazka.su)
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace App\ApiModule\Presenters;

use Kdyby;
use Nette;
use Tracy\Debugger;



/**
 * @author Filip Procházka <filip@prochazka.su>
 */
class ErrorPresenter extends BasePresenter
{

	/**
	 * @param \Exception $exception
	 */
	public function actionDefault($exception)
	{
		if ($exception instanceof Nette\Application\BadRequestException) {
			$this->payload->error = [
				'message' => $exception->getMessage()
			];

		} else {
			Debugger::log($exception, Debugger::ERROR); // and log exception

			$this->payload->error = [
				// 'code' => Presenter::ERR_INTERNAL_ERROR,
				// 'type' => Presenter::$errorsTypes[Presenter::ERR_INTERNAL_ERROR],
				'message' => Debugger::$productionMode ? 'Internal Server Error' : $exception->getMessage()
			];
		}
	}



	/**
	 * @param \Exception $exception
	 */
	public function renderDefault($exception)
	{
		$this->payload->status = 'error';
		$this->sendPayload();
	}
}
