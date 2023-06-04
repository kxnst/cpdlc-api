<?php

namespace App\Entity;

use App\Entity\Trait\IdTrait;
use App\Entity\Trait\TimestampableInterface;
use App\Entity\Trait\TimestampableTrait;
use App\Repository\SessionRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;


#[ORM\Entity(repositoryClass: SessionRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\UniqueConstraint(fields: ['sessionId'])]
class Session implements TimestampableInterface
{
    use IdTrait;
    use TimestampableTrait;

    public const STATUS_OPEN = 0;

    public const STATUS_PENDING = 1;

    public const STATUS_CLOSED = 2;

    public const STATUS_ERROR = 4;

    #[ORM\Column(type: Types::STRING)]
    private string $status;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isSuccess;

    #[ORM\Column(type: Types::STRING, length: 20)]
    private string $sessionId;

    #[ORM\Column(type: Types::STRING, length: 50)]
    private string $initiatorIdentifier;

    #[ORM\Column(type: Types::STRING, length: 50)]
    private string $receiverIdentifier;

    #[ORM\ManyToOne(targetEntity: Standard::class, inversedBy: 'session')]
    #[ORM\JoinColumn(name: 'standard_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Standard $standard;

    #[ORM\OneToMany(
        mappedBy: 'session',
        targetEntity: Message::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $messages;

    #[ORM\Column(type: Types::JSON, length: 50)]
    private array $variables;

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    public function setSessionId(string $sessionId): void
    {
        $this->sessionId = $sessionId;
    }

    public function getInitiatorIdentifier(): string
    {
        return $this->initiatorIdentifier;
    }

    public function setInitiatorIdentifier(string $initiatorIdentifier): void
    {
        $this->initiatorIdentifier = $initiatorIdentifier;
    }

    public function getReceiverIdentifier(): string
    {
        return $this->receiverIdentifier;
    }

    public function setReceiverIdentifier(string $receiverIdentifier): void
    {
        $this->receiverIdentifier = $receiverIdentifier;
    }

    public function isSuccess(): bool
    {
        return $this->isSuccess;
    }

    public function setIsSuccess(bool $isSuccess): void
    {
        $this->isSuccess = $isSuccess;
    }

    public function getStandard(): Standard
    {
        return $this->standard;
    }

    public function setStandard(Standard $standard): void
    {
        $this->standard = $standard;
    }

    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function setMessages(Collection $messages): void
    {
        $this->messages = $messages;
    }

    public function getVariables(): array
    {
        return $this->variables;
    }

    public function setVariables(array $variables): void
    {
        $this->variables = $variables;
    }

    #[Pure]
    public function toArray(): array
    {
        return [
            'status' => $this->getStatus(),
            'identifier' => $this->sessionId ?? null,
            'sender' => $this->initiatorIdentifier,
            'receiver' => $this->receiverIdentifier,
            'isSuccess' => $this->isSuccess
        ];
    }

    public function initIdentifier()
    {
        if (!isset($this->sessionId)) {
            $this->setSessionId('SESSION_' . sprintf("%08d", rand(1, 1000000)));
        }
    }
}