<?php

namespace WyriHaximus\Travis;

use Iterator;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use React\Promise\CancellablePromiseInterface;
use React\Promise\ExtendedPromiseInterface;

class Builds implements Iterator, ExtendedPromiseInterface, CancellablePromiseInterface, EndpointInterface
{
    use IteratorTrait;
    use LazyPromiseTrait;
    use ParentHasClientAwareTrait;

    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(Repository $repository)
    {
        $this->setFactory(function () {
            return $this->getClient()->requestAsync($this);
        });
        $this->setParent($repository);
        $this->repository = $repository;
    }

    public function getRequest(): RequestInterface
    {
        return $this->parent->getClient()->createRequest(
            'GET',
            'repos/' . $this->repository->getRepository() . '/builds'
        );
    }

    public function fromResponse(ResponseInterface $response): EndpointInterface
    {
        $json = json_decode($response->getBody()->getContents());
        $builds = $this->createBuilds($json);
        return new BuildCollection($this->repository, $builds);
    }

    protected function createBuilds($json)
    {
        return array_map(function ($build) {
            return new Build(
                $this->repository,
                $build
            );
        }, $json->builds);
    }
}
