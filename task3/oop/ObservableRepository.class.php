<?php

include_once(__DIR__.'/interfaces.php');

/**
 * Implements both IRepository and INotificationSource interfaces.
 * An ObservableRepository instance triggers appropriate notifications when a new object is added to the repository,
 * or an existing one is updated or deleted.
 */
class ObservableRepository extends Repository implements IObservableRepository
{
    use TNotificationSource;

    function createRecord(array $record): int {
        $newRecordKey = parent::createRecord($record);

        $this->callNotificationHandlers('create', $record);

        return $newRecordKey;
    }
    
    function updateRecord(int $key, array $record) {
        $oldRecord = $this->records[$key];

        $this->records[$key] = $record;

        $this->callNotificationHandlers('update', [$oldRecord, $record]);
    }

    function deleteRecord(int $id) {
        $recordToDelete = $this->records[$id];

        parent::deleteRecord($id);

        $this->callNotificationHandlers('delete', $recordToDelete);
    }
}