# Frontend - Sistema de Telemetria Veicular

Frontend desenvolvido com Vue.js 3 e Quasar Framework para o sistema de monitoramento de frotas veiculares.

## Tecnologias

- Vue.js 3 (Composition API)
- Quasar Framework v2
- Pinia para gerenciamento de estado
- Vue Router para navegação
- Axios para requisições HTTP
- Leaflet para mapas interativos
- Chart.js para gráficos
- Vite como build tool

## Pré-requisitos

- Node.js 18+
- npm 9+ ou Yarn 1.22+
- Backend da aplicação em execução

## Instalação

1. **Clonar o repositório**

   ```bash
   git clone [URL_DO_REPOSITORIO]
   cd case_tecnico_mobis2/frontend
   ```

2. **Instalar dependências**

   ```bash
   npm install
   # ou
   yarn
   ```

3. **Configurar variáveis de ambiente**
   Crie um arquivo `.env` na raiz do projeto:

   ```env
   VITE_API_URL=http://localhost:8000/api
   VITE_MAPBOX_TOKEN=seu_token_aqui
   ```

4. **Iniciar o servidor de desenvolvimento**

   ```bash
   quasar dev

   ou

   npx quasar dev
   ```

   O aplicativo estará disponível em `http://localhost:9000`

## Estrutura do Projeto

```
src/
├── assets/           # Arquivos estáticos (imagens, fontes)
├── boot/             # Inicialização de plugins
├── components/        # Componentes reutilizáveis
│   ├── layout/       # Componentes de layout
│   ├── telemetry/    # Componentes específicos de telemetria
│   └── ui/           # Componentes de interface genéricos
├── composables/      # Composables do Vue 3
├── css/              # Estilos globais
├── layouts/          # Layouts principais
├── pages/            # Páginas da aplicação
│   ├── auth/         # Autenticação
│   ├── dashboard/    # Painel principal
│   ├── vehicles/     # Gestão de veículos
│   └── reports/      # Relatórios
├── router/           # Configuração de rotas
├── services/         # Serviços (API, autenticação, etc.)
├── stores/          # Gerenciamento de estado com Pinia
└── App.vue          # Componente raiz
```

## Rotas Principais

- `/` - Dashboard
- `/login` - Página de login
- `/veiculos` - Lista de veículos
- `/veiculos/:id` - Detalhes do veículo
- `/veiculos/:id/telemetria` - Dados de telemetria
- `/motoristas` - Lista de motoristas
- `/relatorios` - Relatórios
- `/configuracoes` - Configurações

## Componentes Principais

### `NotificationBell.vue`

Sino de notificações que exibe alertas críticos e notificações do sistema.

### `VehicleMap.vue`

Componente de mapa interativo que mostra a localização dos veículos.

### `TelemetryGauges.vue`

Mostra os dados de telemetria em formato de painéis com medidores.

### `AlertList.vue`

Lista de alertas ativos com filtros e ações.

## Integração com a API

O frontend se comunica com a API através do serviço `api.service.js` que encapsula as chamadas HTTP usando Axios.

### Exemplo de chamada à API:

```javascript
// services/vehicle.service.js
import { api } from './api.service'

export const VehicleService = {
  async getVehicles(params = {}) {
    try {
      const response = await api.get('/veiculos', { params })
      return response.data
    } catch (error) {
      console.error('Erro ao buscar veículos:', error)
      throw error
    }
  },

  async getVehicleTelemetry(vehicleId, params = {}) {
    try {
      const response = await api.get(`/veiculos/${vehicleId}/telemetria`, { params })
      return response.data
    } catch (error) {
      console.error('Erro ao buscar telemetria:', error)
      throw error
    }
  },
}
```

## Tema e Estilização

- Utiliza o sistema de design do Quasar
- Cores principais definidas em `quasar.variables.sass`
- Componentes estilizados com Sass
- Layout responsivo para desktop e mobile

## Testes

Para executar os testes:

```bash
# Testes unitários
npm run test:unit

# Testes e2e
npm run test:e2e

# Todos os testes
npm test
```

## Build para Produção

```bash
quasar build
```

Os arquivos de produção serão gerados na pasta `dist/spa`.

## Variáveis de Ambiente

| Variável            | Descrição                 | Obrigatório | Padrão                      |
| ------------------- | ------------------------- | ----------- | --------------------------- |
| `VITE_API_URL`      | URL base da API           | Sim         | `http://localhost:8000/api` |
| `VITE_MAPBOX_TOKEN` | Token de acesso ao Mapbox | Sim         | -                           |
| `VITE_APP_NAME`     | Nome da aplicação         | Não         | `Telemetria Veicular`       |

## Depuração

1. Instale a extensão Vue Devtools no seu navegador
2. Utilize o Vue Devtools para inspecionar componentes e estado
3. Ative o modo de depuração no Quasar:
   ```bash
   quasar dev --devtools
   ```

## Contribuição

1. Faça um Fork do projeto
2. Crie uma Branch para sua Feature (`git checkout -b feature/AmazingFeature`)
3. Adicione suas mudanças (`git add .`)
4. Comite suas mudanças (`git commit -m 'Add some AmazingFeature'`)
5. Faça o Push da Branch (`git push origin feature/AmazingFeature`)
6. Abra um Pull Request

## Licença

Distribuído sob a licença MIT. Veja `LICENSE` para mais informações.

## Contato

Seu Nome - [@seu_twitter](https://twitter.com/seu_twitter) - email@exemplo.com

Link do Projeto: [https://github.com/seu-usuario/telemetria-frontend](https://github.com/seu-usuario/telemetria-frontend)
