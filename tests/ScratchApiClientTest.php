<?php


namespace CoderDojoNagoya\SpriteCollector;

use Goutte\Client as GoutteClient;
use GuzzleHttp\ClientInterface as GuzzleClient;
use Symfony\Component\DomCrawler\Crawler;

class ScratchApiClientTest extends \PHPUnit_Framework_TestCase
{
    public function test_getProjectsInStudio()
    {
        $studioId = '12345';
        $expect = [30377890, 27065464, 27061011, 26060304];
        $goutteClient = $this->getMock(GoutteClient::class);
        $guzzleClient = $this->getMock(GuzzleClient::class);
        $crawler = new Crawler();
        $crawler->addHtmlContent(__DIR__.'/data/studio.html');

        $goutteClient->expects($this->once())
            ->method('request')
            ->with('GET', 'https://scratch.mit.edu/site-api/projects/in/12345/1')
            ->will($this->returnValue($crawler))
        ;
        $goutteClient->expects($this->once())
            ->method('getResponse')
            ->will($this->returnValue($response = $this->getMock('Symfony\Component\BrowserKit\Response')))
        ;
        $response->expects($this->once())
            ->method('getStatus')
            ->will($this->returnValue(200))
        ;

        $client = new ScratchApiClient($goutteClient, $guzzleClient);
        $actual = $client->getProjectsInStudio($studioId);

        $this->assertEquals($expect, $actual);
    }
}
