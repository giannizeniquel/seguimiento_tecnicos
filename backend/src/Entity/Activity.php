<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ActivityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ActivityRepository::class)]
#[ORM\Table(name: 'activities')]
#[ORM\HasLifecycleCallbacks]
class Activity
{
    public const STATUS_PENDING = 'PENDING';
    public const STATUS_IN_PROGRESS = 'IN_PROGRESS';
    public const STATUS_COMPLETED = 'COMPLETED';
    public const STATUS_CANCELLED = 'CANCELLED';

    public const PRIORITY_LOW = 'LOW';
    public const PRIORITY_MEDIUM = 'MEDIUM';
    public const PRIORITY_HIGH = 'HIGH';
    public const PRIORITY_URGENT = 'URGENT';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private string $id;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank(message: 'El título es obligatorio')]
    #[Assert\Length(min: 3, max: 255, minMessage: 'El título debe tener al menos 3 caracteres', maxMessage: 'El título no puede tener más de 255 caracteres')]
    private string $title;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 2000, maxMessage: 'La descripción no puede tener más de 2000 caracteres')]
    private ?string $description;

    #[ORM\Column(type: Types::STRING, length: 50)]
    #[Assert\NotBlank(message: 'El estado es obligatorio')]
    #[Assert\Choice(choices: [self::STATUS_PENDING, self::STATUS_IN_PROGRESS, self::STATUS_COMPLETED, self::STATUS_CANCELLED], message: 'Estado no válido')]
    private string $status = self::STATUS_PENDING;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: 'La fecha de inicio programada es obligatoria')]
    #[Assert\Type('DateTime', message: 'La fecha de inicio programada no es válida')]
    private \DateTimeInterface $scheduledStart;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\Type('DateTime', message: 'La fecha de fin programada no es válida')]
    #[Assert\Expression(expression: 'this.scheduledEnd === null or this.scheduledEnd >= this.scheduledStart', message: 'La fecha de fin programada debe ser posterior a la fecha de inicio')]
    private ?\DateTimeInterface $scheduledEnd;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\Type('DateTime', message: 'La fecha de inicio real no es válida')]
    private ?\DateTimeInterface $actualStart;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\Type('DateTime', message: 'La fecha de fin real no es válida')]
    #[Assert\Expression(expression: 'this.actualEnd === null or this.actualStart === null or this.actualEnd >= this.actualStart', message: 'La fecha de fin real debe ser posterior a la fecha de inicio real')]
    private ?\DateTimeInterface $actualEnd;

    #[ORM\Column(type: Types::STRING, length: 20)]
    #[Assert\NotBlank(message: 'La prioridad es obligatoria')]
    #[Assert\Choice(choices: [self::PRIORITY_LOW, self::PRIORITY_MEDIUM, self::PRIORITY_HIGH, self::PRIORITY_URGENT], message: 'Prioridad no válida')]
    private string $priority = self::PRIORITY_MEDIUM;

    #[ORM\Column(type: Types::STRING, length: 500, nullable: true)]
    #[Assert\Length(max: 500, maxMessage: 'La dirección no puede tener más de 500 caracteres')]
    private ?string $locationAddress;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'created_by', referencedColumnName: 'id')]
    #[Assert\NotNull(message: 'El creador es obligatorio')]
    private User $createdBy;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'assigned_to', referencedColumnName: 'id', nullable: true)]
    private ?User $assignedTo;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::uuid4()->toString();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getScheduledStart(): \DateTimeInterface
    {
        return $this->scheduledStart;
    }

    public function setScheduledStart(\DateTimeInterface $scheduledStart): self
    {
        $this->scheduledStart = $scheduledStart;

        return $this;
    }

    public function getScheduledEnd(): ?\DateTimeInterface
    {
        return $this->scheduledEnd;
    }

    public function setScheduledEnd(?\DateTimeInterface $scheduledEnd): self
    {
        $this->scheduledEnd = $scheduledEnd;

        return $this;
    }

    public function getActualStart(): ?\DateTimeInterface
    {
        return $this->actualStart;
    }

    public function setActualStart(?\DateTimeInterface $actualStart): self
    {
        $this->actualStart = $actualStart;

        return $this;
    }

    public function getActualEnd(): ?\DateTimeInterface
    {
        return $this->actualEnd;
    }

    public function setActualEnd(?\DateTimeInterface $actualEnd): self
    {
        $this->actualEnd = $actualEnd;

        return $this;
    }

    public function getPriority(): string
    {
        return $this->priority;
    }

    public function setPriority(string $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getLocationAddress(): ?string
    {
        return $this->locationAddress;
    }

    public function setLocationAddress(?string $locationAddress): self
    {
        $this->locationAddress = $locationAddress;

        return $this;
    }

    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getAssignedTo(): ?User
    {
        return $this->assignedTo;
    }

    public function setAssignedTo(?User $assignedTo): self
    {
        $this->assignedTo = $assignedTo;

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
