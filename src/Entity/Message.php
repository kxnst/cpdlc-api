<?php

namespace App\Entity;

use App\Entity\Trait\IdTrait;
use App\Entity\Trait\TimestampableInterface;
use App\Entity\Trait\TimestampableTrait;
use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Message implements TimestampableInterface
{
    use IdTrait;
    use TimestampableTrait;

    #[ORM\ManyToOne(targetEntity: Session::class, inversedBy: 'message')]
    #[ORM\JoinColumn(name: 'session_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Session $session;

    #[ORM\Column(type: Types::JSON)]
    private array $request;

    #[ORM\Column(type: Types::JSON)]
    private array $formattedMessage;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isSuccess;

    #[ORM\Column(type: Types::STRING, length: 50)]
    private string $fromIdentifier;

    #[ORM\Column(type: Types::STRING, length: 50)]
    private string $toIdentifier;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    private ?string $messageIdentifier = null;

    public function getSession(): Session
    {
        return $this->session;
    }

    public function setSession(Session $session): void
    {
        $this->session = $session;
    }

    public function getRequest(): array
    {
        return $this->request;
    }

    public function setRequest(array $request): void
    {
        $this->request = $request;
    }

    public function getFromIdentifier(): string
    {
        return $this->fromIdentifier;
    }

    public function setFromIdentifier(string $fromIdentifier): void
    {
        $this->fromIdentifier = $fromIdentifier;
    }

    public function getToIdentifier(): string
    {
        return $this->toIdentifier;
    }

    public function setToIdentifier(string $toIdentifier): void
    {
        $this->toIdentifier = $toIdentifier;
    }

    public function getMessageIdentifier(): ?string
    {
        return $this->messageIdentifier;
    }

    public function setMessageIdentifier(?string $messageIdentifier): void
    {
        $this->messageIdentifier = $messageIdentifier;
    }

    public function getFormattedMessage(): array
    {
        return $this->formattedMessage;
    }

    public function setFormattedMessage(array $formattedMessage): void
    {
        $this->formattedMessage = $formattedMessage;
    }

    public function isSuccess(): bool
    {
        return $this->isSuccess;
    }

    public function setIsSuccess(bool $isSuccess): void
    {
        $this->isSuccess = $isSuccess;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'messageId' => $this->getMessageIdentifier(),
            'isSuccess' => $this->isSuccess(),
            'formattedMessage' => $this->getFormattedMessage(),
            'to' => $this->getToIdentifier(),
            'from' => $this->getFromIdentifier()
        ];
    }

    public function initMessageIdentifier()
    {
        if (!isset($this->sessionId)) {
            $this->setMessageIdentifier('MESSAGE_' . sprintf("%08d", rand(1, 1000000)));
        }
    }
}
