<?php

namespace App\Controller;

use App\Entity\Standard;
use App\Repository\HandlerRepository;
use App\Repository\StandardRepository;
use App\Service\Handling\Handlers\HandlingResult\CompletedHandlingResult;
use App\Service\Handling\MessageHandlingService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractJsonController
{
    #[Route(path: '/{slug}/receiver/', name: 'cpdlc.receive', methods: ['POST'])]
    public function receive(
        Request                $request,
        MessageHandlingService $service,
        StandardRepository     $standardRepository,
        HandlerRepository      $handlerRepository,
        string                 $slug
    ): Response
    {
        $this->initJsonParameters($request);

        $standard = $standardRepository->findBy(['slug' => $slug])[0] ?? null;
        if (!$standard) {
            throw new NotFoundHttpException('Standard not found!');
        }

        $handlerSlug = $request->get('handlerSlug');
        if ($handlerSlug) {
            $handler = $handlerRepository->findBy(['slug' => $handlerSlug, 'standard' => $standard])[0] ?? null;
        } else {
            /** @var Standard|null $standard */
            $handler = $standard->getDefaultHandler();
        }
        if (!$handler) {
            throw new NotFoundHttpException('Default handler for standard ' . $slug . ' not found!');
        }

        $data = ['request' => $request->query->all()];
        $context = ['standard' => $standard, 'handler' => $handler, 'request' => $data];
        $result = $service->processRequest(
            $data,
            $handler->getRules(),
            $context
        );

        return $this->json($result instanceof CompletedHandlingResult
            ? ['message' => $result->message->toArray(), 'result' => $result->result]
            : $result
        );
    }
}