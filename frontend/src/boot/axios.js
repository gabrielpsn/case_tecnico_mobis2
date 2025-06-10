import { boot } from 'quasar/wrappers'
import axios from 'axios'
import { useQuasar } from 'quasar'
import { useRouter } from 'vue-router'
import { useAuth } from 'src/services/auth.service'

// Cria uma instância do Axios com configurações padrão
const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
  timeout: 30000, // 30 segundos
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
  withCredentials: true,
})

export default boot(({ app }) => {
  const $q = useQuasar()
  const router = useRouter()
  const { clearAuth } = useAuth()

  // Adiciona um interceptor para incluir o token em todas as requisições
  api.interceptors.request.use(
    (config) => {
      // Obtém o token do localStorage
      const user = JSON.parse(localStorage.getItem('user'))

      // Se o token existir, adiciona ao cabeçalho
      if (user?.token) {
        config.headers.Authorization = `Bearer ${user.token}`
      }

      return config
    },
    (error) => {
      return Promise.reject(error)
    },
  )

  // Adiciona um interceptor para tratar respostas de erro
  api.interceptors.response.use(
    (response) => {
      // Qualquer código de status que esteja dentro do intervalo de 2xx faz com que esta função seja acionada
      return response
    },
    async (error) => {
      const originalRequest = error.config

      // Se o erro for 401 (não autorizado) e não for uma tentativa de refresh
      if (error.response?.status === 401 && !originalRequest._retry) {
        // Se for a rota de login, apenas rejeita
        if (originalRequest.url.includes('/auth/login')) {
          return Promise.reject(error)
        }

        // Se for a rota de refresh, limpa a autenticação e redireciona para o login
        if (originalRequest.url.includes('/auth/refresh')) {
          clearAuth()
          router.push({ name: 'login' })
          return Promise.reject(error)
        }

        // Tenta renovar o token
        try {
          originalRequest._retry = true

          // Faz uma requisição para renovar o token
          const refreshToken = JSON.parse(localStorage.getItem('user'))?.refreshToken
          if (!refreshToken) {
            throw new Error('Nenhum refresh token disponível')
          }

          const response = await api.post('/auth/refresh', { refreshToken })
          const { token, user: userData } = response.data

          // Atualiza o token no localStorage
          const currentUser = JSON.parse(localStorage.getItem('user'))
          localStorage.setItem(
            'user',
            JSON.stringify({
              ...currentUser,
              token,
              ...userData,
            }),
          )

          // Atualiza o cabeçalho de autorização
          originalRequest.headers.Authorization = `Bearer ${token}`

          // Repete a requisição original com o novo token
          return api(originalRequest)
        } catch (refreshError) {
          // Se não conseguir renovar o token, limpa a autenticação e redireciona para o login
          clearAuth()
          router.push({ name: 'login' })
          return Promise.reject(refreshError)
        }
      }

      // Tratamento de erros comuns
      if (error.response) {
        const { status, data } = error.response

        // Erro 403 - Acesso negado
        if (status === 403) {
          $q.notify({
            type: 'warning',
            message: data.message || 'Você não tem permissão para acessar este recurso.',
            position: 'top',
            timeout: 5000,
          })
        }
        // Erro 404 - Recurso não encontrado
        else if (status === 404) {
          $q.notify({
            type: 'warning',
            message: data.message || 'Recurso não encontrado.',
            position: 'top',
            timeout: 5000,
          })
        }
        // Erro 422 - Validação
        else if (status === 422) {
          // As mensagens de validação serão tratadas no componente
          return Promise.reject(error)
        }
        // Erro 500+ - Erro do servidor
        else if (status >= 500) {
          $q.notify({
            type: 'negative',
            message: data.message || 'Ocorreu um erro no servidor. Tente novamente mais tarde.',
            position: 'top',
            timeout: 5000,
          })
        }
      }
      // Erro de rede
      else if (error.request) {
        $q.notify({
          type: 'negative',
          message: 'Não foi possível conectar ao servidor. Verifique sua conexão com a internet.',
          position: 'top',
          timeout: 5000,
        })
      }
      // Erro ao fazer a requisição
      else {
        console.error('Erro ao configurar a requisição:', error.message)
      }

      return Promise.reject(error)
    },
  )

  // Disponibiliza o axios globalmente como this.$axios
  app.config.globalProperties.$axios = axios
  // Disponibiliza a instância da API como this.$api
  app.config.globalProperties.$api = api
})

export { api, axios }
