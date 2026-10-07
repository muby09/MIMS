<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessImport implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $importData;

    public function __construct($importData)
    {
        $this->importData = $importData;
    }

    public function handle()
    {
        // TODO: process import data in the background
    }
}
