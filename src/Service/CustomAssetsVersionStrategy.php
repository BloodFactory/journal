<?php

namespace App\Service;

use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;

class CustomAssetsVersionStrategy implements VersionStrategyInterface
{
    public function applyVersion($path)
    {
        return sprintf('%s?%s', $path, $this->getVersion($path));
        // TODO: Implement applyVersion() method.
    }

    public function getVersion($path)
    {
        return hash_file('md5', $path);
        // TODO: Implement getVersion() method.
    }
}