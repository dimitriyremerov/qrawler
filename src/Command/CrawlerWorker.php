<?php
namespace Qrawler\Command;

use FileFetcher\FileFetcher;
use FileFetcher\FileFetchingException;
use Knp\Command\Command;
use Qrawler\Model\Result;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CrawlerWorker extends Command
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
        $this->fileFetcher = $fileFetcher;
        parent::__construct();
    }

    public function configure()
    {
        $this
            ->setName('worker:crawler')
            ->setDescription('Launches a crawler worker')
            ->setHelp('This command allows you to start a worker which will process a job')
            ->addArgument('url', InputArgument::REQUIRED, 'The initial URL to crawl')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getArgument('url');
        $result = new Result();
        $result->setUrl($url);

        $urls = [$url => 'input']; // url links map is [url => source]
        $processedUrls = [];
        $currentDepth = 0;
        while ($currentDepth++ <= self::MAX_DEPTH) {
            $this->processUrls($result, $urls);
            $processedUrls += $urls;
            $urls = array_diff_key($result->getLinks(), $processedUrls); // Getting all URLs which are not processed yet
        }
        var_dump($result);
    }

    /**
     * @param Result $result
     * @param array $urls
     * @return bool
     */
    private function processUrls(Result $result, array $urls)
    {
        foreach ($urls as $currentUrl => $source) {
            $data = $this->fetchOne($currentUrl);
            if (!isset($data['error'])) {
                $result->addLinks($data['urls']);
                $result->addEmails($data['emails']);
            }
        }
        return true;
    }

    /**
     * @param string $url
     * @return array
     */
    private function fetchOne(string $url)
    {
        $urlData = parse_url($url);
        $parsedData = [];
        try {
            $data = $this->fileFetcher->fetchFile($url);

            preg_match_all(self::EMAIL_PATTERN, $data, $matches);
            $parsedData['emails'] = array_fill_keys($matches[0], $url);
            preg_match_all(sprintf(self::URL_PATTERN, $urlData['host']), $data, $matches);
            $urls = array_map(function ($url) use ($urlData) {
                // We are aware it only supports requests on default ports (80, 443). We'll change this when we require.
                return ($url[0] === '/') ? sprintf('%s://%s%s', $urlData['scheme'], $urlData['host'], $url) : $url;
            }, $matches[1]);
            $parsedData['urls'] = array_fill_keys($matches[1], $url);

        } catch (FileFetchingException $exception) {
            $parsedData['error'] = $exception->getMessage();
            //TODO Handle error
        }
        return $parsedData;
    }

}
