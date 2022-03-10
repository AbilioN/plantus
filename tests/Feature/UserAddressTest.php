<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAddressTest extends TestCase
{
    use RefreshDatabase, DatabaseMigrations;


    public function setUp(): void
    {
        

        parent::setUp();

        // Storage::fake(Document::DEFAULT_DISK);

        $this->pdf_file = UploadedFile::fake()->create('document.pdf', 1023, 'application/pdf'); // arquivo .pdf com 1023kb
        $this->doc_file = UploadedFile::fake()->create('document.doc', 1023, 'application/msword'); // arquivo .doc com 1023kb
        $this->docx_file = UploadedFile::fake()->create('document.docx', 1023, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'); // arquivo .docx com 1023kb

        $this->user = factory(User::class)->create([
            'cpf' => '10068673418'
        ]);

        $this->userAddress = [
                'street' => 'rua dos salmos',
                'number' => 53,
                'cep'  => '59123525',
                'neighborhood' => 'pajucara',
                'state' => 'RN',
                'city' => 'Natal',
                'adjunct' => '',
                'file' => $this->pdf_file
        ];

        // $this->withExceptionHandling();
        $this->withoutExceptionHandling();

    }

    /**
     * A basic test example.
     *
     * @return void
     */
    /** @test */ 
    public function a_address_can_be_associated_to_an_user()
    {
        $response = $this->actingAs($this->user)->post('api/user/address/update' , $this->userAddress);
        $response->assertStatus(200);
    }
}
