<?php

include_once(__DIR__.'/interfaces.php');

class ObservableRepository extends Repository implements INotificationSource, IRepository
{
    use TNotificationSource;

    function createRecord(array $record): int {
        parent::createRecord($record);

        $this->callNotificationHandlers('create', $record);

        return (int) key(end($this->records));
    }
    
    function deleteRecord(int $id) {
        $recordToDelete = $this->records[$id];

        parent::deleteRecord($id);
        
        $this->callNotificationHandlers('delete', $recordToDelete);
    }
}