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
     * @throws InvalidArgumentException
     */
    private function validateConfig(array $config)
    {
        if (7 !== count($config)) {
            throw new InvalidArgumentException(sprintf(
                'You must configure 7 days (%s given)',
                count($config)
            ));
        }

        foreach ($config as $dayOfWeek) {
            $this->validateDayOfWeekConfig($dayOfWeek);
        }
    }

    /**
     * @param array $dayOfWeek
     * @throws InvalidArgumentException
     */
    private function validateDayOfWeekConfig($dayOfWeek)
    {
        if (!is_array($dayOfWeek)) {
            throw new InvalidArgumentException(sprintf(
                'Each a day must be an array (%s given)',
                gettype($dayOfWeek)
            ));
        }

        $openingsCount = count($dayOfWeek);

        for ($i = 0; $i < $openingsCount; $i++) {
            $this->validateOpeningConfig($dayOfWeek[$i]);

            if (0 !== $i && $dayOfWeek[$i - 1][1] >= $dayOfWeek[$i][0]) {
                throw new InvalidArgumentException(
                    'When multiple openings, each must start after the one before ends'
                );
            }
        }
    }

    /**
     * @param array $opening
     * @throws InvalidArgumentException
     */
    private function validateOpeningConfig($opening)
    {
        if (!is_array($opening)) {
            throw new InvalidArgumentException(sprintf(
                'Each opening of a day must be an array (%s given)',
                gettype($opening)
            ));
        }

        if (2 !== count($opening)) {
            throw new InvalidArgumentException(
                'Each opening for a day must contain two values representing hours range'
            );
        }

        if (!is_int($opening[0]) || !is_int($opening[1])) {
            throw new InvalidArgumentException('Hour(s) must be an integer');
        }

        if ($opening[0] < 0 || $opening[1] > 24) {
            throw new InvalidArgumentException('Hour must be an integer within 0 and 24');
        }

        if ($opening[0] >= $opening[1]) {
            throw new InvalidArgumentException('Invalid hour(s): must be closed after opening');
        }
    }
}
