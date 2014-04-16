<?php

namespace CanalTP\MttBundle\Tests\Functional\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use CanalTP\MttBundle\Entity\BlockRepository;
use CanalTP\MttBundle\Tests\DataFixtures\ORM\Fixture;

class LineControllerTest extends AbstractControllerTest
{
    private function getFormRoute($type)
    {
        return $this->generateRoute(
            'canal_tp_mtt_block_edit',
            // fake params since we mock navitia
            array(
                'externalNetworkId' => Fixture::EXTERNAL_NETWORK_ID,
                'block_type' => $type,
                'dom_id' => 'text_block_2',
                'timetableId' => 1,
                'stop_point' => false
            )
        );
    }

    public function setUp()
    {
        parent::setUp();
    }

    // METH-120
    public function testCalendarsBySeason()
    {
        $navitiaMock = $this->getMockedNavitia();
        $season = $this->getRepository('CanalTPMttBundle:Season')->find(1);
        $navitiaMock->expects($this->once())
            ->method('getRouteCalendars')
            ->with(
                $this->anything(),
                $this->equalTo(Fixture::EXTERNAL_ROUTE_ID),
                $this->equalTo($season->getStartDate()),
                $this->equalTo($season->getEndDate())
            )
            ->will($this->returnValue(array()));

        $this->setService('canal_tp_mtt.navitia', $navitiaMock);
        $crawler = $this->doRequestRoute($this->getFormRoute(BlockRepository::CALENDAR_TYPE));
    }

    public function testTextBlockForm()
    {
        // Check if the form is correctly display
        $route = $this->getFormRoute(BlockRepository::TEXT_TYPE);
        $crawler = $this->doRequestRoute($route);
        // Submit form
        $title = 'I\'m just testing title of text block';
        $content = 'I\'m just testing content of text block';
        $form = $crawler->selectButton('Enregistrer')->form();
        $form['text_block[title]'] = $title;
        $form['text_block[content]'] = $content;
        $crawler = $this->client->submit($form);
        // Check if when we submit form we are redirected
        $this->assertTrue($this->client->getResponse() instanceof RedirectResponse);
        $crawler = $this->client->followRedirect();
        // Check if the value is saved correctly
        $this->assertGreaterThan(0, $crawler->filter('html:contains("' . $title . '")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("' . $content . '")')->count());

        // Check if the form is correctly display
        $route = $this->getFormRoute(BlockRepository::TEXT_TYPE);
        $crawler = $this->doRequestRoute($route);
        // Submit form
        $title = '';
        for ($i = 0; $i <= 256; $i++) {
            $title .= '*';
        }
        $content = 'I\'m just testing content of text block';
        $form = $crawler->selectButton('Enregistrer')->form();
        $form['text_block[title]'] = $title;
        $form['text_block[content]'] = $content;
        $crawler = $this->client->submit($form);
        // Check if when we submit form we are redirected
        $this->assertFalse($this->client->getResponse() instanceof RedirectResponse);
    }

    public function testImgBlockForm()
    {
        // Check if the form is correctly display
        $route = $this->getFormRoute(BlockRepository::IMG_TYPE);
        $crawler = $this->doRequestRoute($route);

        // Submit form
        $title = 'I\'m just testing title of text block';
        $content = 'I\'m just testing content of text block';
        $form = $crawler->selectButton('Enregistrer')->form();

        $form['img_block[title]'] = $title;

        $crawler = $this->client->submit($form);

        // Check if when we submit form we are redirected
        $this->assertFalse($this->client->getResponse() instanceof RedirectResponse);
    }

    public function testChoiceLayoutForm()
    {
        // Check if the form is correctly display
        $route = $this->generateRoute(
            'canal_tp_mtt_line_choose_layout',
            array(
                'externalNetworkId' => Fixture::EXTERNAL_NETWORK_ID,
                'externalRouteId' => Fixture::EXTERNAL_ROUTE_ID,
                'seasonId' => Fixture::SEASON_ID,
                'line_id' => Fixture::EXTERNAL_LINE_ID
            )
        );
        $crawler = $this->doRequestRoute($route);

        // Submit empty formtr
        $form = $crawler->selectButton('Enregistrer')->form();
        $crawler = $this->client->submit($form);

        // TODO: Check why we have redirection ?
        // Check if when we submit form we are not redirected
        $this->assertFalse($this->client->getResponse() instanceof RedirectResponse);

        $crawler = $this->doRequestRoute($route);
        // Submit form
        $form = $crawler->selectButton('Enregistrer')->form();
        $form['line_config[layout]'] = Fixture::EXTERNAL_LAYOUT_ID;
        $crawler = $this->client->submit($form);

        // Check if when we submit form we are redirected
        $this->assertTrue($this->client->getResponse() instanceof RedirectResponse);
    }
}
