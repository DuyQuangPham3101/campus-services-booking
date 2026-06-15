<?php

require_once __DIR__ . '/../models/Booking.php';

class BookingController
{
    private $bookingModel;

    public function __construct()
    {
        $this->bookingModel = new Booking();
    }

    // READ ALL
    public function index($user_id = null)
    {
        return $this->bookingModel->getAll($user_id);
    }

    // CREATE
    public function store($data)
    {
        return $this->bookingModel->create(
            $data['user_id'],
            $data['resource_id'],
            $data['time_slot_id'],
            $data['booking_date'],
            $data['status']
        );
    }

    // GET SINGLE BOOKING
    public function getBooking($id)
    {
        return $this->bookingModel->getById($id);
    }

    // UPDATE
    public function updateBooking($id, $data)
    {
        return $this->bookingModel->update(
            $id,
            $data['user_id'],
            $data['resource_id'],
            $data['time_slot_id'],
            $data['booking_date'],
            $data['status']
        );
    }

    // DELETE
    public function deleteBooking($id)
    {
        return $this->bookingModel->delete($id);
    }

    public function getUsers()
{
    return $this->bookingModel->getUsers();
}

public function getResources()
{
    return $this->bookingModel->getResources();
}

public function getTimeSlots()
{
    return $this->bookingModel->getTimeSlots();
}
}
?>