<?php

namespace Damianchojnacki\CpuMemoryHealthCheck\Checks;

use Damianchojnacki\CpuMemoryHealthCheck\Helpers\MeasureMemoryUsage;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class UsedMemoryCheck extends Check
{
    protected ?string $label = "Memory Usage";
    protected int $warningThreshold = 80;
    protected int $errorThreshold = 95;

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
        $memoryUsage = $this->getMemoryUsage();

        $memoryUsedPercentage = $memoryUsage['percentage'];

        $result = Result::make()
            ->meta(['percentage' => $memoryUsedPercentage])
            ->shortSummary($memoryUsage['status']);

        if ($memoryUsedPercentage > $this->errorThreshold) {
            return $result->failed("The memory is almost full ({$memoryUsedPercentage}% used).");
        }

        if ($memoryUsedPercentage > $this->warningThreshold) {
            return $result->warning("The memory is almost full ({$memoryUsedPercentage}% used).");
        }

        return $result->ok();
    }

    protected function getMemoryUsage(): array
    {
        return (new MeasureMemoryUsage())->execute();
    }
}
