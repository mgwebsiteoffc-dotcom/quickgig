<?php

namespace Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

/**
 * Laravel's RefreshDatabase drives artisan through PendingCommand (which needs
 * Mockery) and re-migrates for every test. This migrates once per run against a
 * throwaway sqlite file, then wraps each test in a transaction and rolls back.
 */
trait RefreshesDatabase
{
    protected static bool $schemaReady = false;

    protected function setUp(): void
    {
        parent::setUp();

        if (! static::$schemaReady) {
            Artisan::call('migrate:fresh', ['--force' => true]);
            static::$schemaReady = true;
        }

        DB::beginTransaction();
    }

    protected function tearDown(): void
    {
        DB::rollBack();

        parent::tearDown();
    }
}
