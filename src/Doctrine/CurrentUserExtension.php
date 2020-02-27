<?php

namespace App\Doctrine;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Customer;
use App\Entity\Invoice;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

final class CurrentUserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, string $operationName = null, array $context = [])
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        // On chope l'utilisateur courant
        $user = $this->security->getUser();

        // Si l'API n'est pas en train de traiter de Customer ou d'Invoice
        // On n'intervient pas !
        if (Customer::class !== $resourceClass && Invoice::class !== $resourceClass)
            return;


        // Sinon, on va déformer la requête DQL
        $rootAlias = $queryBuilder->getRootAliases()[0];
        // Si on traite des customers
        if ($resourceClass === Customer::class) {
            // Il suffit d'ajouter un "WHERE c.user = :current_user"
            $queryBuilder->andWhere(sprintf('%s.user = :current_user', $rootAlias));
        }
        // Sinon, si on traite des invoices
        else if ($resourceClass === Invoice::class) {
            // On va faire la jointure avec le customer de l'invoice
            // et s'assurer que ce customer appartient bien à l'utilisateur
            $queryBuilder->innerJoin(sprintf('%s.customer', $rootAlias), 'c')
                ->andWhere('c.user = :current_user');
        }

        // Au final, on donne la valeur de current_user (qui est l'user connecté)
        $queryBuilder->setParameter('current_user', $user);
    }
}
