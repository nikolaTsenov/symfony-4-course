<?php
namespace App\EventListener;

use App\Entity\LikeNotification;
use App\Entity\MicroPost;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\PersistentCollection;

class LikeNotificationSubscriber implements EventSubscriber
{
    /**
     * @return string[]
     */
    public function getSubscribedEvents()
    {
        return [
            Events::onFlush
        ];
    }

    public function onFlush(OnFlushEventArgs $args)
    {
        $entityManager = $args->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();

        /** @var $collectionUpdate PersistentCollection */
        foreach ($unitOfWork->getScheduledCollectionUpdates() as $collectionUpdate) {
            if (! $collectionUpdate->getOwner() instanceof MicroPost) {
                continue;
            }

            if ('likedBy' !== $collectionUpdate->getMapping()['fieldName']) {
                continue;
            }

            $insertDiff = $collectionUpdate->getInsertDiff();
            if (! count($insertDiff)) {
                continue;
            }

            /** @var $microPost MicroPost */
            $microPost = $collectionUpdate->getOwner();

            $notification = new LikeNotification();
            $notification->setUser($microPost->getUser());
            $notification->setMicroPost($microPost);
            $notification->setLikedBy(reset($insertDiff));

            $entityManager->persist($notification);

            $unitOfWork->computeChangeSet(
                $entityManager->getClassMetadata(LikeNotification::class),
                $notification
            );
        }
    }
}
