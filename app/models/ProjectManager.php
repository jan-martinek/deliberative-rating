<?php

class ProjectManager extends DbManager {

    protected $table = 'projects';
    protected $RecordClass = 'Project';

    public function findAllWithAverages($order = NULL, $where = NULL, $offset = NULL, $limit = NULL)
    {
        return dibi::query(
            'SELECT *, (firstRating+secondRating)/2 as avgRating, 
			IF(firstRatingIneligibility >= 2, 1, 0) AS firstRatingIneligibilityBool,
			IF(secondRatingIneligibility >= 2, 1, 0) AS secondRatingIneligibilityBool
			 FROM [' . $this->table . ']',
            '%if', isset($where), 'WHERE %and', isset($where) ? $where : array(), '%end',
            '%if', isset($order), 'ORDER BY %by', $order, '%end',
            '%if', isset($limit), 'LIMIT %i %end', $limit,
            '%if', isset($offset), 'OFFSET %i %end', $offset
        )->setRowClass($this->RecordClass);
    }

}