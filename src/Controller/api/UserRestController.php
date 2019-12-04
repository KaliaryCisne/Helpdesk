<?php

declare(strict_types=1);

namespace Root\Controller\api;


use Root\Adapter\Connection;
use Root\Entity\User;

final class UserRestController
{

    private $entityManager;
    private $repository;

    public function __construct()
    {
        $this->entityManager = Connection::getEntityManager();
        $this->repository = $this->entityManager->getRepository(User::class);
    }

    public function getAction(): void
    {
        header('Content-Type: application/json');

        $users = $this->repository->findAll();
        $users = array_map(function (User $user) {
            return $user->json();
        }, $users);

        echo json_encode($users);
    }




}