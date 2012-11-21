<?php

namespace PiggyBox\ShopBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use PiggyBox\ShopBundle\Entity\Product;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Acl\Exception\AclAlreadyExistsException;

class ProductAcl
{
    private $container;

    /**
     * Inject the whole container because otherwise we would have a circular dependency
     * DBAL -> ORM -> EventManager -> ACL Provider
     * @link https://groups.google.com/d/topic/symfony2/MreLJgfnn_U/discussion
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $aclProvider = $this->container->get('security.acl.provider');
        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();

        if ($entity instanceof Product) {
            // Add operator rights to target so it can manage the comments on his person
                $securityIdentity = UserSecurityIdentity::fromAccount($user);
                $objectIdentity = ObjectIdentity::fromDomainObject($entity);
                try {
                    $acl = $aclProvider->createAcl($objectIdentity);
                } catch (AclAlreadyExistsException $e) {
                    $acl = $aclProvider->findAcl($objectIdentity);
                }
                $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);

            $aclProvider->updateAcl($acl);
        }
    }
}
