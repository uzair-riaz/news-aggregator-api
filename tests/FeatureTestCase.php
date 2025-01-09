<?php

namespace Tests;

abstract class FeatureTestCase extends TestCase
{
    protected $defaultHeaders = [
        'Accept' => 'application/json'
    ];
}
