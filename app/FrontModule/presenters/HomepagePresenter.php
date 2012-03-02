<?php

class Front_HomepagePresenter extends FrontPresenter {

	public $id;

	public $roundID;
	public $projectID;

	public $round;
	public $project;


    public function actionDefault() {
		$roundManager = new RoundManager;
		$this->template->rounds = $roundManager->findAll(array('id' => 'DESC'));
	}
	
	public function createComponentEditRoundForm() {
		$form = new AppForm;

		$form->addText('name', 'Název projektového kola');
		$form->addSubmit('submit', 'Vložit projektové kolo');
		
		$form->onSubmit[] = callback($this, 'editRoundFormSubmitted');
		return $form;		
	}	
	
	public function editRoundFormSubmitted($form) {
		$values = $form->getValues();
		
		$round = new Round(array('createdDATE%sql' => 'NOW()', 'name%s' => $values['name']));
		$roundManager = new RoundManager;
		$roundManager->create($round);
		
		$this->flashMessage('Projektové kolo bylo vytvořeno.');
		$this->redirect('this');		
	}
	
	public function actionRound($id) {
		$roundManager = new RoundManager;
		$this->template->round = $this->round = $roundManager->find($id);
		$this->roundID = $id;
		
		$projectManager = new ProjectManager;
		$this->template->projects = $projectManager->findAll(array('name' => 'ASC'), array('roundID' => $this->roundID));		
	}
	
	public function renderRound() {		
		switch ($this->round->phase) {
			case 'deliberation':
				break;
				
			case 'results':
				break;
		}
	}
	
	public function createComponentEditProjectForm() {
		$form = new AppForm;

		$form->addText('name', 'Název projektu');
		$form->addText('applicant', 'Žadatel');
		$form->addFile('application', 'Žádost (PDF)');
		$form->addSubmit('submit', 'Uložit projekt');
		
		$form->onSubmit[] = callback($this, 'editProjectFormSubmitted');
		return $form;		
	}
	
	public function editProjectFormSubmitted($form) {
		$values = $form->getValues();
		
		$project = new Project(array('roundID' => $this->roundID, 
			'name' => $values['name'], 
			'applicant' => $values['applicant']));
		$projectManager = new ProjectManager;
		$projectManager->create($project);
		
		$id = dibi::insertId();
		$values['application']->move(WWW_DIR . '/files/application-' .  $id . '.pdf');
		
		$this->flashMessage('Projekt byl uložen.');
		$this->redirect('this');		
	}	
	
	public function actionProject($id) {
		$projectManager = new ProjectManager;
		$this->template->project = $this->project = $projectManager->find($id);
		$this->projectID = $id;
	
		$roundManager = new RoundManager;
		$this->template->round = $this->round = $roundManager->find($this->project->roundID);
		$this->roundID = $this->round->id;		
	}
	
	
	public function renderProject() {				
		switch ($this->round->phase) {
			case 'deliberation':
				break;
				
			case 'results':
				break;
	
		}
	}
	
	public function createComponentRateProjectForm() {
		$form = new AppForm;

		$options = array(
			'0' => '',
			'5' => ' ★ ★ ★ ★ ★ skvělý projekt',
			'4' => ' ★ ★ ★ ★ ',
			'3' => ' ★ ★ ★ ',
			'2' => ' ★ ★ ',
			'1' => ' ★ slabý projekt'
		);
		
 		$categories = $this->round->getRatingCategories();
		foreach ($categories as $id => $category) {
			$form->addSelect($id, $category->name, $options)->setOption('description', $category->description);
		}
		
		$form->addSubmit('submit', 'Uložit hodnocení');
		
		$form->onSubmit[] = callback($this, 'rateProjectFormSubmitted');
		return $form;
	}
	
	public function rateProjectFormSubmitted($form) {
		$values = $form->getValues();
		
		//save
		
		$this->flashMessage('Hodnocení bylo uloženo.');
		$this->redirect('this');		
	}	
	
    protected function createComponentNewsletterForm() {
	$form = new AppForm;
	$form->getElementPrototype()->addClass('newsletterForm');

	$form->addText('address', 'Váš e-mail')
		->addRule(Form::FILLED, 'Prosím, zadejte e-mailovou adresu')
		->addRule(Form::EMAIL, 'Prosím, zadejte platnou e-mailovou adresu.');

	$form->addSubmit('subscribe', 'OK');

	$form->onSubmit[] = callback($this, 'newsletterFormSubmitted');
	return $form;
    }

    public function newsletterFormSubmitted($form) {
	$values = $form->getValues();

	$subscriber = new NewsletterSubscriber(array('address' => $values['address']));
	$newsletterManager = new NewsletterManager;
	$newsletterManager->create($subscriber);

	$this->flashMessage('Váš newsletter byl úspěšně zaregistrován. Děkujeme!');
	$this->redirect('this');
    }
}