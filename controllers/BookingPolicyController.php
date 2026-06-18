<?php

require_once __DIR__ . '/../models/BookingPolicy.php';

class BookingPolicyController
{
    private $policy;

    public function __construct()
    {
        $this->policy = new BookingPolicy();
    }

    public function index()
    {
        return $this->policy->getAll();
    }

    public function getPolicy($id)
    {
        return $this->policy->getById($id);
    }

    public function store($data)
    {
        return $this->policy->create(
            $data['category_id'],
            $data['rule_type'],
            $data['value']
        );
    }

    public function updatePolicy($id, $data)
    {
        return $this->policy->update(
            $id,
            $data['category_id'],
            $data['rule_type'],
            $data['value']
        );
    }

    public function deletePolicy($id)
    {
        return $this->policy->delete($id);
    }
}
?>
