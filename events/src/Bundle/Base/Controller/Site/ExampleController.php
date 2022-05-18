<?php

namespace App\Bundle\Base\Controller\Site;

use App\Bundle\Base\Entity\Example;
use App\Bundle\Base\Form\ExampleType;
use App\Bundle\Base\Repository\ExampleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * 
 * @Route("example")
 *
 * Class ExampleController
 * @package App\Bundle\Base\Controller\Site
 */
class ExampleController extends AbstractController
{
    /**
     * @Route("/", name="example_index", methods={"GET"})
     *
     * @param ExampleRepository $exampleRepository
     * @return Response
     */
    public function index(ExampleRepository $exampleRepository): Response
    {
        return $this->render('@Base/example/index.html.twig', [
            'examples' => $exampleRepository->findAll(),
        ]);
    }

    /**
     * @Route("/pjax", name="example_pjax", methods={"GET", "OPTIONS"})
     *
     * @param Request $request
     * @return Response
     */
    public function pjax(Request $request): Response
    {
        if ($request->headers->has('x-pjax')) {
            return $this->json(['teste']);
        }

        return $this->render('@Base/example/pjax.html.twig');
    }

    /**
     * @Route("/new", name="example_new", methods={"GET","POST"})
     *
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $example = new Example();
        $form = $this->createForm(ExampleType::class, $example);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($example);
            $entityManager->flush();

            return $this->redirectToRoute('example_index');
        }

        return $this->render('views/example/index.html.twig', [
            'examples' => $example,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="example_show", methods={"GET"})
     *
     * @param Example $example
     * @return Response
     */
    public function show(Example $example): Response
    {
        return $this->render('views/example/show.html.twig', [
            'example' => $example,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="example_edit", methods={"GET","POST"})
     *
     * @param Request $request
     * @param Example $example
     * @return Response
     */
    public function edit(Request $request, Example $example): Response
    {
        $form = $this->createForm(ExampleType::class, $example);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('example_index');
        }

        return $this->render('views/example/edit.html.twig', [
            'example' => $example,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="example_delete", methods={"DELETE"})
     *
     * @param Request $request
     * @param Example $example
     * @return Response
     */
    public function delete(Request $request, Example $example): Response
    {
        if ($this->isCsrfTokenValid('delete'.$example->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($example);
            $entityManager->flush();
        }

        return $this->redirectToRoute('example_index');
    }
}