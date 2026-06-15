<?php

require_once __DIR__ . '/../models/Cancellation.php';

class CancellationController
{
    private $cancellationModel;

    public function __construct()
    {
        $this->cancellationModel = new Cancellation();
    }

    // LIST ALL CANCELLATION LOGS
    public function index()
    {
        return $this->cancellationModel->getAll();
    }

    // PROCESS A CANCELLATION
    public function cancel($data)
    {
        return $this->cancellationModel->create(
            $data['booking_id'],
            $data['reason'],
            $data['cancelled_by']
        );
    }
}
