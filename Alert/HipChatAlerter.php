<?php

namespace Innmind\ProvisionerBundle\Alert;

use GorkaLaucirica\HipchatAPIv2Client\API\RoomAPI;
use GorkaLaucirica\HipchatAPIv2Client\Model\Message;

/**
 * Sends a room notification when an alert is raised
 */
class HipChatAlerter implements AlerterInterface
{
    protected $api;
    protected $room;

    /**
     * Set the hipchat room api
     *
     * @param RoomAPI $api
     */
    public function setRoomApi(RoomAPI $api)
    {
        $this->api = $api;
    }

    /**
     * Set the room name where to send notifications to
     *
     * @param string $name
     */
    public function setRoom($name)
    {
        $this->room = (string) $name;
    }

    /**
     * {@inheritdoc}
     */
    public function alert(Alert $alert)
    {
        $message = new Message();

        if ($alert->isOverUsed()) {
            $color = Message::COLOR_RED;
            $text = sprintf(
                'Server at full capacity! Command: %s | CPU: %s | Load: %s | Required: %s | Running: %s',
                (string) $alert->getCommandInput(),
                $alert->getCpuUsage(),
                $alert->getLoadAverage(),
                $alert->getLeftOver(),
                $alert->getRunningProcesses()
            );
        } else {
            $color = Message::COLOR_YELLOW;
            $text = sprintf(
                'Server under used. You may take it down! Command: %s | CPU: %s | Load: %s',
                (string) $alert->getCommandInput(),
                $alert->getCpuUsage(),
                $alert->getLoadAverage()
            );
        }

        $message
            ->setColor($color)
            ->setNotify(true)
            ->setMessageFormat(Message::FORMAT_TEXT)
            ->setMessage($text);

        $this->api->sendRoomNotification($this->room, $message);
    }
}
