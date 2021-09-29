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
    
    function updateRecord(int $key, array $record) {
        $this->records[$key] = $record;
    }

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