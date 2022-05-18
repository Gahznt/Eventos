<?php

namespace App\Bundle\Base\Services;

use App\Bundle\Base\Contracts\ServiceBase;
use App\Bundle\Base\Contracts\ServiceInterface;
use App\Bundle\Base\Repository\UserAssociationRepository;

/**
 * Class UserAssociation
 * @package App\Bundle\Base\Services
 */
class UserAssociation extends ServiceBase implements ServiceInterface
{
    private $repository;


    public function __construct(UserAssociationRepository $userAssociationRepository)
    {
        $this->repository = $userAssociationRepository;
    }

   public function getByUser($user)
   {
       return $this->repository->findOneBy(['user' => $user], ['createdAt' => 'desc']);
   }
}