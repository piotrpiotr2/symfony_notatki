<?php
/**
 * User service
 */

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;

/**
 * Class UserService
 */
class UserService
{
    /**
     * User repository
     *
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * Constructor
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Save
     *
     * @param User $user
     * @return void
     */
    public function save(User $user): void
    {
        $this->userRepository->save($user);
    }

    public function findAll(): array
    {
        return $this->userRepository->all();
    }
}