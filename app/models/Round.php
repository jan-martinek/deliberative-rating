<?php

class Round extends Record {

    protected function getTable() {
		return 'rounds';
    }

	public function setPhase($phase, $jurorID) {
		$jurorManager = new JurorManager;
		$juror = $jurorManager->find($jurorID);
		
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
	
	public function getProjectsRating() {
		$where = array('roundID%i' => $this->id, 'projects.id%n' => 'rating.projectID');
		return dibi::query('SELECT projects.id as projectID, ratingCategoryID, count(*) as count, avg(rating) as avgRating 
			FROM projects, rating WHERE %and', $where, 
			'GROUP BY projects.id, rating.ratingCategoryID')->fetchAssoc('projectID, ratingCategoryID');
	}
	
	public function countProjectRatings() {
		$ratings = $this->getProjectsRating();
		
		if (!count($ratings)) return;
		
		foreach ($ratings as $projectID => $project) {
			$ratings = array();
			foreach ($project as $categoryID => $rating) {
				$ratings[] = $rating['avgRating'];
			}
			$avgRating = array_sum($ratings)/count($ratings);
			
			dibi::query('UPDATE projects SET rating = %f', $avgRating, ' WHERE id = %i', $projectID);
		}
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