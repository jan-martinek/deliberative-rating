<?php

class Front_SettingsPresenter extends FrontPresenter {

	public $id;

    public function renderDefault() {
		
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
		
		
		$jurorManager = new JurorManager;
		$juror = $jurorManager->find($this->id);
		$form->addHidden('id')->setValue(md5($juror->id . 'x'));		

		$form->addSubmit('finish', 'Dokončit registraci');

		$form->onSubmit[] = callback($this, 'createPasswordFormSubmitted');
		return $form;
    }

    public function createPasswordFormSubmitted($form) {
		$values = $form->getValues();

		dibi::query('UPDATE jurors SET password = %s', md5($values['password']), 'WHERE id = %i', $i);

		$this->flashMessage('Vaše heslo bylo úspěšně nastaveno. Pokračujte přihlášením.');
		$this->redirect('this');
    }
}