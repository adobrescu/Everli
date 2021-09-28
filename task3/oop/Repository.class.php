<?php

include_once(__DIR__.'/interfaces.php');

class Repository implements IRepository
{
    protected $records;

    /**
     * Creates repo object with records from a trusted source
     */
    public function __construct($records) {
        $this->records = $records;
    }

    /**
     * Adds new record from trusted source
     */
    function createRecord(array $record): int {
        $this->records[] = $record;

        return (int) key(end($this->records));
    }
    
    function deleteRecord(int $id) {
        unset($this->records[$id]);
    }

    function readAll() {
        return $this->records;
    }
}