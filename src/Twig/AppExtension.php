<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('base64', [$this, 'base64Encode']),
        ];
    }

    public function base64Encode($data)
    {
        // Check if $data is a resource (e.g., a stream)
        if (is_resource($data)) {
            // Read the stream and convert it to a string
            $data = stream_get_contents($data);
        }

        return base64_encode($data);
    }
}
