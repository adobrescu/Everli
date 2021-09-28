<?php


trait TNotificationSource
{
    protected $notificationHandlers = [];

    public function registerNotificationHandler($notificationName, $callback) {
        $this->notificationHandlers[$notificationName][] = $callback;
    }

    protected function callNotificationHandlers ($notificationName) {
        if (!is_array($this->notificationHandlers[$notificationName]) ) {
            return;
        }

        foreach ( $this->notificationHandlers[$notificationName] as $callback ) {
            $callback();
        }
    }
}