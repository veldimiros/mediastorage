<?php

namespace AppBundle\Controller;

use AppBundle\Entity\File;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class DefaultController extends Controller
{

    /**
     * @Route("/", name="homepage")
     * @Method({"GET", "POST"})
     */
    public function indexAction(Request $request)
    {
        $upload = new File();
        $form = $this->createForm('AppBundle\Form\FileType', $upload, [
            'action' => $this->generateUrl('homepage')
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $hashEmail = md5($upload->getEmail());
            $upload->setHashEmail($hashEmail);

            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $upload->getFile();

            // Generate a unique name for the file before saving it
            $hashFile = md5($file) . '.' . $file->guessExtension();
            $upload->setHashFile($hashFile);

            // Move the file to the directory where song are stored
            $file->move(
                    $this->getParameter('upload_directory') . $hashEmail, $hashFile
            );

            $em = $this->getDoctrine()->getManager();
            $em->persist($upload);
            $em->flush($upload);

            $content = [
                'email' => $upload->getEmail(),
                'link' => $request->getUri() . $hashEmail . '/' . $hashFile
            ];
            $this->sendEmail($content);

            return new Response();
        }

        return $this->render('default/index.html.twig', [
                    'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/result", name="result")
     *
     */
    public function resultAction()
    {
        return $this->render('AppBundle:File:result.html.twig');
    }

    /**
     * List of all files.
     *
     * @Route("/list", name="list")
     */
    public function listAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $dql = "SELECT f FROM AppBundle:File f";
        $query = $em->createQuery($dql);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $request->query->getInt('page', 1)/* page number */, 10/* limit per page */
        );

        return $this->render('AppBundle:File:list.html.twig', array('pagination' => $pagination));
    }

    /**
     * Displays a form to edit an existing file entity.
     *
     * @Route("/list/{id}", name="file_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, File $file)
    {
        $deleteForm = $this->createDeleteForm($file);
        $editForm = $this->createEditForm($file->getInfo());
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $data = $editForm->getData();
            $file->setInfo($data['info']);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('file_edit', array('id' => $file->getId()));
        }

        return $this->render('AppBundle:File:edit.html.twig', array(
                    'file' => $file,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a file entity.
     *
     * @Route("/{id}", name="file_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, File $file)
    {
        $form = $this->createDeleteForm($file);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->remove($file);
            $em->flush($file);

            $path = $this->getParameter('upload_directory') .
                    $file->getHashEmail() . '/' . $file->getHashFile();
            if (file_exists($path)) {
                unlink($path);
            }
        }

        return $this->redirectToRoute('list');
    }

    /**
     * Creates a form to delete a file entity.
     *
     * @param File $file The file entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(File $file)
    {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('file_delete', array('id' => $file->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

    /**
     * Send email to RabbitMQ 
     */
    private function sendEmail($content)
    {
        if (!$content) {
            return $this->redirectToRoute('homepage');
        }

        $rabbit = $this->get('rabbit');
        $rabbit->send($content);
    }

    /**
     * Form for edit info file
     */
    private function createEditForm($info)
    {
        return $this->createFormBuilder()
                        ->add('info', TextareaType::class, [
                            'data' => $info,
                            'attr' => [
                                'autofocus' => true,
                                'maxlength' => 150,
                                'style' => 'resize: none'
                            ]
                        ])
                        ->getForm();
    }

}
