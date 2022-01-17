<?php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Model;

final class RefreshTokenDecorator implements OpenApiFactoryInterface
{
    private $decorated;

    public function __construct(OpenApiFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }


    public function __invoke(array $context = []): OpenApi
    {

        $openApi = ($this->decorated)($context);
        $schemas = $openApi->getComponents()->getSchemas();

        $schemas['RefreshToken'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                'refresh_token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ]);

        $schemas['CredentialsRefresh'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'refresh_token' => [
                    'type' => 'string',
                    'example' => '1231asda2323sda23123',
                ],
            ],
        ]);

        $post = new Model\Operation(
            'postCredentialsItem',
            ['RefreshToken'],
            [
                '200' => [
                    'description' => 'Get New JWT token',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/RefreshToken',
                            ],
                        ],
                    ],
                ],
            ],
            'Get JWT token to login.',
            '',
            null,
            [],
            new Model\RequestBody(
                'Generate new JWT Token',
                new \ArrayObject([
                    'application/json' => [
                        'schema' => [
                            '$ref' => '#/components/schemas/CredentialsRefresh',
                        ],
                    ],
                ])
            ),
            null,
            false,
            null,
            null,
            []
        );

        $pathItem = new Model\PathItem(
            'Refresh JWT Token',
            null,
            null,
            null,
            null,
            $post
        );
        $openApi->getPaths()->addPath('/refresh_token', $pathItem);

        return $openApi;

    }
}