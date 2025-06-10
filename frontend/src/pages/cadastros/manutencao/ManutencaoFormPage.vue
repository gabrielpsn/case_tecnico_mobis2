<template>
  <q-page padding>
    <div class="q-mb-lg">
      <div class="row items-center q-mb-md">
        <q-btn flat round dense icon="arrow_back" class="q-mr-sm" @click="$router.go(-1)" />
        <div class="text-h5">
          {{ isEditing ? 'Editar Manutenção' : 'Nova Manutenção' }}
        </div>
      </div>

      <q-form @submit.prevent="salvarManutencao" class="q-gutter-y-md">
        <q-card>
          <q-card-section>
            <div class="text-subtitle1">Dados da Manutenção</div>
            <q-separator class="q-my-md" />

            <div class="row q-col-gutter-md">
              <div class="col-12 col-md-6">
                <q-select
                  v-model="form.veiculo_id"
                  :options="veiculos"
                  option-label="placa_modelo"
                  option-value="id"
                  label="Veículo *"
                  outlined
                  :rules="[(val) => !!val || 'Campo obrigatório']"
                  dense
                  emit-value
                  map-options
                  :loading="carregandoVeiculos"
                >
                  <template v-slot:option="{ itemProps, opt, selected, toggleOption }">
                    <q-item v-bind="itemProps">
                      <q-item-section>
                        <q-item-label>{{ opt.placa }} - {{ opt.modelo }}</q-item-label>
                        <q-item-label caption>{{ opt.marca }} - {{ opt.ano }}</q-item-label>
                      </q-item-section>
                    </q-item>
                  </template>
                  <template v-slot:no-option>
                    <q-item>
                      <q-item-section class="text-grey"> Nenhum veículo encontrado </q-item-section>
                    </q-item>
                  </template>
                </q-select>
              </div>

              <div class="col-12 col-md-3">
                <q-select
                  v-model="form.tipo"
                  :options="tipoOptions"
                  label="Tipo de Manutenção *"
                  outlined
                  :rules="[(val) => !!val || 'Campo obrigatório']"
                  dense
                  emit-value
                  map-options
                />
              </div>

              <div class="col-12 col-md-3">
                <q-input
                  v-model="form.data_manutencao"
                  label="Data da Manutenção *"
                  outlined
                  mask="##/##/####"
                  :rules="[(val) => !!val || 'Campo obrigatório']"
                  dense
                >
                  <template v-slot:append>
                    <q-icon name="event" class="cursor-pointer">
                      <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                        <q-date v-model="form.data_manutencao" mask="DD/MM/YYYY" />
                      </q-popup-proxy>
                    </q-icon>
                  </template>
                </q-input>
              </div>

              <div class="col-12 col-md-6">
                <q-input
                  v-model="form.descricao"
                  label="Descrição *"
                  type="textarea"
                  outlined
                  :rules="[(val) => !!val || 'Campo obrigatório']"
                  dense
                  autogrow
                />
              </div>

              <div class="col-12 col-md-6">
                <q-input
                  v-model="form.observacoes"
                  label="Observações"
                  type="textarea"
                  outlined
                  dense
                  autogrow
                />
              </div>

              <div class="col-12 col-md-3">
                <q-input
                  v-model.number="form.km"
                  type="number"
                  label="km"
                  outlined
                  :rules="[(val) => val >= 0 || 'Valor inválido']"
                  dense
                >
                  <template v-slot:append>
                    <q-icon name="directions_car" />
                  </template>
                </q-input>
              </div>

              <div class="col-12 col-md-3">
                <q-input
                  v-model.number="form.custo"
                  type="number"
                  label="Custo (R$)"
                  outlined
                  :rules="[(val) => val >= 0 || 'Valor inválido']"
                  prefix="R$"
                  dense
                />
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
import { useQuasar, date } from 'quasar'
import { useRoute, useRouter } from 'vue-router'
import { api } from 'src/boot/axios'

const $q = useQuasar()
const route = useRoute()
const router = useRouter()
const loading = ref(false)
const carregandoVeiculos = ref(false)
const veiculos = ref([])

const isEditing = computed(() => route.name === 'manutencao-editar')

const tipoOptions = [
  { label: 'Preventiva', value: 'preventiva' },
  { label: 'Corretiva', value: 'corretiva' },
  { label: 'Preditiva', value: 'preditiva' },
  { label: 'Corretiva Emergencial', value: 'corretiva_emergencial' },
]

const form = ref({
  veiculo_id: null,
  tipo: 'preventiva',
  data_manutencao: date.formatDate(Date.now(), 'YYYY-MM-DD'),
  descricao: '',
  observacoes: '',
  km: 0,
  custo: 0,
  proxima_manutencao_km: null,
  proxima_manutencao_data: null,
})

async function carregarVeiculos() {
  try {
    carregandoVeiculos.value = true
    const response = await api.get('/veiculos')

    // Adiciona uma propriedade composta para exibição
    veiculos.value = (response.data || []).map((veiculo) => ({
      ...veiculo,
      placa_modelo: `${veiculo.placa} - ${veiculo.modelo}`,
    }))
  } catch (error) {
    console.error('Erro ao carregar veículos:', error)
    $q.notify({
      type: 'negative',
      message: 'Erro ao carregar a lista de veículos',
      position: 'top',
    })
  } finally {
    carregandoVeiculos.value = false
  }
}

async function carregarManutencao(id) {
  try {
    loading.value = true
    const response = await api.get(`/manutencoes/${id}`, {
      params: {
        with: 'veiculo',
      },
    })

    // Preenche o formulário com os dados da manutenção
    const manutencao = response.data
    form.value = {
      veiculo_id: manutencao.veiculo_id,
      tipo: manutencao.tipo,
      data_manutencao: manutencao.data_manutencao,
      descricao: manutencao.descricao,
      observacoes: manutencao.observacoes || '',
      km: manutencao.km || 0,
      custo: parseFloat(manutencao.custo) || 0,
      proxima_manutencao_km: manutencao.proxima_manutencao_km,
      proxima_manutencao_data: manutencao.proxima_manutencao_data,
    }
  } catch (error) {
    console.error('Erro ao carregar manutenção:', error)
    $q.notify({
      type: 'negative',
      message: 'Erro ao carregar os dados da manutenção',
      position: 'top',
    })
    router.push({ name: 'manutencoes' })
  } finally {
    loading.value = false
  }
}

async function salvarManutencao() {
  try {
    loading.value = true

    const data = {
      ...form.value,
      km: form.value.km || 0,
      custo: parseFloat(form.value.custo) || 0,
      proxima_manutencao_km: form.value.proxima_manutencao_km || null,
      proxima_manutencao_data: form.value.proxima_manutencao_data || null,
    }

    if (isEditing.value) {
      await api.put(`/manutencoes/${route.params.id}`, data)
      $q.notify({
        type: 'positive',
        message: 'Manutenção atualizada com sucesso!',
        position: 'top',
      })
    } else {
      await api.post('/manutencoes', data)
      $q.notify({
        type: 'positive',
        message: 'Manutenção cadastrada com sucesso!',
        position: 'top',
      })
    }

    router.push({ name: 'manutencoes' })
  } catch (error) {
    console.error('Erro ao salvar manutenção:', error)

    let message = 'Erro ao salvar a manutenção'
    if (error.response?.data?.message) {
      message = error.response.data.message
    } else if (error.response?.data?.errors) {
      // Se houver erros de validação, mostre o primeiro
      const firstError = Object.values(error.response.data.errors)[0][0]
      if (firstError) {
        message = firstError
      }
    }

    $q.notify({
      type: 'negative',
      message,
      position: 'top',
      actions: [{ icon: 'close', color: 'white' }],
    })
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  await carregarVeiculos()

  if (isEditing.value && route.params.id) {
    await carregarManutencao(route.params.id)
  } else {
    // Se for uma nova manutenção, define a data atual
    form.value.data_manutencao = date.formatDate(Date.now(), 'YYYY-MM-DD')
  }
})
</script>
