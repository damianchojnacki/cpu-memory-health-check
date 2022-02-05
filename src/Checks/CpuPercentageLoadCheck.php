<?php

namespace Damianchojnacki\Checks;

use Damianchojnacki\Helpers\MeasureCpuLoadPercentage;
use Spatie\CpuLoadHealthCheck\CpuLoad;
use Spatie\CpuLoadHealthCheck\CpuLoadCheck;
use Spatie\Health\Checks\Result;

class CpuPercentageLoadCheck extends CpuLoadCheck
{
    protected ?string $label = 'CPU Usage';

    protected function measureCpuLoad(): CpuLoad
    {
        $load = (new MeasureCpuLoadPercentage())->execute();

        return new CpuLoad($load, $load, $load);
    }

    public function run(): Result
    {
        $result = parent::run();

        $result->shortSummary("{$result->meta['last_minute']}%");

        return $result;
    }
}
