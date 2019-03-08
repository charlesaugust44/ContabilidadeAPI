#API Contabilidade
A aplicação consiste em uma API para o controle de dados referentes a parte da contabilidade de um pequeno negócio, onde as necessidades são:
* Manter um cadastro de clientes e suas dividas;
* Controle de acesso de usuários com dois niveis (Administradores e outros usuários);
* Auditoria de modificação, criação e deleção de clientes e suas dívidas;

##Diagrama de banco de dados
![Diagrama de banco de dados](images/diagrama_banco.png "Diagrama")
###Dicionário de dados
* **Tabela _clients_**
  * **name** - Nome do cliente;
  * **address** - Endereço, pode ser cidade ou logradouro (a criterio do usuário);
  * **total_debt** - Dívida total, calculada a partir dos dados na tabela _debts_;
  * **deleted** - Flag de deleção lógica do cliente, mantido na persistencia para auditoria;
* **Tabela _client_modifications_**
  * **user_id** - Id do usuário responsável pela modificação 
  * **client_id** - Id do cliente modificado;
  * **type** - Tipo da modificação efetuada (CREATE = 0, UPDATE = 1, DELETE = 2);
  * **changes** - Campo utilizado para uma modificação do tipo UPDATE, onde é gravado um objeto JSON com a antiga e nova versão do cliente;  
* **Tabela _debts_**
  * **value** - Valor da dívida ou abate (Unsigned);
  * **writeoff** - Flag que indica se o valor é um abate(pagamento efetuado pelo cliente) ou uma dívida;
  * **client_id** - Id do cliente a quem pertence a dívida; 
  * **user_id** -  Id do usuário que cadastrou a dívida;
* **Tabela _users_**
  * **name** - Nome do usuário; 
  * **user** - Usuário de login;
  * **password** - Senha protegida por hash;
  * **admin** - Flag que indica se o usuário é um Administrador;
  * **token** - Token gerado pela API para comunicação segura com o usuário;
  * **secret** -  Segredo(senha) utilizada para verificar a autenticidade do token; 
##Autenticação
A autenticação é feita por meio de tokens, os quais são gerados no login e enviado para o usuário, que deverá utilizar apenas o token para se comunicar com os endpoints protegidos. Ele possuí um tempo de expiração embutido, que para caso de testes está setado para um mês.
O token segue os padrões da [RFC 7519](https://tools.ietf.org/html/rfc7519) para JSON Web Tokens, ele é validado por meio de uma chave de 60 carácteres alfanuméricos, incluindo simbolos especiais, utilizada para checar a autenticidade do token.
Existem dois tipos de usuários no sistema _Administradores_ e _Outros_, todos os endpoints podem ser acessados pelo _Administrador_. _Outros_ são impedidos de utilizar os endpoints relaciodados ao CRUD de usuário e à auditoria de modificações do cliente. 
##Endpoints

Todos os endpoints contém o prefixo _/api/v1_ que serão omitidos na tabela abaixo.

##### Users

| Método | URI | Usuário | Descrição | 
| :---: | :--- | :---: | :--- | 
| **POST** | /user | Admin | Create user |
| **GET** | /user/token | - | Authenticate user |
| **GET** | /user/{id} | Admin | Read user |
| **GET** | /user | Admin | Read all users |
| **PUT** | /user/{id} | Admin | Update user |
| **DELETE** | /user/{id} | Admin | Delete user |

##### Client

| Método | URI | Usuário | Descrição | 
| :---: | :--- | :---: | :---  |
| **POST** | /client | Others | Create client |
| **GET** | /client | Others | Read all clients |
| **GET** | /client/deleted | Others | Read all deleted clients |
| **GET** | /client/nondeleted | Others | Read all non deleted clients |
| **GET** | /client/{id} | Others | Read client |
| **GET** | /client/{id}/modfications | Admin | Read all client modifications |
| **GET** | /client/{id}/debts | Other | Read all client debts |
| **PUT** | /client/{id} | Other | Update client |
| **DELETE** | /client/{id} | Other | Delete client |

##### Debt

| Método | URI | Usuário | Descrição | 
| :---: | :--- | :---: | :---  |
| **POST** | /debt | Others | Create debt |
| **DELETE** | /debt/{id} | Admin | Delete debt |

### Data-Flow


#### [POST] /user
* Input esperado:
    * api_token - string;
    * name - string;
    * user - string;
    * admin - boolean (0 \| 1);
    * password - string;
* Output:
    * Status 201 - Created
    
#### [GET] /user/token
* Input esperado:
    * user - string;
    * password - string;
* Output:
    * Status 401 - Unauthorized
    * Status 200 - OK
    ```JSON
    {
      "token":"API_TOKEN"  
    }    
    ```
    
#### [GET] /user/{id}
* Input esperado:
    * api_token - string;
* Output:
    * Status 404 - Not Found
    * Status 200 - OK
    ```JSON
    {
        "id": 1,
        "name": "Administrator",
        "user": "admin",
        "admin": 1,
        "created_at": "2019-03-08 21:28:27",
        "updated_at": "2019-03-08 21:28:27"
    }  
    ```
    
#### [GET] /user
* Input esperado:
    * api_token - string;
* Output:
    * Status 204 - No Content
    * Status 200 - OK
    ```JSON
    [  
        {
            "id": 1,
            "name": "Administrator",
            "user": "admin",
            "admin": 1,
            "created_at": "2019-03-08 21:28:27",
            "updated_at": "2019-03-08 21:28:27"
        }
    ]    
    ```
    
#### [PUT] /user/{id}
* Input esperado:
    * api_token - string;
    * name - string
    * user - string
    * admin - boolean
    * old_password - string (Se este campo estiver vazio a senha não será alterada)
    * new_password - string
* Output:
    * Status 400 - Bad Request
    * Status 404 - Not Found
    * Status 204 - No Content 
    
#### [DELETE] /user/{id}
* Input esperado:
    * api_token - string;
* Output:
    * Status 404 - Not Found
    * Status 204 - No Content 
    
#### [POST] /client
* Input esperado:
    * api_token - string;
    * name - string
    * address - string
* Output:
    * Status 201 - Created

#### [GET] /client ou /client/deleted ou /client/nondeleted
* Input esperado:
    * api_token - string;
* Output:
    * Status 204 - No Content
    * Status 200 - OK
    ```JSON
    [
        {
            "id": 1,
            "name": "Fulano",
            "address": "Cidade Tal",
            "total_debt": 0,
            "created_at": "2019-03-08 21:49:59",
            "updated_at": "2019-03-08 21:49:59"
        }
    ] 
    ```
#### [GET] /client/{id}
* Input esperado:
    * api_token - string;
* Output:
    * Status 404 - Not Found
    * Status 200 - OK
    ```JSON
    {
        "id": 1,
        "name": "Fulano",
        "address": "Cidade Tal",
        "total_debt": 0,
        "created_at": "2019-03-08 21:49:59",
        "updated_at": "2019-03-08 21:49:59"
    } 
    ```

#### [GET] /client/{id}/modifications
* Input esperado:
    * api_token - string;
* Output:
    * Status 204 - No Content
    * Status 200 - OK
    ```JSON
    [
        {
            "id": 5,
            "client_id": 1,
            "user_id": 1,
            "type": 0,
            "changes": null,
            "created_at": "2019-03-08 21:49:59",
            "updated_at": "2019-03-08 21:49:59"
        }
    ] 
    ```

#### [GET] /client/{id}/debts
* Input esperado:
    * api_token - string;
* Output:
    * Status 204 - No Content
    * Status 200 - OK
    ```JSON
    [
        {
            "id": 1,
            "value": 200,
            "writeoff": 0,
            "client_id": 1,
            "user_id": 1,
            "created_at": "2019-03-07 23:12:45",
            "updated_at": "2019-03-07 23:12:45"
        }
    ] 
    ```
    
#### [PUT] /client/{id}
* Input esperado:
    * api_token - string;
    * name - string
    * user - string
    * admin - boolean
    * old_password - string (Se este campo estiver vazio a senha não será alterada)
    * new_password - string
* Output:
    * Status 404 - Not Found
    * Status 204 - No Content 
    
#### [DELETE] /client/{id}
* Input esperado:
    * api_token - string;
* Output:
    * Status 404 - Not Found
    * Status 204 - No Content
    
#### [POST] /debt
* Input esperado:
    * api_token - string;
    * name - string;
    * user - string;
    * admin - boolean (0 \| 1);
    * password - string;
* Output:
    * Status 201 - Created

#### [DELETE] /debt/{id}
* Input esperado:
    * api_token - string;
    * value - double;
    * writeoff - boolean;
    * client_id - int;        
* Output:
    * Status 404 - Not Found
    * Status 204 - No Content