<?php

require_once __DIR__ . '/../models/TimeSlot.php';

class TimeSlotController
{
    private $timeslot;

    public function __construct()
    {
        $this->timeslot = new TimeSlot();
    }

    // LIST
    public function index()
    {
        return $this->timeslot->getAll();
    }

    // STORE
    public function store($data)
    {
        return $this->timeslot->create(
            $data['slot_name'],
            $data['start_time'],
            $data['end_time'],
            $data['day_of_week'],
            $data['is_peak_hour']
        );
    }

    // SHOW
    public function show($id)
    {
        return $this->timeslot->getById($id);
    }

    // UPDATE
    public function update($id, $data)
    {
        return $this->timeslot->update(
            $id,
            $data['slot_name'],
            $data['start_time'],
            $data['end_time'],
            $data['day_of_week'],
            $data['is_peak_hour']
        );
    }

    // DELETE
    public function delete($id)
    {
        return $this->timeslot->delete($id);
    }
}
?>