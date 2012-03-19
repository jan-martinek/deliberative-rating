<?php

class Project extends Record {

    protected function getTable() {
		return 'projects';
    }

	public function rate($values, $jurorID) {
		$roundManager = new RoundManager;
		$round = $roundManager->find($this->roundID);
		$phase = $round->phase;
		
		foreach ($values as $ratingCategoryID => $rating) {
			$values = array(
				'projectID%i' => $this->id,
				'ratingCategoryID%i' => $ratingCategoryID,
				'rating' => $rating,
				'jurorID%i' => $jurorID,
				'phase%s' => $phase,
				'time%sql' => 'NOW()'
			);
			dibi::query("INSERT INTO ratings", $values, 'ON DUPLICATE KEY UPDATE rating = %i', $rating);
		}
		
		$jurorManager = new JurorManager;
		$juror = $jurorManager->find($jurorID);			
			
		$logManager = new LogManager;
		$logManager->log('Porotce ' . $juror->name . ' ohodnotil projekt ' . $this->name . '.');
	}
	
	public function setEligibility($notEligible, $reasons, $jurorID) {
		$roundManager = new RoundManager;
		$round = $roundManager->find($this->roundID);
		$phase = $round->phase;
		
		$values = array(
			'jurorID%i' => $jurorID,
			'projectID%i' => $this->id,
			'notEligible' => $notEligible,
			'reasons%s' => $reasons,
			'phase%s' => $phase
		);
		
		dibi::query('INSERT INTO eligibilities', $values, 
			'ON DUPLICATE KEY UPDATE reasons = %s', $reasons, ', notEligible = %i', $notEligible);
	}
	
	public function getRatings($phase, $jurorID = NULL, $sortBy = NULL) {

		$where = array(
			'projectID%i' => $this->id,
			'phase%s' => $phase
		);
		if ($jurorID) {
			$where['jurorID%i'] = $jurorID;
		}			
		
		$result = dibi::query('SELECT ratingCategoryID, jurorID, rating FROM ratings WHERE %and', $where);

		if ($jurorID) return $result->fetchPairs('ratingCategoryID', 'rating');
		else {
			$allRatings = $result->fetchAll();
			$result = array();
			
			foreach ($allRatings as $rating) {
				switch ($sortBy) {
					case 'juror':
						$result[$rating->jurorID][$rating->ratingCategoryID] = $rating->rating;
						break;

					case 'category':
					default:
						$result[$rating->ratingCategoryID][$rating->jurorID] = $rating->rating;
						break;					
				}
			}
			return $result;
		}
	}
	
	public function getRatingsByCategory($phase) {
		return $this->getRatings($phase, NULL, 'category');
	}
	
	public function getRatingsByJuror($phase) {		
		return $this->getRatings($phase, NULL, 'juror');
	}
	
	public function getEligibility($phase, $jurorID = NULL) {
		$where = array(
			'projectID%i' => $this->id,
			'phase%s' => $phase
		);
		if ($jurorID) {
			$where['jurorID%i'] = $jurorID;
		}
		
		$result = dibi::query('SELECT * FROM eligibilities WHERE %and', $where);
		
		if ($jurorID) return $result->fetch();
		else return $result->fetchAll();
	}	
}