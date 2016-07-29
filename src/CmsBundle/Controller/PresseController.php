<?php

namespace CmsBundle\Controller;


use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use CmsBundle\Entity\Presse;
use CmsBundle\Form\PresseType;

/**
 * Presse controller.
 *
 */
class PresseController extends Controller
{
    /**
     * Creates a new Presse entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id_item_max = $em->getRepository('CmsBundle:Presse')->getIdItemPresse();

        $langue = new Presse();

        // dummy code - this is here just so that the Task has some tags
        // otherwise, this isn't an interesting example
        $presse_fr = new Presse();
        $presse_fr->setLangue('fr');
        $langue->getPresse()->add($presse_fr);
        $presse_en = new Presse();
        $presse_en->setLangue('en');
        $langue->getPresse()->add($presse_en);
        $presse_es= new Presse();
        $presse_es->setLangue('es');
        $langue->getPresse()->add($presse_es);

        // end dummy code

        $form = $this->createFormBuilder($langue)
            ->add('presse', CollectionType::class, array(
                'entry_type' => PresseType::class
            ))
            ->add('submit','submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $presse_fr->setItemId($id_item_max[0][1] + 1);
            $presse_en->setItemId($presse_fr->getItemId());
            $presse_es->setItemId($presse_fr->getItemId());

            $em->persist($presse_fr);

            $presse_en->setImage($presse_fr->getImage());
            $presse_es->setImage($presse_fr->getImage());

            $em->persist($presse_en);
            $em->persist($presse_es);
            $em->flush();

            return $this->redirectToRoute('user_presse');
        }

        return $this->render('@Cms/presse/new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Presse entity.
     *
     */
    public function editAction(Request $request, Presse $presse)
    {
        $em = $this->getDoctrine()->getManager();

        $id_item = $presse->getItemId();

        $presse_fr = $em->getRepository('CmsBundle:Presse')->findOneBy(array('langue' => 'fr', 'item_id' => $id_item));
        $presse_en = $em->getRepository('CmsBundle:Presse')->findOneBy(array('langue' => 'en', 'item_id' => $id_item));
        $presse_es = $em->getRepository('CmsBundle:Presse')->findOneBy(array('langue' => 'es', 'item_id' => $id_item));

        $langue = new Presse();

        // dummy code - this is here just so that the Task has some tags
        // otherwise, this isn't an interesting example
        $langue->getPresse()->add($presse_fr);
        $langue->getPresse()->add($presse_en);
        $langue->getPresse()->add($presse_es);

        $editForm = $this->createFormBuilder($langue)
            ->add('presse', CollectionType::class, array(
                'entry_type' => PresseType::class
            ))
            ->getForm();

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

//            if($editForm->get('file')->getData() != null) {
//
//                if($partenaire->getImage() != null) {
//                    unlink(__DIR__.'/../../../web/uploads/imgcms/'.$partenaire->getImage());
//                    $partenaire->setImage(null);
//                }
//            }


            $presse->preUpload();

            $em->persist($presse_fr);
            $em->persist($presse_en);
            $em->persist($presse_es);
            $em->flush();

            return $this->redirectToRoute('user_presse', array('id' => $presse->getId()));
        }

        return $this->render('CmsBundle:presse:edit.html.twig', array(
            'form' => $editForm->createView(),
        ));
    }
    /**
     * Remove an existing record and a file.
     *
     */
    public function deleteAction($id) {

        $em = $this->getDoctrine()->getManager();
        $presse = $em->getRepository('CmsBundle:Presse')->find($id);
        $presses = $em->getRepository('CmsBundle:Presse')->findAll();

        if (!$presse) {
            throw $this->createNotFoundException(
                'Pas de document trouvé' . $id
            );
        }

        $em->remove($presse);
        $em->flush();

        return $this->redirect($this->generateUrl('user_presse', array(
            'presses' => $presses,
        )));
    }
}
