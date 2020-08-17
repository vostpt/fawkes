<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ImageGetRequest;
use App\Http\Requests\ImageUploadRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    public function upload(ImageUploadRequest $request)
    {
        $uuid = Str::uuid();
        $path = Storage::disk('local_photos')->put($uuid.'.'.$request->file('image')->extension(), $request->file('image')->get());
        return ['status' => 'UPLOADED','uuid' => $uuid];
    }

    public function get(ImageGetRequest $request)
    {
        $validated = $request->validated();
        $uuid      = $validated['uuid'];
        $formats   = ['jpeg','jpg', 'png', 'bmp', 'gif', 'svg', 'webp'];
        $url       = '';
        foreach ($formats as $format) {
            if (Storage::disk('local_photos')->exists($uuid.'.'.$format)) {
                return ['status' => 'NOT_PROCESSED'];
            }
            if (Storage::disk('public_photos')->exists($uuid.'.'.$format)) {
                return ['status' => 'PROCESSED','url' => $url];
            }
        }
        return ['status' => 'NOT_FOUND'];
    }
}
