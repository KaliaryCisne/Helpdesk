<?php

declare(strict_types=1);

namespace Root\Controller;

use FontLib\Table\Type\head;
use Root\Adapter\Connection;
use Root\Entity\Department;
use Root\Entity\User;
use Root\Validator\UserValidator;

final class UserController extends AbstractController
{

    private $entityManager;


    public function __construct()
    {
        $this->entityManager = Connection::getEntityManager();

        $this->departmentRepository = $this->entityManager->getRepository(Department::class);
        $this->UserRepository = $this->entityManager->getRepository(User::class);
    }

    public function profileAction(): void
    {
      $this->render('User/profile');
    }

    public function listAction(): void
    {
        $users = $this->UserRepository->findAll();

        $this->render('User/list', [
          'users' => $users,
      ]);
    }

    public function addAction(): void
    {
      if (!$_POST) {
          $departments = $this->departmentRepository->findAll();

          $this->render('User/add', [
              'departments' => $departments,
          ]);
          return;
      }

      if (!UserValidator::validateUser($_POST)) {
          $this->render('User/add');
          return;
      }
      $password = password_hash($_POST['password'], PASSWORD_ARGON2I);
      $department = $this->departmentRepository->find($_POST['department']);
      $user = new User($_POST['name'], $_POST['email'], $_POST['password']);
      $user->setDepartment($department);
      $user->setType($_POST['type']);
      $user->setPassword($password);
      try {
          $this->entityManager->persist($user);
          $this->entityManager->flush();
          header('location: /admin/usuarios');
      } catch (\Exception $e) {
          $user = $this->UserRepository->findAll();
          $this->render('User/list', [
              'error' => 'Erro ao adicionar esse usuário, por gentileza verificar todos os campos!'
          ]);

      }
    }

    public function removeAction(): void
    {
        $id = $_GET['id'];
        $user = $this->UserRepository->find($id);

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        header('location: /admin/usuarios');
    }

    public function editAction()
    {
        $id = $_GET['id'];
        $user = $this->UserRepository->find($id);
        $departments = $this->departmentRepository->findAll();


        if($_POST) {
            $department = $this->departmentRepository->find($_POST['department']);
            $user->setName($_POST['name']);
            $user->setPassword($_POST['email']);
            $user->setDepartment($department);
            $user->setType($_POST['type']);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            header('location: /admin/usuarios');
        }


        $this->render('User/edit', [
            'user' => $user,
            'department' => $departments,
        ]);
    }
}
