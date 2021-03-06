<?php
declare(strict_types=1);

namespace WyriHaximus\Travis\Resource\Sync;

use WyriHaximus\ApiClient\Resource\CallAsyncTrait;
use WyriHaximus\Travis\Resource\Repository as BaseRepository;
use function Clue\React\Block\await;
use function React\Promise\resolve;

class Repository extends BaseRepository
{
    use CallAsyncTrait;

    public function builds(): array
    {
        return $this->wait($this->observableToPromise($this->callAsync('builds')->toArray()));
    }

    public function build(int $id): Build
    {
        return $this->wait($this->callAsync('build', $id));
    }

    public function commits(): array
    {
        return $this->wait($this->observableToPromise($this->callAsync('commits')->toArray()));
    }
}
