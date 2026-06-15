<?php

require_once __DIR__ . '/../models/User.php';

class UserController
{
    private $user;

    public function __construct()
    {
        $this->user = new User();
    }

    // INDEX
    public function index()
    {
        return $this->user->getAll();
    }

    // STORE
    public function store($data)
    {
        return $this->user->create(
            $data['name'],
            $data['email'],
            $data['password'],
            $data['role']
        );
    }

    // SHOW
    public function show($id)
    {
        return $this->user->getById($id);
    }

    // UPDATE
    public function update($id, $data)
    {
        return $this->user->update(
            $id,
            $data['name'],
            $data['email'],
            $data['role']
        );
    }

    // DELETE
    public function delete($id)
    {
        return $this->user->delete($id);
    }
}
?>