<?php

namespace App\Service\Handling\Handlers;

use App\Entity\Session;
use App\Repository\SessionRepository;
use App\Service\Handling\Handlers\HandlingResult\FailedHandlingResult;
use App\Service\Handling\Handlers\HandlingResult\HandlingResultInterface;
use App\Service\Handling\Handlers\HandlingResult\SessionInitSuccessHandlingResult;
use App\Service\Handling\Handlers\HandlingResult\SuccessHandlingResult;

class SessionInitHandler extends AbstractHandler
{
    public function handle(mixed $handlerValue, string $nodeName, array &$data, array &$context): HandlingResultInterface
    {
        if (empty($handlerValue)) {
            return new SuccessHandlingResult();
        }

        $initiator = $this->getNodeValue($handlerValue['initiatorPath'], $data);
        $receiver = $this->getNodeValue($handlerValue['receiverPath'], $data);

        if (!$initiator || !$receiver) {
            return new FailedHandlingResult('Participants data is invalid');
        }

        $context['sender'] = $initiator;
        $context['receiver'] = $receiver;

        if (isset($handlerValue['session_id_path'])) {
            $sessionId = $this->getNodeValue($handlerValue['session_id_path'], $data);
            if (!$sessionId) {
                return new FailedHandlingResult('Session id not provided');
            }
            /** @var SessionRepository $sessionRepository */
            $sessionRepository = $this->container->get(SessionRepository::class);
            $session = $sessionRepository
                    ->findBy(
                        ['sessionId' => $sessionId, 'standard' => $context['standard'], 'isSuccess' => true]
                    )[0] ?? null;
            if (!$session) {
                return new FailedHandlingResult('Invalid session ID');
            }

        } elseif (isset($handlerValue['createNew']) && $handlerValue['createNew']) {
            $session = new Session();
            $sessionStatus = $handlerValue['setStatus'] ?? null;
            if (is_null($sessionStatus)) {
                return new FailedHandlingResult('Default session status is not provided');
            }

            $session->setStandard($context['standard']);
            $session->setStatus($sessionStatus);
            $session->setInitiatorIdentifier($initiator);
            $session->setReceiverIdentifier($receiver);
            $session->setIsSuccess(true);

            $context['onSuccess'][] = function () use ($session) {
                $session->setIsSuccess(true);
            };
            $context['onFail'][] = function () use ($session) {
                $session->setIsSuccess(false);
            };

        } else {
            return new FailedHandlingResult('Session id not provided');
        }

        if (isset($handlerValue['setStatusOnSuccess']) && ($successStatus = $handlerValue['setStatusOnSuccess'])) {
            $context['onSuccess'][] = function () use ($session, $successStatus) {
                $session->setStatus($successStatus);
            };
        }
        if (isset($handlerValue['setStatusOnFail']) && ($failStatus = $handlerValue['setStatusOnFail'])) {
            $context['onFail'][] = function () use ($session, $failStatus) {
                $session->setStatus($failStatus);
            };
        }

        $data['session'] = $session->toArray();
        $context['session'] = $session;

        return new SessionInitSuccessHandlingResult($session);
    }
}