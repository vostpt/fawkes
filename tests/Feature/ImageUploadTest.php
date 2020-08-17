<?php
declare(strict_types=1);

namespace Tests\Feature\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class ImageUploadTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testRequiresImage(): void
    {
        $this->json('POST', 'api/upload')
            ->assertStatus(422)
            ->assertJson([
                'status' => 'ERROR',
                'errors' => [
                    'image' => ['IMAGE_REQUIRED'],
                ],
            ]);
    }

    public function testValidationOfImageFormat(): void
    {
        $this->json('POST', 'api/upload', [
            'image' => UploadedFile::fake()->create('image.pdf', 100),
        ])
            ->assertStatus(422)
            ->assertJson([
                'status' => 'ERROR',
                'errors' => [
                    'image' => ['IMAGE_BAD_FORMAT'],
                ],
            ]);
    }

    public function testValidationOfImageSize(): void
    {
        $this->json('POST', 'api/upload', [
            'image' => UploadedFile::fake()->image('photo.png', 1000, 1000)->size(5000),
        ])
            ->assertStatus(422)
            ->assertJson([
                'status' => 'ERROR',
                'errors' => [
                    'image' => ['IMAGE_SIZE'],
                ],
            ]);
    }

    public function testRequiredUUID(): void
    {
        $this->json('POST', 'api/get')
            ->assertStatus(422)
            ->assertJson([
                'status' => 'ERROR',
                'errors' => [
                    'uuid' => ['UUID_REQUIRED'],
                ],
            ]);
    }

    public function testUUIDFormat(): void
    {
        $this->json('POST', 'api/get', [
            'uuid' => Str::random(),
        ])
            ->assertStatus(422)
            ->assertJson([
                'status' => 'ERROR',
                'errors' => [
                    'uuid' => ['UUID_NOT_UUID'],
                ],
            ]);
    }

    public function testNotFoundUUID(): void
    {
        $this->json('POST', 'api/get', [
            'uuid' => Str::uuid(),
        ])->assertStatus(200)
            ->assertJson([
                'status' => 'NOT_FOUND',
            ]);
    }

    public function testStoresImageAndStatusReturnsUnprocessed(): void
    {
        $response = $this->json('POST', 'api/upload', [
            'image' => UploadedFile::fake()->image('photo.png')->size(500),
        ]);
        $response->assertJson(['status' => 'UPLOADED']);
        $uuid = \json_decode($response->getContent(), true)['uuid'];

        // Assert the file was stored with the response name
        Storage::disk('local_photos')->assertExists($uuid.'.png');

        // Assert if the file was not stored with the original name
        Storage::disk('local_photos')->assertMissing('photo.jpg');

        $this->json('POST', 'api/get', [
            'uuid' => $uuid,
        ])->assertStatus(200)
        ->assertJson(['status' => 'NOT_PROCESSED']);
    }

    public function testStoresImageAndStatusReturnsProceseed(): void
    {
        $response = $this->json('POST', 'api/upload', [
            'image' => UploadedFile::fake()->image('photo.png')->size(500),
        ]);
        $uuid = \json_decode($response->getContent(), true)['uuid'];

        // Simulate Fawkes Processing
        $pathSource      = Storage::disk('local_photos')->getDriver()->getAdapter()->applyPathPrefix($uuid.'.png');
        $destinationPath = Storage::disk('public_photos')->getDriver()->getAdapter()->applyPathPrefix($uuid.'.png');

        File::move($pathSource, $destinationPath);

        $response = $this->json('POST', 'api/get', [
            'uuid' => $uuid,
        ]);
        $response->assertStatus(200)
        ->assertJson(['status' => 'PROCESSED']);
    }
}
