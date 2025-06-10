<template>
  <q-page class="q-pa-md">
    <div class="row q-col-gutter-md">
      <!-- Coluna esquerda - Informações do perfil -->
      <div class="col-12 col-md-8">
        <!-- Cartão de informações do perfil -->
        <q-card class="q-mb-md">
          <q-card-section class="bg-primary text-white">
            <div class="text-h6">Meu Perfil</div>
            <div class="text-caption">Gerencie suas informações pessoais</div>
          </q-card-section>

          <q-card-section>
            <q-form @submit="updateProfile" class="q-gutter-y-md">
              <div class="row q-col-gutter-md">
                <div class="col-12 col-sm-6">
                  <q-input
                    v-model="formData.name"
                    label="Nome completo"
                    outlined
                    dense
                    :rules="[(val) => !!val || 'Campo obrigatório']"
                  />
                </div>
                <div class="col-12 col-sm-6">
                  <q-input
                    v-model="formData.email"
                    label="E-mail"
                    type="email"
                    outlined
                    dense
                    :rules="[
                      (val) => !!val || 'Campo obrigatório',
                      (val) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val) || 'E-mail inválido',
                    ]"
                  />
                </div>
              </div>

              <div class="row q-col-gutter-md">
                <div class="col-12 col-sm-6">
                  <q-input
                    v-model="formData.phone"
                    label="Telefone"
                    outlined
                    dense
                    mask="(##) #####-####"
                    unmasked-value
                    :rules="[(val) => !val || val.length === 11 || 'Telefone inválido']"
                  >
                    <template v-slot:prepend>
                      <q-icon name="phone" />
                    </template>
                  </q-input>
                </div>
                <div class="col-12 col-sm-6">
                  <q-input
                    v-model="formData.birth_date"
                    label="Data de Nascimento"
                    outlined
                    dense
                    mask="##/##/####"
                  >
                    <template v-slot:prepend>
                      <q-icon name="event" class="cursor-pointer">
                        <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                          <q-date v-model="formData.birth_date" mask="DD/MM/YYYY">
                            <div class="row items-center justify-end">
                              <q-btn v-close-popup label="Fechar" color="primary" flat />
                            </div>
                          </q-date>
                        </q-popup-proxy>
                      </q-icon>
                    </template>
                  </q-input>
                </div>
              </div>

              <div class="row q-mt-md justify-end">
                <q-btn type="submit" color="primary" :loading="loading" :disable="!isFormChanged">
                  <q-icon name="save" class="q-mr-xs" />
                  Salvar alterações
                </q-btn>
              </div>
            </q-form>
          </q-card-section>
        </q-card>

        <!-- Cartão para alteração de senha -->
        <q-card class="q-mb-md">
          <q-card-section class="bg-primary text-white">
            <div class="text-h6">Alterar Senha</div>
            <div class="text-caption">Atualize sua senha de acesso</div>
          </q-card-section>

          <q-card-section>
            <q-form @submit="changePassword" class="q-gutter-y-md">
              <q-input
                v-model="passwordForm.current_password"
                label="Senha Atual"
                :type="showCurrentPassword ? 'text' : 'password'"
                outlined
                dense
                :rules="[(val) => !!val || 'Campo obrigatório']"
              >
                <template v-slot:append>
                  <q-icon
                    :name="showCurrentPassword ? 'visibility_off' : 'visibility'"
                    class="cursor-pointer"
                    @click="showCurrentPassword = !showCurrentPassword"
                  />
                </template>
              </q-input>

              <q-input
                v-model="passwordForm.new_password"
                label="Nova Senha"
                :type="showNewPassword ? 'text' : 'password'"
                outlined
                dense
                :rules="[
                  (val) => !!val || 'Campo obrigatório',
                  (val) => val.length >= 8 || 'Mínimo de 8 caracteres',
                  (val) => /[A-Z]/.test(val) || 'Pelo menos uma letra maiúscula',
                  (val) => /[0-9]/.test(val) || 'Pelo menos um número',
                ]"
              >
                <template v-slot:append>
                  <q-icon
                    :name="showNewPassword ? 'visibility_off' : 'visibility'"
                    class="cursor-pointer"
                    @click="showNewPassword = !showNewPassword"
                  />
                </template>
                <template v-slot:hint>
                  <div class="text-caption text-grey-7">
                    Mínimo de 8 caracteres, incluindo pelo menos uma letra maiúscula e um número
                  </div>
                </template>
              </q-input>

              <q-input
                v-model="passwordForm.new_password_confirmation"
                label="Confirmar Nova Senha"
                :type="showConfirmPassword ? 'text' : 'password'"
                outlined
                dense
                :rules="[
                  (val) => !!val || 'Campo obrigatório',
                  (val) => val === passwordForm.new_password || 'As senhas não conferem',
                ]"
              >
                <template v-slot:append>
                  <q-icon
                    :name="showConfirmPassword ? 'visibility_off' : 'visibility'"
                    class="cursor-pointer"
                    @click="showConfirmPassword = !showConfirmPassword"
                  />
                </template>
              </q-input>

              <div class="row q-mt-md justify-end">
                <q-btn
                  type="submit"
                  color="primary"
                  :loading="changingPassword"
                  :disable="!isPasswordFormValid"
                >
                  <q-icon name="lock" class="q-mr-xs" />
                  Alterar Senha
                </q-btn>
              </div>
            </q-form>
          </q-card-section>
        </q-card>
      </div>

      <!-- Coluna direita - Avatar e informações adicionais -->
      <div class="col-12 col-md-4">
        <q-card class="q-mb-md">
          <q-card-section class="text-center">
            <div class="q-mb-md">
              <q-avatar
                size="120px"
                class="q-mb-sm"
                :class="{ 'cursor-pointer': !uploadingAvatar }"
                @click="!uploadingAvatar && $refs.avatarInput.click()"
              >
                <q-img v-if="user.avatar" :src="user.avatar" />
                <q-icon v-else name="person" size="64px" color="grey-6" />
                <q-badge
                  v-if="!uploadingAvatar"
                  floating
                  color="primary"
                  class="cursor-pointer"
                  @click.stop
                >
                  <q-icon name="photo_camera" size="16px" />
                </q-badge>
                <q-spinner v-else color="primary" size="2em" />
              </q-avatar>
              <input
                ref="avatarInput"
                type="file"
                accept="image/*"
                style="display: none"
                @change="handleAvatarUpload"
              />
              <div class="text-caption text-grey-7 q-mt-sm">Clique na imagem para alterar</div>
            </div>
            <div class="text-h6">{{ user.name }}</div>
            <div class="text-subtitle2 text-grey-7">{{ user.email }}</div>
            <div class="q-mt-md">
              <q-chip v-if="user.email_verified_at" color="positive" text-color="white" dense>
                <q-icon name="verified" class="q-mr-xs" />
                E-mail verificado
              </q-chip>
              <q-chip
                v-else
                color="warning"
                text-color="dark"
                dense
                @click="sendVerificationEmail"
                class="cursor-pointer"
              >
                <q-icon name="warning" class="q-mr-xs" />
                E-mail não verificado
              </q-chip>
            </div>
          </q-card-section>

          <q-separator />

          <q-card-section>
            <div class="text-subtitle2 text-grey-7 q-mb-sm">Informações adicionais</div>
            <div class="q-gutter-y-sm">
              <div v-if="user.phone" class="row items-center">
                <q-icon name="phone" size="16px" class="q-mr-sm text-grey-6" />
                <span>{{ formatPhone(user.phone) }}</span>
              </div>
              <div v-if="user.birth_date" class="row items-center">
                <q-icon name="cake" size="16px" class="q-mr-sm text-grey-6" />
                <span>{{ formatDate(user.birth_date) }}</span>
              </div>
              <div class="row items-center">
                <q-icon name="person_pin" size="16px" class="q-mr-sm text-grey-6" />
                <span>Membro desde {{ formatDate(user.created_at, 'MM/yyyy') }}</span>
              </div>
              <div v-if="user.last_login_at" class="row items-center">
                <q-icon name="login" size="16px" class="q-mr-sm text-grey-6" />
                <span>Último acesso: {{ formatDate(user.last_login_at, 'dd/MM/yyyy HH:mm') }}</span>
              </div>
            </div>
          </q-card-section>
        </q-card>

        <q-card>
          <q-card-section class="bg-grey-2">
            <div class="text-subtitle2">Segurança da Conta</div>
            <div class="text-caption text-grey-7">Mantenha sua conta segura com essas opções</div>
          </q-card-section>

          <q-list separator>
            <q-item clickable @click="showTwoFactorAuthDialog = true">
              <q-item-section avatar>
                <q-icon name="security" color="primary" />
              </q-item-section>
              <q-item-section>
                <q-item-label>Autenticação em Duas Etapas</q-item-label>
                <q-item-label caption>
                  {{ user.two_factor_enabled ? 'Ativada' : 'Não ativada' }}
                </q-item-label>
              </q-item-section>
              <q-item-section side>
                <q-icon name="keyboard_arrow_right" />
              </q-item-section>
            </q-item>

            <q-item clickable @click="showSessions = true">
              <q-item-section avatar>
                <q-icon name="devices" color="primary" />
              </q-item-section>
              <q-item-section>
                <q-item-label>Sessões Ativas</q-item-label>
                <q-item-label caption>
                  {{ activeSessionsCount }} dispositivo(s) conectado(s)
                </q-item-label>
              </q-item-section>
              <q-item-section side>
                <q-icon name="keyboard_arrow_right" />
              </q-item-section>
            </q-item>

            <q-item clickable @click="confirmLogoutAll">
              <q-item-section avatar>
                <q-icon name="logout" color="negative" />
              </q-item-section>
              <q-item-section>
                <q-item-label>Sair de todos os dispositivos</q-item-label>
                <q-item-label caption> Encerra todas as sessões ativas </q-item-label>
              </q-item-section>
            </q-item>
          </q-list>
        </q-card>
      </div>
    </div>

    <!-- Diálogos modais serão adicionados aqui -->
  </q-page>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useQuasar } from 'quasar'
import { useAuth } from 'src/services/auth.service'
import { api } from 'src/boot/axios'

const $q = useQuasar()
const { user, updateUser } = useAuth()

// Dados do formulário de perfil
const formData = ref({
  name: '',
  email: '',
  phone: '',
  birth_date: '',
})

// Dados do formulário de alteração de senha
const passwordForm = ref({
  current_password: '',
  new_password: '',
  new_password_confirmation: '',
})

// Estados de visibilidade das senhas
const showCurrentPassword = ref(false)
const showNewPassword = ref(false)
const showConfirmPassword = ref(false)

// Estados de carregamento
const loading = ref(false)
const changingPassword = ref(false)
const uploadingAvatar = ref(false)

// Estados para autenticação em duas etapas
const showTwoFactorAuthDialog = ref(false)
const twoFactorQrCode = ref('')
const twoFactorSecret = ref('')
const twoFactorCode = ref('')
const enablingTwoFactor = ref(false)
const disablingTwoFactor = ref(false)
const recoveryCodes = ref('')

// Estados para gerenciamento de sessões
const showSessions = ref(false)
const sessions = ref([])
const showLogoutAllConfirm = ref(false)

// Carrega os dados do perfil
onMounted(() => {
  if (user.value) {
    formData.value = {
      name: user.value.name || '',
      email: user.value.email || '',
      phone: user.value.phone || '',
      birth_date: user.value.birth_date || '',
    }
  }

  // Carrega as sessões ativas
  loadSessions()
})

// Verifica se o formulário foi alterado
const isFormChanged = computed(() => {
  return (
    formData.value.name !== user.value.name ||
    formData.value.email !== user.value.email ||
    formData.value.phone !== (user.value.phone || '') ||
    formData.value.birth_date !== (user.value.birth_date || '')
  )
})

// Verifica se o formulário de senha é válido
const isPasswordFormValid = computed(() => {
  return (
    passwordForm.value.current_password &&
    passwordForm.value.new_password &&
    passwordForm.value.new_password_confirmation &&
    passwordForm.value.new_password === passwordForm.value.new_password_confirmation &&
    passwordForm.value.new_password.length >= 8 &&
    /[A-Z]/.test(passwordForm.value.new_password) &&
    /[0-9]/.test(passwordForm.value.new_password)
  )
})

// Conta o número de sessões ativas (excluindo a atual)
const activeSessionsCount = computed(() => {
  return sessions.value.filter((s) => !s.is_current_device).length
})

// Formata o telefone para exibição
function formatPhone(phone) {
  if (!phone) return ''
  const cleaned = ('' + phone).replace(/\D/g, '')
  const match = cleaned.match(/^(\d{2})(\d{5})(\d{4})$/)
  return match ? `(${match[1]}) ${match[2]}-${match[3]}` : phone
}

// Formata a data
function formatDate(date, format = 'dd/MM/yyyy') {
  if (!date) return ''

  const d = new Date(date)
  if (isNaN(d.getTime())) return date // Retorna o valor original se não for uma data válida

  const day = d.getDate().toString().padStart(2, '0')
  const month = (d.getMonth() + 1).toString().padStart(2, '0')
  const year = d.getFullYear()
  const hours = d.getHours().toString().padStart(2, '0')
  const minutes = d.getMinutes().toString().padStart(2, '0')

  return format
    .replace('dd', day)
    .replace('MM', month)
    .replace('yyyy', year)
    .replace('HH', hours)
    .replace('mm', minutes)
}

// Funções de negócio
async function updateProfile() {
  try {
    loading.value = true
    // Simula uma requisição à API
    await new Promise((resolve) => setTimeout(resolve, 1000))

    // Atualiza os dados do usuário no estado global
    const updatedUser = { ...user.value, ...formData.value }
    updateUser(updatedUser)

    $q.notify({
      type: 'positive',
      message: 'Perfil atualizado com sucesso!',
      position: 'top',
      timeout: 3000,
    })
  } catch (error) {
    console.error('Erro ao atualizar perfil:', error)
    $q.notify({
      type: 'negative',
      message: 'Erro ao atualizar perfil. Tente novamente mais tarde.',
      position: 'top',
      timeout: 3000,
    })
  } finally {
    loading.value = false
  }
}

async function changePassword() {
  try {
    changingPassword.value = true
    // Simula uma requisição à API
    await new Promise((resolve) => setTimeout(resolve, 1000))

    // Limpa o formulário
    passwordForm.value = {
      current_password: '',
      new_password: '',
      new_password_confirmation: '',
    }

    $q.notify({
      type: 'positive',
      message: 'Senha alterada com sucesso!',
      position: 'top',
      timeout: 3000,
    })
  } catch (error) {
    console.error('Erro ao alterar senha:', error)
    $q.notify({
      type: 'negative',
      message: 'Erro ao alterar senha. Verifique os dados e tente novamente.',
      position: 'top',
      timeout: 3000,
    })
  } finally {
    changingPassword.value = false
  }
}

async function handleAvatarUpload(event) {
  const file = event.target.files[0]
  if (!file) return

  // Verifica o tipo do arquivo
  if (!file.type.match('image.*')) {
    $q.notify({
      type: 'negative',
      message: 'Por favor, selecione uma imagem válida',
      position: 'top',
      timeout: 3000,
    })
    return
  }

  // Verifica o tamanho do arquivo (máximo 2MB)
  if (file.size > 2 * 1024 * 1024) {
    $q.notify({
      type: 'negative',
      message: 'A imagem não pode ter mais que 2MB',
      position: 'top',
      timeout: 3000,
    })
    return
  }

  try {
    uploadingAvatar.value = true

    // Simula upload do arquivo
    await new Promise((resolve) => setTimeout(resolve, 1500))

    // Cria uma URL temporária para a imagem
    const reader = new FileReader()
    reader.onload = (e) => {
      const avatarUrl = e.target.result
      // Atualiza o avatar do usuário no estado global
      updateUser({ ...user.value, avatar: avatarUrl })

      $q.notify({
        type: 'positive',
        message: 'Foto de perfil atualizada com sucesso!',
        position: 'top',
        timeout: 3000,
      })
    }
    reader.readAsDataURL(file)
  } catch (error) {
    console.error('Erro ao fazer upload do avatar:', error)
    $q.notify({
      type: 'negative',
      message: 'Erro ao atualizar a foto de perfil',
      position: 'top',
      timeout: 3000,
    })
  } finally {
    uploadingAvatar.value = false
  }
}

async function sendVerificationEmail() {
  try {
    // Simula envio de e-mail
    await new Promise((resolve) => setTimeout(resolve, 800))

    $q.notify({
      type: 'info',
      message: 'Link de verificação enviado para o seu e-mail',
      position: 'top',
      timeout: 5000,
    })
  } catch (error) {
    console.error('Erro ao enviar e-mail de verificação:', error)
    $q.notify({
      type: 'negative',
      message: 'Erro ao enviar e-mail de verificação',
      position: 'top',
      timeout: 3000,
    })
  }
}

async function loadSessions() {
  try {
    // Simula carregamento de sessões
    await new Promise((resolve) => setTimeout(resolve, 500))

    // Dados de exemplo para sessões ativas
    sessions.value = [
      {
        id: 1,
        ip_address: '192.168.1.1',
        user_agent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        last_activity: new Date(),
        is_current_device: true,
      },
      {
        id: 2,
        ip_address: '177.220.180.1',
        user_agent: 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X)',
        last_activity: new Date(Date.now() - 3600000 * 2),
        is_current_device: false,
      },
    ]
  } catch (error) {
    console.error('Erro ao carregar sessões:', error)
  }
}

function confirmLogoutAll() {
  $q.dialog({
    title: 'Confirmar ação',
    message: 'Tem certeza que deseja sair de todos os dispositivos?',
    cancel: true,
    persistent: true,
  }).onOk(async () => {
    try {
      // Simula logout de todos os dispositivos
      await new Promise((resolve) => setTimeout(resolve, 800))

      // Atualiza a lista de sessões
      sessions.value = sessions.value.filter((s) => s.is_current_device)

      $q.notify({
        type: 'positive',
        message: 'Você saiu de todos os outros dispositivos',
        position: 'top',
        timeout: 3000,
      })
    } catch (error) {
      console.error('Erro ao sair dos dispositivos:', error)
      $q.notify({
        type: 'negative',
        message: 'Erro ao sair dos dispositivos',
        position: 'top',
        timeout: 3000,
      })
    }
  })
}

// Funções para autenticação em duas etapas
async function prepareTwoFactorAuth() {
  try {
    // Simula geração do QR Code
    await new Promise((resolve) => setTimeout(resolve, 500))
    twoFactorQrCode.value =
      'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZmZmIi8+PHRleHQgeD0iNTAiIHk9IjUwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTAiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGRvbWluYW50LWJhc2VsaW5lPSJtaWRkbGUiIGZpbGw9IiMwMDAiPkRlbW8gUVIgQ29kZTwvdGV4dD48L3N2Zz4='
    twoFactorSecret.value = 'JBSWY3DPEHPK3PXP'
  } catch (error) {
    console.error('Erro ao preparar autenticação em duas etapas:', error)
    $q.notify({
      type: 'negative',
      message: 'Erro ao preparar autenticação em duas etapas',
      position: 'top',
      timeout: 3000,
    })
  }
}

async function enableTwoFactorAuth() {
  try {
    enablingTwoFactor.value = true
    // Simula ativação da autenticação em duas etapas
    await new Promise((resolve) => setTimeout(resolve, 1000))

    // Atualiza o estado do usuário
    updateUser({ ...user.value, two_factor_enabled: true })

    // Gera códigos de recuperação
    recoveryCodes.value = Array(8)
      .fill()
      .map(() => Math.random().toString(36).substring(2, 8).toUpperCase())
      .join('\n')

    $q.notify({
      type: 'positive',
      message: 'Autenticação em duas etapas ativada com sucesso!',
      position: 'top',
      timeout: 5000,
    })
  } catch (error) {
    console.error('Erro ao ativar autenticação em duas etapas:', error)
    $q.notify({
      type: 'negative',
      message: 'Erro ao ativar autenticação em duas etapas',
      position: 'top',
      timeout: 3000,
    })
  } finally {
    enablingTwoFactor.value = false
  }
}

async function disableTwoFactorAuth() {
  try {
    disablingTwoFactor.value = true
    // Simula desativação da autenticação em duas etapas
    await new Promise((resolve) => setTimeout(resolve, 800))

    // Atualiza o estado do usuário
    updateUser({ ...user.value, two_factor_enabled: false })

    // Limpa os estados
    twoFactorCode.value = ''
    recoveryCodes.value = ''

    $q.notify({
      type: 'positive',
      message: 'Autenticação em duas etapas desativada',
      position: 'top',
      timeout: 3000,
    })
  } catch (error) {
    console.error('Erro ao desativar autenticação em duas etapas:', error)
    $q.notify({
      type: 'negative',
      message: 'Erro ao desativar autenticação em duas etapas',
      position: 'top',
      timeout: 3000,
    })
  } finally {
    disablingTwoFactor.value = false
  }
}

// Inicializa a autenticação em duas etapas quando o diálogo for aberto
watch(showTwoFactorAuthDialog, (newVal) => {
  if (newVal && !user.value.two_factor_enabled) {
    prepareTwoFactorAuth()
  }
})

// Função auxiliar para copiar para a área de transferência
function copyToClipboard(text) {
  navigator.clipboard
    .writeText(text)
    .then(() => {
      $q.notify({
        type: 'info',
        message: 'Copiado para a área de transferência',
        position: 'top',
        timeout: 2000,
      })
    })
    .catch((err) => {
      console.error('Erro ao copiar:', err)
    })
}

// Função auxiliar para obter ícone do dispositivo
function getDeviceIcon(session) {
  const ua = session.user_agent.toLowerCase()
  if (ua.includes('windows')) return 'mdi-microsoft-windows'
  if (ua.includes('mac')) return 'mdi-apple'
  if (ua.includes('linux')) return 'mdi-linux'
  if (ua.includes('iphone') || ua.includes('ipad')) return 'mdi-apple-ios'
  if (ua.includes('android')) return 'mdi-android'
  return 'mdi-devices'
}

// Função auxiliar para obter nome amigável do dispositivo
function getDeviceName(session) {
  const ua = session.user_agent.toLowerCase()

  if (ua.includes('windows')) {
    return 'Windows'
  } else if (ua.includes('mac os x')) {
    return 'Mac OS X'
  } else if (ua.includes('linux')) {
    return 'Linux'
  } else if (ua.includes('iphone') || ua.includes('ipad')) {
    return ua.includes('iphone') ? 'iPhone' : 'iPad'
  } else if (ua.includes('android')) {
    return 'Dispositivo Android'
  }

  return 'Dispositivo desconhecido'
}
</script>

<style scoped>
/* Estilos personalizados para a página de perfil */
.q-avatar {
  border: 3px solid #f5f5f5;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
}

.q-avatar:hover {
  transform: scale(1.05);
}

/* Estilo para os cards */
.q-card {
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
  transition:
    transform 0.3s ease,
    box-shadow 0.3s ease;
}

.q-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Estilo para os itens de sessão */
n.session-item {
  transition: background-color 0.2s ease;
}

.session-item:hover {
  background-color: #f5f5f5;
}

/* Estilo para o QR Code */
n.qrcode-container {
  display: flex;
  justify-content: center;
  padding: 20px 0;
}

/* Estilo para os códigos de recuperação */
n.recovery-codes {
  font-family: monospace;
  background-color: #f5f5f5;
  padding: 10px;
  border-radius: 4px;
  white-space: pre-wrap;
  word-break: break-all;
}

/* Ajustes para dispositivos móveis */
n@media (max-width: 600px) {
  .q-pa-md {
    padding: 8px;
  }

  .q-mb-md {
    margin-bottom: 16px;
  }
}
</style>
