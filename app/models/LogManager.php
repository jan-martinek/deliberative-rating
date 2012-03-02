<?php

class LogManager extends DbManager {

    protected $table = 'log';
    protected $RecordClass = 'LogRecord';

	public function log($event) {
		$values = array('time%sql' => 'NOW()', 'event%s' => $event);
		$log = new LogRecord();
		
	}

}