## Plataformam Plantus

Esta api refere-se ao back end da plataforma plantus.


## Descrição.

Este projeto foi criado para gerenciar dados e arquivos referentes aos usuários.
Os usuários possuem diferentes seções (roles) mas o usuário do tipo admnistrativo tem mais poder na aplicação, pois o próprio que terá acesso a dar update dos dados para si mesmo e para os demais, as rotas para atualizar os dados dos usuários é sempre a mesma porém quando um usuário do tipo Admnistrativo quiser atualizar dados de terceiros ele terá que adicionar o parametro user_id nas rotas.



## Rotas: 

## criar usuário:
Esta rota cria um usuário na plataforma e recebe como retorno um json confirmando os dados do usuário.

url : baseUrl/api/user/create
method: POST
body: {
    "name" => "nome do usuario",
    "email" => "email do usuário",
    "cpf" => "cpf do usuário",
    "password" => "password do usuário"
}


## login:
Esta rota efetua um login de um usuário e retorna um json contendo o token de acesso e quantos segundos durará a validação deste token, ao acabar este tempo um novo login deve ser efetuado    

url : baseUrl/api/auth/login
method: POST
body: {
    "cpf" => "cpf do usuário",
    "password" => "password do usuário"
}

exemplo de retorno :
{
"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY1MDI0MTA3NywiZXhwIjoxNjUwMjQ0Njc3LCJuYmYiOjE2NTAyNDEwNzcsImp0aSI6Ik10Rk92RVEyblZYdE9VWGgiLCJzdWIiOjEsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEifQ.Hem3_y-JzTIV8OhPFBfTsectpt8mGFh_DlFXgI6dYKg",
"token_type": "bearer",
"expires_in": 3600
}

## Rotas de acesso a dados

Este grupo de rotas necessita de que o token de acesso esteja no Bearer da requisição.
As rotas possuem um parâmetro opcional (user_id), caso seja enviado baixará os dados respectivo do id daquele usuário, caso não seja enviado buscará os dados validados pelo próprio Token


UserProfile:
    url: baseUrl/api/user/profile/find/{?user_id}
UserAddress:
    url: baseUrl/api/user/address/find/{?user_id}
UserHealth:
    url: baseUrl/api/user/health/find/{?user_id}
UserProfessional:
    url: baseUrl/api/user/health/find/{?user_id}
UserDocument:      
    url: baseUrl/api/user/document/find/{?user_id}
Roles:
    url: baseUrl/api/role/show
Team: 
    url: baseUrl/api/team/show

## Rotas de update:

estas rotas vao estar restritas ao usuário Admnistrador, apenas ele pode criar e editar dados do usuário, um parâmetro opcional user_id também pode ser enviado para as rotas, caso não seja enviado a aplicação entenderá que o usuário admnistrador está alterando seus próprios dados, para alterar dados de terceiros é necessário adicionar o id do usuário desejado ao final da rota

UserProfile:
url: baseUrl/api/user/profile/update/{?user_id}
body: {
    "name" => "nome do usuário",
    "birth_date" => "data de nascimento no formato dd-mm-yyyy",
    "email" => "email do usuário",
    "phone" => "telefone do usuário apenas string sem máscara",
    "whatsapp" => "whatsapp do usuário apenas string sem máscara",
    "avatar" => "arquivo de imagem do usuário (foto)",
    "start_plantus" => "data de inicio na plataforma no formato dd-mm-yyyy",    
}

exemplo de retorno :
{
"id": 1,
"name": "darrlin",
"email": "arquimedes@gmail.com",
"email_verified_at": null,
"birth_date": "04-02-1992",
"cpf": "10492234035",
"phone": "84996530353",
"whatsapp": "84996530353",
"avatar": "https:\/\/plantus.s3.amazonaws.com\/user\/avatar\/1\/L40VwB2Zd9RJ8ezfhowVw8eWe6XQrTuh4ZSVC5rg.png",
"start_plantus": "2022-03-14T00:00:00.000000Z",
"created_at": "2022-04-12 16:36:42",
"updated_at": "2022-04-18 00:33:33"
}

UserAddress:
url: baseUrl/api/user/address/update
body: {
    "street" => "nome da rua",
    "number" => "numero da casa",
    "cep" => "cep do endereço" (ainda sem validação com api externa para saber se o cep é real),
    "neighborhood" => "bairro do endereço",
    "state" => "estado do endereço (RN, SP ... )",
    "city" => "cidade do endereço",
    "adjunct" => "complemento (opcional)",
    "file" => "arquivo do comprovante de endereco"    
}
exemplo de retorno:
{
"id": 1,
"user_id": 1,
"street": "Rua dos salmos",
"number": "53",
"cep": "59123525",
"neighborhood": "panatis",
"state": "RN",
"city": "Natal",
"adjunct": "final da rua2",
"created_at": "2022-04-17 18:31:23",
"updated_at": "2022-04-17 18:31:23",
"file": {
    "url": "https:\/\/plantus.s3.amazonaws.com\/user\/address\/1\/LtVU4V7IVORZwreCSl5JP6kf13yM4BDkcdFafqTX.png",
    "extension": "png"
    }
}

UserHealth:
url: baseUrl/api/user/health/update
body: {
    "is_allergy" => 1 para verdadeiro, 0 para falso,
    "allergy_description" => "descrição da alergia", (necessário ser verdadeiro em is_allergy)
    "use_medicine" => 1 para verdadeiro, 0 para falso,
    "medicine_description" => "descrição da medicação", (necessário ser verdadeiro em use_medicine)
    "blood_type" => "tipo sanguíneo",
    "sus_card" => "número do cartão sus (apenas string)",
    "sus_card_file" => "arquivo do cartão do sus",
    "emergency_phone_number_a" => "numero de contato a",
    "emergency_contact_name_a" => "nome do contato do número a",    
    "emergency_kinship_a" => "parentesco do número de contato a",  
    "emergency_phone_number_b" => "numero de contato b",
    "emergency_contact_name_b" => "nome do contato do número b",    
    "emergency_kinship_b" => "parentesco do número de contato b",

}

UserDocument:      
    url: baseUrl/api/user/document/update/{?user_id}
body: {
    'document' => [
        'rg' => 'numero do rg',
        'date_emission' =>'data de emissão no formato dd-mm-yyyy',
        'issuing_agency'  => 'orgão emissor',
        'issuing_state' => 'estado de emissão (RN, SP, AM ...)',
        'file' => "arquivo do documento com foto"
        ],
    'work_card' => [
        'number' => 'número da carteira de trabalho',
        'serie' => 'número de série',
        'pis_pased' => 'número do pis',
        'date_emission' => 'data de emissão no formato dd-mm-yyyy',
        'file' => "arquivo do documento da carteira de trabalho"
    ],
    'vote_card' => [
        'number' => 'número do título de eleitor',
        'session' => 'seção de votação',
        'zone' => 'zona eleitoral',
        'file' => "arquivo do documento do título de eleitor"
    ],
    'passport' => [
        'passport' => 'ID do passaporte',
        'date_emission' => 'data de emissão no formato dd-mm-yyyy',
        'expiration_date'  => 'data de vencimento no formato dd-mm-yyyy',
        'file' => "arquivo do documento do passaporte"
    ],
    'american_visas' => [
        'number' => 'número do visto americano',
        'date_emission' => 'data de emissão no formato dd-mm-yyyy',
        'expiration_date'  => 'data de vencimento no formato dd-mm-yyyy',
        'file' => "arquivo do documento do visto americano"
    ],
    'document_data' => [
        'gender' => 'gênero',
        'marital_status' => 'stado civil',
        'mother' => 'nome da mãe',
        'father' => 'nome do pai',
        'bank_account' => 'dados bancários'
    ]
}

