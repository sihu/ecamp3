<?php
/**
 *
 * Copyright (C) 2011 pirminmattmann
 *
 * This file is part of eCamp.
 *
 * eCamp is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * eCamp is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with eCamp.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
 
use CoreApi\Service\Params\Params;

class WebApp_RegisterController
	extends \WebApp\Controller\BaseController
{


	/**
	 * @var CoreApi\Service\UserService
	 * @Inject CoreApi\Service\UserService
	 */
	private $userService;
	
	
	/**
	 * @var CoreApi\Service\RegisterService
	 * @Inject CoreApi\Service\RegisterService
	 */
	private $registerService;
	
	
	

	public function indexAction()
	{		
		$registerForm = new \WebApp\Form\Register();

		if($id = $this->getRequest()->getParam('id'))
		{
			/** @var $user \CoreApi\Entity\User */
			$user = $this->userService->get($id);

			if(!is_null($user) && $user->getState() == \CoreApi\Entity\User::STATE_NONREGISTERED)
			{
				$registerForm->setDefault('email', 		$user->getEmail());
				$registerForm->setDefault('scoutname', 	$user->getScoutname());
				$registerForm->setDefault('firstname', 	$user->getFirstname());
				$registerForm->setDefault('surname', 	$user->getSurname());
			}
		}

		$registerForm->setDefaults($this->getRequest()->getParams());

		$this->view->registerForm = $registerForm;
	}


	public function registerAction()
	{
		$params = $this->getRequest()->getParams();
		
		$registerForm = new \WebApp\Form\Register();
		$registerForm->populate($params);

		
		try
		{
			$user = $this->registerService->Register(Params::Create($registerForm));
			
			$link = "/register/activate/" . $user->getId() . "/key/" . $user->getActivationCode();
			echo "<a href='" . $link . "'>" . $link . "</a>";
			die();
		}
		catch (\Core\Service\ValidationException $e)
		{
			$this->view->registerForm = $registerForm;
			$this->render("index");
			return;
		}
	}


	public function activateAction()
	{
		$id = $this->getRequest()->getParam('id');
		$key = $this->getRequest()->getParam('key');


		if($this->registerService->Activate($id, $key))
		{
			//$this->forward('web+general', 'index', 'login');
			$this->_redirect('/');
		}
		else
		{
			/** @var $user \Entity\User */
			$user = $this->$userService->Get($id);

			$ac = $user->createNewActivationCode();
			\Zend_Registry::get('doctrine')->getEntityManager()->flush();
			die($ac);
		}

	}

}
