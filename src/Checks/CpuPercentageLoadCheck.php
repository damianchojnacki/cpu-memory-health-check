<?php

namespace Damianchojnacki\CpuMemoryHealthCheck\Checks;

use Damianchojnacki\CpuMemoryHealthCheck\Helpers\MeasureCpuLoadPercentage;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class CpuPercentageLoadCheck extends Check
{
    protected ?string $label = 'CPU Usage';

    protected int $warningThreshold = 90;
    protected int $errorThreshold = 100;

    public function warnWhenUsedSpaceIsAbovePercentage(int $percentage): self
    {
        $this->warningThreshold = $percentage;

        return $this;
    }

    public function failWhenUsedSpaceIsAbovePercentage(int $percentage): self
    {
        $this->errorThreshold = $percentage;

        return $this;
    }

    public function run(): Result
    {
        $load = $this->measureCpuLoad();

        $result = Result::make()
            ->meta(['percentage' => $load])
            ->shortSummary("$load%");

        if ($load > $this->errorThreshold) {
            return $result->failed("CPU Load is $load% which is higher than the allowed $this->errorThreshold%.");
        }

        if ($load > $this->warningThreshold) {
            return $result->warning("CPU Load is $load% which is higher than the allowed $this->warningThreshold%.");
        }

        return $result->ok();
    }

    protected function measureCpuLoad(): int
    {
        return (new MeasureCpuLoadPercentage())->execute();
    }
}
