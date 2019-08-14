<?php

namespace Tests\Feature;

use Cmpsr\Package;
use Tests\TestCase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ComposerPackageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_hash_from_composer_file()
    {
        $dataHash = md5($data = $this->data());

        // Should put the composer.json file in the relevant directory...
        Storage::shouldReceive('disk->put')
            ->once()
            ->with("{$dataHash}/composer.json", $data)
            ->andReturnTrue();

        // Should call the Artisan command to install the packages...
        Artisan::shouldReceive('call')->once()->with("composer:install {$dataHash}");

        // Should check if the package was created in the right directory
        Storage::shouldReceive('disk->exists')
            ->once()
            ->with("{$dataHash}.zip")
            ->andReturnTrue();

        $this->postJson('/install', ['data' => $data])
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson([
                'status' => true,
                'hash' => $dataHash,
                'url' => asset("packages/{$dataHash}.zip"),
            ]);

        // Should save the entry to the database...
        $this->assertDatabaseHas('packages', ['hash' => $dataHash]);
    }

    /** @test */
    public function it_fetches_existing_packages_from_hash()
    {
        $hash = factory(Package::class)->create()['hash'];

        $this->getJson("/fetch/{$hash}")
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson([
                'hash' => $hash,
                'url' => asset("packages/{$hash}.zip")
            ]);
    }

    /**
     * @param  string $contents
     * @return void
     */
    protected function json_recode(string $contents): string
    {
        return json_encode(json_decode($contents));
    }

    /**
     * @return string
     */
    protected function data(): string
    {
        return $this->json_recode(
            Storage::disk('test')->get('files/composer.json')
        );
    }
}
