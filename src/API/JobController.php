<?php
namespace Qrawler\API;

use Qrawler\Model\Job;
use Qrawler\Model\JobQuery;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JobController extends AbstractController
{

    public function get($id): Response
    {
        $id = (int) $id;
        $job = JobQuery::create()->findOneById($id);
        if (!$job) {
            return $this->formatResponse(['error' => 'No such job'], 404);
        }
        return $this->formatResponse($job->toArray());
    }

    public function post() : Response
    {
        $url = $this->request->request->get('url');
        // Checking if there's already a job with this input
        $job = JobQuery::create()->findOneByInput($url);
        if (!$job) {
            $job = new Job($url);
            $job->save();
        }
        return $this->formatResponse($job->toArray());
    }

}
