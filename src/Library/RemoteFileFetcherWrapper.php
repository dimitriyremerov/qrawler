<?php
namespace Qrawler\Library;

use FileFetcher\FileFetcher;
use FileFetcher\FileFetchingException;

class RemoteFileFetcherWrapper implements FileFetcher
{
    /**
     * @var FileFetcher
     */
    private $fileFetcher;

    public function __construct(FileFetcher $fileFetcher)
    {
        $this->fileFetcher = $fileFetcher;
    }

    /**
     * Returns the contents of the specified file.
     * @throws FileFetchingException
     */
    public function fetchFile(string $fileUrl): string
    {
        if (!preg_match('#^https?://#i', $fileUrl)) {
            throw new FileFetchingException(sprintf('Only HTTP and HTTPS are allowed, requested: %s', $fileUrl));
        }
        return $this->fileFetcher->fetchFile($fileUrl);
    }
}
