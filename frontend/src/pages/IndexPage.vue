<template>
  <q-page class="q-pa-md">
    <!-- Cabeçalho com boas-vindas -->
    <div class="row q-mb-lg">
      <div class="col-12">
        <q-banner class="bg-primary text-white rounded-borders">
          <template v-slot:avatar>
            <q-avatar>
              <img :src="user?.avatar || 'https://cdn.quasar.dev/img/avatar.png'" />
            </q-avatar>
          </template>
          <div class="text-h4">Bem-vindo(a), {{ formattedUserName }}!</div>
        </q-banner>
      </div>
    </div>

    <!-- Cards de informações -->
    <div class="row q-col-gutter-md">
      <!-- Card de informações do usuário -->
      <div class="col-12 col-md-6">
        <q-card class="my-card" flat bordered>
          <q-card-section horizontal>
            <q-card-section class="q-pt-xs">
              <div class="text-h5 q-mt-sm q-mb-xs">Seus Dados</div>
              <div class="text-caption text-grey">
                <div class="row items-center q-gutter-sm q-mb-sm">
                  <q-icon name="email" size="sm" />
                  <span>{{ user?.email || 'Nenhum e-mail cadastrado' }}</span>
                </div>
                <div class="row items-center q-gutter-sm">
                  <q-icon name="person" size="sm" />
                  <span>{{ user?.role || 'Usuário' }}</span>
                </div>
              </div>
            </q-card-section>

            <q-card-section class="col-5 flex flex-center">
              <q-avatar size="100px" class="shadow-4">
                <img :src="user?.avatar || 'https://cdn.quasar.dev/img/avatar.png'" />
              </q-avatar>
            </q-card-section>
          </q-card-section>
        </q-card>
      </div>

      <!-- Card de status do sistema -->
      <div class="col-12 col-md-6">
        <q-card class="my-card" flat bordered>
          <q-card-section>
            <div class="text-h5 q-mb-md">Status do Sistema</div>
            <div class="q-gutter-y-md">
              <div class="row items-center">
                <q-icon name="check_circle" color="positive" size="sm" class="q-mr-sm" />
                <span>Sistema operacional</span>
              </div>
              <div class="row items-center">
                <q-icon name="check_circle" color="positive" size="sm" class="q-mr-sm" />
                <span>Conexão com o servidor</span>
              </div>
              <div class="row items-center">
                <q-icon name="check_circle" color="positive" size="sm" class="q-mr-sm" />
                <span>Última atualização: {{ lastUpdate }}</span>
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <!-- Seção de atividades recentes -->
    <div class="row q-mt-lg">
      <div class="col-12">
        <q-card flat bordered>
          <q-card-section>
            <div class="text-h5">Atividades Recentes</div>
          </q-card-section>

          <q-separator />

          <q-card-section>
            <q-list bordered separator>
              <q-item v-for="(activity, index) in recentActivities" :key="index" class="q-pa-md">
                <q-item-section avatar>
                  <q-icon :name="activity.icon" :color="activity.color" size="md" />
                </q-item-section>
                <q-item-section>
                  <q-item-label>{{ activity.title }}</q-item-label>
                  <q-item-label caption>{{ activity.description }}</q-item-label>
                </q-item-section>
                <q-item-section side>
                  <q-item-label caption>{{ activity.time }}</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </q-card-section>
        </q-card>
      </div>
    </div>
  </q-page>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useAuth } from 'src/services/auth.service'

// Usa o hook de autenticação
const { user } = useAuth()

// Estado para controle de carregamento
const loading = ref(true)

// Atividades recentes
const recentActivities = ref([
  {
    icon: 'login',
    color: 'primary',
    title: 'Login realizado',
    description: 'Você fez login no sistema',
    time: 'Há alguns segundos',
  },
  {
    icon: 'edit_note',
    color: 'amber',
    title: 'Dados atualizados',
    description: 'Seu perfil foi atualizado',
    time: 'Há 1 hora',
  },
  {
    icon: 'security',
    color: 'red',
    title: 'Alerta de segurança',
    description: 'Sua senha será expirada em 7 dias',
    time: 'Há 1 dia',
  },
])

// Data/hora da última atualização
const lastUpdate = computed(() => {
  return new Date().toLocaleString('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
})

// Função para formatar o nome do usuário
const formattedUserName = computed(() => {
  if (!user.value) return 'Usuário'
  return user.value.name || user.value.email?.split('@')[0] || 'Usuário'
})

onMounted(() => {
  // Atualiza o estado de carregamento
  loading.value = false

  // Atualiza a atividade de login com o horário atual
  if (recentActivities.value.length > 0) {
    recentActivities.value[0].time = 'Agora mesmo'
  }
})
</script>

<style scoped>
.my-card {
  height: 100%;
}
</style>
