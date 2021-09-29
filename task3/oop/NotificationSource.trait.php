<?php


trait TNotificationSource
{
    protected $notificationHandlers = [];

    public function registerNotificationHandler($notificationName, $callback) {
        $this->notificationHandlers[$notificationName][] = $callback;
    }

    /**
     * Called everytime a notification source obejct needs to trigger a notification.
     * Calls one by one all notification handlers based on notification name/type
     */
    protected function callNotificationHandlers ($notificationName, $notification = null) {
        if (!is_array($this->notificationHandlers[$notificationName]) ) {
            return;
        }

        foreach ( $this->notificationHandlers[$notificationName] as $callback ) {
            $callback($notification, $notificationName);
        }
    }
}