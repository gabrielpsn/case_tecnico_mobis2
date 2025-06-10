// Layouts
import MainLayout from 'src/layouts/MainLayout.vue'

// Páginas
import IndexPage from 'src/pages/IndexPage.vue'
import LoginPage from 'src/pages/LoginPage.vue'
import NotFoundPage from 'src/pages/ErrorNotFound.vue'
import UsuariosPage from 'src/pages/cadastros/usuario/UsuariosPage.vue'
import VeiculoListPage from 'src/pages/cadastros/vaiculo/VeiculoListPage.vue'
import ManutencaoPage from 'src/pages/cadastros/manutencao/ManutencaoListPage.vue'
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
            name: 'usuario s',
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
            component: VeiculoListPage,
            meta: {
              title: 'Veículos',
              icon: 'directions_car',
              requiresAuth: true,
            },
          },
          {
            path: 'manutencao',
            name: 'manutencao',
            component: ManutencaoPage,
            meta: {
              title: 'Histórico de Manutenção',
              icon: 'build',
              requiresAuth: true,
            },
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
            path: 'telemetrias',
            name: 'telemetrias',
            component: TelemetryListPage,
            meta: {
              title: 'Telemetrias',
              icon: 'signal_cellular_4_bar',
              requiresAuth: true,
            },
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
