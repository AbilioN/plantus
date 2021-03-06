<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserDocumentTest extends TestCase
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
            'cpf' => '10068673418',
            'password' => 'password'
        ]);

        // $dataLogin = ['cpf' => '10068673418', 'password' => 'password'];
        // $this->login = $this->post('api/auth/login' , $dataLogin);
        // dd($this->login);
        $this->document = [
                'document' => [
                    'rg' => '2497357',
                    'date_emission' =>'02-04-2006',
                    'issuing_agency'  => 'ITEP',
                    'issuing_state' => 'RN',
                    'file' => $this->pdf_file
                ],
                'work_card' => [
                    'number' => '235554454615421',
                    'serie' => '32563',
                    'pis_pased' => '32653562553',
                    'date_emission' => '01-09-1994',
                    'file' => $this->pdf_file
                ],
                'vote_card' => [
                    'number' => '653556565423233',
                    'session' => '21',
                    'zone' => '51',
                ],
                'passport' => [
                    'passport' => '58553ABD5622',
                    'date_emission' => '01-09-1994',
                    'expiration_date'  => '01-09-2004',
                    'file' => $this->pdf_file
                ],
                'american_visas' => [
                    'number' => '653556565423233',
                    'date_emission' => '01-09-1994',
                    'expiration_date'  => '01-09-2004',
                    'file' => $this->pdf_file
                ],
                'document_data' => [
                    'gender' => 'Feminino',
                    'marital_status' => 'Casada',
                    'mother' => 'Maria Silva',
                    'father' => 'Jo??o Silva',
                    'bank_account' => 'Banco do Brasil - 001 ag??ncia 253-x - Conta Corrente 253265-1'
                ]
        ];

        $this->withoutExceptionHandling();

        // $this->withExceptionHandling();

    }
    /**
     * A basic feature test example.
     *
     * @return void
     */

    /** @test */ 
    public function a_user_document_can_be_updated()
    {

        $response = $this->actingAs($this->user)->post('api/user/documents/update' , $this->document);
        $response->assertStatus(200);
    }
}
