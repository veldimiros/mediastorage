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

            $hashEmail = md5($upload->getHashEmail());
            $upload->setHashEmail($hashEmail);

            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $upload->getHashFile();

            // Generate a unique name for the file before saving it
            $hashFile = md5(uniqid()) . '.' . $file->guessExtension();
            $upload->setHashFile($hashFile);

            // Move the file to the directory where song are stored
            $file->move(
                    $this->getParameter('upload_directory') . "/$hashEmail", $hashFile
            );

            $em = $this->getDoctrine()->getManager();
            $em->persist($upload);
            $em->flush($upload);

            return new Response();
        }

        return $this->render('default/index.html.twig', [
                    'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/send", name="send")
     *
     */
    public function sendAction()
    {
        return $this->render('default/done.html.twig');
    }

}
