<!-- front_end/src/pages/telemetry/TelemetryDetailPage.vue -->
<template>
  <q-page padding>
    <q-card>
      <q-card-section>
        <div class="row items-center q-mb-md">
          <q-btn flat round icon="arrow_back" class="q-mr-sm" @click="$router.go(-1)" />
          <div class="text-h6">Telemetria - {{ vehicle.placa || 'Carregando...' }}</div>
          <q-space />
          <q-btn
            color="primary"
            icon="refresh"
            label="Atualizar"
            @click="fetchTelemetryData"
            :loading="loading"
          />
        </div>

        <!-- Filtros -->
        <q-card class="q-mb-md">
          <q-card-section>
            <div class="text-subtitle1 q-mb-md">Filtrar Dados</div>
            <div class="row q-col-gutter-md">
              <div class="col-12 col-sm-5 col-md-3">
                <q-input
                  v-model="dateRange.from"
                  label="Data inicial"
                  type="date"
                  outlined
                  dense
                  stack-label
                />
              </div>
              <div class="col-12 col-sm-5 col-md-3">
                <q-input
                  v-model="dateRange.to"
                  label="Data final"
                  type="date"
                  outlined
                  dense
                  stack-label
                />
              </div>
              <div class="col-12 col-sm-2 col-md-2 flex items-end">
                <q-btn
                  color="primary"
                  label="Aplicar"
                  @click="applyFilters"
                  :loading="loading"
                  class="full-width"
                />
              </div>
            </div>
          </q-card-section>
        </q-card>

        <!-- Abas de Navegação -->
        <q-tabs
          v-model="activeTab"
          dense
          class="text-grey"
          active-color="primary"
          indicator-color="primary"
          align="justify"
          narrow-indicator
        >
          <q-tab name="map" icon="map" label="Mapa" />
          <q-tab name="status" icon="speed" label="Status" />
          <q-tab name="logs" icon="list_alt" label="Registros" />
        </q-tabs>

        <q-separator />

        <!-- Conteúdo das Abas -->
        <q-tab-panels v-model="activeTab" animated class="bg-transparent">
          <!-- Aba Mapa -->
          <q-tab-panel name="map" class="q-pa-none">
            <telemetry-map
              :telemetry-data="filteredTelemetryData"
              :center="mapCenter"
              :zoom="mapZoom"
              class="q-mt-md"
            />
          </q-tab-panel>

          <!-- Aba Status -->
          <q-tab-panel name="status" class="q-pa-none">
            <telemetry-status :telemetry-data="filteredTelemetryData" class="q-mt-md" />
          </q-tab-panel>

          <!-- Aba Logs -->
          <q-tab-panel name="logs" class="q-pa-none">
            <telemetry-logs
              :telemetry-data="filteredTelemetryData"
              :loading="loading"
              @show-on-map="handleShowOnMap"
              @view-details="showTelemetryDetails"
              @download-data="downloadTelemetryData"
              class="q-mt-md"
            />
          </q-tab-panel>
        </q-tab-panels>
      </q-card-section>
    </q-card>

    <!-- Diálogo de Detalhes -->
    <q-dialog v-model="showDetailsDialog">
      <q-card style="width: 500px; max-width: 90vw">
        <q-card-section>
          <div class="text-h6">Detalhes do Registro</div>
        </q-card-section>

        <q-card-section class="q-pt-none">
          <q-list dense>
            <q-item v-for="(value, key) in selectedTelemetry" :key="key">
              <q-item-section>
                <q-item-label class="text-weight-medium">{{ formatKey(key) }}</q-item-label>
                <q-item-label caption>{{ formatValue(key, value) }}</q-item-label>
              </q-item-section>
            </q-item>
          </q-list>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Fechar" color="primary" v-close-popup />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useQuasar, date } from 'quasar'
import { api } from 'src/boot/axios'

// Componentes
import TelemetryMap from 'components/telemetry/TelemetryMap.vue'
import TelemetryStatus from 'components/telemetry/TelemetryStatus.vue'
import TelemetryLogs from 'components/telemetry/TelemetryLogs.vue'

export default {
  name: 'TelemetryDetailPage',

  components: {
    TelemetryMap,
    TelemetryStatus,
    TelemetryLogs,
  },

  setup() {
    const $q = useQuasar()
    const route = useRoute()
    const router = useRouter()

    // Estado
    const vehicle = ref({})
    const telemetryData = ref([])
    const loading = ref(false)
    const activeTab = ref('map')
    const showDetailsDialog = ref(false)
    const selectedTelemetry = ref(null)

    // Filtros
    const dateRange = ref({
      from: date.formatDate(Date.now() - 7 * 24 * 60 * 60 * 1000, 'YYYY-MM-DD'),
      to: date.formatDate(Date.now(), 'YYYY-MM-DD'),
    })

    // Configuração do Mapa
    const mapCenter = ref({ lat: -15.7801, lng: -47.9292 }) // Centro do Brasil
    const mapZoom = ref(4)

    // Dados filtrados
    const filteredTelemetryData = computed(() => {
      if (!telemetryData.value || !telemetryData.value.lo) return []
      return telemetryData.value.lo.filter((item) => {
        const itemDate = new Date(item.timestamp).toISOString().split('T')[0]
        return itemDate >= dateRange.value.from && itemDate <= dateRange.value.to
      })
    })

    // Métodos
    const fetchVehicleData = async () => {
      try {
        loading.value = true
        const response = await api.get(`/veiculos/${route.params.id}`)
        vehicle.value = response.data
      } catch (error) {
        console.error('Erro ao carregar dados do veículo:', error)
        $q.notify({
          color: 'negative',
          message: 'Erro ao carregar dados do veículo',
          icon: 'report_problem',
        })
      } finally {
        loading.value = false
      }
    }

    const fetchTelemetryData = async () => {
      try {
        loading.value = true
        const params = {
          data_inicio: dateRange.value.from,
          data_fim: dateRange.value.to,
        }

        const response = await api.get(`/veiculos/${route.params.id}/telemetria`, { params })
        telemetryData.value = response.data

        // Atualiza o centro do mapa se houver dados de localização
        updateMapCenter()
      } catch (error) {
        console.error('Erro ao carregar dados de telemetria:', error)
        $q.notify({
          color: 'negative',
          message: 'Erro ao carregar dados de telemetria',
          icon: 'report_problem',
        })
      } finally {
        loading.value = false
      }
    }

    const updateMapCenter = () => {
      if (telemetryData.value.length > 0) {
        const lastPoint = telemetryData.value[0]
        if (lastPoint.localizacao?.latitude && lastPoint.localizacao?.longitude) {
          mapCenter.value = {
            lat: parseFloat(lastPoint.localizacao.latitude),
            lng: parseFloat(lastPoint.localizacao.longitude),
          }
          mapZoom.value = 12 // Zoom mais próximo para mostrar a localização atual
        }
      }
    }

    const applyFilters = () => {
      fetchTelemetryData()
    }

    const handleShowOnMap = (location) => {
      if (location?.latitude && location?.longitude) {
        mapCenter.value = {
          lat: parseFloat(location.latitude),
          lng: parseFloat(location.longitude),
        }
        mapZoom.value = 15
        activeTab.value = 'map'
      }
    }

    const showTelemetryDetails = (item) => {
      selectedTelemetry.value = flattenObject(item)
      showDetailsDialog.value = true
    }

    const downloadTelemetryData = async (item) => {
      try {
        let data, filename

        if (item) {
          // Baixar um único registro
          data = JSON.stringify(item, null, 2)
          filename = `telemetria-${item.id}-${new Date(item.timestamp).toISOString().split('T')[0]}.json`
        } else {
          // Baixar todos os dados filtrados
          data = JSON.stringify(filteredTelemetryData.value, null, 2)
          filename = `telemetria-${vehicle.value.placa}-${dateRange.value.from}_to_${dateRange.value.to}.json`
        }

        const blob = new Blob([data], { type: 'application/json' })
        const url = URL.createObjectURL(blob)
        const link = document.createElement('a')
        link.href = url
        link.download = filename
        document.body.appendChild(link)
        link.click()
        document.body.removeChild(link)

        $q.notify({
          color: 'positive',
          message: 'Dados exportados com sucesso',
          icon: 'cloud_download',
        })
      } catch (error) {
        console.error('Erro ao exportar dados:', error)
        $q.notify({
          color: 'negative',
          message: 'Erro ao exportar dados',
          icon: 'report_problem',
        })
      }
    }

    // Utilitários
    const flattenObject = (obj, prefix = '') => {
      return Object.keys(obj).reduce((acc, k) => {
        const pre = prefix.length ? `${prefix}.` : ''
        if (typeof obj[k] === 'object' && obj[k] !== null && !Array.isArray(obj[k])) {
          Object.assign(acc, flattenObject(obj[k], pre + k))
        } else {
          acc[pre + k] = obj[k]
        }
        return acc
      }, {})
    }

    const formatKey = (key) => {
      return key
        .replace(/([A-Z])/g, ' $1')
        .replace(/\./g, ' > ')
        .replace(/^./, (str) => str.toUpperCase())
        .trim()
    }

    const formatValue = (key, value) => {
      if (value === null || value === undefined) return 'N/A'

      // Formata datas
      if (
        key.toLowerCase().includes('timestamp') ||
        key.toLowerCase().includes('data') ||
        key.toLowerCase().includes('hora')
      ) {
        return date.formatDate(value, 'DD/MM/YYYY HH:mm:ss')
      }

      // Formata números decimais
      if (typeof value === 'number' && key.toLowerCase().includes('lat')) {
        return value.toFixed(6)
      }

      if (typeof value === 'number' && key.toLowerCase().includes('lng')) {
        return value.toFixed(6)
      }

      // Converte booleanos para texto
      if (typeof value === 'boolean') {
        return value ? 'Sim' : 'Não'
      }

      return value
    }

    // Ciclo de vida
    onMounted(async () => {
      await Promise.all([fetchVehicleData(), fetchTelemetryData()])
    })

    return {
      // Estado
      vehicle,
      loading,
      activeTab,
      dateRange,
      mapCenter,
      mapZoom,
      showDetailsDialog,
      selectedTelemetry,
      filteredTelemetryData,

      // Métodos
      applyFilters,
      fetchTelemetryData,
      handleShowOnMap,
      showTelemetryDetails,
      downloadTelemetryData,
      formatKey,
      formatValue,
    }
  },
}
</script>

<style scoped>
/* Estilos personalizados podem ser adicionados aqui */
</style>
