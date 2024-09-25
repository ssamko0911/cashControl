<?php

namespace App\Tests\Functional\Controller\Api;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Kernel;

final class AccountControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testCreateAccount(): void
    {
        $this->client->request('GET', '/api/accounts/89');

        self::assertResponseHeaderSame('Content-Type', 'application/json', 'Failed');
    }
}