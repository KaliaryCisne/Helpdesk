<?php

declare(strict_types=1);

namespace Root\Controller;

use Dompdf\Dompdf;
use Root\Adapter\Connection;
use Root\Entity\Department;

final class DepartmentController extends AbstractController
{
    private $repository;
    private $entityManager;

    public function __construct()
    {
        $this->entityManager = Connection::getEntityManager();
        $this->departmentRepository = $this->entityManager->getRepository(Department::class);
    }

    public function listAction(): void
    {
        $departments = $this->departmentRepository->findAll();

        $this->render('Department/list', [
            'departments' => $departments,
        ]);
    }

    public function addAction(): void
    {
        if($_POST) {
            $department = new Department();
            $department->setName($_POST['name']);
            $department->setDescription($_POST['description']);

            $this->entityManager->persist($department);
            $this->entityManager->flush();

            header('location: /admin/departamentos');

        }
        $this->render('Department/add');
    }

    public function removeAction(): void
    {

        $id = $_GET['id'];
        $department = $this->departmentRepository->find($id);

        try {
            $this->entityManager->remove($department);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $departments = $this->departmentRepository->findAll();
            $this->render('Department/list', [
                'error' => 'Não foi possível excluir esse registro, por gentileza consultar a TI!',
                'departments' => $departments,
           ]);

        }
        header('location: /admin/departamentos');
    }

    public function editAction()
    {
        $id = $_GET['id'];
        $department = $this->departmentRepository->find($id);

        if($_POST) {
            $department->setName($_POST['name']);
            $department->setDescription($_POST['description']);

            $this->entityManager->persist($department);
            $this->entityManager->flush($department);

            header('location:/admin/departamentos');
        }

        $this->render('Department/edit', [
            'department' => $department,
        ]);
    }

    public function pdfAction() : void
    {
        $departments = $this->departmentRepository->findAll();

        $html = include_once '../src/View/Department/pdf.phtml';
        $date = date('dmYHis');

        $dompdf = new Dompdf();

//        $dompdf->loadHtml($html);
        $dompdf->loadHtml(html_entity_decode($html));
        $dompdf->render();
        $dompdf->stream("Departments-{$date}.pdf", [
            'Attachment' => 0,
        ]);
    }
}
