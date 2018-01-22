<?php
namespace Qrawler\API;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractController
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * JobController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param array $responseArray
     * @param null $status
     * @return Response
     */
    protected function formatResponse(array $responseArray, $status = null) : Response
    {
        if (!isset($status)) {
            $status = 200;
        }
        return new JsonResponse($responseArray, $status);
    }

}