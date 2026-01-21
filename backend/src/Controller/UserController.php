<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Activity;
use App\Repository\ActivityRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/users')]
class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private ActivityRepository $activityRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private ValidatorInterface $validator
    ) {
    }

    #[Route('', name: 'user_list', methods: ['GET'])]
    public function list(Request $request, UserInterface $user): JsonResponse
    {
        $currentUser = $this->userRepository->findOneBy(['email' => $user->getUserIdentifier()]);

        if (!$currentUser || !in_array($currentUser->getRole(), ['ADMIN', 'COORDINATOR'])) {
            return new JsonResponse(['error' => 'Access denied'], 403);
        }

        $filters = $request->query->all();
        $qb = $this->userRepository->createQueryBuilder('u')
            ->orderBy('u.createdAt', 'DESC');

        if (isset($filters['role'])) {
            $qb->andWhere('u.role = :role')
               ->setParameter('role', $filters['role']);
        }

        if (isset($filters['isActive'])) {
            $qb->andWhere('u.isActive = :isActive')
               ->setParameter('isActive', $filters['isActive'] === 'true');
        }

        if (isset($filters['search'])) {
            $qb->andWhere('u.name LIKE :search OR u.email LIKE :search')
               ->setParameter('search', '%' . $filters['search'] . '%');
        }

        $users = $qb->getQuery()->getResult();

        $data = array_map(function ($user) {
            return $this->serializeUser($user);
        }, $users);

        return new JsonResponse($data);
    }

    #[Route('', name: 'user_create', methods: ['POST'])]
    public function create(Request $request, UserInterface $user): JsonResponse
    {
        $currentUser = $this->userRepository->findOneBy(['email' => $user->getUserIdentifier()]);

        if (!$currentUser || $currentUser->getRole() !== 'ADMIN') {
            return new JsonResponse(['error' => 'Access denied'], 403);
        }

        $data = json_decode($request->getContent(), true);

        $newUser = new \App\Entity\User();
        $newUser->setEmail($data['email'] ?? '');
        $newUser->setName($data['name'] ?? '');
        $newUser->setRole($data['role'] ?? 'TECHNICIAN');
        $newUser->setPhone($data['phone'] ?? null);
        $newUser->setPasswordHash($this->passwordHasher->hashPassword($newUser, $data['password'] ?? ''));

        $errors = $this->validator->validate($newUser);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return new JsonResponse(['error' => 'Validation failed', 'details' => $errorMessages], 400);
        }

        $existingUser = $this->userRepository->findOneBy(['email' => $data['email'] ?? '']);
        if ($existingUser) {
            return new JsonResponse(['error' => 'Email already exists'], 409);
        }

        $this->entityManager->persist($newUser);
        $this->entityManager->flush();

        return new JsonResponse($this->serializeUser($newUser), 201);

        $errors = $this->validator->validate($userEntity);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return new JsonResponse(['error' => 'Validation failed', 'details' => $errorMessages], 400);
        }

        $existingUser = $this->userRepository->findOneBy(['email' => $data['email'] ?? '']);
        if ($existingUser) {
            return new JsonResponse(['error' => 'Email already exists'], 409);
        }

        $this->entityManager->persist($userEntity);
        $this->entityManager->flush();

        return new JsonResponse($this->serializeUser($userEntity), 201);
    }

    #[Route('/{id}', name: 'user_show', methods: ['GET'])]
    public function show(string $id, UserInterface $user): JsonResponse
    {
        $currentUser = $this->userRepository->findOneBy(['email' => $user->getUserIdentifier()]);

        if (!$currentUser || !in_array($currentUser->getRole(), ['ADMIN', 'COORDINATOR'])) {
            return new JsonResponse(['error' => 'Access denied'], 403);
        }

        $userEntity = $this->userRepository->find($id);

        if (!$userEntity) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        return new JsonResponse($this->serializeUser($userEntity));
    }

    #[Route('/{id}', name: 'user_update', methods: ['PUT'])]
    public function update(string $id, Request $request, UserInterface $user): JsonResponse
    {
        $currentUser = $this->userRepository->findOneBy(['email' => $user->getUserIdentifier()]);

        if (!$currentUser || !in_array($currentUser->getRole(), ['ADMIN', 'COORDINATOR'])) {
            return new JsonResponse(['error' => 'Access denied'], 403);
        }

        $userEntity = $this->userRepository->find($id);

        if (!$userEntity) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if ($currentUser->getRole() === 'ADMIN') {
            if (isset($data['email']) && $data['email'] !== $userEntity->getEmail()) {
                $existingUser = $this->userRepository->findOneBy(['email' => $data['email']]);
                if ($existingUser) {
                    return new JsonResponse(['error' => 'Email already exists'], 409);
                }
                $userEntity->setEmail($data['email']);
            }

            if (isset($data['role'])) {
                if ($userEntity->getId() === $currentUser->getId()) {
                    return new JsonResponse(['error' => 'Cannot change your own role'], 422);
                }
                $userEntity->setRole($data['role']);
            }

            if (isset($data['isActive'])) {
                $userEntity->setIsActive($data['isActive']);
            }

            if (isset($data['password'])) {
                $userEntity->setPasswordHash($this->passwordHasher->hashPassword($userEntity, $data['password']));
            }
        }

        if (isset($data['name'])) {
            $userEntity->setName($data['name']);
        }

        if (isset($data['phone'])) {
            $userEntity->setPhone($data['phone']);
        }

        $errors = $this->validator->validate($userEntity);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return new JsonResponse(['error' => 'Validation failed', 'details' => $errorMessages], 400);
        }

        $this->entityManager->flush();

        return new JsonResponse($this->serializeUser($userEntity));
    }

    #[Route('/{id}', name: 'user_delete', methods: ['DELETE'])]
    public function delete(string $id, UserInterface $user): JsonResponse
    {
        $currentUser = $this->userRepository->findOneBy(['email' => $user->getUserIdentifier()]);

        if (!$currentUser || $currentUser->getRole() !== 'ADMIN') {
            return new JsonResponse(['error' => 'Access denied'], 403);
        }

        $userEntity = $this->userRepository->find($id);

        if (!$userEntity) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        if ($userEntity->getId() === $currentUser->getId()) {
            return new JsonResponse(['error' => 'Cannot delete yourself'], 422);
        }

        if ($userEntity->getRole() === 'ADMIN') {
            $adminCount = $this->userRepository->createQueryBuilder('u')
                ->select('COUNT(u.id)')
                ->where('u.role = :role')
                ->andWhere('u.isActive = :isActive')
                ->setParameter('role', 'ADMIN')
                ->setParameter('isActive', true)
                ->getQuery()
                ->getSingleScalarResult();

            if ($adminCount <= 1) {
                return new JsonResponse(['error' => 'Cannot delete the last admin'], 422);
            }
        }

        $activeActivities = $this->activityRepository->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.assignedTo = :userId')
            ->andWhere('a.status IN (:statuses)')
            ->setParameter('userId', $id)
            ->setParameter('statuses', [Activity::STATUS_PENDING, Activity::STATUS_IN_PROGRESS])
            ->getQuery()
            ->getSingleScalarResult();

        if ($activeActivities > 0) {
            return new JsonResponse(['error' => 'Cannot delete user with active activities'], 422);
        }

        $this->entityManager->remove($userEntity);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'User deleted successfully']);
    }

    #[Route('/{id}/toggle-active', name: 'user_toggle_active', methods: ['PUT'])]
    public function toggleActive(string $id, UserInterface $user): JsonResponse
    {
        $currentUser = $this->userRepository->findOneBy(['email' => $user->getUserIdentifier()]);

        if (!$currentUser || $currentUser->getRole() !== 'ADMIN') {
            return new JsonResponse(['error' => 'Access denied'], 403);
        }

        $userEntity = $this->userRepository->find($id);

        if (!$userEntity) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        if ($userEntity->getId() === $currentUser->getId()) {
            return new JsonResponse(['error' => 'Cannot deactivate yourself'], 422);
        }

        if ($userEntity->getRole() === 'ADMIN' && $userEntity->isActive()) {
            $adminCount = $this->userRepository->createQueryBuilder('u')
                ->select('COUNT(u.id)')
                ->where('u.role = :role')
                ->andWhere('u.isActive = :isActive')
                ->setParameter('role', 'ADMIN')
                ->setParameter('isActive', true)
                ->getQuery()
                ->getSingleScalarResult();

            if ($adminCount <= 1) {
                return new JsonResponse(['error' => 'Cannot deactivate the last admin'], 422);
            }
        }

        $userEntity->setIsActive(!$userEntity->isActive());
        $this->entityManager->flush();

        return new JsonResponse($this->serializeUser($userEntity));
    }

    private function serializeUser(\App\Entity\User $userEntity): array
    {
        return [
            'id' => $userEntity->getId(),
            'email' => $userEntity->getEmail(),
            'name' => $userEntity->getName(),
            'role' => $userEntity->getRole(),
            'phone' => $userEntity->getPhone(),
            'isActive' => $userEntity->isActive(),
            'createdAt' => $userEntity->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $userEntity->getUpdatedAt()->format('Y-m-d H:i:s')
        ];
    }
}
