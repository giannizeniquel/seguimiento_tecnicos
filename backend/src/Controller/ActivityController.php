<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\ActivityLog;
use App\Entity\Assignment;
use App\Repository\ActivityRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/activities')]
class ActivityController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ActivityRepository $activityRepository,
        private UserRepository $userRepository,
        private ValidatorInterface $validator
    ) {
    }

    #[Route('', name: 'activity_list', methods: ['GET'])]
    public function list(Request $request, UserInterface $user): JsonResponse
    {
        $filters = $request->query->all();

        // Aplicar filtros según rol del usuario
        $qb = $this->activityRepository->createQueryBuilder('a')
            ->leftJoin('a.assignedTo', 'assigned')
            ->leftJoin('a.createdBy', 'creator')
            ->addSelect('assigned', 'creator')
            ->orderBy('a.createdAt', 'DESC');

        // Filtros comunes
        if (isset($filters['status'])) {
            $qb->andWhere('a.status = :status')
               ->setParameter('status', $filters['status']);
        }

        if (isset($filters['priority'])) {
            $qb->andWhere('a.priority = :priority')
               ->setParameter('priority', $filters['priority']);
        }

        if (isset($filters['assigned_to'])) {
            $qb->andWhere('a.assignedTo = :assignedTo')
               ->setParameter('assignedTo', $filters['assigned_to']);
        }

        // Obtener usuario completo para verificar rol
        $currentUser = $this->userRepository->findOneBy(['email' => $user->getUserIdentifier()]);

        // Filtros según rol
        if ($currentUser && $currentUser->getRole() === 'TECHNICIAN') {
            // Técnico solo ve sus asignaciones
            $qb->andWhere('a.assignedTo = :user')
               ->setParameter('user', $currentUser->getId());
        } elseif ($currentUser && $currentUser->getRole() === 'ATTENDEE') {
            // Acudiente no ve nada (solo lectura limitada)
            $qb->andWhere('1 = 0'); // No mostrar nada
        }

        $activities = $qb->getQuery()->getResult();

        $data = array_map(function (Activity $activity) {
            return $this->serializeActivity($activity);
        }, $activities);

        return new JsonResponse($data);
    }

    #[Route('', name: 'activity_create', methods: ['POST'])]
    public function create(Request $request, UserInterface $user): JsonResponse
    {
        // Obtener usuario completo para verificar rol
        $currentUser = $this->userRepository->findOneBy(['email' => $user->getUserIdentifier()]);

        // Solo admin y coordinador pueden crear actividades
        if (!$currentUser || !in_array($currentUser->getRole(), ['ADMIN', 'COORDINATOR'])) {
            return new JsonResponse(['error' => 'Access denied'], 403);
        }

        $data = json_decode($request->getContent(), true);

        $activity = new Activity();
        $activity->setTitle($data['title'] ?? '');
        $activity->setDescription($data['description'] ?? null);
        $activity->setPriority($data['priority'] ?? Activity::PRIORITY_MEDIUM);
        $activity->setLocationAddress($data['locationAddress'] ?? null);
        $activity->setCreatedBy($currentUser);

        // Validar fechas
        if (isset($data['scheduledStart'])) {
            $activity->setScheduledStart(new \DateTime($data['scheduledStart']));
        }

        if (isset($data['scheduledEnd'])) {
            $activity->setScheduledEnd(new \DateTime($data['scheduledEnd']));
        }

        // Asignar técnico si se especifica
        if (isset($data['assignedTo'])) {
            $assignedUser = $this->userRepository->find($data['assignedTo']);
            if ($assignedUser && $assignedUser->getRole() === 'TECHNICIAN') {
                $activity->setAssignedTo($assignedUser);

                // Crear asignación
                $assignment = new Assignment();
                $assignment->setActivity($activity);
                $assignment->setTechnician($assignedUser);
                $assignment->setAssignedBy($currentUser);
                $assignment->setNotes($data['assignmentNotes'] ?? null);

                $this->entityManager->persist($assignment);
            }
        }

        // Validar actividad
        $errors = $this->validator->validate($activity);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return new JsonResponse(['error' => 'Validation failed', 'details' => $errorMessages], 400);
        }

        $this->entityManager->persist($activity);

        // Crear log de actividad
        $log = new ActivityLog();
        $log->setActivity($activity);
        $log->setUser($user);
        $log->setAction(ActivityLog::ACTION_CREATED);
        $log->setNewValue($this->serializeActivity($activity));

        $this->entityManager->persist($log);
        $this->entityManager->flush();

        return new JsonResponse($this->serializeActivity($activity), 201);
    }

    #[Route('/{id}', name: 'activity_show', methods: ['GET'])]
    public function show(string $id, UserInterface $user): JsonResponse
    {
        $activity = $this->activityRepository->find($id);

        if (!$activity) {
            return new JsonResponse(['error' => 'Activity not found'], 404);
        }

        // Obtener usuario completo para verificar permisos
        $currentUser = $this->userRepository->findOneBy(['email' => $user->getUserIdentifier()]);

        // Verificar permisos
        if ($currentUser && $currentUser->getRole() === 'TECHNICIAN' && $activity->getAssignedTo() !== $currentUser) {
            return new JsonResponse(['error' => 'Access denied'], 403);
        }

        if ($currentUser && $currentUser->getRole() === 'ATTENDEE') {
            return new JsonResponse(['error' => 'Access denied'], 403);
        }

        return new JsonResponse($this->serializeActivity($activity));
    }

    #[Route('/{id}', name: 'activity_update', methods: ['PUT'])]
    public function update(string $id, Request $request, UserInterface $user): JsonResponse
    {
        $activity = $this->activityRepository->find($id);

        if (!$activity) {
            return new JsonResponse(['error' => 'Activity not found'], 404);
        }

        // Obtener usuario completo para verificar permisos
        $currentUser = $this->userRepository->findOneBy(['email' => $user->getUserIdentifier()]);

        // Verificar permisos
        if (!$currentUser || !in_array($currentUser->getRole(), ['ADMIN', 'COORDINATOR'])) {
            return new JsonResponse(['error' => 'Access denied'], 403);
        }

        $oldData = $this->serializeActivity($activity);
        $data = json_decode($request->getContent(), true);

        // Actualizar campos permitidos
        if (isset($data['title'])) {
            $activity->setTitle($data['title']);
        }

        if (isset($data['description'])) {
            $activity->setDescription($data['description']);
        }

        if (isset($data['priority'])) {
            $activity->setPriority($data['priority']);
        }

        if (isset($data['locationAddress'])) {
            $activity->setLocationAddress($data['locationAddress']);
        }

        if (isset($data['scheduledStart'])) {
            $activity->setScheduledStart(new \DateTime($data['scheduledStart']));
        }

        if (isset($data['scheduledEnd'])) {
            $activity->setScheduledEnd(new \DateTime($data['scheduledEnd']));
        }

        // Validar actividad
        $errors = $this->validator->validate($activity);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return new JsonResponse(['error' => 'Validation failed', 'details' => $errorMessages], 400);
        }

        $this->entityManager->flush();

        // Crear log de actividad
        $log = new ActivityLog();
        $log->setActivity($activity);
        $log->setUser($currentUser);
        $log->setAction(ActivityLog::ACTION_STATUS_CHANGED);
        $log->setOldValue($oldData);
        $log->setNewValue($this->serializeActivity($activity));

        $this->entityManager->persist($log);
        $this->entityManager->flush();

        return new JsonResponse($this->serializeActivity($activity));
    }

    #[Route('/{id}', name: 'activity_delete', methods: ['DELETE'])]
    public function delete(string $id, UserInterface $user): JsonResponse
    {
        $activity = $this->activityRepository->find($id);

        if (!$activity) {
            return new JsonResponse(['error' => 'Activity not found'], 404);
        }

        // Obtener usuario completo para verificar permisos
        $currentUser = $this->userRepository->findOneBy(['email' => $user->getUserIdentifier()]);

        // Solo admin puede eliminar actividades
        if (!$currentUser || $currentUser->getRole() !== 'ADMIN') {
            return new JsonResponse(['error' => 'Access denied'], 403);
        }

        $this->entityManager->remove($activity);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Activity deleted successfully']);
    }

    #[Route('/{id}/start', name: 'activity_start', methods: ['POST'])]
    public function start(string $id, UserInterface $user): JsonResponse
    {
        $activity = $this->activityRepository->find($id);

        if (!$activity) {
            return new JsonResponse(['error' => 'Activity not found'], 404);
        }

        // Obtener usuario completo para verificar permisos
        $currentUser = $this->userRepository->findOneBy(['email' => $user->getUserIdentifier()]);

        // Solo técnico asignado puede iniciar
        if (!$currentUser || $currentUser->getRole() !== 'TECHNICIAN' || $activity->getAssignedTo() !== $currentUser) {
            return new JsonResponse(['error' => 'Access denied'], 403);
        }

        if ($activity->getStatus() !== Activity::STATUS_PENDING) {
            return new JsonResponse(['error' => 'Activity cannot be started'], 400);
        }

        $oldData = $this->serializeActivity($activity);
        $activity->setStatus(Activity::STATUS_IN_PROGRESS);
        $activity->setActualStart(new \DateTime());

        $this->entityManager->flush();

        // Crear log de actividad
        $log = new ActivityLog();
        $log->setActivity($activity);
        $log->setUser($user);
        $log->setAction(ActivityLog::ACTION_STATUS_CHANGED);
        $log->setOldValue($oldData);
        $log->setNewValue($this->serializeActivity($activity));

        $this->entityManager->persist($log);
        $this->entityManager->flush();

        return new JsonResponse($this->serializeActivity($activity));
    }

    #[Route('/{id}/complete', name: 'activity_complete', methods: ['POST'])]
    public function complete(string $id, Request $request, UserInterface $user): JsonResponse
    {
        $activity = $this->activityRepository->find($id);

        if (!$activity) {
            return new JsonResponse(['error' => 'Activity not found'], 404);
        }

        // Obtener usuario completo para verificar permisos
        $currentUser = $this->userRepository->findOneBy(['email' => $user->getUserIdentifier()]);

        // Solo técnico asignado puede completar
        if (!$currentUser || $currentUser->getRole() !== 'TECHNICIAN' || $activity->getAssignedTo() !== $currentUser) {
            return new JsonResponse(['error' => 'Access denied'], 403);
        }

        if ($activity->getStatus() !== Activity::STATUS_IN_PROGRESS) {
            return new JsonResponse(['error' => 'Activity cannot be completed'], 400);
        }

        $oldData = $this->serializeActivity($activity);
        $activity->setStatus(Activity::STATUS_COMPLETED);
        $activity->setActualEnd(new \DateTime());

        $this->entityManager->flush();

        // Crear log de actividad
        $log = new ActivityLog();
        $log->setActivity($activity);
        $log->setUser($currentUser);
        $log->setAction(ActivityLog::ACTION_STATUS_CHANGED);
        $log->setOldValue($oldData);
        $log->setNewValue($this->serializeActivity($activity));

        $this->entityManager->persist($log);
        $this->entityManager->flush();

        return new JsonResponse($this->serializeActivity($activity));
    }

    #[Route('/{id}/cancel', name: 'activity_cancel', methods: ['POST'])]
    public function cancel(string $id, Request $request, UserInterface $user): JsonResponse
    {
        $activity = $this->activityRepository->find($id);

        if (!$activity) {
            return new JsonResponse(['error' => 'Activity not found'], 404);
        }

        // Obtener usuario completo para verificar permisos
        $currentUser = $this->userRepository->findOneBy(['email' => $user->getUserIdentifier()]);

        // Solo admin y coordinador pueden cancelar
        if (!$currentUser || !in_array($currentUser->getRole(), ['ADMIN', 'COORDINATOR'])) {
            return new JsonResponse(['error' => 'Access denied'], 403);
        }

        if (in_array($activity->getStatus(), [Activity::STATUS_COMPLETED, Activity::STATUS_CANCELLED])) {
            return new JsonResponse(['error' => 'Activity cannot be cancelled'], 400);
        }

        $oldData = $this->serializeActivity($activity);
        $activity->setStatus(Activity::STATUS_CANCELLED);

        $this->entityManager->flush();

        // Crear log de actividad
        $log = new ActivityLog();
        $log->setActivity($activity);
        $log->setUser($user);
        $log->setAction(ActivityLog::ACTION_STATUS_CHANGED);
        $log->setOldValue($oldData);
        $log->setNewValue($this->serializeActivity($activity));

        $this->entityManager->persist($log);
        $this->entityManager->flush();

        return new JsonResponse($this->serializeActivity($activity));
    }

    private function serializeActivity(Activity $activity): array
    {
        return [
            'id' => $activity->getId(),
            'title' => $activity->getTitle(),
            'description' => $activity->getDescription(),
            'status' => $activity->getStatus(),
            'priority' => $activity->getPriority(),
            'scheduledStart' => $activity->getScheduledStart()?->format('Y-m-d H:i:s'),
            'scheduledEnd' => $activity->getScheduledEnd()?->format('Y-m-d H:i:s'),
            'actualStart' => $activity->getActualStart()?->format('Y-m-d H:i:s'),
            'actualEnd' => $activity->getActualEnd()?->format('Y-m-d H:i:s'),
            'locationAddress' => $activity->getLocationAddress(),
            'createdBy' => [
                'id' => $activity->getCreatedBy()->getId(),
                'name' => $activity->getCreatedBy()->getName(),
                'email' => $activity->getCreatedBy()->getEmail()
            ],
            'assignedTo' => $activity->getAssignedTo() ? [
                'id' => $activity->getAssignedTo()->getId(),
                'name' => $activity->getAssignedTo()->getName(),
                'email' => $activity->getAssignedTo()->getEmail()
            ] : null,
            'createdAt' => $activity->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $activity->getUpdatedAt()->format('Y-m-d H:i:s')
        ];
    }
}
