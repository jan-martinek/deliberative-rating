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

	public function createComponentSwitchPhaseForm() {
		$form = new AppForm;

		$round = new Round(array());
		$form->addSelect('phase', 'Fáze', $round->getPhases());
		$form->addHidden('id');
		$form->addSubmit('submit', 'Přepnout fázi projektového kola');
		
		$form->onSubmit[] = callback($this, 'switchPhaseFormSubmitted');
		return $form;		
	}	
	
	public function switchPhaseFormSubmitted($form) {
		$values = $form->getValues();
		
		$roundManager = new RoundManager;
		$round = $roundManager->find($values['id']);
		$round->setPhase($values['phase'], Environment::getUser()->getId());
		
		$this->flashMessage('Fáze projektového kola byla změněna.');
		$this->redirect('this');		
	}	
	
	public function actionRound($id) {
		$roundManager = new RoundManager;
		$this->template->round = $this->round = $roundManager->find($id);
		$this->roundID = $id;
		
		$projectManager = new ProjectManager;
		
		switch ($this->round->phase) {
			
			case 'deliberation':
				$sort = array('firstRatingIneligibilityBool' => 'ASC', 'firstRating' => 'DESC');			
				break;
			case 'results':
				$sort = array('secondRatingIneligibilityBool' => 'ASC', 'avgRating' => 'DESC');
				break;
			case 'preparation':
			case 'firstRating':
			case 'secondRating':
			default:
				$sort = array('name' => 'ASC');
				break;
		}
		
		$this->template->projects = $projectManager->findAllWithAverages($sort, array('roundID' => $this->roundID));
		
		switch ($this->round->phase) {
			case 'firstRating':
			case 'secondRating':
				$this->template->ratingCounts = $this->round->getProjectsRatingCounts($this->round->phase);
				break;

		}
				
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
		$form->addText('applicationLink', 'Odkaz na žádost (PDF)');
		$form->addTextArea('otherLinks', 'Odkazy na další dokumenty (PDF)')
			->setOption('description', 'Vkládejte každý odkaz na samostatný řádek.');
		$form->addSubmit('submit', 'Uložit projekt');
		
		$form->onSubmit[] = callback($this, 'editProjectFormSubmitted');
		return $form;		
	}
	
	public function editProjectFormSubmitted($form) {
		$values = $form->getValues();
		
		$project = new Project($values);
		$project->roundID = $this->roundID;
		
		$projectManager = new ProjectManager;
		$projectManager->create($project);
		
		//usable only if files could be stored locally
		//$id = dibi::insertId();
		//$values['application']->move(WWW_DIR . '/files/application-' .  $id . '.pdf');
		
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
				$this->template->ratings = $this->project->getRatingsByCategory('firstRating');
				$this->template->eligibility = $this->project->getEligibility('firstRating');
				$this->template->categories = $this->round->getRatingCategories();
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
		
		$form->addCheckBox('notEligible', 'Projekt není vhodné podpořit')
			->addCondition(Form::EQUAL, 1)->toggle('eligibility');		
		$form->addTextArea('notEligibleReasons', 'Zdůvodnění nevhodnosti k podpoře', 20, 4);
		
		$form->addSubmit('submit', 'Uložit hodnocení');
		
		$form->onSubmit[] = callback($this, 'rateProjectFormSubmitted');
		return $form;
	}
	
	public function rateProjectFormSubmitted($form) {
		$values = $form->getValues();
		
		$jurorID = Environment::getUser()->getId();
		
		$rating = array();
		foreach ($values as $id => $value) {
			if ($id > 0 AND $value >= 0 AND $value <= 5) {
				$rating[$id] = $value;
			}
		}
		$this->project->rate($rating, $jurorID);
		
		$this->project->setEligibility($values['notEligible'], $values['notEligibleReasons'], $jurorID);		
		
		$this->flashMessage('Hodnocení bylo uloženo.');
		$this->redirect('this');		
	}	
}