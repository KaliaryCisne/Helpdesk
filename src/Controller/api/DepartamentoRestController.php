<?php

declare(strict_types=1);

namespace Root\Controller\api;


use Root\Adapter\Connection;
use Root\Entity\Department;
use Root\Entity\User;

final class DepartamentoRestController
{
    public function __construct()
    {
        $this->entityManager = Connection::getEntityManager();
        $this->repository = $this->entityManager->getRepository(Department::class);
    }

    public function getAction(): void
    {
        header('Content-Type: application/json');

        $departments = $this->repository->findAll();

        /*foreach ($departments as $key => $department) {
            $departments[$key] = $department->json();
        }*/

        $departments = array_map(function (Department $department) {
            return $department->json();
        }, $departments);

        echo json_encode($departments);
    }

//

}