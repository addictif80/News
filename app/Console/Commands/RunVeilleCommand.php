<?php

namespace App\Console\Commands;

use App\Services\VeilleService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('veille:run')]
#[Description('Poll active source sites for keyword matches and import matching articles for admin validation')]
class RunVeilleCommand extends Command
{
    public function handle(VeilleService $veille): int
    {
        $result = $veille->run();

        $this->info("Sites interrogés : {$result['polled']} — Articles importés : {$result['imported']}");

        return self::SUCCESS;
    }
}
