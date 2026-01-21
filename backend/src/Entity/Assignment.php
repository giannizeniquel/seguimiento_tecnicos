<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AssignmentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AssignmentRepository::class)]
#[ORM\Table(name: 'assignments')]
#[ORM\HasLifecycleCallbacks]
class Assignment
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Activity::class, inversedBy: 'assignments')]
    #[ORM\JoinColumn(name: 'activity_id', referencedColumnName: 'id')]
    #[Assert\NotNull(message: 'La actividad es obligatoria')]
    private Activity $activity;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'technician_id', referencedColumnName: 'id')]
    #[Assert\NotNull(message: 'El técnico es obligatorio')]
    private User $technician;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'assigned_by', referencedColumnName: 'id')]
    #[Assert\NotNull(message: 'El usuario que asigna es obligatorio')]
    private User $assignedBy;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\Type('DateTime', message: 'La fecha de asignación no es válida')]
    private \DateTimeInterface $assignedAt;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 2000, maxMessage: 'Las notas no pueden tener más de 2000 caracteres')]
    private ?string $notes;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::uuid4()->toString();
        $this->assignedAt = new \DateTime();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getActivity(): Activity
    {
        return $this->activity;
    }

    public function setActivity(Activity $activity): self
    {
        $this->activity = $activity;

        return $this;
    }

    public function getTechnician(): User
    {
        return $this->technician;
    }

    public function setTechnician(User $technician): self
    {
        $this->technician = $technician;

        return $this;
    }

    public function getAssignedBy(): User
    {
        return $this->assignedBy;
    }

    public function setAssignedBy(User $assignedBy): self
    {
        $this->assignedBy = $assignedBy;

        return $this;
    }

    public function getAssignedAt(): \DateTimeInterface
    {
        return $this->assignedAt;
    }

    public function setAssignedAt(\DateTimeInterface $assignedAt): self
    {
        $this->assignedAt = $assignedAt;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->updatedAt = new \DateTime();
    }
}
