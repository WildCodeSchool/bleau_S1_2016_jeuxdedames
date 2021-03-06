<?php

namespace CmsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use CmsBundle\Entity\Accueil;
use CmsBundle\Form\AccueilType;


/**
 * Accueil controller.
 *
 */
class AccueilController extends Controller
{
    /**
     * Creates a new Accueil entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if (!empty($em->getRepository('CmsBundle:Accueil')->findAll())){
            $accueil = $em->getRepository('CmsBundle:Accueil')->findAll();
            return $this->render('CmsBundle:User:index.html.twig', array(
                'accueils' => $accueil,
            ));
        }

        $accueil = new Accueil();

        $form = $this->createForm(AccueilType::class, $accueil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($accueil);
            $em->flush();

            return $this->redirectToRoute('cms_homepage');
        }

        return $this->render('CmsBundle:Accueil:new.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing Accueil entity.
     *
     */
    public function editAction(Request $request, Accueil $accueil)
    {
        $editForm = $this->createForm('CmsBundle\Form\AccueilType', $accueil);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $previousImg1 = $accueil->getImage();
            $previousImg2 = $accueil->getImage2()->getUrl();

            $accueil->preUpload();
            $accueil->getImage2()->preUpload();
            $em->persist($accueil);
            $em->flush();

            if ($previousImg1 != $accueil->getImage()){
                $picture = [
                    'caption' => "Retrouvez nous sur http://wwww.lesjeuxdedames.com",
                    'source' => $accueil->getAbsolutePath()
                ];
                $this->get('app_core.facebook')->postPicture($picture);
            }
            if ($previousImg2 != $accueil->getImage2()->getUrl()){
                $picture2 = [
                    'caption' => "Retrouvez nous sur http://wwww.lesjeuxdedames.com",
                    'source' => $accueil->getImage2()->getAbsolutePath()
                ];
                $this->get('app_core.facebook')->postPicture($picture2);
            }

            return $this->redirectToRoute('cms_homepage');
        }

        return $this->render('CmsBundle:Accueil:edit.html.twig', array(
            'accueil' => $accueil,
            'edit_form' => $editForm->createView(),
        ));
    }
}
