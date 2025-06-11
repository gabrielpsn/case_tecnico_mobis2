// Layouts
import MainLayout from 'src/layouts/MainLayout.vue'

// Páginas
import IndexPage from 'src/pages/IndexPage.vue'
import LoginPage from 'src/pages/LoginPage.vue'
import NotFoundPage from 'src/pages/ErrorNotFound.vue'
import UsuariosPage from 'src/pages/cadastros/usuario/UsuariosPage.vue'
import VeiculoListPage from 'src/pages/cadastros/vaiculo/VeiculoListPage.vue'
import VeiculoFormPage from 'src/pages/cadastros/vaiculo/VeiculoFormPage.vue'
import ManutencaoListPage from 'src/pages/cadastros/manutencao/ManutencaoListPage.vue'
import ManutencaoFormPage from 'src/pages/cadastros/manutencao/ManutencaoFormPage.vue'
import MotoristaListPage from 'src/pages/cadastros/motorista/MotoristaListPage.vue'
import TelemetryListPage from 'src/pages/cadastros/telemetry/TelemetryListPage.vue'
import MotoristaFormPage from 'src/pages/cadastros/motorista/MotoristaFormPage.vue'

// Exporta as rotas como um array
const routes = [
  {
    path: '/',
    component: MainLayout,
    children: [
      {
        path: '',
        name: 'dashboard',
        component: IndexPage,
        meta: {
          title: 'Dashboard',
          icon: 'dashboard',
          requiresAuth: true,
        },
      },
      {
        path: 'profile',
        name: 'profile',
        component: () => import('pages/ProfilePage.vue'),
        meta: {
          title: 'Meu Perfil',
          icon: 'person',
          requiresAuth: true,
        },
      },
      {
        path: 'cadastros',
        name: 'cadastros',
        redirect: { name: 'usuarios' },
        meta: {
          title: 'Cadastros',
          icon: 'apps',
          requiresAuth: true,
        },
        children: [
          {
            path: 'usuarios',
            name: 'usuarios',
            component: UsuariosPage,
            meta: {
              title: 'Usuários',
              icon: 'people',
              requiresAuth: true,
            },
          },
        ],
      },
      // Menu de Frota
      {
        path: 'frota',
        name: 'frota',
        redirect: { name: 'veiculos' },
        meta: {
          title: 'Frota',
          icon: 'directions_car',
          requiresAuth: true,
        },
        children: [
          {
            path: 'veiculos',
            name: 'veiculos',
            redirect: { name: 'veiculos-list' },
            meta: {
              title: 'Veículos',
              icon: 'directions_car',
              requiresAuth: true,
            },
            children: [
              {
                path: '',
                name: 'veiculos-list',
                component: VeiculoListPage,
                meta: {
                  title: 'Lista de Veículos',
                  icon: 'directions_car',
                  requiresAuth: true,
                },
              },
              {
                path: 'novo',
                name: 'veiculos-novo',
                component: VeiculoFormPage,
                meta: {
                  title: 'Novo Veículo',
                  icon: 'add_circle_outline',
                  requiresAuth: true,
                },
              },
              {
                path: 'editar/:id',
                name: 'veiculos-editar',
                component: VeiculoFormPage,
                meta: {
                  title: 'Editar Veículo',
                  icon: 'edit',
                  requiresAuth: true,
                },
              },
            ],
          },
          {
            path: 'manutencao',
            name: 'manutencao',
            redirect: { name: 'manutencao-list' },
            meta: {
              title: 'Histórico de Manutenção',
              icon: 'build',
              requiresAuth: true,
            },
            children: [
              {
                path: '',
                name: 'manutencao-list',
                component: ManutencaoListPage,
                meta: {
                  title: 'Lista de Manutenções',
                  icon: 'build',
                  requiresAuth: true,
                },
              },
              {
                path: 'novo',
                name: 'manutencao-nova',
                component: ManutencaoFormPage,
                meta: {
                  title: 'Nova Manutenção',
                  icon: 'add_circle_outline',
                  requiresAuth: true,
                },
              },
              {
                path: 'editar/:id',
                name: 'manutencao-editar',
                component: ManutencaoFormPage,
                meta: {
                  title: 'Editar Manutenção',
                  icon: 'edit',
                  requiresAuth: true,
                },
              },
            ],
          },
          {
            path: 'motoristas',
            name: 'motoristas',
            redirect: { name: 'motoristas-list' },
            meta: {
              title: 'Motoristas',
              icon: 'person',
              requiresAuth: true,
            },
            children: [
              {
                path: '',
                name: 'motoristas-list',
                component: MotoristaListPage,
                meta: {
                  title: 'Lista de Motoristas',
                  icon: 'person',
                  requiresAuth: true,
                },
              },
              {
                path: 'novo',
                name: 'motoristas-novo',
                component: MotoristaFormPage,
                meta: {
                  title: 'Novo Motorista',
                  icon: 'person_add',
                  requiresAuth: true,
                },
              },
              {
                path: 'editar/:id',
                name: 'motoristas-editar',
                component: MotoristaFormPage,
                meta: {
                  title: 'Editar Motorista',
                  icon: 'edit',
                  requiresAuth: true,
                },
              },
            ],
          },
          {
            path: 'telemetria',
            name: 'telemetria',
            redirect: { name: 'telemetria-list' },
            meta: {
              title: 'Telemetria',
              icon: 'gps_fixed',
              requiresAuth: true,
            },
            children: [
              {
                path: '',
                name: 'telemetria-list',
                component: TelemetryListPage,
                meta: {
                  title: 'Monitoramento em Tempo Real',
                  icon: 'gps_fixed',
                  requiresAuth: true,
                },
              },
              {
                path: 'veiculo/:id',
                name: 'telemetria-veiculo',
                component: () => import('pages/cadastros/telemetry/TelemetryDetailPage.vue'),
                meta: {
                  title: 'Detalhes da Telemetria',
                  requiresAuth: true,
                  hideInMenu: true,
                },
                props: true,
              },
            ],
          },
        ],
      },
    ],
  },
  {
    path: '/login',
    name: 'login',
    component: LoginPage,
    meta: {
      layout: 'empty',
      requiresGuest: true,
    },
  },
  {
    path: '/:catchAll(.*)*',
    component: NotFoundPage,
    meta: {
      layout: 'empty',
    },
  },
]

export default routes
