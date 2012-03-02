<?php

class Juror extends Record {

    protected function getTable() {
		return 'jurors';
    }

    public function  cleanCache() {
		Environment::getCache()->clean(array(
		    Cache::TAGS => array("jurors")
		));
    }

    public function  processSavedata($savedata) {
	if (isset($savedata['password']) AND $savedata['password'] != '') {
	    $savedata['password'] = md5($savedata['password']);
	} else unset($savedata['password']);

	return $savedata;
    }

    public function getRoleText() {
		$values = $this->getRoleValues();
		return $values[$this->role];
    }

    public function getRoleValues() {
		return array(
			'juror' => 'porotce',
			'chairman' => 'předseda'
		);
    }
}