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
	
	public function getRatings($phase, $jurorID = NULL) {
		if ($jurorID) {
			$where = array(
				'jurorID%i' => $jurorID,
				'projectID%i' => $this->id,
				'phase%s' => $phase
			);
			return dibi::query('SELECT ratingCategoryID, rating FROM ratings WHERE %and', $where)
				->fetchPairs('ratingCategoryID', 'rating');			
		}
	}
	
	public function getEligibility($phase, $jurorID = NULL) {
		if ($jurorID) {
			$where = array(
				'jurorID%i' => $jurorID,
				'projectID%i' => $this->id,
				'phase%s' => $phase
			);
			return dibi::query('SELECT notEligible, reasons FROM eligibilities WHERE %and', $where)->fetch();
		}
	}
}