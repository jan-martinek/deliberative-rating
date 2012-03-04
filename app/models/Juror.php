<?php

class Juror extends Record {

    protected function getTable() {
		return 'jurors';
    }

    public function getRoleText() {
		$values = $this->getRoleValues();
		return $values[$this->role];
    }

    public function getRoleValues() {
		return array(
			'juror' => 'porotce',
			'chairman' => 'předseda',
			'webmaster' => 'správce webu (není členem poroty)'
		);
    }
}