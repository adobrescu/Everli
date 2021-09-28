<?php

include_once(__DIR__.'/interfaces.php');

class ObservableRepository extends Repository implements IObservableRepository
{
    use TNotificationSource;

    function createRecord(array $record): int {
        $newRecordKey = parent::createRecord($record);

        $this->callNotificationHandlers('create', $record);

        return $newRecordKey;
    }
    
    function deleteRecord(int $id) {
        $recordToDelete = $this->records[$id];

        parent::deleteRecord($id);

        $this->callNotificationHandlers('delete', $recordToDelete);
    }
}