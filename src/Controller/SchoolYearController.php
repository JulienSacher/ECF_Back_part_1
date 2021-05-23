<?php

namespace App\Controller;

use App\Entity\SchoolYear;
use App\Form\SchoolYearType;
use App\Repository\SchoolYearRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/schoolyear")
 */
class SchoolYearController extends AbstractController
{
    /**
     * @Route("/", name="school_year_index", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
     */
    public function index(SchoolYearRepository $schoolYearRepository): Response
    {
        return $this->render('school_year/index.html.twig', [
            'school_years' => $schoolYearRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="school_year_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request): Response
    {
        $schoolYear = new SchoolYear();
        $form = $this->createForm(SchoolYearType::class, $schoolYear);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($schoolYear);
            $entityManager->flush();

            return $this->redirectToRoute('school_year_index');
        }

        return $this->render('school_year/new.html.twig', [
            'school_year' => $schoolYear,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="school_year_show", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_STUDENT')")
     */
    public function show(SchoolYear $schoolYear): Response
    {
        return $this->render('school_year/show.html.twig', [
            'school_year' => $schoolYear,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="school_year_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, SchoolYear $schoolYear): Response
    {
        $form = $this->createForm(SchoolYearType::class, $schoolYear);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('school_year_index');
        }

        return $this->render('school_year/edit.html.twig', [
            'school_year' => $schoolYear,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="school_year_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, SchoolYear $schoolYear): Response
    {
        if ($this->isCsrfTokenValid('delete'.$schoolYear->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($schoolYear);
            $entityManager->flush();
        }

        return $this->redirectToRoute('school_year_index');
    }
}