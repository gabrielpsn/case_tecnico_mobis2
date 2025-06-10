<template>
  <q-page padding>
    <div class="row items-center q-mb-md">
      <div class="text-h5">Lista de Veículos</div>
      <q-space />
      <q-btn color="primary" icon="add" label="Novo Veículo" :to="{ name: 'veiculos-novo' }" />
    </div>

    <q-card class="q-mb-md">
      <q-card-section>
        <div class="row q-col-gutter-md">
          <div class="col-12 col-md-6">
            <q-input
              v-model="filter"
              outlined
              dense
              label="Buscar"
              placeholder="Placa, modelo, marca..."
              clearable
              @keyup.enter="onFilter"
              @clear="onFilter"
            >
              <template v-slot:append>
                <q-icon name="search" />
              </template>
            </q-input>
          </div>
          <div class="col-12 col-md-4">
            <q-select
              v-model="statusFilter"
              outlined
              dense
              label="Status"
              :options="statusOptions"
              emit-value
              map-options
              clearable
              @update:model-value="onFilter"
            />
          </div>
          <div class="col-12 col-md-2 flex flex-center">
            <q-btn color="grey-7" icon="refresh" dense flat round @click="onResetFilter">
              <q-tooltip>Limpar filtros</q-tooltip>
            </q-btn>
          </div>
        </div>
      </q-card-section>
    </q-card>

    <q-table
      :rows="veiculos"
      :columns="columns"
      row-key="id"
      :loading="loading"
      :pagination.sync="pagination"
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
            @click="editVeiculo(props.row.id)"
          >
            <q-tooltip>Editar</q-tooltip>
          </q-btn>
          <q-btn
            flat
            round
            color="negative"
            icon="delete"
            size="sm"
            @click="deleteVeiculo(props.row.id)"
          >
            <q-tooltip>Excluir</q-tooltip>
          </q-btn>
        </q-td>
      </template>

      <template v-slot:body-cell-status="props">
        <q-td :props="props">
          <q-badge
            :color="
              {
                disponivel: 'positive',
                em_manutencao: 'warning',
                em_uso: 'info',
                desativado: 'negative',
              }[props.value] || 'grey'
            "
            :label="
              {
                disponivel: 'Disponível',
                em_manutencao: 'Em Manutenção',
                em_uso: 'Em Uso',
                desativado: 'Desativado',
              }[props.value] || props.value
            "
          />
        </q-td>
      </template>

      <template v-slot:no-data>
        <div class="full-width row flex-center text-grey-8 q-gutter-sm">
          <span>Nenhum veículo encontrado</span>
          <q-icon size="2em" name="sentiment_dissatisfied" />
        </div>
      </template>
    </q-table>
  </q-page>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { api } from 'src/boot/axios'
import { useRouter } from 'vue-router'
import veiculoService from 'src/services/veiculo.service'

export default {
  name: 'VeiculoListPage',

  setup() {
    const $q = useQuasar()
    const router = useRouter()
    const veiculos = ref([])
    const loading = ref(false)
    const filter = ref('')
    const statusFilter = ref('')
    const statusOptions = [
      { label: 'Todos', value: '' },
      { label: 'Disponível', value: 'disponivel' },
      { label: 'Em Manutenção', value: 'em_manutencao' },
      { label: 'Em Uso', value: 'em_uso' },
      { label: 'Desativado', value: 'desativado' },
    ]

    const pagination = ref({
      sortBy: 'placa',
      descending: false,
      page: 1,
      rowsPerPage: 10,
      rowsNumber: 0,
    })

    const columns = [
      { name: 'id', label: 'ID', field: 'id', align: 'left', sortable: true },
      { name: 'placa', label: 'Placa', field: 'placa', align: 'left', sortable: true },
      { name: 'modelo', label: 'Modelo', field: 'modelo', align: 'left', sortable: true },
      { name: 'marca', label: 'Marca', field: 'marca', align: 'left', sortable: true },
      { name: 'ano', label: 'Ano', field: 'ano', align: 'center', sortable: true },
      {
        name: 'status',
        label: 'Status',
        field: 'status',
        align: 'center',
        sortable: true,
        format: (val) => {
          const statusMap = {
            disponivel: 'Disponível',
            em_manutencao: 'Em Manutenção',
            em_uso: 'Em Uso',
            desativado: 'Desativado',
          }
          return statusMap[val] || val
        },
      },
      {
        name: 'quilometragem',
        label: 'Quilometragem',
        field: 'quilometragem',
        align: 'right',
        format: (val) => (val ? `${parseFloat(val).toLocaleString('pt-BR')} km` : 'N/A'),
      },
      {
        name: 'motorista',
        label: 'Motorista',
        field: (row) => row.motorista?.nome || 'Sem motorista',
        align: 'left',
      },
      { name: 'actions', label: 'Ações', align: 'center' },
    ]

    const fetchVeiculos = async (props) => {
      loading.value = true

      const { page, rowsPerPage, sortBy, descending } = props?.pagination || pagination.value

      // Atualiza a paginação local
      pagination.value.page = page
      pagination.value.rowsPerPage = rowsPerPage
      pagination.value.sortBy = sortBy
      pagination.value.descending = descending

      const params = {
        page,
        per_page: rowsPerPage,
        sort_by: sortBy,
        sort_order: descending ? 'desc' : 'asc',
        search: filter.value || undefined,
        status: statusFilter.value || undefined,
      }

      veiculos.value = await veiculoService.listar(params)
      loading.value = false
    }

    const onRequest = (props) => {
      fetchVeiculos(props)
    }

    const onFilter = () => {
      // Reseta para a primeira página ao aplicar filtros
      pagination.value.page = 1
      fetchVeiculos()
    }

    const onResetFilter = () => {
      filter.value = ''
      statusFilter.value = ''
      onFilter()
    }

    const editVeiculo = (id) => {
      router.push(`/frota/veiculos/editar/${id}`)
    }

    const deleteVeiculo = (id) => {
      $q.dialog({
        title: 'Confirmar Exclusão',
        message: 'Tem certeza que deseja excluir este veículo?',
        cancel: true,
        persistent: true,
      }).onOk(() => {
        loading.value = true
        api
          .delete(`/veiculos/${id}`)
          .then(() => {
            $q.notify({
              color: 'positive',
              message: 'Veículo excluído com sucesso!',
              icon: 'check',
            })
            fetchVeiculos()
          })
          .catch((error) => {
            $q.notify({
              color: 'negative',
              message: 'Erro ao excluir veículo',
              icon: 'report_problem',
            })
            console.error('Erro:', error)
          })
          .finally(() => {
            loading.value = false
          })
      })
    }

    // Carrega os dados iniciais
    onMounted(() => {
      fetchVeiculos()
    })

    return {
      veiculos,
      loading,
      filter,
      statusFilter,
      statusOptions,
      pagination,
      columns,
      onRequest,
      onFilter,
      onResetFilter,
      editVeiculo,
      deleteVeiculo,
    }
  },
}
</script>
