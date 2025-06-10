<template>
  <q-layout view="hHh LpR fFf">
    <!-- Cabeçalho -->
    <q-header elevated class="bg-primary text-white">
      <q-toolbar>
        <q-btn
          flat
          dense
          round
          icon="menu"
          aria-label="Menu"
          @click="toggleLeftDrawer"
          class="q-mr-sm"
        />

        <q-toolbar-title class="text-weight-bold">
          {{ appName }}
        </q-toolbar-title>

        <!-- Notificações -->
        <q-btn flat round dense icon="notifications" class="q-mr-xs">
          <q-badge v-if="unreadNotifications > 0" color="red" floating>
            {{ unreadNotifications > 9 ? '9+' : unreadNotifications }}
          </q-badge>
          <q-menu>
            <q-list style="min-width: 300px">
              <q-item>
                <q-item-section>
                  <q-item-label class="text-weight-bold">Notificações</q-item-label>
                </q-item-section>
                <q-item-section side>
                  <q-btn
                    flat
                    dense
                    size="sm"
                    label="Marcar todas como lidas"
                    color="primary"
                    v-if="unreadNotifications > 0"
                    @click="markAllAsRead"
                  />
                </q-item-section>
              </q-item>
              <q-separator />
              <template v-if="notifications.length > 0">
                <q-item
                  v-for="(notification, index) in notifications"
                  :key="index"
                  clickable
                  v-ripple
                  :class="{ 'bg-grey-2': !notification.read }"
                >
                  <q-item-section avatar>
                    <q-icon
                      :name="getNotificationIcon(notification.type)"
                      :color="notification.read ? 'grey' : 'primary'"
                      size="md"
                    />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label :class="{ 'text-weight-bold': !notification.read }">
                      {{ notification.title }}
                    </q-item-label>
                    <q-item-label caption lines="2">
                      {{ notification.message }}
                    </q-item-label>
                    <q-item-label caption class="text-right">
                      {{ formatDate(notification.date) }}
                    </q-item-label>
                  </q-item-section>
                </q-item>
              </template>
              <q-item v-else>
                <q-item-section class="text-center text-grey-7 q-py-md">
                  Nenhuma notificação recente
                </q-item-section>
              </q-item>
              <q-separator />
              <q-item clickable v-ripple to="/notificacoes" class="text-center">
                <q-item-section>
                  <q-item-label class="text-primary">Ver todas as notificações</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </q-menu>
        </q-btn>

        <!-- Menu do usuário -->
        <q-btn-dropdown flat :label="userName" :icon="userAvatar">
          <q-list>
            <q-item clickable v-close-popup to="/perfil">
              <q-item-section avatar>
                <q-icon name="person" />
              </q-item-section>
              <q-item-section>
                <q-item-label>Meu Perfil</q-item-label>
              </q-item-section>
            </q-item>

            <q-item clickable v-close-popup to="/configuracoes">
              <q-item-section avatar>
                <q-icon name="settings" />
              </q-item-section>
              <q-item-section>
                <q-item-label>Configurações</q-item-label>
              </q-item-section>
            </q-item>

            <q-separator />
            <q-item clickable v-close-popup @click="confirmLogout">
              <q-item-section avatar>
                <q-icon name="logout" />
              </q-item-section>
              <q-item-section>
                <q-item-label>Sair</q-item-label>
              </q-item-section>
            </q-item>
          </q-list>
        </q-btn-dropdown>
      </q-toolbar>
    </q-header>

    <!-- Menu lateral -->
    <q-drawer
      v-model="leftDrawerOpen"
      show-if-above
      bordered
      :width="280"
      :breakpoint="1024"
      class="bg-grey-1"
    >
      <div class="column full-height">
        <!-- Logo e nome do aplicativo -->
        <div class="bg-primary text-white q-pa-md">
          <div class="text-h6 text-weight-bold">{{ appName }}</div>
          <div class="text-caption">v{{ appVersion }}</div>
        </div>

        <!-- Menu de navegação -->
        <q-scroll-area class="col-grow">
          <app-menu />
        </q-scroll-area>

        <!-- Rodapé do menu -->
        <div class="q-pa-sm bg-grey-2 text-center">
          <div class="text-caption text-grey-8">
            &copy; {{ new Date().getFullYear() }} {{ appName }}
          </div>
          <div class="text-caption text-grey-6">Todos os direitos reservados</div>
        </div>
      </div>
    </q-drawer>

    <!-- Área de conteúdo -->
    <q-page-container>
      <router-view v-slot="{ Component }">
        <transition
          enter-active-class="animated fadeIn"
          leave-active-class="animated fadeOut"
          mode="out-in"
          :duration="300"
        >
          <component :is="Component" />
        </transition>
      </router-view>
    </q-page-container>

    <!-- Barra de status (opcional) -->
    <q-footer elevated class="bg-grey-8 text-white">
      <q-toolbar class="q-px-md">
        <div class="row items-center full-width">
          <div class="col">
            <div class="text-caption">
              {{ currentDateTime }}
            </div>
          </div>
          <div class="col-auto">
            <div class="row items-center q-gutter-x-sm">
              <q-icon name="wifi" size="16px" />
              <q-icon name="battery_full" size="16px" />
              <q-icon name="signal_cellular_alt" size="16px" />
            </div>
          </div>
        </div>
      </q-toolbar>
    </q-footer>
  </q-layout>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useQuasar } from 'quasar'
import { useAuth } from 'src/services/auth.service'
import AppMenu from 'components/layout/AppMenu.vue'

const $q = useQuasar()
const { user, logout } = useAuth()

// Estado do menu lateral
const leftDrawerOpen = ref(false)

// Informações do app
const appName = import.meta.env.VITE_APP_NAME || 'Sistema de Gestão'
const appVersion = import.meta.env.VITE_APP_VERSION || '1.0.0'

// Dados do usuário
const userName = computed(() => {
  return user.value?.nome || 'Usuário'
})

const userAvatar = computed(() => {
  return user.value?.avatar ? 'img:' + user.value.avatar : 'person'
})

// Notificações (exemplo)
const notifications = ref([
  {
    id: 1,
    title: 'Atualização do sistema',
    message: 'Uma nova atualização está disponível',
    type: 'system',
    date: new Date(),
    read: false,
  },
  {
    id: 2,
    title: 'Nova mensagem',
    message: 'Você tem uma nova mensagem não lida',
    type: 'message',
    date: new Date(Date.now() - 1000 * 60 * 5),
    read: false,
  },
  {
    id: 3,
    title: 'Tarefa concluída',
    message: 'A tarefa foi concluída com sucesso',
    type: 'task',
    date: new Date(Date.now() - 1000 * 60 * 60),
    read: true,
  },
])

const unreadNotifications = computed(() => {
  return notifications.value.filter((n) => !n.read).length
})

// Data e hora atuais
const currentDateTime = ref('')

// Alternar visibilidade do menu lateral
function toggleLeftDrawer() {
  leftDrawerOpen.value = !leftDrawerOpen.value
}

// Confirmar logout
function confirmLogout() {
  $q.dialog({
    title: 'Confirmar saída',
    message: 'Tem certeza que deseja sair do sistema?',
    cancel: true,
    persistent: true,
    ok: {
      label: 'Sair',
      color: 'negative',
      flat: true,
    },
    cancel: {
      label: 'Cancelar',
      color: 'primary',
      flat: true,
    },
  }).onOk(() => {
    logout()
    $q.notify({
      type: 'positive',
      message: 'Você saiu do sistema com sucesso!',
      position: 'top',
      timeout: 3000,
    })
  })
}

// Marcar todas as notificações como lidas
function markAllAsRead() {
  notifications.value = notifications.value.map((n) => ({ ...n, read: true }))
}

// Obter ícone da notificação
function getNotificationIcon(type) {
  const icons = {
    system: 'system_update',
    message: 'email',
    task: 'task_alt',
    warning: 'warning',
    error: 'error',
  }
  return icons[type] || 'notifications'
}

// Formatar data
function formatDate(date) {
  if (!(date instanceof Date)) {
    date = new Date(date)
  }

  const now = new Date()
  const diffInHours = Math.floor((now - date) / (1000 * 60 * 60))

  if (diffInHours < 24) {
    // Se for hoje, mostra apenas a hora
    return date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })
  } else if (diffInHours < 48) {
    // Se for ontem
    return 'Ontem ' + date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })
  } else if (diffInHours < 168) {
    // Se for na última semana, mostra o dia da semana
    return date.toLocaleDateString('pt-BR', {
      weekday: 'short',
      hour: '2-digit',
      minute: '2-digit',
    })
  } else {
    // Se for mais antigo, mostra a data completa
    return date.toLocaleDateString('pt-BR')
  }
}

// Atualizar data e hora a cada minuto
let dateTimeInterval
onMounted(() => {
  updateDateTime()
  dateTimeInterval = setInterval(updateDateTime, 60000) // Atualiza a cada minuto
})

onBeforeUnmount(() => {
  if (dateTimeInterval) {
    clearInterval(dateTimeInterval)
  }
})

function updateDateTime() {
  const now = new Date()
  currentDateTime.value = now.toLocaleString('pt-BR', {
    weekday: 'long',
    day: '2-digit',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })

  // Capitaliza o primeiro caractere do dia da semana
  currentDateTime.value =
    currentDateTime.value.charAt(0).toUpperCase() + currentDateTime.value.slice(1)
}
</script>

<style lang="scss" scoped>
// Estilos personalizados para o layout
.q-layout {
  // Garante que o rodapé fique sempre visível
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.q-page-container {
  flex: 1 0 auto;
}

// Estilo para o menu lateral
.q-drawer {
  .q-item {
    border-radius: 0 24px 24px 0;
    margin: 2px 8px;
    width: calc(100% - 16px);

    &.q-router-link--active {
      background-color: rgba(25, 118, 210, 0.1);
      color: var(--q-primary);
      font-weight: 500;

      .q-icon {
        color: var(--q-primary);
      }
    }
  }

  .q-item__section--avatar {
    min-width: 40px;
  }
}

// Estilo para o cabeçalho
.q-header {
  box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);

  .q-toolbar__title {
    font-size: 1.2rem;
    letter-spacing: 0.5px;
  }
}

// Estilo para o rodapé
.q-footer {
  padding: 4px 0;
  font-size: 12px;

  .q-toolbar {
    min-height: 32px;
  }
}

// Transições de página
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
