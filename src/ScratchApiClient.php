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

    /**
     * @param int $projectId
     * @return array
     */
    public function getProject($projectId)
    {
        $response = $this->httpClient->get('http://projects.scratch.mit.edu/internalapi/project/'.$projectId.'/get/');
        if (200 != $response->getStatusCode()) {
            throw new \RuntimeException('Could not fetch json for project '.$projectId);
        }

        $json = $response->getBody()->getContents();
        $data = json_decode($json, true);
        if (!$data) {
            throw new \RuntimeException('Could not decode project json'.$json);
        }

        return $data;
    }
}
