<template>
  <q-page padding>
    <div class="q-mb-lg">
      <div class="row items-center q-mb-md">
        <q-btn flat round dense icon="arrow_back" class="q-mr-sm" @click="$router.go(-1)" />
        <div class="text-h5">
          {{ isEditing ? 'Editar Motorista' : 'Novo Motorista' }}
        </div>
      </div>

      <q-form @submit.prevent="salvarMotorista" class="q-gutter-y-md">
        <q-card>
          <q-card-section>
            <div class="text-subtitle1">Dados Pessoais</div>
            <q-separator class="q-my-md" />

            <div class="row q-col-gutter-md">
              <div class="col-12 col-md-6">
                <q-input
                  v-model="form.nome"
                  label="Nome completo *"
                  outlined
                  :rules="[(val) => !!val || 'Campo obrigatório']"
                  dense
                />
              </div>
              <div class="col-12 col-md-6">
                <q-input
                  v-model="form.email"
                  label="E-mail *"
                  type="email"
                  outlined
                  :rules="[
                    (val) => !!val || 'Campo obrigatório',
                    (val) => isValidEmail(val) || 'E-mail inválido',
                  ]"
                  dense
                />
              </div>
              <div class="col-12 col-md-4">
                <q-input
                  v-model="form.telefone"
                  label="Telefone *"
                  outlined
                  mask="(##) #####-####"
                  :rules="[(val) => !!val || 'Campo obrigatório']"
                  dense
                />
              </div>
              <div class="col-12 col-md-4">
                <q-input
                  v-model="form.cpf"
                  label="CPF *"
                  outlined
                  mask="###.###.###-##"
                  :rules="[
                    (val) => !!val || 'Campo obrigatório',
                    (val) => isValidCPF(val) || 'CPF inválido',
                  ]"
                  dense
                />
              </div>
              <div class="col-12 col-md-4">
                <q-input
                  v-model="form.data_nascimento"
                  label="Data de Nascimento *"
                  outlined
                  mask="##/##/####"
                  :rules="[(val) => !!val || 'Campo obrigatório']"
                  dense
                >
                  <template v-slot:append>
                    <q-icon name="event" class="cursor-pointer">
                      <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                        <q-date v-model="form.data_nascimento" mask="DD/MM/YYYY" />
                      </q-popup-proxy>
                    </q-icon>
                  </template>
                </q-input>
              </div>
            </div>
          </q-card-section>

          <q-card-section>
            <div class="text-subtitle1">Documentação</div>
            <q-separator class="q-my-md" />

            <div class="row q-col-gutter-md">
              <div class="col-12 col-md-6">
                <q-input
                  v-model="form.cnh"
                  label="Número da CNH *"
                  outlined
                  :rules="[(val) => !!val || 'Campo obrigatório']"
                  dense
                />
              </div>
              <div class="col-12 col-md-6">
                <q-select
                  v-model="form.categoria_cnh"
                  :options="categoriasCNH"
                  label="Categoria CNH *"
                  outlined
                  :rules="[(val) => !!val || 'Campo obrigatório']"
                  dense
                />
              </div>
              <div class="col-12 col-md-6">
                <q-input
                  v-model="form.validade_cnh"
                  label="Validade da CNH *"
                  outlined
                  mask="##/##/####"
                  :rules="[(val) => !!val || 'Campo obrigatório']"
                  dense
                >
                  <template v-slot:append>
                    <q-icon name="event" class="cursor-pointer">
                      <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                        <q-date v-model="form.validade_cnh" mask="DD/MM/YYYY" />
                      </q-popup-proxy>
                    </q-icon>
                  </template>
                </q-input>
              </div>
            </div>
          </q-card-section>

          <q-card-actions align="right" class="q-pa-md">
            <q-btn flat label="Cancelar" color="negative" @click="$router.go(-1)" class="q-mr-sm" />
            <q-btn
              type="submit"
              :label="isEditing ? 'Atualizar' : 'Salvar'"
              color="primary"
              :loading="loading"
            />
          </q-card-actions>
        </q-card>
      </q-form>
    </div>
  </q-page>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useQuasar } from 'quasar'
import { useRoute, useRouter } from 'vue-router'
import { api } from 'src/boot/axios'
import motoristaService from 'src/services/motorista.service'

const $q = useQuasar()
const route = useRoute()
const router = useRouter()
const loading = ref(false)

const isEditing = computed(() => route.name === 'motoristas-editar')

const categoriasCNH = ['A', 'B', 'C', 'D', 'E', 'AB', 'AC', 'AD', 'AE']

const form = ref({
  nome: '',
  email: '',
  telefone: '',
  cpf: '',
  data_nascimento: '',
  cnh: '',
  categoria_cnh: '',
  validade_cnh: '',
})

function isValidEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return re.test(email)
}

function isValidCPF(cpf) {
  cpf = cpf.replace(/[\D]/g, '')

  if (cpf.length !== 11) return false

  // Verifica se todos os dígitos são iguais
  if (/^(\d)\1+$/.test(cpf)) return false

  // Validação do CPF
  let soma = 0
  let resto

  for (let i = 1; i <= 9; i++) {
    soma = soma + parseInt(cpf.substring(i - 1, i)) * (11 - i)
  }

  resto = (soma * 10) % 11

  if (resto === 10 || resto === 11) resto = 0
  if (resto !== parseInt(cpf.substring(9, 10))) return false

  soma = 0

  for (let i = 1; i <= 10; i++) {
    soma = soma + parseInt(cpf.substring(i - 1, i)) * (12 - i)
  }

  resto = (soma * 10) % 11

  if (resto === 10 || resto === 11) resto = 0
  if (resto !== parseInt(cpf.substring(10, 11))) return false

  return true
}

async function carregarMotorista(id) {
  try {
    loading.value = true
    const response = await motoristaService.getMotorista(id)
    console.log(response)
    form.value = response.data
  } catch (error) {
    console.error('Erro ao carregar motorista:', error)
    $q.notify({
      type: 'negative',
      message: 'Erro ao carregar os dados do motorista',
      position: 'top',
    })
    router.push({ name: 'motoristas' })
  } finally {
    loading.value = false
  }
}

async function salvarMotorista() {
  try {
    loading.value = true

    const data = {
      ...form.value,
      cpf: form.value.cpf.replace(/[\D]/g, ''),
      telefone: form.value.telefone.replace(/[\D]/g, ''),
      data_nascimento: formatarDataParaAPI(form.value.data_nascimento),
      validade_cnh: formatarDataParaAPI(form.value.validade_cnh),
    }

    if (isEditing.value) {
      await api.put(`/motoristas/${route.params.id}`, data)
      $q.notify({
        type: 'positive',
        message: 'Motorista atualizado com sucesso!',
        position: 'top',
      })
    } else {
      await api.post('/motoristas', data)
      $q.notify({
        type: 'positive',
        message: 'Motorista cadastrado com sucesso!',
        position: 'top',
      })
    }

    router.push({ name: 'motoristas' })
  } catch (error) {
    console.error('Erro ao salvar motorista:', error)

    let message = 'Erro ao salvar o motorista'
    if (error.response?.data?.message) {
      message = error.response.data.message
    }

    $q.notify({
      type: 'negative',
      message,
      position: 'top',
    })
  } finally {
    loading.value = false
  }
}

function formatarDataParaAPI(data) {
  if (!data) return ''
  const [dia, mes, ano] = data.split('/')
  return `${ano}-${mes.padStart(2, '0')}-${dia.padStart(2, '0')}`
}

onMounted(async () => {
  if (isEditing.value && route.params.id) {
    await carregarMotorista(route.params.id)
  }
})
</script>
