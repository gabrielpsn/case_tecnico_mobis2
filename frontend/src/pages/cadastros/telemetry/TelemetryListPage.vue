<!-- front_end/src/pages/telemetry/TelemetryListPage.vue -->
<template>
  <q-page padding>
    <q-card class="q-mb-md">
      <q-card-section>
        <div class="text-h6">Relatório de Telemetria</div>
      </q-card-section>

      <q-card-section>
        <q-table
          v-model:pagination="pagination"
          :rows="vehicles"
          :columns="columns"
          row-key="id"
          :loading="loading"
          :filter="filter"
        >
          <template v-slot:top-right>
            <q-input borderless dense v-model="filter" placeholder="Buscar veículo">
              <template v-slot:append>
                <q-icon name="search" />
              </template>
            </q-input>
          </template>

          <template v-slot:body-cell-actions="props">
            <q-td :props="props" class="q-gutter-x-sm">
              <q-btn
                icon="visibility"
                color="primary"
                dense
                flat
                @click="viewTelemetry(props.row)"
                title="Visualizar telemetria"
              />
            </q-td>
          </template>
        </q-table>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { api } from 'src/boot/axios'
import { useRouter } from 'vue-router'

export default {
  name: 'TelemetryListPage',

  setup() {
    const $q = useQuasar()
    const router = useRouter()
    const vehicles = ref([])
    const loading = ref(false)
    const filter = ref('')

    const columns = [
      { name: 'placa', label: 'Placa', field: 'placa', align: 'left', sortable: true },
      { name: 'modelo', label: 'Modelo', field: 'modelo', align: 'left', sortable: true },
      { name: 'marca', label: 'Marca', field: 'marca', align: 'left', sortable: true },
      { name: 'status', label: 'Status', field: 'status', align: 'center', sortable: true },
      {
        name: 'localizacao',
        label: 'Localização',
        field: 'localizacao',
        align: 'left',
        sortable: true,
        format: (val) => {
          return val.endereco
        },
      },
      {
        name: 'ultima_atualizacao',
        label: 'Última Atualização',
        field: 'ultima_atualizacao',
        align: 'center',
        sortable: true,
      },
      { name: 'actions', label: 'Ações', align: 'center' },
    ]

    const pagination = {
      sortBy: 'placa',
      descending: false,
      page: 1,
      rowsPerPage: 10,
    }

    const fetchVehicles = async () => {
      try {
        loading.value = true
        // Busca a telemetria dos veículos
        const { data } = await api.get('telemetria/veiculos/telemetry')

        console.log('Dados de telemetria recebidos:', data.data)

        vehicles.value = data.data
      } catch (error) {
        console.error('Erro ao buscar veículos:', error)
        $q.notify({
          type: 'negative',
          message: 'Erro ao carregar dados de telemetria',
        })
      } finally {
        loading.value = false
      }
    }

    const viewTelemetry = (vehicle) => {
      router.push({
        name: 'telemetria-veiculo',
        params: { id: vehicle.id },
        query: {
          data_inicio: new Date(Date.now() - 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
          data_fim: new Date().toISOString().split('T')[0],
          tab: 'detalhes',
        },
      })
    }

    onMounted(() => {
      fetchVehicles()
    })

    return {
      vehicles,
      loading,
      filter,
      columns,
      pagination,
      viewTelemetry,
    }
  },
}
</script>
