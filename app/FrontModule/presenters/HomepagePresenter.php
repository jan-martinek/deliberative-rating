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

		$jurorManager = new JurorManager;
		$this->template->jurors = $jurorManager->findAll(array('role' => 'ASC', 'name' => 'ASC'));
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

	public function createComponentRoundDetailsForm() {
		$form = new AppForm;

		$round = new Round(array());
		$form->addSelect('phase', 'Fáze projektového kola', $round->getPhases());
		$form->addText('amount', 'Rozdělovaná částka', 10, 10);
		$form->addText('deliberationTimePlace', 'Čas a místo konání diskuse', 80, 80);
		$form->addTextarea('deliberationMinutes', 'Zápis z diskuse');
		$form->addHidden('id');
		$form->addSubmit('submit', 'Uložit podrobnosti projektového kola');


		$form->onSubmit[] = callback($this, 'roundDetailsFormSubmitted');
		return $form;
	}

	public function roundDetailsFormSubmitted($form) {
		$values = $form->getValues();

		$roundManager = new RoundManager;
		$round = $roundManager->find($values['id']);

		if ($values['phase'] != $round->phase) {
			$round->setPhase($values['phase'], Environment::getUser()->getId());
		}

		$round->deliberationTimePlace = $values['deliberationTimePlace'];
		$round->deliberationMinutes = $values['deliberationMinutes'];
		$round->amount = $values['amount'];

		$round->save();

		$this->flashMessage('Podrobnosti projektového kola byly uloženy.');
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

				if (Environment::getUser()->isAllowed('project', 'rate')) {
					$this->template->myRating = $this->round->getProjectsRating($this->round->phase, Environment::getUser()->getId());
				}
				break;

		}

	}

	public function renderRound() {
		$ratingCategoryManager = new RatingCategoryManager;
		$this->template->currentCategories = $ratingCategoryManager->findByRound($this->roundID);
		$this->template->previousCategories = $ratingCategoryManager->findByRound($ratingCategoryManager->findPreviousRound($this->roundID));

		switch ($this->round->phase) {
			case 'deliberation':
				break;

			case 'results':
				break;
		}
	}


	public function createComponentCloneRatingCategoriesForm() {
		$form = new AppForm;

		$form->addHidden('roundID')->setValue($this->roundID);

		$ratingCategoryManager = new RatingCategoryManager;
		$form->addHidden('previousRoundID')->setValue($ratingCategoryManager->findPreviousRound($this->roundID));

		$form->addSubmit('submit', 'Přenést kritéria hodnocení z minulého kola (později je možné je upravit)');

		$form->onSubmit[] = callback($this, 'cloneRatingCategoriesFormSubmitted');
		return $form;
	}

	public function cloneRatingCategoriesFormSubmitted($form) {
		$values = $form->getValues();

		$ratingCategoryManager = new RatingCategoryManager;
		$ratingCategoryManager->copyAmongRounds($values['previousRoundID'], $values['roundID']);

		$this->flashMessage('Kritéria byla přenesena.');
		$this->redirect('this');
	}


	public function createComponentAddRatingCategoriesForm() {
		$form = new AppForm;

		for ($i = 0; $i < 10; $i++) {
			$form->addText('name'.$i, 'Název', NULL, 250);
			$form->addTextArea('description'.$i, 'Popis');
		}

		$form->addHidden('roundID')->setValue($this->roundID);
		$form->addSubmit('submit', 'Vložit kritéria');

		$form->onSubmit[] = callback($this, 'addRatingCategoriesFormSubmitted');
		return $form;
	}

	public function addRatingCategoriesFormSubmitted($form) {
		$values = $form->getValues();

		$roundManager = new RoundManager;
		$round = $roundManager->find($values['roundID']);

		for ($i = 0; $i < 10; $i++) {
			if ($values['name' . $i]) {
				$round->addRatingCategory($values['name' . $i], $values['description' . $i]);
			}
		}


		$this->flashMessage('Kritéria byla přidána.');
		$this->redirect('this');
	}

	public function handleUnlinkRatingCategory($categoryID) {
		$roundManager = new RoundManager;
		$round = $roundManager->find($this->roundID);
		$round->unlinkRatingCategory($categoryID);

		$this->redirect('this');
	}

	public function createComponentEditProjectForm() {
		$form = new AppForm;

		$form->addText('name', 'Název projektu');
		$form->addText('applicant', 'Žadatel');
		$form->addText('amount', 'Částka');
		$form->addText('applicationLink', 'Odkaz na žádost (PDF)');
		$form->addTextArea('otherLinks', 'Odkazy na další dokumenty (PDF)')
			->setOption('description', 'Vkládejte každý odkaz na samostatný řádek.');
		$form->addHidden('id');
		$form->addSubmit('submit', 'Uložit projekt');

		$form->onSubmit[] = callback($this, 'editProjectFormSubmitted');
		return $form;
	}

	public function editProjectFormSubmitted($form) {
		$values = $form->getValues();

		if ($values['id']) {
			$projectManager = new ProjectManager;
			$project = $projectManager->find($values['id']);

			$project->name = $values['name'];
			$project->applicant = $values['applicant'];
			$project->amount = $values['amount'];
			$project->negotiatedAmount = $values['amount'];
			$project->applicationLink = $values['applicationLink'];
			$project->otherLinks = $values['otherLinks'];

			$project->save();
		} else {
			$project = new Project($values);
			$project->negotiatedAmount = $values['amount'];
			$project->roundID = $this->roundID;

			$projectManager = new ProjectManager;
			$projectManager->create($project);
		}

		//usable only if files could be stored locally
		//$id = dibi::insertId();
		//$values['application']->move(WWW_DIR . '/files/application-' .  $id . '.pdf');

		$this->flashMessage('Projekt byl uložen.');
		$this->redirect('this');
	}

	public function createComponentEditNegotiatedProjectAmountForm() {
		$form = new AppForm;

		$form->addText('negotiatedAmount', 'Dohodnutá částka');
		$form->addHidden('id');
		$form->addSubmit('submit', 'Uložit dojednanou částku');

		$form->onSubmit[] = callback($this, 'editNegotiatedProjectAmountFormSubmitted');
		return $form;
	}

	public function editNegotiatedProjectAmountFormSubmitted($form) {
		$values = $form->getValues();

		$projectManager = new ProjectManager;
		$project = $projectManager->find($values['id']);

		if ($values['negotiatedAmount'] > $project->amount) {
			$this->flashMessage('Dojednaná částka nemůže bý vyšší než původní.', 'error');
		} else {
			$project->negotiatedAmount = $values['negotiatedAmount'];
			$project->save();
			$this->flashMessage('Dojednaná výše požadované částky byla uložena.');
		}
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
			case 'firstRating':
				$this->template->jurors = $this->project->getJurorsWhoRatedThisProject('firstRating');
				break;

			case 'secondRating':
				$this->template->jurors = $this->project->getJurorsWhoRatedThisProject('secondRating');
				break;

			case 'deliberation':
				$this->template->ratings = $this->project->getRatingsByCategory('firstRating');
				$this->template->eligibility = $this->project->getEligibility('firstRating');
				$this->template->categories = $this->round->getRatingCategories();
				break;

			case 'results':
				$this->template->firstRatings = $this->project->getRatingsByCategory('firstRating');
				$this->template->secondRatings = $this->project->getRatingsByCategory('secondRating');
				$this->template->firstEligibility = $this->project->getEligibility('firstRating');
				$this->template->secondEligibility = $this->project->getEligibility('secondRating');
				$this->template->categories = $this->round->getRatingCategories();
				$this->template->jurors = $this->round->getJurorsWhoRatedThisRound();
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

		$form->addCheckBox('notEligible', 'Projekt není vhodné podpořit');
		$form->addTextArea('reasons', 'Zdůvodnění hodnocení', 60, 15);

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

		$this->project->setEligibility($values['notEligible'], $values['reasons'], $jurorID);

		$this->flashMessage('Hodnocení bylo uloženo.');
		$this->redirect('this');
	}
}