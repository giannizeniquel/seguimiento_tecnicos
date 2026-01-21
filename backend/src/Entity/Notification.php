<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
#[ORM\Table(name: 'notifications')]
#[ORM\Index(columns: ['user_id', 'is_read'])]
class Notification
{
    public const TYPE_EMAIL = 'EMAIL';
    public const TYPE_PUSH = 'PUSH';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private string $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[Assert\NotNull(message: 'El usuario es obligatorio')]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Activity::class)]
    #[ORM\JoinColumn(name: 'activity_id', referencedColumnName: 'id', nullable: true)]
    private ?Activity $activity;

    #[ORM\Column(type: Types::STRING, length: 20)]
    #[Assert\NotBlank(message: 'El tipo de notificación es obligatorio')]
    #[Assert\Choice(choices: [self::TYPE_EMAIL, self::TYPE_PUSH], message: 'Tipo de notificación no válido')]
    private string $type;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank(message: 'El título es obligatorio')]
    #[Assert\Length(min: 3, max: 255, minMessage: 'El título debe tener al menos 3 caracteres', maxMessage: 'El título no puede tener más de 255 caracteres')]
    private string $title;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'El mensaje es obligatorio')]
    #[Assert\Length(min: 5, max: 2000, minMessage: 'El mensaje debe tener al menos 5 caracteres', maxMessage: 'El mensaje no puede tener más de 2000 caracteres')]
    private string $message;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Assert\Type('bool', message: 'El estado de lectura no es válido')]
    private bool $isRead = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\Type('DateTime', message: 'La fecha de envío no es válida')]
    private ?\DateTimeInterface $sentAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\Type('DateTime', message: 'La fecha de creación no es válida')]
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

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    public function setActivity(?Activity $activity): self
    {
        $this->activity = $activity;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
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

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function isRead(): bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): self
    {
        $this->isRead = $isRead;

        return $this;
    }

    public function getSentAt(): ?\DateTimeInterface
    {
        return $this->sentAt;
    }

    public function setSentAt(?\DateTimeInterface $sentAt): self
    {
        $this->sentAt = $sentAt;

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
