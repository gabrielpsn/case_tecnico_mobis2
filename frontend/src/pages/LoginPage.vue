<template>
  <div class="row justify-center items-center full-height bg-grey-2">
    <div class="col-12 col-sm-8 col-md-6 col-lg-4 q-pa-md">
      <q-card class="q-pa-lg shadow-5">
        <q-card-section class="text-center">
          <div class="text-h4 text-primary q-mb-md">Bem-vindo</div>
          <div class="text-subtitle1 text-grey-7">Faça login para continuar</div>
        </q-card-section>

        <q-card-section>
          <q-form @submit.prevent="handleLogin" class="q-gutter-y-md">
            <q-input
              v-model="form.email"
              label="E-mail"
              type="email"
              outlined
              dense
              :rules="[
                (val) => !!val || 'E-mail é obrigatório',
                (val) => /.+@.+\..+/.test(val) || 'E-mail inválido',
              ]"
              lazy-rules
              :error-message="errors.email"
              :error="!!errors.email"
              @update:model-value="clearError('email')"
            >
              <template v-slot:prepend>
                <q-icon name="email" />
              </template>
            </q-input>

            <q-input
              v-model="form.password"
              label="Senha"
              :type="showPassword ? 'text' : 'password'"
              outlined
              dense
              :rules="[(val) => !!val || 'Senha é obrigatória']"
              :error-message="errors.password"
              :error="!!errors.password"
              @update:model-value="clearError('password')"
            >
              <template v-slot:prepend>
                <q-icon name="lock" />
              </template>
              <template v-slot:append>
                <q-icon
                  :name="showPassword ? 'visibility_off' : 'visibility'"
                  class="cursor-pointer"
                  @click="showPassword = !showPassword"
                />
              </template>
            </q-input>

            <div class="text-right q-mb-md">
              <q-btn
                flat
                dense
                no-caps
                color="primary"
                label="Esqueceu sua senha?"
                class="q-pa-none"
                :to="{ name: 'forgot-password' }"
              />
            </div>

            <q-btn
              type="submit"
              color="primary"
              label="Entrar"
              class="full-width q-mt-md"
              :loading="loading"
              :disable="loading"
              size="lg"
            />

            <div class="text-center q-mt-lg">
              <div class="text-grey-7 q-mb-sm">Não tem uma conta?</div>
              <q-btn flat color="primary" label="Criar conta" :to="{ name: 'register' }" no-caps />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useQuasar } from 'quasar'
import { useRoute } from 'vue-router'
import { useAuth } from 'src/services/auth.service'

const $q = useQuasar()
const route = useRoute()
const { login, setReturnUrl } = useAuth()

const loading = ref(false)
const showPassword = ref(false)
const errors = reactive({})

const form = reactive({
  email: '',
  password: '',
})

// Define a URL de retorno se existir
if (route.query.returnUrl) {
  setReturnUrl(route.query.returnUrl)
}

function clearError(field) {
  if (errors[field]) {
    errors[field] = ''
  }
}

async function handleLogin() {
  try {
    loading.value = true

    // Limpa erros anteriores
    Object.keys(errors).forEach((key) => {
      errors[key] = ''
    })

    await login(form.email, form.password)

    $q.notify({
      type: 'positive',
      message: 'Login realizado com sucesso!',
      position: 'top',
      timeout: 2000,
    })
  } catch (error) {
    if (error.response?.data?.errors) {
      // Trata erros de validação do backend
      Object.entries(error.response.data.errors).forEach(([field, message]) => {
        errors[field] = Array.isArray(message) ? message[0] : message
      })
    } else {
      $q.notify({
        type: 'negative',
        message: error.message || 'Falha ao realizar login. Verifique suas credenciais.',
        position: 'top',
        timeout: 3000,
      })
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.full-height {
  min-height: 100vh;
}
</style>
