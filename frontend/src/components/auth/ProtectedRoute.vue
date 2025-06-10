<template>
  <slot v-if="isAuthenticated" />
  <div v-else class="row justify-center items-center full-height">
    <q-spinner color="primary" size="3em" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuth } from 'src/services/auth.service'
import { useQuasar } from 'quasar'

const $q = useQuasar()
const router = useRouter()
const route = useRoute()
const { user, checkAuth } = useAuth()

const isAuthenticated = ref(false)
const isLoading = ref(true)

onMounted(async () => {
  try {
    // Verifica se o usuário está autenticado
    await checkAuth()

    if (!user.value) {
      // Se não estiver autenticado, redireciona para a página de login
      $q.notify({
        type: 'warning',
        message: 'Você precisa estar logado para acessar esta página',
        position: 'top',
      })

      // Armazena a rota atual para redirecionar após o login
      const returnUrl = route.fullPath
      router.push({ name: 'login', query: { returnUrl } })
      return
    }

    // Se estiver tudo certo, permite o acesso à rota
    isAuthenticated.value = true
  } catch (error) {
    console.error('Erro ao verificar autenticação:', error)
    $q.notify({
      type: 'negative',
      message: 'Erro ao verificar autenticação',
      position: 'top',
    })
    router.push({ name: 'login' })
  } finally {
    isLoading.value = false
  }
})
</script>

<style scoped>
.full-height {
  min-height: 80vh;
}
</style>
