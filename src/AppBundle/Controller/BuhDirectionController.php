<?php

namespace AppBundle\Controller;

use AppBundle\Form\BuhDirectionForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\BuhDirection;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;


class BuhDirectionController extends Controller
{

    /**
     * @var null
     */
    protected $em = null;

    /**
     * @return null
     */
    public function getEntityManager()
    {
        if ($this->em == null) {
            $this->em = $this->getDoctrine()->getEntityManager();
        }

        return $this->em;
    }

    /**
     * @param Request $request
     * @param $id
     * @Route("/direction/edit/{id}", name="edit_direction", requirements={"id"="\d+"})
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id)
    {
        $obj = $this->getEntity($id);
        if (!$obj) {
            throw $this->createNotFoundException('Направление не найдено');
        }
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getEntityManager();
        $form = $this->createForm(BuhDirectionForm::class, $obj);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('direction_list');
        }

        return $this->render(
            'AppBundle:BuhDirection:edit.html.twig',
            [
                'form' => $form->createView(),
            ]
        );

    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/direction/del/{id}", name="del_direction", requirements={"id"="\d+"})
     */
    public function delAction($id)
    {
        $obj = $this->getEntity($id);
        if (!$obj) {
            throw $this->createNotFoundException('Направление не найдено');
        }
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getEntityManager();
        $em->remove($obj);
        $em->flush();

        return $this->redirectToRoute('direction_list');
    }

    /**
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/direction/list/{page}", name="direction_list", requirements={"page"="\d+"})
     */
    public function getListAction($page = 1)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getEntityManager();
        $query = $em->getRepository(BuhDirection::class)->findAll();
        $paginator = $this->get('knp_paginator');
        $items = $paginator->paginate(
            $query,
            $page,
            5
        );

        return $this->render(
            'AppBundle:BuhDirection:list.html.twig',
            [
                'pagination' => $items,
            ]
        );
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/direction/add", name="direction_add")
     */
    public function addAction(Request $request)
    {
        $form = $this->createForm(BuhDirectionForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $author = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($author);
            $em->flush();

            return $this->redirectToRoute('direction_list');
        }

        return $this->render(
            'AppBundle:BuhDirection:add.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }

    /**
     * @param $id
     *
     * @return bool|null|object
     */
    protected function getEntity($id)
    {
        try {
            /** @var \Doctrine\ORM\EntityManager $em */
            $em = $this->getEntityManager();
            return $em->getRepository(BuhDirection::class)
                ->find($id);
        } catch (Exception $e) {
            return false;
        }

    }

}
