<?php

namespace AppBundle\Controller;

use AppBundle\Entity\File;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
                'link' => 'http://mediastorage.app/uploads/' . $hashEmail . '/' . $hashFile
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
     * @Route("/admin", name="admin")
     * @Method("GET")
     */
    public function adminAction()
    {
        $em = $this->getDoctrine()->getManager();
           $files = $em->getRepository('AppBundle:File')->findAllFiles();
//        $files = $em->getRepository('AppBundle:File')->findAll();

        return $this->render('AppBundle:File:files.html.twig', array(
                    'files' => $files,
        ));
    }

    /**
     * Displays a form to edit an existing file entity.
     *
     * @Route("/admin/{id}", name="file_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, File $file)
    {
        $deleteForm = $this->createDeleteForm($file);
        $editForm = $this->createForm('AppBundle\Form\AdminFileType', $file);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted()) {
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

        return $this->redirectToRoute('admin');
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
     * Sending Email.
     */
    private function sendEmail($content)
    {
        if (!$content) {
            return $this->redirectToRoute('homepage');
        }
        $message = \Swift_Message::newInstance()
                ->setSubject('Hello from mediastorage.app!')
                ->setFrom('ms.app@example.com')
                ->setTo($content['email'])
                ->setBody(
                $this->renderView(
                        'AppBundle:File:email.html.twig', array('link' => $content['link'])
                ), 'text/html'
                )
        ;

        $this->get('mailer')->send($message);
    }

}
