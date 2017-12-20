<?php

namespace AppBundle\Controller;

use AppBundle\Form\BuhSchetForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\BuhDirection;
use AppBundle\Entity\BuhSchet;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;


class BuhSchetController extends Controller
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
     * @Route("/schet/edit/{id}", name="schet_edit", requirements={"id"="\d+"})
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
        $form = $this->createForm(BuhSchetForm::class, $obj);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('schet_list');
        }

        return $this->render(
            'AppBundle:BuhSchet:edit.html.twig',
            [
                'form' => $form->createView(),
            ]
        );

    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/schet/del/{id}", name="schet_del", requirements={"id"="\d+"})
     */
    public function delAction($id)
    {
        $obj = $this->getEntity($id);
        if (!$obj) {
            throw $this->createNotFoundException('Счет не найдено');
        }
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getEntityManager();
        $em->remove($obj);
        $em->flush();

        return $this->redirectToRoute('schet_list');
    }

    /**
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/schet/list/{page}", name="schet_list", requirements={"page"="\d+"})
     */
    public function getListAction($page = 1)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getEntityManager();
        $query = $em->getRepository(BuhSchet::class)->findAll();
        $paginator = $this->get('knp_paginator');
        $items = $paginator->paginate(
            $query,
            $page,
            100
        );

        return $this->render(
            'AppBundle:BuhSchet:list.html.twig',
            [
                'pagination' => $items,
            ]
        );
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/schet/add", name="schet_add")
     */
    public function addAction(Request $request)
    {
        $form = $this->createForm(BuhSchetForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $author = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($author);
            $em->flush();

            return $this->redirectToRoute('schet_list');
        }

        return $this->render(
            'AppBundle:BuhSchet:add.html.twig',
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
            return $em->getRepository(BuhSchet::class)
                ->find($id);
        } catch (Exception $e) {
            return false;
        }

    }

}
