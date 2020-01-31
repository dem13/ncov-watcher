<?php

namespace App\Services;

use App\Ncov;
use App\Ncov\Exceptions\NcovDataIsEmptyException;

class NcovService
{
    private $keys = ['deaths', 'infected', 'cured'];

    /**
     * Check if ncov model is equal to ncov data
     *
     * @param Ncov $ncov
     * @param array $ncovData
     * @return bool
     */
    public function compare(Ncov $ncov, array $ncovData): bool
    {
        foreach ($this->keys as $key) {
            if ((int)$ncovData[$key] !== $ncov->{$key}) {
                return false;
            }
        }

        return true;
    }


    /**
     * Check if ncov data is not empty
     *
     * @param array $ncov
     * @return bool
     * @throws NcovDataIsEmptyException
     */
    public function validateNcovData(array $ncov): array
    {
        $result = [];

        foreach ($this->keys as $key) {
            if (empty($ncov[$key])) {
                throw new NcovDataIsEmptyException("Ncov data is empty");
            }

            $result[$key] = $ncov[$key];
        }

        return $result;
    }
}
