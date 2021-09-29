<?php

interface INotificationSource
{
    function registerNotificationHandler($notificationName, $callback);
}

interface IRepository
{
    function createRecord(array $record): int;
    function updateRecord(int $key, array $record);
    //function readRecord(int $key);
    function deleteRecord(int $key);

    function readAll();

    function countAll();
}

interface IObservableRepository extends INotificationSource, IRepository
{
}
