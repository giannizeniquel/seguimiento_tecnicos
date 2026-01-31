<?php

namespace App\Command;

use App\Entity\User;
use App\Entity\Activity;
use App\Entity\Assignment;
use App\Repository\UserRepository;
use App\Repository\ActivityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:load-test-data',
    description: 'Carga datos de prueba para el sistema'
)]
class LoadTestDataCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository,
        private ActivityRepository $activityRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Carga de Datos de Prueba');

        // Verificar si ya existen datos
        $existingUsers = $this->userRepository->count([]);
        if ($existingUsers > 1) {
            $io->warning('Ya existen datos en la base de datos. Para recargar, borra los usuarios primero.');
            $confirm = $io->confirm('¿Desea continuar de todas formas? (Esto creará datos duplicados)', false);
            if (!$confirm) {
                return Command::SUCCESS;
            }
        }

        $createdUsers = [];
        $createdActivities = [];

        // Crear Coordinador
        $coordinator = $this->createUser(
            'coordinador@demo.com',
            'Coordinador Principal',
            'COORDINATOR',
            '3007654321',
            'coord123'
        );
        $this->entityManager->persist($coordinator);
        $createdUsers[] = [$coordinator->getEmail(), 'coord123', $coordinator->getRole()];

        // Crear Técnicos
        $technicians = [
            ['tecnico1@demo.com', 'Juan Pérez', '3001111111', 'tecnico123'],
            ['tecnico2@demo.com', 'María García', '3002222222', 'tecnico123'],
            ['tecnico3@demo.com', 'Carlos López', '3003333333', 'tecnico123'],
            ['tecnico4@demo.com', 'Ana Rodríguez', '3004444444', 'tecnico123'],
            ['tecnico5@demo.com', 'Pedro Sánchez', '3005555555', 'tecnico123'],
        ];

        $technicianEntities = [];
        foreach ($technicians as $techData) {
            $technician = $this->createUser(
                $techData[0],
                $techData[1],
                'TECHNICIAN',
                $techData[2],
                $techData[3]
            );
            $this->entityManager->persist($technician);
            $technicianEntities[] = $technician;
            $createdUsers[] = [$technician->getEmail(), $techData[3], $technician->getRole()];
        }

        // Crear actividades del coordinador
        $activitiesData = [
            [
                'title' => 'Reparación de aire acondicionado - Cliente A',
                'description' => 'Reparar aire acondicionado split en habitación principal. Cliente reporta que no enfría adecuadamente.',
                'priority' => 'HIGH',
                'scheduledStart' => '+1 day',
                'scheduledEnd' => '+1 day +2 hours',
                'locationAddress' => 'Calle 123 #45, Barrio Centro',
                'creator' => $coordinator,
                'assignTo' => $technicianEntities[0]
            ],
            [
                'title' => 'Mantenimiento preventivo - Oficina B',
                'description' => 'Realizar mantenimiento preventivo de unidades de climatización.',
                'priority' => 'MEDIUM',
                'scheduledStart' => '+2 days',
                'scheduledEnd' => '+2 days +4 hours',
                'locationAddress' => 'Av. Principal 500, Edificio Torre',
                'creator' => $coordinator,
                'assignTo' => $technicianEntities[1]
            ],
            [
                'title' => 'Instalación de refrigeración - Local Comercial',
                'description' => 'Instalar sistema de refrigeración comercial en nuevo local.',
                'priority' => 'URGENT',
                'scheduledStart' => 'now',
                'scheduledEnd' => 'now +6 hours',
                'locationAddress' => 'Calle Comercial 789, Local 12',
                'creator' => $coordinator,
                'assignTo' => $technicianEntities[0]
            ],
            [
                'title' => 'Revisión de ductos - Edificio Residencial',
                'description' => 'Inspeccionar y limpiar ductos de ventilación.',
                'priority' => 'LOW',
                'scheduledStart' => '+3 days',
                'scheduledEnd' => '+3 days +3 hours',
                'locationAddress' => 'Calle Residencial 321, Torre 3, Apt 505',
                'creator' => $coordinator,
                'assignTo' => $technicianEntities[2]
            ],
            [
                'title' => 'Reemplazo de compresor - Cliente C',
                'description' => 'Reemplazar compresor dañado en unidad de refrigeración.',
                'priority' => 'HIGH',
                'scheduledStart' => '+1 day +3 hours',
                'scheduledEnd' => '+1 day +5 hours',
                'locationAddress' => 'Carrera 45 #67-89',
                'creator' => $coordinator,
                'assignTo' => null // Sin asignar
            ],
            [
                'title' => 'Limpieza de filtros - Restaurante XYZ',
                'description' => 'Limpieza y mantenimiento de filtros de aire.',
                'priority' => 'MEDIUM',
                'scheduledStart' => '+4 days',
                'scheduledEnd' => '+4 days +2 hours',
                'locationAddress' => 'Calle Gastronomía 100',
                'creator' => $coordinator,
                'assignTo' => $technicianEntities[3]
            ],
        ];

        foreach ($activitiesData as $actData) {
            $activity = new Activity();
            $activity->setTitle($actData['title']);
            $activity->setDescription($actData['description']);
            $activity->setPriority($actData['priority']);
            $activity->setLocationAddress($actData['locationAddress']);
            $activity->setCreatedBy($actData['creator']);

            if (isset($actData['scheduledStart'])) {
                $activity->setScheduledStart(new \DateTime($actData['scheduledStart']));
            }

            if (isset($actData['scheduledEnd'])) {
                $activity->setScheduledEnd(new \DateTime($actData['scheduledEnd']));
            }

            $this->entityManager->persist($activity);

            if ($actData['assignTo']) {
                $assignment = new Assignment();
                $assignment->setActivity($activity);
                $assignment->setTechnician($actData['assignTo']);
                $assignment->setAssignedBy($actData['creator']);
                $assignment->setNotes('Asignación inicial');

                $this->entityManager->persist($assignment);
                $activity->setAssignedTo($actData['assignTo']);
            }

            $createdActivities[] = $activity->getTitle();
        }

        // Guardar todo
        $this->entityManager->flush();

        // Mostrar resumen
        $io->section('Usuarios Creados');
        $io->table(
            ['Email', 'Contraseña', 'Rol', 'Nombre'],
            array_map(function ($user) {
                return [
                    $user[0],
                    $user[1],
                    $user[2],
                    $this->userRepository->findOneBy(['email' => $user[0]])->getName()
                ];
            }, $createdUsers)
        );

        $io->section('Actividades Creadas');
        $io->listing($createdActivities);

        $io->success('Datos de prueba cargados exitosamente');

        return Command::SUCCESS;
    }

    private function createUser(string $email, string $name, string $role, string $phone, string $password): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setName($name);
        $user->setRole($role);
        $user->setPhone($phone);
        $user->setPasswordHash($this->passwordHasher->hashPassword($user, $password));
        $user->setIsActive(true);

        return $user;
    }
}
