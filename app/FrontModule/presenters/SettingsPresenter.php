<?php

class Front_SettingsPresenter extends FrontPresenter {

	public $id;

    public function renderDefault() {
		
	}	
	
	public function actionManageJurors() {
		$jurorManager = new JurorManager;
		$this->template->jurors = $jurorManager->findAll(array('role' => 'ASC', 'name' => 'ASC'));
	}
	
    protected function createComponentCreateJurorForm() {
		$form = new AppForm;

		$form->addText('name', 'Jméno');
		$form->addText('email', 'E-mail');
		$form->addText('number', 'UČO');
		
		$form->addSubmit('create', 'Přidat uživatele');

		$form->onSubmit[] = callback($this, 'createJurorFormSubmitted');
		return $form;
    }

    public function createJurorFormSubmitted($form) {
		$values = $form->getValues();
		
		$juror = new Juror($values);
		$jurorManager = new JurorManager;
		$jurorManager->create($juror);
		
		$this->flashMessage('Porotce byl přidán.');
		$this->redirect('this');
    }	
	
	public function actionManageCategories() {
		
	}
	
	public function actionCreatePassword($id) {
		$this->id = $id;
		
		$jurorManager = new JurorManager;
		$this->template->juror = $jurorManager->find($id);
	}
	
    protected function createComponentCreatePasswordForm() {
		$form = new AppForm;

		$form->addPassword('password', 'Vaše nové heslo')
			->addRule(Form::FILLED, 'Prosím, zadejte heslo');
		
		$form->addPassword('passwordCheck', 'Ověření hesla')
		->addRule(Form::EQUAL, 'Hesla musí být stejné', $form['password']);
		
		$form->addHidden('id')->setValue($this->id);
		$form->addSubmit('finish', 'Nastavit heslo');

		$form->onSubmit[] = callback($this, 'createPasswordFormSubmitted');
		return $form;
    }

    public function createPasswordFormSubmitted($form) {
		$values = $form->getValues();
		
		$jurorManager = new JurorManager;
		$juror = $jurorManager->find($values['id']);
		$juror->password = sha1($values['password']);
		$juror->active = 1;
		$juror->save();
		
		$this->flashMessage('Vaše heslo bylo úspěšně nastaveno. Pokračujte přihlášením.');
		$this->redirect('this');
    }
}