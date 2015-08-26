<?php
/**
 * This file is part of the CoderDojoNagoya.SpriteCollector
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace CoderDojoNagoya\SpriteCollector;

use Goutte\Client;
use GuzzleHttp\ClientInterface as HttpClient;

class ScratchApiClient
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var HttpClient
     */
    private $httpClient;

    public function __construct(Client $client, HttpClient $httpClient)
    {
        $this->client = $client;
        $this->httpClient = $httpClient;
    }

    /**
     * @param int $studioId
     * @return array
     */
    public function getProjectsInStudio($studioId)
    {
        $crawler = $this->client->request('GET', 'https://scratch.mit.edu/site-api/projects/in/'.$studioId.'/1');
        if ($this->client->getResponse()->getStatus() != 200) {
            throw new \RuntimeException();
        }

        $projectIds = $crawler->filter('.project .title a')->each(function(\DOMNode $node){
            return trim(str_replace('projects', '', $node->getAttribute('href') ? $node->getAttribute('href') : ''), '/');
        });

        return $projectIds;
    }
}
