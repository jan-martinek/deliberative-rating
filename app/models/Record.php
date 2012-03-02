<?

abstract class Record extends DibiRow // DibiRow obstará korektní načtení dat
{
    protected function getTable() {
	
    }

    public function delete()
    {
	$this->cleanCache();

        return dibi::query('DELETE FROM [' . $this->getTable() . '] WHERE [id]=%i', $this->id);
    }

    public function save()
    {
	$this->cleanCache();

	$arr = $this->processSavedata((array) $this);
        return dibi::query('UPDATE [' . $this->getTable() . '] SET', $arr, 'WHERE [id]=%i', $this->id); // využijeme toho, že DibiRow dědí od ArrayObject
    }

    public function processSavedata($array) {
	return $array;
    }

    public function cleanCache() {
	return;
    }
}
