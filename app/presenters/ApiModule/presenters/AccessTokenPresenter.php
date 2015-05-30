<?php

/**
 * This file is part of the Kdyby (http://www.kdyby.org)
 * Copyright (c) 2008 Filip Procházka (filip@prochazka.su)
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace App\ApiModule\Presenters;

use Nette;
use Nette\Http\IResponse;



/**
 * @author Filip Procházka <filip@prochazka.su>
 */
class AccessTokenPresenter extends BasePresenter
{

	/**
	 * Disclaimer: it's not my fault if you're dumb.
	 * I'm lazy to save it in database.
	 */
	const PLACEHOLDER_API_ACCESS_TOKEN_DO_NOT_USE_ON_PRODUCTION = 'abcd';



	public function actionCreate()
	{
		if (!$username = $this->request->getPost('username')) {
			$this->error("Missing field 'username'", IResponse::S400_BAD_REQUEST);
		}

		if (!$password = $this->request->getPost('password')) {
			$this->error("Missing field 'password'", IResponse::S400_BAD_REQUEST);
		}

		try {
			$this->user->login($username, $password);

			$this->payload->user = [
				'username' => $this->user->id,
			];
			$this->payload->access_token = [
				'token' => self::PLACEHOLDER_API_ACCESS_TOKEN_DO_NOT_USE_ON_PRODUCTION,
				'expiration' => (new \DateTime('+1 day'))->format('Y-m-d H:i:s')
			];
			$this->success();

		} catch (Nette\Security\AuthenticationException $e) {
			$this->error("Invalid credentials", IResponse::S401_UNAUTHORIZED);
		}
	}

}
