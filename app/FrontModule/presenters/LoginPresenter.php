<?php

/**
 * My Application
 *
 * @copyright  Copyright (c) 2010 John Doe
 * @package    MyApplication
 */

/**
 * Login / logout presenters.
 *
 * @author     John Doe
 * @package    MyApplication
 */
class Front_LoginPresenter extends FrontPresenter {

    public function actionDefault($backlink = NULL) {
	if (Environment::getUser()->isLoggedIn()) $this->redirect('Homepage:default');

	$this->template->robots = 'noindex,noarchive';

	if (isset(Environment::getUser()->getIdentity()->data['login'])) {
	    $login = Environment::getUser()->getIdentity()->data['login'];
	    $this['loginForm']['username']->setValue($login);
	}
    }

    /**
     * Login form component factory.
     * @return mixed
     */
    protected function createComponentLoginForm() {
	$form = new AppForm;
	$form->addText('email', 'E-mail')
		->addRule(Form::FILLED, 'Please provide an e-mail.');

	$form->addPassword('password', 'Heslo')
		->addRule(Form::FILLED, 'Please provide a password.');

	//$form->addCheckbox('remember', 'Zapamatuj si mě')->value = TRUE;

	$form->addSubmit('login', 'Přihlásit se');

	$form->onSubmit[] = callback($this, 'loginFormSubmitted');
	return $form;
    }

    public function loginFormSubmitted($form) {
	$values = $form->values;

	//savana workaround
	$jurorManager = new JurorManager;
	if (!$jurorManager->findAll(NULL, array('[email] = "' . $values['email'] . '"',
	    '[password] = "' . md5($values['password']) . '"'))->fetch()) {
	    $form->addError('Nesprávny e-mail alebo heslo.');
	    return;
	}

	try {
	    $this->getUser()->setExpiration("+ 14 days", FALSE);	    
	    $this->getUser()->login($values['email'], $values['password']);
	} catch (AuthenticationException $e) {
	    $form->addError($e->getMessage());
	}

	if ($this->getParam('backlink')) {
	    $this->getApplication()->restoreRequest($this->getParam('backlink'));
	} else $this->redirect('Homepage:default');
    }

    public function actionLogout() {
	Environment::getUser()->logout(TRUE);

	$this->redirect('Homepage:default');
    }

}
