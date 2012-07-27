<?php

namespace Core\Repository;


use Doctrine\ORM\EntityRepository;
use CoreApi\Entity\User;
use CoreApi\Entity\UserRelationship;

class UserRelationshipRepository extends EntityRepository
{
	
	public function __construct($em, \Doctrine\ORM\Mapping\ClassMetadata $class)
	{
		parent::__construct($em, $class);
	}
	
	/**
	 * @param User $user1
	 * @param User $user2
	 * @return UserRelationship
	 */
	public function findByUsers(User $fromUser, User $toUser)
	{
		$query = $this->createQueryBuilder("ur")
					->where("ur.from_id = " . $fromUser)
					->andWhere("ur.to_id = " . $toUser)
					->andWhere("ur.type = " . UserRelationship::TYPE_FRIEND)
					->getQuery();
		return $query->getResult();
	}
	
	
	/**
	 * @param User $user
	 */
	public function findRequests(User $user)
	{
		$query = $this->createQueryBuilder("ur")
					->where("ur.from_id = " . $user->getId())
					->andWhere("ur.counterpart IS NULL")
					->andWhere("ur.type = " . UserRelationship::TYPE_FRIEND)
					->getQuery();
		return $query->getResult();
	}
	
	
	/**
	 * @param User $user
	 */
	public function findInvitation(User $user)
	{
		$query = $this->createQueryBuilder("ur")
					->where("ur.to_id = " . $user->getId())
					->andWhere("ur.counterpart IS NULL")
					->andWhere("ur.type = " . UserRelationship::TYPE_FRIEND)
					->getQuery();
		return $query->getResult();
	}
	
}
