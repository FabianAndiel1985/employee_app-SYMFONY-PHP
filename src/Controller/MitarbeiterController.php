<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Employee;
use App\Form\NewEmployeeType;
use Symfony\Component\HttpFoundation\Request;



class MitarbeiterController extends AbstractController
{
    /**
     * @Route("/employees", name="employees")
     */
    public function emplpoyeeOverview(Request $request): Response
    {
        
        $em = $this->getDoctrine()->getManager();
        
        $employees = $em->getRepository(Employee::class)->findAll();

        $arraySize = count($employees);

        $employeesArray = [];

        for ($i = 0; $i < $arraySize; $i++) {
            
            $employee = new Employee();
            $employee->setSalutation($employees[$i]->getSalutation());
            $employee->setFirstname($employees[$i]->getFirstname());
            $employee->setLastname($employees[$i]->getLastname());
            $employee->setStreet($employees[$i]->getStreet());
            $employee->setHousenumber($employees[$i]->getHousenumber());
            $employee->setZip($employees[$i]->getZip());
            $employee->setCity($employees[$i]->getCity());

            array_push($employeesArray, $employee);
        }

 

        return $this->render('employees/employeeOverview.html.twig', [
            'controller_name' => 'EmplopyeeController',
            'employees' => $employeesArray
        ]);
    }

    /**
     * @Route("/employees/{slug}", name="emplpoyeeDetail",requirements={"slug"="\d+"})
     */
    public function employeeDetail(Request $request): Response
    {
        return $this->render('employees/employeeDetail.html.twig', [
            'controller_name' => 'EmployeeDetailsController',
        ]);
    }

     /**
     * @Route("/employees/new", name="new")
     */
      public function new(Request $request): Response
    {
        $employee= new Employee();

        $form = $this->createForm(NewEmployeeType::class, $employee);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                $employee = $form->getData();
            
                // ... perform some action, such as saving the task to the database
                // for example, if Task is a Doctrine entity, save it!
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($employee);
                $entityManager->flush();

                // return $this->redirectToRoute('success', array('fullfilledAction' => ' created'));

            return $this->render('employees/success.html.twig', [
                'fullfilledAction' => ' updated'
            ]);
    
        }

        return $this->render('employees/newEmployee.html.twig', [
            'form'=>$form->createView()
        ]);
    }

      /**
     * @Route("/employees/update", name="update")
     */
    public function update(Request $request): Response
    {
   
         $em = $this->getDoctrine()->getManager();
         $id = $request->query->get('id');
         
        
         $employee = $em->getRepository(Employee::class)->findOneBy([
            'id'=>$id
        ]);

        // var_dump($employee);

        if (!$employee) {
            throw $this->createNotFoundException('This employee does not exist');
        }

        echo "employee found";

        $form = $this->createForm(NewEmployeeType::class, $employee);
        
        // WICHTIG HIER JETZT EINFÜGEN ANALOG VON NEW EMPLOYEE

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $employee = $form->getData();
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($employee);
            $entityManager->flush();

  // return $this->redirectToRoute('success', array('fullfilledAction' => ' created'));          

        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);

    }
        return $this->render('employees/newEmployee.html.twig', [
            'form'=>$form->createView(),
            'controller_name' => 'MainController',
        ]);

    }



    /**
     * @Route("/employees/delete", name="delete")
     */
    public function delete(Request $request): Response
    {
   
         $em = $this->getDoctrine()->getManager();
         $id = $request->query->get('id');
 
         $employee = $em->getRepository(Employee::class)->findOneBy([
            'id'=>$id
        ]);
         
        try {
            $em->remove($employee);
            $em->flush();
        } catch (\Throwable $th) {
            print_r("deleting failed");
        }
        
        return $this->render('employees/employeeDetail.html.twig', [
        ]);
    }
}

