<?php

declare(strict_types=1);

namespace Root\Controller\api;


use Root\Adapter\Connection;
use Root\Entity\Department;
use Root\Entity\User;

final class UserRestController
{

    private $entityManager;
    private $repository;

    public function __construct()
    {
        $this->entityManager = Connection::getEntityManager();
        $this->userRepository = $this->entityManager->getRepository(User::class);
    }

    public function getAction(): void
    {
        header('Content-Type: application/json');

        $users = $this->userRepository->findAll();
        $users = array_map(function (User $user) {
            return $user->json();
        }, $users);

        echo json_encode($users);
    }

    public function postAction(): void
    {
        $json = json_decode(file_get_contents('php://input'));

        if(!$this->validateUser($json)) {
            echo json_encode([
                'erro' => 'Todos os atributos são obrigatórios!',
            ]);
            return;
        }

        $password = password_hash($json->password, PASSWORD_ARGON2I);

        $departmentRepository = $this->entityManager->getRepository(Department::class);
        $department = $departmentRepository->find($json->department);

        if(!$department) {
            echo json_encode([
                'erro' => 'O id do departamento não foi encontrado!',
            ]);
            return;
        }

        try {
            $user = new User($json->name, $json->email, $password);
            $user->setDepartment($department);
            $user->setType($json->type);
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            echo json_encode($user->json());
            return;
        } catch (\Exception $e) {
            echo json_encode([
               'erro' => 'Houve erro ao salvar as informações, tente novamente!'
            ]);
            return;
        }

    }

    public function putAction(): void
    {
        $json = json_decode(file_get_contents('php://input'));

        $id = $_GET['id'];

        $user = $this->userRepository->find($id);

        $departmentRepository = $this->entityManager->getRepository(Department::class);

        if(isset($json->department)){
            $department = $departmentRepository->find($json->department);

            if(!$department) {
                echo "Departamento não Econtrado!";
                return;
            }
        }

        if(isset($json->password)){
            $password = password_hash($json->password, PASSWORD_ARGON2I);
        }

        if(!$user) {
            echo "Usuário não Econtrado!";
            return;
        }

        try {
            $user->setName($json->name ?? $user->getName());
            $user->setDepartment($department ?? $user->getDepartment());
            $user->setType($json->type ?? $user->getType());
            $user->setEmail($json->email ?? $user->getEmail());
            $user->setPassword($password ?? $user->getPassword());

            $this->entityManager->persist($user);
            $this->entityManager->flush();
            echo json_encode($user->json());
        } catch (\Exception $e) {
            echo "Não foi possível atualizar esse usuário, por gentileza tente novamente";
        }

    }

    public function deleteAction(): void
    {
        $id = $_GET['id'];

        $user = $this->userRepository->find($id);

        if(!$user) {
            echo "Usuário não encontrado";
            return;
        }

        try {
            $this->entityManager->remove($user);
            $this->entityManager->flush();

            echo "Usuário deletado com sucesso!";
        } catch (\Exception $e) {
            echo "Não foi possível deletar este usuário no momento!";
        }
    }


    private function validateUser($json): bool
    {
        if (!isset($json->name)) {
            return false;
        }
        if (!isset($json->email)) {
            return false;
        }
        if (!isset($json->password)) {
            return false;
        }
        return true;
    }



}