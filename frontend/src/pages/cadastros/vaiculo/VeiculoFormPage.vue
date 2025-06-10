<template>
  <q-page padding>
    <div class="q-mb-lg">
      <div class="row items-center q-mb-md">
        <q-btn flat round dense icon="arrow_back" class="q-mr-sm" @click="$router.go(-1)" />
        <div class="text-h5">
          {{ isEditing ? 'Editar Veículo' : 'Novo Veículo' }}
        </div>
      </div>

      <q-form @submit.prevent="salvarVeiculo" class="q-gutter-y-md">
        <q-card>
          <q-card-section>
            <div class="text-subtitle1">Dados do Veículo</div>
            <q-separator class="q-my-md" />

            <div class="row q-col-gutter-md">
              <div class="col-12 col-md-3">
                <q-input
                  v-model="form.placa"
                  label="Placa *"
                  outlined
                  :rules="[(val) => !!val || 'Campo obrigatório', isValidPlaca]"
                  mask="AAA-####"
                  unmasked-value
                  hint="Formato: AAA-0000"
                  dense
                />
              </div>
              <div class="col-12 col-md-5">
                <q-input
                  v-model="form.modelo"
                  label="Modelo *"
                  outlined
                  :rules="[(val) => !!val || 'Campo obrigatório']"
                  dense
                />
              </div>
              <div class="col-12 col-md-4">
                <q-input
                  v-model="form.marca"
                  label="Marca *"
                  outlined
                  :rules="[(val) => !!val || 'Campo obrigatório']"
                  dense
                />
              </div>
              <div class="col-12 col-md-2">
                <q-input
                  v-model.number="form.ano"
                  type="number"
                  label="Ano *"
                  outlined
                  :rules="[
                    (val) => !!val || 'Campo obrigatório',
                    (val) => (val >= 1900 && val <= new Date().getFullYear() + 1) || 'Ano inválido',
                  ]"
                  dense
                />
              </div>
              <div class="col-12 col-md-3">
                <q-select
                  v-model="form.cor"
                  :options="cores"
                  label="Cor *"
                  outlined
                  :rules="[(val) => !!val || 'Campo obrigatório']"
                  dense
                  emit-value
                  map-options
                />
              </div>
              <div class="col-12 col-md-3">
                <q-select
                  v-model="form.status"
                  :options="statusOptions"
                  label="Status *"
                  outlined
                  :rules="[(val) => !!val || 'Campo obrigatório']"
                  dense
                  emit-value
                  map-options
                />
              </div>
              <div class="col-12 col-md-4">
                <q-input
                  v-model="form.quilometragem"
                  type="number"
                  label="Quilometragem *"
                  outlined
                  :rules="[
                    (val) => (val !== null && val !== '') || 'Campo obrigatório',
                    (val) => val >= 0 || 'A quilometragem não pode ser negativa',
                  ]"
                  dense
                  step="0.01"
                  :hint="'Quilometragem atual: ' + formatarQuilometragem(form.quilometragem)"
                >
                  <template v-slot:append>
                    <q-icon name="directions_car" />
                  </template>
                </q-input>
              </div>
              <div class="col-12 col-md-6">
                <q-input
                  v-model="form.chassi"
                  label="Número do Chassi"
                  outlined
                  hint="Opcional - Número único de identificação do veículo"
                  dense
                  maxlength="17"
                />
              </div>
              <div class="col-12 col-md-6">
                <q-select
                  v-model="form.motorista_id"
                  :options="motoristas"
                  label="Motorista Responsável"
                  outlined
                  dense
                  clearable
                  hint="Opcional - Selecione o motorista responsável"
                >
                  <template v-slot:no-option>
                    <q-item>
                      <q-item-section class="text-grey">
                        Nenhum motorista encontrado
                      </q-item-section>
                    </q-item>
                  </template>
                </q-select>
              </div>
              <div class="col-12">
                <q-input
                  v-model="form.observacoes"
                  label="Observações"
                  outlined
                  type="textarea"
                  rows="2"
                  dense
                  hint="Informações adicionais sobre o veículo"
                />
              </div>
            </div>
          </q-card-section>

          <q-card-actions align="right" class="q-pa-md">
            <q-btn flat label="Cancelar" color="negative" @click="$router.go(-1)" />
            <q-btn type="submit" label="Salvar" color="primary" :loading="loading" />
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

const $q = useQuasar()
const route = useRoute()
const router = useRouter()

const isEditing = computed(() => route.name === 'veiculos-editar')
const loading = ref(false)

const statusOptions = [
  { label: 'Disponível', value: 'disponivel' },
  { label: 'Em Manutenção', value: 'em_manutencao' },
  { label: 'Em Uso', value: 'em_uso' },
  { label: 'Desativado', value: 'desativado' },
]

const cores = [
  'Branco',
  'Preto',
  'Prata',
  'Cinza',
  'Vermelho',
  'Azul',
  'Verde',
  'Amarelo',
  'Laranja',
  'Marrom',
  'Outra',
]

const form = ref({
  placa: '',
  modelo: '',
  marca: '',
  ano: null,
  cor: '',
  chassi: '',
  quilometragem: 0,
  status: 'disponivel',
  motorista_id: null,
  observacoes: '',
})

const motoristas = ref([])

const isValidPlaca = (val) => {
  const placaRegex = /^[A-Z]{3}[0-9][A-Z0-9][0-9]{2}$/
  return placaRegex.test(val) || 'Formato de placa inválido. Use o formato: AAA-0000'
}

const formatarQuilometragem = (valor) => {
  if (valor === null || valor === undefined) return '0 km'
  return (
    new Intl.NumberFormat('pt-BR', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    }).format(valor) + ' km'
  )
}

const carregarMotoristas = async () => {
  try {
    const { data } = await api.get('/motoristas')
    motoristas.value = data.data.map((m) => ({
      label: `${m.nome} (${m.cpf})`,
      value: m.id,
    }))
  } catch (error) {
    console.error('Erro ao carregar motoristas:', error)
    $q.notify({
      type: 'negative',
      message: 'Não foi possível carregar a lista de motoristas',
    })
  }
}

const carregarVeiculo = async (id) => {
  try {
    loading.value = true
    const { data } = await api.get(`/veiculos/${id}`)
    form.value = {
      ...data,
      motorista_id: data.motorista_id || null,
    }
  } catch (error) {
    console.error('Erro ao carregar veículo:', error)
    $q.notify({
      type: 'negative',
      message: 'Não foi possível carregar os dados do veículo',
    })
    router.push({ name: 'veiculos' })
  } finally {
    loading.value = false
  }
}

const salvarVeiculo = async () => {
  try {
    loading.value = true

    const payload = {
      ...form.value,
      placa: form.value.placa.toUpperCase(),
      motorista_id: form.value.motorista_id.value || null,
    }

    if (isEditing.value) {
      await api.put(`/veiculos/${route.params.id}`, payload)
      $q.notify({
        type: 'positive',
        message: 'Veículo atualizado com sucesso!',
      })
    } else {
      await api.post('/veiculos', payload)
      $q.notify({
        type: 'positive',
        message: 'Veículo cadastrado com sucesso!',
      })
    }

    router.push({ name: 'veiculos' })
  } catch (error) {
    console.error('Erro ao salvar veículo:', error)
    const message = error.response?.data?.message || 'Não foi possível salvar o veículo'
    $q.notify({
      type: 'negative',
      message,
    })
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  await carregarMotoristas()

  if (isEditing.value && route.params.id) {
    await carregarVeiculo(route.params.id)
  } else {
    // Valores padrão para novo veículo
    form.value = {
      ...form.value,
      status: 'disponivel',
      quilometragem: 0,
    }
  }
})
</script>
