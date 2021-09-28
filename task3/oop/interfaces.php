<?php

interface INotificationSource
{
    function registerNotificationHandler($notificationName, $callback);
}

interface IRepository
{
    function createRecord(array $record): int;
    function deleteRecord(integer $id);

    function readAll();
}
