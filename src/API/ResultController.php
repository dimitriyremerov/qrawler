<?php
namespace Qrawler\API;

use Qrawler\Model\Email;
use Qrawler\Model\ResultQuery;
use Qrawler\Model\UrlQuery;
use Symfony\Component\HttpFoundation\Response;

class ResultController extends AbstractController
{

    public function get($id): Response
    {
        $id = (int) $id;
        $result = ResultQuery::create()->findOneById($id);
        if (!$result) {
            return $this->formatResponse(['error' => 'No such result'], 404);
        }
        $emails = $result->getEmails();
        $emailIds = [];
        foreach ($emails as $email) {
            /* @var $email Email */
            $emailIds[] = $email->getId();
            $emailsOutput[$email->getId()] = $email->toArray();
        }

        $urls = UrlQuery::create()->findByResultId($result->getId());
        $urlsById = [];
        foreach ($urls as $url) {
            $urlsById[$url->getId()] = $url;
        }

        $emailsOutput = array_map(function($emailRecord) use ($urlsById) {
                return [
                    'Email' => $emailRecord['Email'],
                    'Url' => $urlsById[$emailRecord['UrlId']]->getUrl(),
                ];
            },
            $emails->toArray()
        );

        return $this->formatResponse($result->toArray() + ['Emails' => $emailsOutput]);
    }

}
