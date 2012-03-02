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

}
