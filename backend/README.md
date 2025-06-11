# Backend - Sistema de Telemetria Veicular

Backend desenvolvido com Laravel 10 para gerenciar dados de telemetria de veículos, fornecendo APIs RESTful para o frontend.

## 🚀 Tecnologias

-   PHP 8.1+
-   Laravel 10.x
-   MongoDB 6.0+ (para dados de telemetria)
-   MySQL 8.0+ (para dados relacionais)
-   JWT para autenticação
-   Laravel Sanctum para API
-   Laravel Horizon para gerenciamento de filas
-   Laravel Telescope para depuração

## 📋 Pré-requisitos

-   PHP 8.1+
-   Composer 2.5+

-   MySQL 8.0+

-   MongoDB 6.0+

-   Redis (opcional, para cache e filas)

## 🛠️ Instalação

1. **Clonar o repositório**

    ```bash
    git clone [URL_DO_REPOSITORIO]
    cd case_tecnico_mobis2/backend

    ```

2. **Instalar dependências**

    ```bash
    composer install
    ```

3. **Configurar variáveis de ambiente**

    ```bash
    cp .env.example .env
    ```

4. **Gerar chave de aplicação**

    ```bash
    php artisan key:generate
    ```

5. **Configurar banco de dados**

    ```bash
    php artisan migrate
    ```

6. **Iniciar o servidor de desenvolvimento**
    ```bash
    php artisan serve
    ```
    O aplicativo estará disponível em `http://localhost:8000`

## 📚 Documentação

A documentação da API pode ser encontrada em `docs/api.md`.

## 📝 Licença

Distribuído sob a licença MIT. Veja `LICENSE` para mais informações.

## 📞 Contato

Seu Nome - [@seu_twitter](https://twitter.com/seu_twitter) - email@exemplo.com

Link do Projeto: [https://github.com/seu-usuario/telemetria-backend](https://github.com/seu-usuario/telemetria-backend)

## 📁 Estrutura do Projeto

```
app/
├── Console/           # Comandos do Artisan
├── Events/            # Eventos
├── Exceptions/        # Tratamento de exceções
├── Http/
│   ├── Controllers/   # Controladores da API
│   │   ├── API/       # Controladores da API
│   │   └── Auth/      # Autenticação
│   ├── Middleware/    # Middlewares
│   └── Requests/      # Form Requests
├── Jobs/              # Jobs em fila
├── Listeners/         # Ouvintes de eventos
├── Models/            # Modelos
│   ├── MongoDB/       # Modelos MongoDB
│   └── MySQL/         # Modelos MySQL
├── Policies/          # Políticas de autorização
└── Services/          # Lógica de negócios
config/                # Arquivos de configuração
database/
├── factories/         # Factories para testes
├── migrations/        # Migrações do banco de dados
└── seeders/           # Seeders para dados iniciais
routes/                # Rotas da aplicação
tests/                 # Testes automatizados
```

## Contato

João Gabriel - gabrielpsn@gmail.com
