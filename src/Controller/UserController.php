<?php

namespace App\Controller;

use App\Entity\User;
use App\Mapper\UserMapper;
use App\Repository\UserRepository;
use App\Annotation\RequestMapping;
use App\Annotation\PathVariable;
use App\Annotation\RequestBodyVariable;

class UserController
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    /**
     * @return string
     *
     * @RequestMapping(method="GET", path="/users/all")
     */
    public function getAll(): string
    {
        return json_encode($this->userRepository->findAll());
    }

    /**
     * @param string $email
     *
     * @return string
     *
     * @RequestMapping(method="GET", path="/users/byEmail/{email}")
     * @PathVariable(variableName="email")
     */
    public function getOne(string $email): string
    {
        return json_encode($this->userRepository->findByEmail($email));
    }

    /**
     * @param string $userJson
     *
     * @return string
     *
     * @RequestMapping(method="POST", path="/users/new")
     * @RequestBodyVariable(variableName="user")
     */
    public function add(string $userJson): string
    {
        return json_encode($this->userRepository->addUser(UserMapper::toEntity(json_decode($userJson, true))));
    }

    /**
     * @param string $userJson
     *
     * @return string
     *
     * @RequestMapping(method="PUT", path="/users/update")
     * @RequestBodyVariable(variableName="user")
     */
    public function update(string $userJson): string
    {
        return json_encode($this->userRepository->updateUser(UserMapper::toEntity(json_decode($userJson, true))));
    }

    /**
     * @param string $email
     *
     * @return void
     *
     * @RequestMapping(method="DELETE", path="/users/delete/{email}")
     * @PathVariable(variableName="email")
     */
    public function delete(string $email): void
    {
        $this->userRepository->deleteUser($email);
    }
}
