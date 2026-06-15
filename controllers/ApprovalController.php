<?php

require_once __DIR__ . '/../models/Approval.php';

class ApprovalController
{
    private $approvalModel;

    public function __construct()
    {
        $this->approvalModel = new Approval();
    }

    // LIST ALL APPROVAL LOGS
    public function index()
    {
        return $this->approvalModel->getAll();
    }

    // PROCESS AN APPROVAL (APPROVE/REJECT)
    public function approve($data)
    {
        return $this->approvalModel->create(
            $data['booking_id'],
            $data['approved_by'],
            $data['status'], // 'approved' or 'rejected'
            $data['note']
        );
    }
}
