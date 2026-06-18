<?php

require_once __DIR__ . '/../models/ResourceCategory.php';

class ResourceCategoryController
{
    private $category;

    public function __construct()
    {
        $this->category = new ResourceCategory();
    }

    public function index()
    {
        return $this->category->getAll();
    }

    public function getCategory($id)
    {
        return $this->category->getById($id);
    }

    public function store($data)
    {
        return $this->category->create(
            $data['name'],
            $data['description'],
            $data['location'],
            $data['max_capacity'],
            $data['requires_approval'],
            $data['max_booking_per_week'],
            $data['open_time'],
            $data['close_time']
        );
    }

    public function updateCategory($id, $data)
    {
        return $this->category->update(
            $id,
            $data['name'],
            $data['description'],
            $data['location'],
            $data['max_capacity'],
            $data['requires_approval'],
            $data['max_booking_per_week'],
            $data['open_time'],
            $data['close_time']
        );
    }

    public function deleteCategory($id)
    {
        return $this->category->delete($id);
    }
}
?>
