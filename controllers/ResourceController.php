<?php

require_once __DIR__ . '/../models/Resource.php';

class ResourceController
{
    private $resource;

    public function __construct()
    {
        $this->resource = new Resource();
    }

    // GET ALL
    public function index()
    {
        return $this->resource->getAll();
    }

    // GET ONE
    public function getResource($id)
    {
        return $this->resource->getById($id);
    }

    // STORE
    public function store($data)
    {
        return $this->resource->create(
            $data['category_id'],
            $data['name'],
            $data['location'],
            $data['capacity'],
            $data['status']
        );
    }

    // UPDATE
    public function updateResource($id, $data)
    {
        return $this->resource->update(
            $id,
            $data['category_id'],
            $data['name'],
            $data['location'],
            $data['capacity'],
            $data['status']
        );
    }

    // DELETE
    public function deleteResource($id)
    {
        return $this->resource->delete($id);
    }
}
?>