<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ActivityLogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

#[ORM\Entity(repositoryClass: ActivityLogRepository::class)]
#[ORM\Table(name: 'activity_logs')]
#[ORM\Index(columns: ['created_at'])]
class ActivityLog
{
    public const ACTION_CREATED = 'CREATED';
    public const ACTION_UPDATED = 'UPDATED';
    public const ACTION_STATUS_CHANGED = 'STATUS_CHANGED';
    public const ACTION_ASSIGNED = 'ASSIGNED';
    public const ACTION_TIME_LOGGED = 'TIME_LOGGED';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Activity::class)]
    #[ORM\JoinColumn(name: 'activity_id', referencedColumnName: 'id')]
    private Activity $activity;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private User $user;

    #[ORM\Column(type: Types::STRING, length: 50)]
    private string $action;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $oldValue;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $newValue;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->id = Uuid::uuid4()->toString();
        $this->createdAt = new \DateTime();
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

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function setAction(string $action): self
    {
        $this->action = $action;
        return $this;
    }

    public function getOldValue(): ?array
    {
        return $this->oldValue;
    }

    public function setOldValue(?array $oldValue): self
    {
        $this->oldValue = $oldValue;
        return $this;
    }

    public function getNewValue(): ?array
    {
        return $this->newValue;
    }

    public function setNewValue(?array $newValue): self
    {
        $this->newValue = $newValue;
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
}
