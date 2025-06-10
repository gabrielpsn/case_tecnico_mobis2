<template>
  <q-page padding>
    <div class="q-pa-md">
      <div class="row q-mb-md justify-between items-center">
        <div class="text-h5">Motoristas</div>
        <q-btn color="primary" icon="add" label="Novo Motorista" @click="openForm()" />
      </div>

      <!-- Filtros -->
      <q-card class="q-mb-md">
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-6">
              <q-input
                v-model="filters.search"
                label="Pesquisar (Nome, CPF ou E-mail)"
                outlined
                dense
                clearable
                :loading="loading"
                @update:model-value="
                  (val) => {
                    filters.search = val
                  }
                "
              >
                <template v-slot:prepend>
                  <q-icon name="search" />
                </template>
              </q-input>
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- Tabela de Motoristas -->
      <q-card>
        <q-table
          v-model:pagination="pagination"
          :rows="motoristas"
          :columns="columns"
          :loading="loading"
          :rows-per-page-options="[10, 15, 20, 50]"
          row-key="id"
          @request="onRequest"
        >
          <template v-slot:body-cell-actions="props">
            <q-td :props="props" class="q-gutter-x-sm">
              <q-btn flat round color="primary" icon="edit" size="sm" @click="openForm(props.row)">
                <q-tooltip>Editar</q-tooltip>
              </q-btn>
              <q-btn
                flat
                round
                color="negative"
                icon="delete"
                size="sm"
                @click="confirmDelete(props.row)"
              >
                <q-tooltip>Excluir</q-tooltip>
              </q-btn>
            </q-td>
          </template>
        </q-table>
      </q-card>
    </div>

    <!-- Diálogo de formulário -->
    <motorista-form-dialog
      v-model:show="showFormDialog"
      :motorista="selectedMotorista"
      @saved="onMotoristaSaved"
    />
  </q-page>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useQuasar } from 'quasar'
import { debounce } from 'quasar'
import MotoristaFormDialog from './components/MotoristaFormDialog.vue'
import motoristaService from 'src/services/motorista.service'
import { useRouter } from 'vue-router'

const $q = useQuasar()
const router = useRouter()

// Estado
const motoristas = ref([])
const loading = ref(false)
const showFormDialog = ref(false)
const selectedMotorista = ref(null)
const filters = ref({
  search: '',
})

// Debounce para a busca
const debouncedFetchMotoristas = debounce(fetchMotoristas, 500)

// Observa mudanças nos filtros para aplicar o debounce
watch(
  () => filters.value.search,
  () => {
    // Resetar para a primeira página ao buscar
    pagination.value.page = 1
    debouncedFetchMotoristas()
  },
)

// Paginação
const pagination = ref({
  sortBy: 'nome',
  descending: false,
  page: 1,
  rowsPerPage: 15,
  rowsNumber: 0,
})

// Colunas da tabela
const columns = [
  {
    name: 'nome',
    label: 'Nome',
    field: 'nome',
    align: 'left',
    sortable: true,
  },
  {
    name: 'email',
    label: 'E-mail',
    field: 'email',
    align: 'left',
    sortable: true,
  },
  {
    name: 'cpf',
    label: 'CPF',
    field: 'cpf',
    align: 'left',
    sortable: true,
  },
  {
    name: 'categoria_cnh',
    label: 'Categoria CNH',
    field: 'categoria_cnh',
    align: 'center',
    sortable: true,
  },
  {
    name: 'actions',
    label: 'Ações',
    field: 'actions',
    align: 'center',
  },
]

// Métodos
async function fetchMotoristas() {
  try {
    loading.value = true

    const { page, rowsPerPage, sortBy, descending } = pagination.value
    const params = {
      page,
      per_page: rowsPerPage,
      search: filters.value.search || undefined,
      sort_by: sortBy,
      sort_order: descending ? 'desc' : 'asc',
    }

    const response = await motoristaService.getMotoristas(params)

    motoristas.value = response.data
    pagination.value.rowsNumber = response.meta.total
    pagination.value.page = response.meta.current_page
    pagination.value.rowsPerPage = response.meta.per_page
  } catch (error) {
    console.error('Erro ao buscar motoristas:', error)
    $q.notify({
      type: 'negative',
      message: 'Erro ao carregar a lista de motoristas',
    })
  } finally {
    loading.value = false
  }
}

function onRequest(props) {
  const { page, rowsPerPage, sortBy, descending } = props.pagination

  pagination.value.page = page
  pagination.value.rowsPerPage = rowsPerPage
  pagination.value.sortBy = sortBy
  pagination.value.descending = descending

  fetchMotoristas()
}

function openForm(motorista = null) {
  if (motorista) {
    router.push({ name: 'motoristas-editar', params: { id: motorista.id } })
  } else {
    router.push({ name: 'motoristas-novo' })
  }
}

function onMotoristaSaved() {
  fetchMotoristas()
  showFormDialog.value = false
}

async function confirmDelete(motorista) {
  $q.dialog({
    title: 'Confirmar Exclusão',
    message: `Tem certeza que deseja excluir o motorista ${motorista.nome}?`,
    cancel: true,
    persistent: true,
  }).onOk(async () => {
    try {
      await motoristaService.deleteMotorista(motorista.id)
      $q.notify({
        type: 'positive',
        message: 'Motorista excluído com sucesso!',
      })
      fetchMotoristas()
    } catch (error) {
      console.error('Erro ao excluir motorista:', error)
      $q.notify({
        type: 'negative',
        message: 'Erro ao excluir o motorista',
      })
    }
  })
}

// Inicialização
onMounted(() => {
  fetchMotoristas()
})
</script>
