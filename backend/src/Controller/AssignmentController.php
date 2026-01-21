<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Assignment;
use App\Repository\ActivityRepository;
use App\Repository\AssignmentRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/assignments')]
class AssignmentController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private AssignmentRepository $assignmentRepository,
        private ActivityRepository $activityRepository,
        private UserRepository $userRepository,
        private ValidatorInterface $validator,
    ) {
    }

    #[Route('', name: 'assignment_list', methods: ['GET'])]
    public function list(Request $request, UserInterface $user): JsonResponse
    {
        $currentUser = $this->userRepository->findOneBy(['email' => $user->getUserIdentifier()]);

        $filters = $request->query->all();
        $qb = $this->assignmentRepository->createQueryBuilder('a')
            ->leftJoin('a.activity', 'activity')
            ->leftJoin('a.technician', 'technician')
            ->leftJoin('a.assignedBy', 'assignedBy')
            ->addSelect('activity', 'technician', 'assignedBy')
            ->orderBy('a.assignedAt', 'DESC');

        if (isset($filters['activity_id'])) {
            $qb->andWhere('a.activity = :activityId')
               ->setParameter('activityId', $filters['activity_id']);
        }

        if (isset($filters['technician_id'])) {
            $qb->andWhere('a.technician = :technicianId')
               ->setParameter('technicianId', $filters['technician_id']);
        }

        if (isset($filters['assigned_by'])) {
            $qb->andWhere('a.assignedBy = :assignedBy')
               ->setParameter('assignedBy', $filters['assigned_by']);
        }

        if (isset($filters['date_from'])) {
            $qb->andWhere('a.assignedAt >= :dateFrom')
               ->setParameter('dateFrom', new \DateTime($filters['date_from']));
        }

        if (isset($filters['date_to'])) {
            $qb->andWhere('a.assignedAt <= :dateTo')
               ->setParameter('dateTo', new \DateTime($filters['date_to']));
        }

        if ($currentUser && 'TECHNICIAN' === $currentUser->getRole()) {
            $qb->andWhere('a.technician = :user')
               ->setParameter('user', $currentUser->getId());
        }

        $assignments = $qb->getQuery()->getResult();

        $data = array_map(function (Assignment $assignment) {
            return $this->serializeAssignment($assignment);
        }, $assignments);

        return new JsonResponse($data);
    }

    #[Route('', name: 'assignment_create', methods: ['POST'])]
    public function create(Request $request, UserInterface $user): JsonResponse
    {
        $currentUser = $this->userRepository->findOneBy(['email' => $user->getUserIdentifier()]);

        if (!$currentUser || !in_array($currentUser->getRole(), ['ADMIN', 'COORDINATOR'])) {
            return new JsonResponse(['error' => 'Access denied'], 403);
        }

        $data = json_decode($request->getContent(), true);

        $activity = $this->activityRepository->find($data['activityId'] ?? '');
        if (!$activity) {
            return new JsonResponse(['error' => 'Activity not found'], 404);
        }

        $technician = $this->userRepository->find($data['technicianId'] ?? '');
        if (!$technician) {
            return new JsonResponse(['error' => 'Technician not found'], 404);
        }

        if ('TECHNICIAN' !== $technician->getRole()) {
            return new JsonResponse(['error' => 'User is not a technician'], 422);
        }

        if (in_array($activity->getStatus(), [Activity::STATUS_COMPLETED, Activity::STATUS_CANCELLED])) {
            return new JsonResponse(['error' => 'Cannot assign completed or cancelled activities'], 422);
        }

        $existingAssignment = $this->assignmentRepository->createQueryBuilder('a')
            ->where('a.activity = :activityId')
            ->andWhere('a.technician = :technicianId')
            ->setParameter('activityId', $activity->getId())
            ->setParameter('technicianId', $technician->getId())
            ->getQuery()
            ->getOneOrNullResult();

        if ($existingAssignment) {
            return new JsonResponse(['error' => 'Assignment already exists for this activity and technician'], 409);
        }

        $assignment = new Assignment();
        $assignment->setActivity($activity);
        $assignment->setTechnician($technician);
        $assignment->setAssignedBy($currentUser);
        $assignment->setNotes($data['notes'] ?? null);

        $errors = $this->validator->validate($assignment);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return new JsonResponse(['error' => 'Validation failed', 'details' => $errorMessages], 400);
        }

        $this->entityManager->persist($assignment);
        $this->entityManager->flush();

        return new JsonResponse($this->serializeAssignment($assignment), 201);
    }

    #[Route('/{id}', name: 'assignment_show', methods: ['GET'])]
    public function show(string $id, UserInterface $user): JsonResponse
    {
        $currentUser = $this->userRepository->findOneBy(['email' => $user->getUserIdentifier()]);

        $assignment = $this->assignmentRepository->find($id);

        if (!$assignment) {
            return new JsonResponse(['error' => 'Assignment not found'], 404);
        }

        if ($currentUser && 'TECHNICIAN' === $currentUser->getRole() && $assignment->getTechnician()->getId() !== $currentUser->getId()) {
            return new JsonResponse(['error' => 'Access denied'], 403);
        }

        if ($currentUser && !in_array($currentUser->getRole(), ['ADMIN', 'COORDINATOR', 'TECHNICIAN'])) {
            return new JsonResponse(['error' => 'Access denied'], 403);
        }

        return new JsonResponse($this->serializeAssignment($assignment));
    }

    #[Route('/{id}', name: 'assignment_update', methods: ['PUT'])]
    public function update(string $id, Request $request, UserInterface $user): JsonResponse
    {
        $currentUser = $this->userRepository->findOneBy(['email' => $user->getUserIdentifier()]);

        if (!$currentUser || !in_array($currentUser->getRole(), ['ADMIN', 'COORDINATOR'])) {
            return new JsonResponse(['error' => 'Access denied'], 403);
        }

        $assignment = $this->assignmentRepository->find($id);

        if (!$assignment) {
            return new JsonResponse(['error' => 'Assignment not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['notes'])) {
            $assignment->setNotes($data['notes']);
        }

        $errors = $this->validator->validate($assignment);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return new JsonResponse(['error' => 'Validation failed', 'details' => $errorMessages], 400);
        }

        $this->entityManager->flush();

        return new JsonResponse($this->serializeAssignment($assignment));
    }

    #[Route('/{id}', name: 'assignment_delete', methods: ['DELETE'])]
    public function delete(string $id, UserInterface $user): JsonResponse
    {
        $currentUser = $this->userRepository->findOneBy(['email' => $user->getUserIdentifier()]);

        if (!$currentUser || 'ADMIN' !== $currentUser->getRole()) {
            return new JsonResponse(['error' => 'Access denied'], 403);
        }

        $assignment = $this->assignmentRepository->find($id);

        if (!$assignment) {
            return new JsonResponse(['error' => 'Assignment not found'], 404);
        }

        $activityStatus = $assignment->getActivity()->getStatus();

        if (in_array($activityStatus, [Activity::STATUS_IN_PROGRESS, Activity::STATUS_COMPLETED])) {
            return new JsonResponse(['error' => 'Cannot delete assignment for activity in progress or completed'], 422);
        }

        $this->entityManager->remove($assignment);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Assignment deleted successfully']);
    }

    private function serializeAssignment(Assignment $assignment): array
    {
        return [
            'id' => $assignment->getId(),
            'activity' => [
                'id' => $assignment->getActivity()->getId(),
                'title' => $assignment->getActivity()->getTitle(),
                'status' => $assignment->getActivity()->getStatus(),
                'scheduledStart' => $assignment->getActivity()->getScheduledStart()->format('Y-m-d H:i:s'),
                'scheduledEnd' => $assignment->getActivity()->getScheduledEnd()?->format('Y-m-d H:i:s'),
            ],
            'technician' => [
                'id' => $assignment->getTechnician()->getId(),
                'name' => $assignment->getTechnician()->getName(),
                'email' => $assignment->getTechnician()->getEmail(),
            ],
            'assignedBy' => [
                'id' => $assignment->getAssignedBy()->getId(),
                'name' => $assignment->getAssignedBy()->getName(),
                'email' => $assignment->getAssignedBy()->getEmail(),
            ],
            'notes' => $assignment->getNotes(),
            'assignedAt' => $assignment->getAssignedAt()->format('Y-m-d H:i:s'),
            'createdAt' => $assignment->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $assignment->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
