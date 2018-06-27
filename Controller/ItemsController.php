<?php

namespace gsiwerd\MyBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use gsiwerd\MyBundle\Entity\Items;
use gsiwerd\MyBundle\Form\ItemsType;

class ItemsController extends FOSRestController
{

  public function getAllAction()
  {
    return $this->redirectToRoute('items_page', array('criteria' => 'all'));
  }

  public function getSelectedAction($criteria)
  {
    $items = $this->getItems($criteria);
    $data = new Items();

    return $this->preparePage($items, $criteria, $data, 'POST',
      $this->generateUrl('add_item', array('criteria' => $criteria)), 'Dodaj');
  }

  public function getOneAction($criteria, $id)
  {
    $items = $this->getItems($criteria);
    $single_item = $this->getDoctrine()->getRepository('MyBundle:Items')->find($id);

    return $this->preparePage($items, $criteria, $single_item, 'PUT',
      $this->generateUrl('update_item', array('criteria' => $criteria, 'id' => $id)), 'Zapisz');
  }

  public function createAction($criteria, Request $request)
  {
    $form = $request->request->get('items');
    $data = $this->createItemFromForm($form);
    try {
      $manager = $this->getDoctrine()->getManager();
      $manager->persist($data);
      $manager->flush();
    } catch(\Doctrine\DBAL\DBALException $e) {
      // TODO add error message
    }

    return $this->redirectToRoute('items_page', array('criteria' => $criteria));
  }

  public function updateAction($criteria, $id, Request $request)
  {
    $form = $request->request->get('items');
    $data = $this->createItemFromForm($form);
    try {
      $manager = $this->getDoctrine()->getManager();
      $item = $this->getDoctrine()->getRepository('MyBundle:Items')->find($id);
      $item->setName($data->getName());
      $item->setAmount($data->getAmount());
      $manager->flush();
    } catch(\Doctrine\DBAL\DBALException $e) {
      // TODO add error message
    }

    return $this->redirectToRoute('items_page', array('criteria' => $criteria));
  }

  public function deleteAction($criteria, $id)
  {
    try {
      $manager = $this->getDoctrine()->getManager();
      $item = $this->getDoctrine()->getRepository('MyBundle:Items')->find($id);
      $manager->remove($item);
      $manager->flush();
    } catch(\Doctrine\DBAL\DBALException $e) {
      // TODO add error message
    }

    return $this->redirectToRoute('items_page', array('criteria' => $criteria));
  }

  private function preparePage($items, $criteria, $form_data, $method, $action, $label)
  {
    $default_url = $this->generateUrl('default_page');

    $form = $this->createForm(
      ItemsType::class,
      $form_data,
      array('action' => $action, 'method' => $method, 'label' => $label)
    );

    if($method == 'PUT') {
      $form->add('delete', ButtonType::class, array('label' => 'Usuń',  'attr' => array('onclick' => "
        if (confirm('Na pewno usunąć?')) {
          var input = document.getElementsByName('_method')[0];
          input.setAttribute('value', 'DELETE');
          document.getElementById('item_edit_form').submit();
        }
        return false;")));
    }

    $content = $this->renderView(
        'MyBundle:Items:index.html.twig',
        array('form' => $form->createView(),
          'items' => $items,
          'default_url' => $default_url,
          'criteria' => $criteria
        )
    );
    $response = new Response($content , 200);
    $response->headers->set('Content-Type', 'text/html');

    return $response;
  }

  private function createItemFromForm($form)
  {
    $data = new Items();
    $name = $form['name'];
    $amount = $form['amount'];
    $data->setName($name);
    $data->setAmount($amount);

    return $data;
  }

  private function getItems($criteria)
  {
    if ($criteria == 'available') {
      $items = $this->getDoctrine()->getRepository('MyBundle:Items')->findAvailable();
    } elseif ($criteria == 'not_available') {
      $items = $this->getDoctrine()->getRepository('MyBundle:Items')->findNotAvailable();
    } elseif ($criteria == 'more_than_five') {
      $items = $this->getDoctrine()->getRepository('MyBundle:Items')->findMoreThanFiveAvailable();
    } else {
      $items = $this->getDoctrine()->getRepository('MyBundle:Items')->findAll();
    }
    return $items;
  }
}
