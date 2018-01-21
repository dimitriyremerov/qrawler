<?php

namespace Qrawler\Model;

class Result
{
    /**
     * @var string
     */
    private $url;
    /**
     * @var array
     */
    private $links = [];
    /**
     * @var array
     */
    private $emailsLinksMap = [];

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url)
    {
        $this->url = $url;
    }

    /**
     * @return array
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    /**
     * @param array $links
     */
    public function setLinks(array $links)
    {
        $this->links = $links;
    }

    /**
     * @param array $links
     */
    public function addLinks(array $links)
    {
        $this->links += $links;
    }

    /**
     * @return array
     */
    public function getEmailsLinksMap(): array
    {
        return $this->emailsLinksMap;
    }

    /**
     * @param array $emailsLinksMap
     */
    public function setEmailsLinksMap(array $emailsLinksMap)
    {
        $this->emailsLinksMap = $emailsLinksMap;
    }

    /**
     * @param array $emails
     */
    public function addEmails(array $emails)
    {
        $this->emailsLinksMap += $emails;
    }

}