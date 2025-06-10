<template>
  <q-page padding>
    <div class="q-mb-lg">
      <div class="row items-center justify-between q-mb-md">
        <div>
          <div class="text-h5">Manutenções</div>
          <div class="text-subtitle2 text-grey-7">Gerencie as manutenções dos veículos</div>
        </div>
        <q-btn
          color="primary"
          icon="add"
          label="Nova Manutenção"
          @click="$router.push({ name: 'manutencao-nova' })"
        />
      </div>

      <q-card>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-6">
              <q-input
                v-model="filter"
                outlined
                dense
                label="Buscar"
                placeholder="Placa, tipo, status..."
                clearable
                @keyup.enter="carregarManutencoes"
                @clear="carregarManutencoes"
              >
                <template v-slot:append>
                  <q-icon name="search" />
                </template>
              </q-input>
            </div>
          </div>
        </q-card-section>

        <q-separator />

        <q-table
          v-model:pagination="pagination"
          :rows="manutencoes"
          :columns="columns"
          row-key="id"
          :loading="loading"
          @request="onRequest"
          binary-state-sort
          class="q-mb-md"
          :rows-per-page-options="[10, 20, 50, 100]"
          :rows-per-page-label="'Itens por página'"
        >
          <template v-slot:body-cell-actions="props">
            <q-td :props="props" class="q-gutter-x-sm">
              <q-btn
                flat
                round
                color="primary"
                icon="edit"
                size="sm"
                @click="editarManutencao(props.row)"
              >
                <q-tooltip>Editar</q-tooltip>
              </q-btn>
              <q-btn
                flat
                round
                color="negative"
                icon="delete"
                size="sm"
                @click="confirmarExclusao(props.row)"
              >
                <q-tooltip>Excluir</q-tooltip>
              </q-btn>
            </q-td>
          </template>
        </q-table>
      </q-card>
    </div>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { useRouter } from 'vue-router'
import ManutencaoService from 'src/services/manutencao.service'

const $q = useQuasar()
const router = useRouter()

// Estado
const loading = ref(false)
const filter = ref('')
const manutencoes = ref([])

// Paginação
const pagination = ref({
  sortBy: 'data_manutencao',
  descending: true,
  page: 1,
  rowsPerPage: 10,
  rowsNumber: 0,
})

// Colunas da tabela
const columns = [
  {
    name: 'veiculo_placa',
    label: 'Veículo',
    field: (row) => row.veiculo?.placa || 'N/A',
    align: 'left',
    sortable: true,
  },
  {
    name: 'veiculo_modelo',
    label: 'Modelo',
    field: (row) => row.veiculo?.modelo || 'N/A',
    align: 'left',
    sortable: true,
  },
  {
    name: 'tipo',
    label: 'Tipo',
    field: 'tipo',
    align: 'left',
    sortable: true,
    format: (val) => {
      const tipos = {
        preventiva: 'Preventiva',
        corretiva: 'Corretiva',
        preditiva: 'Preditiva',
        corretiva_emergencial: 'Emergencial',
      }
      return tipos[val] || val
    },
  },
  {
    name: 'data_manutencao',
    label: 'Data',
    field: 'data_manutencao',
    align: 'center',
    sortable: true,
    format: formatarData,
  },
  {
    name: 'valor',
    label: 'Valor',
    field: 'valor',
    align: 'right',
    format: formatarMoeda,
  },
  {
    name: 'actions',
    label: 'Ações',
    align: 'center',
    field: 'actions',
  },
]

// Funções auxiliares
function formatarMoeda(valor) {
  if (!valor) return 'R$ 0,00'
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
  }).format(valor)
}

function formatarData(data) {
  if (!data) return ''
  return new Date(data).toLocaleDateString('pt-BR')
}

// Métodos
async function carregarManutencoes() {
  try {
    loading.value = true

    const params = {
      page: pagination.value.page,
      per_page: pagination.value.rowsPerPage,
      sort_by: pagination.value.sortBy,
      sort_order: pagination.value.descending ? 'desc' : 'asc',
      search: filter.value,
      with: 'veiculo', // Garante que os dados do veículo serão incluídos
    }

    const response = await ManutencaoService.listar(params)

    // Garante que temos um array de manutenções
    manutencoes.value = Array.isArray(response) ? response : []

    // Atualiza a paginação
    if (response.meta) {
      pagination.value.rowsNumber = response.meta.total || 0
      pagination.value.page = response.meta.current_page || 1
      pagination.value.rowsPerPage = parseInt(response.meta.per_page || 10)
    }
  } catch (error) {
    console.error('Erro ao carregar manutenções:', error)
    $q.notify({
      type: 'negative',
      message: 'Erro ao carregar manutenções',
      position: 'top',
      icon: 'report_problem',
    })
  } finally {
    loading.value = false
  }
}

function editarManutencao(manutencao) {
  router.push({ name: 'manutencao-editar', params: { id: manutencao.id } })
}

async function confirmarExclusao(manutencao) {
  try {
    const confirm = await $q.dialog({
      title: 'Confirmar Exclusão',
      message: `Tem certeza que deseja excluir a manutenção do veículo ${manutencao.veiculo?.placa}?`,
      cancel: true,
      persistent: true,
    })

    if (confirm) {
      loading.value = true
      await ManutencaoService.excluir(manutencao.id)

      $q.notify({
        type: 'positive',
        message: 'Manutenção excluída com sucesso!',
        position: 'top',
        icon: 'check',
      })

      await carregarManutencoes()
    }
  } catch (error) {
    $q.notify({
      type: 'negative',
      message: 'Erro ao excluir manutenção',
      position: 'top',
      icon: 'report_problem',
    })
    console.error('Erro ao excluir manutenção:', error)
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

  carregarManutencoes()
}

// Inicialização
onMounted(() => {
  carregarManutencoes()
})
</script>
