import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { api } from 'boot/axios'

// Estado global do usuário
const user = ref(JSON.parse(localStorage.getItem('user')) || null)
const isAuthenticated = computed(() => !!user.value?.token)
const returnUrl = ref('/')

export function useAuth() {
  const router = useRouter()

  async function login(email, password) {
    try {
      const response = await api.post('/login', { email, password })

      if (response.data.token) {
        // Armazena os dados do usuário e o token
        user.value = {
          ...response.data.user,
          token: response.data.token,
        }

        localStorage.setItem('user', JSON.stringify(user.value))

        // Configura o token de autorização
        api.defaults.headers.common['Authorization'] = `Bearer ${user.value.token}`

        // Redireciona para a rota inicial ou a rota que o usuário tentou acessar
        router.push(returnUrl.value || '/')
        return user.value
      }

      throw new Error('Token não recebido na resposta de login')
    } catch (error) {
      console.error('Erro no login:', error)
      throw new Error(error.response?.data?.message || 'Falha na autenticação')
    }
  }

  async function logout() {
    try {
      // Chama o endpoint de logout no backend
      await api.post('/logout')
    } catch (error) {
      console.error('Erro ao fazer logout no servidor:', error)
    } finally {
      // Sempre limpa os dados de autenticação, mesmo que a chamada ao servidor falhe
      clearAuth()
    }
  }

  function clearAuth() {
    // Remove o token do cabeçalho das requisições
    delete api.defaults.headers.common['Authorization']

    // Limpa o estado local
    user.value = null
    localStorage.removeItem('user')

    // Redireciona para a página de login
    router.push('/login')
  }

  // Verifica se o usuário está autenticado
  function checkAuth() {
    const storedUser = localStorage.getItem('user')
    if (storedUser) {
      try {
        const parsedUser = JSON.parse(storedUser)
        if (parsedUser?.token) {
          user.value = parsedUser
          api.defaults.headers.common['Authorization'] = `Bearer ${parsedUser.token}`
          return true
        }
      } catch (e) {
        console.error('Erro ao analisar usuário do localStorage:', e)
      }
    }
    return false
  }

  // Função para definir a URL de retorno após o login
  function setReturnUrl(url) {
    returnUrl.value = url || '/'
  }

  return {
    user,
    isAuthenticated,
    login,
    logout,
    clearAuth,
    checkAuth,
    setReturnUrl,
  }
}

// Guarda de rota para proteger rotas que requerem autenticação
export function useAuthGuard() {
  const { checkAuth, setReturnUrl } = useAuth()
  const router = useRouter()

  return (to, from, next) => {
    const publicPages = ['/login', '/register']
    const authRequired = !publicPages.includes(to.path)
    const loggedIn = checkAuth()

    if (authRequired && !loggedIn) {
      // Salva a URL que o usuário estava tentando acessar
      setReturnUrl(to.fullPath)
      return next('/login')
    }

    // Se estiver logado e tentando acessar uma página pública, redireciona para a página inicial
    if (loggedIn && publicPages.includes(to.path)) {
      return next('/')
    }

    next()
  }
}

// Exporta o estado do usuário para uso fora da composição
export { user, isAuthenticated }
