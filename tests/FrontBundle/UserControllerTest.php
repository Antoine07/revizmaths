<?php

namespace Tests\FrontBundle;

use Symfony\Component\BrowserKit\Cookie;

class UserControllerTest extends BaseTest
{

    private $client = null;

    public function setUp()
    {
        parent::setUp();

        $this->client = static::createClient();
    }

    public function tearDown()
    {
        parent::tearDown(); // TODO: Change the autogenerated stub

        $this->client = null;
    }

    public function testConnexionFail()
    {

        // Create a new entry in the database
        $crawler = $this->client->request('GET', '/account/login');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /admin/user/");

        // Fill in the form and submit it
        $form = $crawler->selectButton('Connexion')->form(array(
            '_username' => 'AntoineL',
            '_password' => 'Antoine'
        ));

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('div:contains("Identifiants invalides.")')->count(), 'Missing element div:contains("Identifiants invalides.');
    }

    public function testConnexionAndRedirectToDashboard()
    {
        $userManager = $this->container->get('fos_user.user_manager');

        $userAdmin = $userManager->createUser();

        $userAdmin->setUsername('AntoineL');
        $userAdmin->setEmail('antoine@example.com');
        $userAdmin->setPlainPassword('Antoine');
        $userAdmin->setEnabled(true);
        $userAdmin->setRoles(['ROLE_ADMIN']);

        $userManager->updateUser($userAdmin, true);

        // Create a new entry in the database
        $crawler = $this->client->request('GET', '/account/login');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /admin/user/");

        // Fill in the form and submit it
        $form = $crawler->selectButton('Connexion')->form(array(
            '_username' => 'AntoineL',
            '_password' => 'Antoine'
        ));

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('h1:contains("Coucou ADMIN")')->count(), 'Missing element h1:contains("Coucou ADMIN")');

    }


    private function logIn()
    {
        $session = $this->container->get('session');
        /** @var $userManager \FOS\UserBundle\Doctrine\UserManager */
        $userManager = $this->container->get('fos_user.user_manager');

        /** @var $loginManager \FOS\UserBundle\Security\LoginManager */
        $loginManager = $this->container->get('fos_user.security.login_manager');
        $firewallName = $this->container->getParameter('fos_user.firewall_name');

        $userAdmin = $userManager->createUser();

        $userAdmin->setUsername('AntoineL');
        $userAdmin->setEmail('antoine@example.com');
        $userAdmin->setPlainPassword('Antoine');
        $userAdmin->setEnabled(true);
        $userAdmin->setRoles(['ROLE_ADMIN']);

        $userManager->updateUser($userAdmin, true);

        $loginManager->loginUser($firewallName, $userAdmin);

        // save the login token into the session and put it in a cookie
        $this->container->get('session')->set('_security_' . $firewallName,
            serialize($this->container->get('security.token_storage')->getToken()));
        $this->container->get('session')->save();
        $this->client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

        return $this->client;
    }


}