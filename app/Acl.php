<?php

class Acl extends Permission {

    public function __construct() {
		$this->addRole('guest');
		$this->addRole('juror');
		$this->addRole('chairman', 'juror');
		$this->addRole('webmaster', 'chairman');

		// definujeme zdroje
		$this->addResource('round');
		$this->addResource('project');

	
		$this->allow('juror', 'project', 'rate');
		$this->allow('chairman', 'round', 'edit');
			

		//webmaster
		$this->allow('webmaster', Permission::ALL);
    }
}