<?php
/**
 * Created by PhpStorm.
 * User: rafik
 * Date: 20/11/19
 * Time: 21:12
 */

namespace App\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use App\Entity\Utilisateur;
use Psr\Log\LoggerInterface;


class DoctrineEvent implements EventSubscriber
{
    private $logger;

   public  function __construct(LoggerInterface $logger)
   {
       $this->logger = $logger;
   }

    public function getSubscribedEvents() {
        return array('prePersist', 'preUpdate');//les événements écoutés
    }

    public function prePersist(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        //Si c'est bien une entité utilisateur qui va être "persisté"
        if ($entity instanceof Utilisateur) {
            //on met à jour les donner
            $this->logger->info('user  prepersiste!');
        }
    }

    public function preUpdate(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        $changeset = $args->getEntityManager()->getUnitOfWork()->getEntityChangeSet($entity);
        //Si c'est bien une entité Utilisateur qui va être modifié
        if ($entity instanceof Utilisateur) {
            //Si il y'a eu une mise a jour sur les propriétés en relation avec l'entity
            if (array_key_exists("firstname", $changeset) || array_key_exists("lastname", $changeset) || array_key_exists("creationDate", $changeset) || array_key_exists("updateDate", $changeset)) {
                //on met à jour les coordonnées via l'appel à google map
                $this->logger->info('user  preupdate!');
            }
        }
    }
}