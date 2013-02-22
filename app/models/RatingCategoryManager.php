<?php

class RatingCategoryManager extends DbManager {

    protected $table = 'ratingCategories';
    protected $RecordClass = 'RatingCategory';

	public function findByRound($roundID) {
		$where = array('roundXratingCategory.categoryID%n' => 'ratingCategories.id',
		'roundXratingCategory.roundID%i' => $roundID);
		return dibi::query('SELECT ratingCategories.*
			FROM roundXratingCategory, ratingCategories WHERE %and', $where)->setRowClass($this->RecordClass)->fetchAssoc('id');
	}

	public function findPreviousRound($roundID) {
		return dibi::query('SELECT [roundID] FROM [roundXratingCategory]
			WHERE [roundID] < %i', $roundID, 'ORDER BY roundID DESC LIMIT 0, 1')->fetchSingle();
	}

	public function copyAmongRounds($fromID, $toID) {
		$categories = $this->findByRound($fromID);

		$roundManager = new RoundManager;
		$round = $roundManager->find($toID);

		foreach ($categories as $category) {
			$round->linkRatingCategory($category->id);
		}
		return true;
	}

}
