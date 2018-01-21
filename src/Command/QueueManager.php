<?php
namespace Qrawler\Command;

use FileFetcher\FileFetcher;
use FileFetcher\FileFetchingException;
use Knp\Command\Command;
use Qrawler\Library\RemoteFileFetcherWrapper;
use Qrawler\Model\Email;
use Qrawler\Model\Job;
use Qrawler\Model\JobQuery;
use Qrawler\Model\Result;
use Qrawler\Model\Url;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class QueueManager extends Command
{
    const EMAIL_PATTERN = '/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i';
    const URL_PATTERN = '#<a[^>]*href=[\'"]((?:https?://%s)?/[^\'"]*)[\'"]#';
    // By the original requirement in the task we should only follow the links from the *original* page, not recursively.
    const MAX_DEPTH = 1;

    /**
     * @var FileFetcher
     */
    protected $fileFetcher;

    public function __construct(FileFetcher $fileFetcher)
    {
        // Only allow remote files to be fetched for security reason
        $this->fileFetcher = new RemoteFileFetcherWrapper($fileFetcher);
        parent::__construct();
    }

    public function configure()
    {
        $this
            ->setName('crawler:daemon')
            ->setDescription('Launches a crawler queue manager')
            ->setHelp('This command allows you to start a queue manager that will process the queue')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        while (true) {
            $jobs = JobQuery::create()->findByStatus(Job::STATUS_NEW);
            foreach ($jobs as $job) {
                /* @var $job Job */
                $job->setStatus(Job::STATUS_IN_PROGRESS);
                $this->processJob($job);
            }
            sleep(5);
        }
    }

    /**
     * @param Job $job
     */
    public function processJob(Job $job)
    {
        $url = $job->getInput();

        $urls = [$url => 'input']; // url links map is [url => source]
        $processedUrls = [];
        $currentDepth = 0;
        $resultArray = [
            'urls' => [],
            'emails' => []
        ];
        try {
            while ($currentDepth++ <= self::MAX_DEPTH) {
                $resultArrayCurrent = $this->processUrls($urls);
                $resultArray['urls'] += $resultArrayCurrent['urls']; // getting urls from the current page(s)
                $resultArray['emails'] += $resultArrayCurrent['emails']; // getting all emails from the current page(s)
                $processedUrls += $urls;
                $urls = array_diff_key($resultArray['urls'], $processedUrls); // Getting all URLs which are not processed yet
            }
            $result = $this->createResult($url, $resultArray['emails']);
            $job->setResult($result);
            $job->setStatus(Job::STATUS_SUCCESS);
            $result->save();
            $job->save();
        } catch (FileFetchingException $exception) {
            $job->setStatus(Job::STATUS_ERROR);
            $job->setError($exception->getMessage());
            $job->save();
        }
    }

    /**
     * @param $url
     * @param $emailsMap
     * @return Result
     */
    private function createResult($url, $emailsMap)
    {
        $result = new Result();
        $result->setInput($url);
        foreach ($emailsMap as $emailVal => $urlVal) {
            $url = new Url($urlVal, $result);
            $email = new Email($emailVal, $url, $result);

            $result->addEmail($email);
        }
        return $result;
    }

    /**
     * @param array $urls
     * @return array
     */
    private function processUrls(array $urls)
    {
        $resultArray = [
            'urls' => [],
            'emails' => [],
        ];
        foreach ($urls as $currentUrl => $source) {
            $data = $this->fetchOne($currentUrl);
            if (!isset($data['error'])) {
                $resultArray['urls'] += $data['urls'];
                $resultArray['emails'] += $data['emails'];
            }
        }
        return $resultArray;
    }

    /**
     * @param string $url
     * @return array
     */
    private function fetchOne(string $url)
    {
        $urlData = parse_url($url);
        $parsedData = [];
        $data = $this->fileFetcher->fetchFile($url);

        preg_match_all(self::EMAIL_PATTERN, $data, $matches);
        $parsedData['emails'] = array_fill_keys($matches[0], $url);
        preg_match_all(sprintf(self::URL_PATTERN, $urlData['host']), $data, $matches);
        $urls = array_map(function ($url) use ($urlData) {
            // We are aware it only supports requests on default ports (80, 443). We'll change this when we require.
            return ($url[0] === '/') ? sprintf('%s://%s%s', $urlData['scheme'], $urlData['host'], $url) : $url;
        }, $matches[1]);
        $parsedData['urls'] = array_fill_keys($urls, $url);

        return $parsedData;
    }

}
