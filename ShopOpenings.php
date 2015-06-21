<?php

namespace MacDada;

use InvalidArgumentException;

class ShopOpenings
{
    /**
     * @var array
     */
    private $config;

    /**
     * @param array $config
     * @throws InvalidArgumentException
     */
    public function __construct(array $config)
    {
        $this->validateConfig($config);

        $this->config = $config;
    }

    /**
     * @param int $dayOfWeek
     * @return array
     * @throws InvalidArgumentException
     */
    public function getForDayOfWeek($dayOfWeek)
    {
        if ($dayOfWeek < 1 || $dayOfWeek > 7) {
            throw new InvalidArgumentException();
        }

        return $this->config[$dayOfWeek - 1];
    }

    /**
     * @param int $dayOfWeek
     * @return bool
     * @throws InvalidArgumentException
     */
    public function isOpenOnDayOfWeek($dayOfWeek)
    {
        return 0 !== count($this->getForDayOfWeek($dayOfWeek));
    }

    /**
     * @param array $config
     */
    private function validateConfig(array $config)
    {
        if (7 !== count($config)) {
            throw new InvalidArgumentException(sprintf('You must configure 7 days (%s given)', count($config)));
        }

        // todo: validate the openings
    }
}
