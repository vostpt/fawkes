<?php
declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class RunFawkes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    private static function deleteOldFiles($disk, $path, $files): void
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $local_photos_prefix  = Storage::disk('local_photos')->getAdapter()->getPathPrefix();
        $public_photos_prefix = Storage::disk('public_photos')->getAdapter()->getPathPrefix();
        //Run Fawkes Protection
        echo("Starting fawkes\n");
        $script  = \explode(' ', env('FAWKES_EXECUTABLE').$local_photos_prefix);
        $process = new Process($script);
        $process->setTimeout(3600);
        $process->start();
        $process->wait(static function ($type, $buffer): void {
            if (Process::ERR === $type) {
                echo($buffer);
            } else {
                echo($buffer);
            }
        });
        //Move Processed Photos
        $files = Storage::disk('local_photos')->files();

        foreach ($files as $file) {
            if ($file === '.gitignore') {
                continue;
            }
            if (\mb_strpos($file, '_min_cloaked') !== false) {
                $uuid = \str_replace('_min_cloaked.png', '', $file);

                $formats = ['jpeg','jpg', 'png', 'bmp', 'gif', 'svg', 'webp'];
                foreach ($formats as $format) {
                    if (Storage::disk('local_photos')->exists($uuid.'.'.$format)) {
                        Storage::disk('local_photos')->delete($uuid.'.'.$format);
                        break;
                    }
                }

                $pathSource      = $local_photos_prefix.$file;
                $destinationPath = $public_photos_prefix.$uuid.'.png';

                File::move($pathSource, $destinationPath);
            }
        }

        //Delete Original Photos
        $files = Storage::disk('local_photos')->files();
        foreach ($files as $file) {
            if ($file === '.gitignore') {
                continue;
            }
            $fullpath = $local_photos_prefix.$file;
            Storage::disk('local_photos')->delete($file);
        }

        //Delete Old Photos
        $files = Storage::disk('public_photos')->files();
        foreach ($files as $file) {
            if ($file === '.gitignore') {
                continue;
            }
            $fullpath   = $public_photos_prefix.$file;
            $timepassed = \time() - File::lastModified($fullpath);
            if ($timepassed > env('MINUTES_TO_STORE_FILES') * 60) {
                Storage::disk('public_photos')->delete($file);
            }
        }
    }
}
