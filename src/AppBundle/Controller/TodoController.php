<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use AppBundle\Entity\Item;

class TodoController extends Controller {

    /**
     * @Route("/todo/index")
     */
    public function indexAction()
    {
        $items = $this->getDoctrine()
            ->getRepository('AppBundle:Item')
            ->findAll();
        
        return $this->render('todo/index.html.twig', array(
            'items' => $items,
        ));
    }

    /**
     * @Route("/todo/view/{id}")
     */
    public function viewAction(Request $request, int $id)
    {
        $item = $this->getDoctrine()
            ->getRepository('AppBundle:Item')
            ->find($id);

        $form = $this->createFormBuilder($item)
            ->add('item', TextType::class)
            ->add('done', ChoiceType::class, array(
                'choices' => array(
                    'Yes' => 1,
                    'No' => 0
                )
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Save',
                'attr' => array(
                    'class' => 'btn btn-info'
                )
            ))
            ->add('back', SubmitType::class, array(
                'label' => 'Back',
                'attr' => array(
                    'class' => 'btn btn-danger'
                )
            ))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->getData()) {
            
            if ($form->get('save')->isClicked()) {

                $item = $form->getData();
                $item->setLastmodified(new \DateTime('now'));

                $em = $this->getDoctrine()->getManager();
                $em->persist($item);
                $em->flush();

                $this->addFlash('notice', 'Item has been saved.');
            }

            return $this->redirect('/todo/index');
        }

        return $this->render('todo/create.html.twig', array(
            'item' => $item,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/todo/create")
     */
    public function createAction(Request $request)
    {
        $item = new Item;
        $form = $this->createFormBuilder($item)
            ->add('item', TextType::class)
            ->add('done', ChoiceType::class, array(
                'choices' => array(
                    'Yes' => 1,
                    'No' => 0
                )
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Save',
                'attr' => array(
                    'class' => 'btn btn-info'
                )
            ))
            ->add('back', SubmitType::class, array(
                'label' => 'Back',
                'attr' => array(
                    'class' => 'btn btn-danger'
                )
            ))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->getData()) {

            if ($form->get('save')->isClicked()) {

                $item = $form->getData();
                $item->setLastmodified(new \DateTime('now'));

                $em = $this->getDoctrine()->getManager();
                $em->persist($item);
                $em->flush();

                $this->addFlash('notice', 'Item has been created.');
            }

            return $this->redirect('/todo/index');
        }

        return $this->render('todo/create.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/todo/delete/{id}")
     */
    public function deleteAction(int $id)
    {
        $item = $this->getDoctrine()
            ->getRepository('AppBundle:Item')
            ->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($item);
        $em->flush();

        $this->addFlash( 'notice', 'Item has been deleted.');

        return $this->redirect('/todo/index');
    }
}
