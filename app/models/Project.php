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
				'ratingCategoryID%i' => $rating,
				'jurorID%i' => $ratingCategoryID,
				'phase%s' => $phase,
				'time%sql' => 'NOW()'
			);
			dibi::query("INSERT INTO rating", $values);
		}
		
		$jurorManager = new JurorManager;
		$juror = $jurorManager->find($jurorID);			
			
		$logManager = new LogManager;
		$logManager->log('Porotce ' . $juror->name . ' ohodnotil projekt ' . $this->name . '.');
	}
}