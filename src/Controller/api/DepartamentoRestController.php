<?php

declare(strict_types=1);

namespace Root\Controller\api;


use Root\Adapter\Connection;
use Root\Entity\Department;

final class DepartamentoRestController
{
    private $entityManager;
    private $repository;

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

    public function postAction(): void
    {
        $json = json_decode(file_get_contents('php://input'));

        if (!isset($json->name)) {
            echo json_encode([
                'erro' => 'O atributo nome é obrigatório',
            ]);
            return;
        }

        $department = new Department();
        $department->setName($json->name);
        $department->setDescription($json->description ?? '');

        $this->entityManager->persist($department);
        $this->entityManager->flush();

        echo json_encode($department->json());

    }

    public function putAction(): void
    {
        $json = json_decode(file_get_contents('php://input'));

        $id = $_GET['id'];

        try {
            $department = $this->repository->find($id);

            if(!$department) {
                echo "Departamento não Econtrado!";
                return;
            }
            $department->setName($json->name ?? $department->getName());
            $department->setDescription($json->description ?? $department->getDescription());

            $this->entityManager->persist($department);
            $this->entityManager->flush();
            echo "Departamento atualizado!";
        } catch (\Exception $e) {
            echo "Departamento não atualizado!";
        }
    }

    public function deleteAction(): void
    {
        try {
            $id = $_GET['id'];

            $department = $this->repository->find($id);

            $this->entityManager->remove($department);
            $this->entityManager->flush();

            echo json_encode([
                'success' => 'Departamento excluído',
            ]);
        } catch (\Exception $e) {
            echo "Não foi possível deletar esse departamento!";
        }
    }

}