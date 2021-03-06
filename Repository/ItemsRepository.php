<?php

namespace gsiwerd\MyBundle\Repository;

/**
 * ItemsRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ItemsRepository extends \Doctrine\ORM\EntityRepository
{
  public function findAll()
  {
    return $this->findBy(array(), array('id' => 'ASC'));
  }

  public function findAvailable()
  {
    $query = $this->createQueryBuilder('i')
    ->where('i.amount != :amount')
    ->setParameter('amount', '0')
    ->orderBy('i.id', 'ASC')
    ->getQuery();

    return $query->getResult();
  }

  public function findNotAvailable()
  {
    return $this->findBy(array('amount' => 0), array('id' => 'ASC'));
  }

  public function findMoreThanFiveAvailable()
  {
    $query = $this->createQueryBuilder('i')
    ->where('i.amount > :amount')
    ->setParameter('amount', '5')
    ->orderBy('i.id', 'ASC')
    ->getQuery();

    return $query->getResult();
  }
}
