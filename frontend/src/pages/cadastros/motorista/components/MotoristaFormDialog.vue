<template>
  <q-dialog v-model="show" @hide="onHide" persistent>
    <q-card style="min-width: 500px">
      <q-card-section class="row items-center q-pb-none">
        <div class="text-h6">{{ motorista?.id ? 'Editar' : 'Novo' }} Motorista</div>
        <q-space />
        <q-btn icon="close" flat round dense v-close-popup />
      </q-card-section>

      <q-form @submit.prevent="onSubmit">
        <q-card-section class="q-pt-none">
          <div class="row q-col-gutter-md">
            <div class="col-12">
              <q-input
                v-model="formData.nome"
                label="Nome *"
                outlined
                dense
                :rules="[(val) => !!val || 'Campo obrigatório']"
              />
            </div>

            <div class="col-12 col-md-6">
              <q-input
                v-model="formData.email"
                label="E-mail *"
                type="email"
                outlined
                dense
                :rules="[
                  (val) => !!val || 'Campo obrigatório',
                  (val) => /.+@.+\..+/.test(val) || 'E-mail inválido',
                ]"
              />
            </div>

            <div class="col-12 col-md-6">
              <q-input
                v-model="formData.cpf"
                label="CPF *"
                mask="###.###.###-##"
                outlined
                dense
                :rules="[(val) => !!val || 'Campo obrigatório']"
              />
            </div>

            <div class="col-12 col-md-6">
              <q-input
                v-model="formData.data_nascimento"
                label="Data de Nascimento *"
                type="date"
                outlined
                dense
                :rules="[(val) => !!val || 'Campo obrigatório']"
              />
            </div>

            <div class="col-12 col-md-6">
              <q-select
                v-model="formData.categoria_cnh"
                :options="categoriasCNH"
                label="Categoria CNH *"
                outlined
                dense
                :rules="[(val) => !!val || 'Campo obrigatório']"
              />
            </div>

            <div class="col-12">
              <q-input
                v-model="formData.telefone"
                label="Telefone *"
                mask="(##) #####-####"
                outlined
                dense
                :rules="[(val) => !!val || 'Campo obrigatório']"
              />
            </div>
          </div>
        </q-card-section>

        <q-card-actions align="right" class="text-primary">
          <q-btn flat label="Cancelar" v-close-popup />
          <q-btn color="primary" label="Salvar" type="submit" :loading="loading" />
        </q-card-actions>
      </q-form>
    </q-card>
  </q-dialog>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
import { useQuasar } from 'quasar'
import { api } from 'src/boot/axios'

const props = defineProps({
  modelValue: Boolean,
  motorista: {
    type: Object,
    default: null,
  },
})

const emit = defineEmits(['update:modelValue', 'saved'])

const $q = useQuasar()

const show = computed({
  get() {
    return props.modelValue
  },
  set(value) {
    emit('update:modelValue', value)
  },
})

const loading = ref(false)
const formData = ref({
  nome: '',
  email: '',
  cpf: '',
  data_nascimento: '',
  categoria_cnh: '',
  telefone: '',
})

const categoriasCNH = ['A', 'B', 'C', 'D', 'E', 'AB', 'AC', 'AD', 'AE']

// Atualiza o formulário quando o motorista prop mudar
watch(
  () => props.motorista,
  (newVal) => {
    if (newVal) {
      formData.value = { ...newVal }
    } else {
      resetForm()
    }
  },
  { immediate: true },
)

function resetForm() {
  formData.value = {
    nome: '',
    email: '',
    cpf: '',
    data_nascimento: '',
    categoria_cnh: '',
    telefone: '',
  }
}

function onHide() {
  resetForm()
  emit('update:modelValue', false)
}

async function onSubmit() {
  try {
    loading.value = true

    const url = formData.value.id ? `/api/motoristas/${formData.value.id}` : '/api/motoristas'

    const method = formData.value.id ? 'put' : 'post'

    await api[method](url, formData.value)

    $q.notify({
      type: 'positive',
      message: `Motorista ${formData.value.id ? 'atualizado' : 'cadastrado'} com sucesso!`,
    })

    emit('saved')
    emit('update:modelValue', false)
  } catch (error) {
    console.error('Erro ao salvar motorista:', error)

    let message = 'Erro ao salvar o motorista'
    if (error.response?.data?.message) {
      message = error.response.data.message
    }

    $q.notify({
      type: 'negative',
      message,
    })
  } finally {
    loading.value = false
  }
}
</script>
