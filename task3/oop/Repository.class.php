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
        
        end($this->records);
        
        return (int) key($this->records);
    }
    /**
     * Updates an existing record from trusted source
     */
    function updateRecord(int $key, array $record) {
        $this->records[$key] = $record;
    }

    /**
     * Deletes an existing record, given a valid record repo internal key/id
     */
    function deleteRecord(int $key) {
        unset($this->records[$key]);
    }

    function readAll() {
        return $this->records;
    }
    public function countAll() {
        return count($this->records);
    }
}