<?php

interface INotificationSource
{
    function registerNotificationHandler($notificationName, $callback);
}

interface IRepository
{
    function createRecord(array $record): int;
    function deleteRecord(int $key);

    function readAll();

    function countAll();
}

interface IObservableRepository extends INotificationSource, IRepository
{
}
