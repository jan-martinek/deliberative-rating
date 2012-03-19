<?php

class Round extends Record {

    protected function getTable() {
		return 'rounds';
    }

	public function setPhase($phase, $jurorID) {
		$jurorManager = new JurorManager;
		$juror = $jurorManager->find($jurorID);
		
		if ($phase = 'deliberation') {
			$this->countProjectRatings('firstRating');
			$this->countProjectsIneligibilityCounts('firstRating');
		} elseif ($phase = 'results') {
			$this->countProjectRatings('secondRating');
			$this->countProjectsIneligibilityCounts('secondRating');			
		}

		$this->phase = $phase;
		$this->save();
		
		$logManager = new LogManager;
		$logManager->log('Předseda ' . $juror->name . ' změnil fázi rozhodování na "' . $this->getPhaseName($phase) . '".');
	}

	public function getProjects() {
		$projectsManager = new ProjectsManager;
		return $projectsManager->findAll(NULL, array('roundID%i' => $this->id));
	}
	
	public function getRatingCategories() {
		$ratingCategoryManager = new RatingCategoryManager;
 		return $ratingCategoryManager->findByRound($this->id);
	}
	
	public function getProjectsRating($phase) {
		$where = array('phase%s' => $phase, 'roundID%i' => $this->id, 'projects.id%n' => 'ratings.projectID');
		return dibi::query('SELECT projects.id as projectID, ratingCategoryID, count(*) as count, avg(rating) as avgRating 
			FROM projects, ratings WHERE %and', $where, 
			'GROUP BY projects.id, ratings.ratingCategoryID')->fetchAssoc('projectID,ratingCategoryID');
	}
	
	public function getProjectsIneligibilityCounts($phase) {
		$where = array('notEligible' => 1,'phase%s' => $phase, 'roundID%i' => $this->id, 'projects.id%n' => 'eligibilities.projectID');
		return dibi::query('SELECT projects.id as projectID, count(*) as count 
			FROM projects, eligibilities WHERE %and', $where, 
			'GROUP BY projects.id')->fetchPairs('projectID', 'count');
	}
	
	public function countProjectsIneligibilityCounts($phase) {
		$ineligibilities = $this->getProjectsIneligibilityCounts($phase);
		
		foreach ($ineligibilities as $projectID => $count) {
			dibi::query('UPDATE projects SET [' . $phase . 'Ineligibility] = %i', $count, ' WHERE id = %i', $projectID);
		}
	}
	
	public function countProjectRatings($phase) {
		$ratings = $this->getProjectsRating($phase);
		
		if (!count($ratings)) return;
		
		foreach ($ratings as $projectID => $project) {
			$ratings = array();
			foreach ($project as $categoryID => $rating) {
				$ratings[] = $rating['avgRating'];
			}
			$avgRating = array_sum($ratings)/count($ratings);

			dibi::query('UPDATE projects SET [' . $phase . '] = %f', $avgRating, ' WHERE id = %i', $projectID);
		}
	}
	
	public function getProjectsRatingCounts($phase) {
		$where = array('phase%s' => $phase, 'roundID%i' => $this->id, 'projects.id%n' => 'eligibilities.projectID');
		
		return dibi::query('SELECT projects.id AS id, count(*) AS count 
			FROM eligibilities, projects where %and', $where, 
			'GROUP BY projects.id')->fetchPairs('id', 'count');
	}
	
	public function getPhaseName($phase = NULL) {
		$phases = $this->getPhases();
		if (!$phase) $phase = $this->phase;
		return $phases[$phase];
	}
	
    public function getPhases() {	
		return array(
			'preparation' => 'příprava',
			'firstRating' => 'první hodnocení',
			'deliberation' => 'deliberace',
			'secondRating' => 'druhé hodnocení',
			'results' => 'zveřejnění výsledků'
		);
	
    }	
}