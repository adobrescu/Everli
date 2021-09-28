<?php

interface INotificationSource
{
    function registerNotificationHandler($notificationName, $callback);
}

interface IRepository
{
    function createRecord(array $record): int;
    function deleteRecord(int $id);

    function readAll();
}

interface IObservableRepository extends INotificationSource, IRepository
{
}
