<?php

namespace App\Traits;

trait TestableRenderComponent
{
    public bool $isTestModeEnabledForCurrentInstance = false;
    public array $testData = [];

    public function testRender(callable $callback)
    {
        return $callback($this->testData);
    }

    /**
     * Auto-detect if test mode should be enabled based on request __test__ route
     * @return void
     */
    public function autoDetectTestMode(): void
    {
        if(request()->is('__test__*')){
            $this->isTestModeEnabledForCurrentInstance = true;
        }
    }

}
