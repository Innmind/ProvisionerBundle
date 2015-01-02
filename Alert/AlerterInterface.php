<?php

namespace Innmind\ProvisionerBundle\Alert;

/**
 * Interface for any class that need to alert that the server is under/over used
 */
interface AlerterInterface
{
    /**
     * Alert that the server could be taken down as it does do much work anymore
     *
     * @param Alert $alert
     */
    public function alert(Alert $alert);
}
