import { defineRouter } from '#q-app/wrappers'
import {
  createRouter,
  createMemoryHistory,
  createWebHistory,
  createWebHashHistory,
} from 'vue-router'
import routes from './routes'

/*
 * If not building with SSR mode, you can
 * directly export the Router instantiation;
 *
 * The function below can be async too; either use
 * async/await or return a Promise which resolves
 * with the Router instance.
 */

export default defineRouter(function (/* { store, ssrContext } */) {
  const createHistory = process.env.SERVER
    ? createMemoryHistory
    : process.env.VUE_ROUTER_MODE === 'history'
      ? createWebHistory
      : createWebHashHistory

  const Router = createRouter({
    scrollBehavior(to, from, savedPosition) {
      // Retorna a posição salva se existir (útil para navegação com botão voltar/avançar)
      if (savedPosition) {
        return savedPosition
      }
      // Rola para o topo da página por padrão
      return { top: 0, behavior: 'smooth' }
    },
    routes,
    history: createHistory(process.env.VUE_ROUTER_BASE || '/'),
  })

  // Configura o título da página com base na rota atual
  Router.beforeEach((to, from, next) => {
    const appName = import.meta.env.VITE_APP_NAME || 'Sistema de Gestão'

    // Define o título da página
    document.title = to.meta.title ? `${to.meta.title} | ${appName}` : appName

    // Verifica se a rota requer autenticação
    if (to.meta.requiresAuth) {
      const user = JSON.parse(localStorage.getItem('user'))
      if (!user || !user.token) {
        // Redireciona para a página de login se não estiver autenticado
        return next({
          name: 'login',
          query: { redirect: to.fullPath }, // Salva a rota para redirecionar após o login
        })
      }
    }

    // Verifica se a rota é apenas para visitantes não autenticados
    if (to.meta.requiresGuest) {
      const user = JSON.parse(localStorage.getItem('user'))
      if (user && user.token) {
        // Redireciona para a página inicial se já estiver autenticado
        return next({ name: 'dashboard' })
      }
    }

    next()
  })

  return Router
})
