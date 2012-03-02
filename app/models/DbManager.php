<?php

abstract class DbManager
{
    protected $table;
    protected $RecordClass;

    public function findAll($order = NULL, $where = NULL, $offset = NULL, $limit = NULL)
    {
        return dibi::query(
            'SELECT * FROM [' . $this->table . ']',
            '%if', isset($where), 'WHERE %and', isset($where) ? $where : array(), '%end',
            '%if', isset($order), 'ORDER BY %by', $order, '%end',
            '%if', isset($limit), 'LIMIT %i %end', $limit,
            '%if', isset($offset), 'OFFSET %i %end', $offset
        )->setRowClass($this->RecordClass);
    }

    public function find($id)
    {
        return dibi::query('SELECT * FROM [' . $this->table . '] WHERE [id]=%i LIMIT 1', $id)
                ->setRowClass($this->RecordClass)
                ->fetch();
    }

    public function count($where = NULL)
    {
        return dibi::fetchSingle('SELECT COUNT([id]) FROM [' . $this->table . '] %if',
                     isset($where), 'WHERE %and', isset($where) ? $where : array()
        );
    }

    public function create(Record $record)
    {
	$record->cleanCache();

	$savedata = $record->processSavedata((array) $record);
	//Debug::dump($savedata); die;
        return dibi::query('INSERT INTO [' . $this->table . ']', $savedata);
    }



}