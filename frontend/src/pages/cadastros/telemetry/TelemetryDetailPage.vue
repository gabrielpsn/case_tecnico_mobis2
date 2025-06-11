<template>
  <q-page padding>
    <q-card>
      <q-card-section>
        <div class="row items-center q-mb-md">
          <q-btn flat round icon="arrow_back" class="q-mr-sm" @click="router.go(-1)" />
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

        <!-- Abas de Navegação -->
        <q-tabs
          v-model="activeTab"
          dense
          class="text-grey-8"
          active-color="primary"
          indicator-color="primary"
          align="justify"
          narrow-indicator
        >
          <q-tab name="detalhes" icon="info" label="Detalhes" />
          <q-tab name="mapa" icon="map" label="Mapa" />
          <q-tab name="historico" icon="history" label="Histórico" />
        </q-tabs>

        <q-separator />

        <!-- Conteúdo das Abas -->
        <q-tab-panels v-model="activeTab" animated class="bg-transparent">
          <!-- Aba Detalhes -->
          <q-tab-panel name="detalhes" class="q-pa-sm">
            <div v-if="loading" class="text-center q-pa-md">
              <q-spinner color="primary" size="3em" />
              <div class="q-mt-sm">Carregando dados do veículo...</div>
            </div>
            <div v-else>
              <div class="row q-col-gutter-md">
                <!-- Informações do Veículo -->
                <div class="col-12 col-md-6">
                  <q-card class="q-mb-md">
                    <q-card-section class="bg-primary text-white">
                      <div class="text-h6">Informações do Veículo</div>
                    </q-card-section>
                    <q-separator />
                    <q-card-section>
                      <div class="q-pa-sm">
                        <div class="row q-py-xs" v-for="(value, key) in vehicleDetails" :key="key">
                          <div class="col-6 text-grey-8">{{ value.label }}:</div>
                          <div class="col-6 text-weight-medium">{{ value.value || 'N/A' }}</div>
                        </div>
                      </div>
                    </q-card-section>
                  </q-card>
                </div>

                <!-- Status Atual -->
                <div class="col-12 col-md-6">
                  <q-card class="q-mb-md">
                    <q-card-section class="bg-primary text-white">
                      <div class="text-h6">Status Atual</div>
                    </q-card-section>
                    <q-separator />
                    <q-card-section>
                      <div class="q-pa-sm">
                        <div class="row q-py-xs">
                          <div class="col-6 text-grey-8">Status:</div>
                          <div class="col-6">
                            <q-badge :color="telemetryStatus.statusColor">
                              {{ telemetryStatus.status }}
                            </q-badge>
                          </div>
                        </div>
                        <div class="row q-py-xs">
                          <div class="col-6 text-grey-8">Nível de Combustível:</div>
                          <div class="col-6">
                            <template v-if="telemetryStatus.nivelCombustivel !== undefined">
                              <q-linear-progress
                                :value="telemetryStatus.nivelCombustivel / 100"
                                :color="getFuelColor(telemetryStatus.nivelCombustivel)"
                                class="q-mt-sm"
                                style="height: 10px"
                              />
                              <div class="text-right">{{ telemetryStatus.nivelCombustivel }}%</div>
                            </template>
                            <span v-else>N/A</span>
                          </div>
                        </div>
                        <div class="row q-py-xs">
                          <div class="col-6 text-grey-8">Nível da Bateria:</div>
                          <div class="col-6">
                            <template v-if="telemetryStatus.nivelBateria !== undefined">
                              <q-linear-progress
                                :value="telemetryStatus.nivelBateria / 100"
                                :color="getBatteryColor(telemetryStatus.nivelBateria)"
                                class="q-mt-sm"
                                style="height: 10px"
                              />
                              <div class="text-right">{{ telemetryStatus.nivelBateria }}%</div>
                            </template>
                            <span v-else>N/A</span>
                          </div>
                        </div>
                        <div class="row q-py-xs">
                          <div class="col-6 text-grey-8">Quilometragem:</div>
                          <div class="col-6">{{ telemetryStatus.quilometragem || 'N/A' }}</div>
                        </div>
                        <div class="row q-py-xs">
                          <div class="col-6 text-grey-8">Mensagem:</div>
                          <div class="col-6">
                            {{ telemetryStatus.mensagem || 'Nenhuma mensagem' }}
                          </div>
                        </div>
                        <div class="row q-py-xs">
                          <div class="col-6 text-grey-8">Última Atualização:</div>
                          <div class="col-6">
                            {{
                              telemetryStatus.ultimaAtualizacao
                                ? formatDate(telemetryStatus.ultimaAtualizacao)
                                : 'N/A'
                            }}
                          </div>
                        </div>
                      </div>
                    </q-card-section>
                  </q-card>
                </div>
              </div>
            </div>
          </q-tab-panel>

          <!-- Aba Mapa -->
          <q-tab-panel name="mapa" class="q-pa-none">
            <div v-if="loading" class="text-center q-pa-md">
              <q-spinner color="primary" size="3em" />
              <div class="q-mt-sm">Carregando mapa...</div>
            </div>
            <div v-else-if="!hasLocationData" class="text-center q-pa-md text-grey-7">
              <q-icon name="location_off" size="3em" color="grey-5" />
              <div class="q-mt-sm">Nenhuma localização disponível</div>
            </div>
            <div v-else class="map-container">
              <div id="map" ref="mapContainer" style="height: 100%; width: 100%"></div>
              <q-btn
                icon="open_in_new"
                round
                color="primary"
                size="sm"
                :href="openStreetMapUrl"
                target="_blank"
                class="absolute-top-right q-ma-sm"
                title="Abrir no OpenStreetMap"
              />
            </div>
          </q-tab-panel>

          <!-- Aba Histórico -->
          <q-tab-panel name="historico" class="q-pa-none">
            <div v-if="loading" class="text-center q-pa-md">
              <q-spinner color="primary" size="3em" />
              <div class="q-mt-sm">Carregando histórico...</div>
            </div>
            <div v-else>
              <q-tabs
                v-model="historyTab"
                dense
                class="text-grey-8"
                active-color="primary"
                indicator-color="primary"
                align="left"
                narrow-indicator
              >
                <q-tab name="localizacoes" label="Localizações" />
                <q-tab name="status" label="Status" />
              </q-tabs>

              <q-tab-panels v-model="historyTab" animated class="bg-transparent">
                <!-- Histórico de Localizações -->
                <q-tab-panel name="localizacoes" class="q-pa-none">
                  <q-table
                    :rows="formattedLocations"
                    :columns="locationColumns"
                    row-key="id"
                    :loading="loading"
                    v-model:pagination="pagination"
                    :rows-per-page-options="[10, 20, 50]"
                    class="q-mt-md"
                    flat
                    bordered
                  >
                    <template v-slot:body-cell-timestamp="props">
                      <q-td :props="props">
                        {{ formatDate(props.value) }}
                      </q-td>
                    </template>
                    <template v-slot:body-cell-localizacao="props">
                      <q-td :props="props">
                        <div v-if="props.value">
                          <div>Lat: {{ props.value.latitude?.toFixed(6) || 'N/A' }}</div>
                          <div>Lng: {{ props.value.longitude?.toFixed(6) || 'N/A' }}</div>
                          <div v-if="props.value.velocidade">
                            Velocidade: {{ props.value.velocidade }} km/h
                          </div>
                        </div>
                        <div v-else>N/A</div>
                      </q-td>
                    </template>
                  </q-table>
                </q-tab-panel>

                <!-- Histórico de Status -->
                <q-tab-panel name="status" class="q-pa-none">
                  <q-table
                    :rows="formattedStatus"
                    :columns="statusColumns"
                    row-key="id"
                    :loading="loading"
                    v-model:pagination="pagination"
                    :rows-per-page-options="[10, 20, 50]"
                    class="q-mt-md"
                    flat
                    bordered
                  >
                    <template v-slot:body-cell-timestamp="props">
                      <q-td :props="props">
                        {{ formatDate(props.value) }}
                      </q-td>
                    </template>
                    <template v-slot:body-cell-status="props">
                      <q-td :props="props">
                        <q-badge :color="getStatusColor(props.value)">
                          {{ props.value }}
                        </q-badge>
                      </q-td>
                    </template>
                    <template v-slot:body-cell-nivel_combustivel="props">
                      <q-td :props="props">
                        <div v-if="props.value !== null && props.value !== undefined">
                          <q-linear-progress
                            :value="props.value / 100"
                            :color="getFuelColor(props.value)"
                            style="height: 10px"
                            class="q-mt-sm"
                          />
                          <div class="text-right">{{ props.value }}%</div>
                        </div>
                        <div v-else>N/A</div>
                      </q-td>
                    </template>
                    <template v-slot:body-cell-nivel_bateria="props">
                      <q-td :props="props">
                        <div v-if="props.value !== null && props.value !== undefined">
                          <q-linear-progress
                            :value="props.value / 100"
                            :color="getBatteryColor(props.value)"
                            style="height: 10px"
                            class="q-mt-sm"
                          />
                          <div class="text-right">{{ props.value }}%</div>
                        </div>
                        <div v-else>N/A</div>
                      </q-td>
                    </template>
                  </q-table>
                </q-tab-panel>
              </q-tab-panels>
            </div>
          </q-tab-panel>
        </q-tab-panels>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script>
import { ref, computed, onMounted, onBeforeUnmount, nextTick, watch } from 'vue'
import 'leaflet/dist/leaflet.css'
import L from 'leaflet'
import { useQuasar, date } from 'quasar'
import { useRoute, useRouter } from 'vue-router'
import { api } from 'src/boot/axios'

// Corrigir ícones do Leaflet
const defaultIcon = L.icon({
  iconUrl: 'https://unpkg.com/leaflet@1.7.1/dist/images/marker-icon.png',
  iconRetinaUrl: 'https://unpkg.com/leaflet@1.7.1/dist/images/marker-icon-2x.png',
  shadowUrl: 'https://unpkg.com/leaflet@1.7.1/dist/images/marker-shadow.png',
  iconSize: [25, 41],
  iconAnchor: [12, 41],
  popupAnchor: [1, -34],
  shadowSize: [41, 41],
})
L.Marker.prototype.options.icon = defaultIcon

export default {
  name: 'TelemetryDetailPage',

  props: {
    id: {
      type: [String, Number],
      required: true,
    },
  },

  setup(props) {
    const $q = useQuasar()
    const route = useRoute()
    const router = useRouter()

    // Estado
    const vehicle = ref({})
    const telemetryData = ref({
      veiculo: {},
      ultima_localizacao: null,
      ultimo_status: null,
      historico_localizacoes: [],
      historico_status: [],
    })
    const loading = ref(false)
    const activeTab = ref('detalhes')
    const historyTab = ref('localizacoes')
    const pagination = ref({
      sortBy: 'timestamp',
      descending: true,
      page: 1,
      rowsPerPage: 10,
    })

    // Filtros
    const dateRange = ref({
      from: date.formatDate(Date.now() - 7 * 24 * 60 * 60 * 1000, 'YYYY-MM-DD'),
      to: date.formatDate(Date.now(), 'YYYY-MM-DD'),
    })

    // Colunas das tabelas
    const locationColumns = [
      { name: 'timestamp', label: 'Data/Hora', field: 'timestamp', align: 'left', sortable: true },
      { name: 'localizacao', label: 'Localização', field: 'localizacao', align: 'left' },
      {
        name: 'endereco',
        label: 'Endereço',
        field: (row) => row.localizacao?.endereco || 'N/A',
        align: 'left',
      },
    ]

    const statusColumns = [
      { name: 'timestamp', label: 'Data/Hora', field: 'timestamp', align: 'left', sortable: true },
      { name: 'status', label: 'Status', field: 'status', align: 'left' },
      { name: 'descricao', label: 'Descrição', field: 'descricao', align: 'left' },
      {
        name: 'nivel_combustivel',
        label: 'Combustível',
        field: 'nivel_combustivel',
        align: 'left',
      },
      { name: 'nivel_bateria', label: 'Bateria', field: 'nivel_bateria', align: 'left' },
      { name: 'mensagem', label: 'Mensagem', field: 'mensagem', align: 'left' },
      { name: 'quilometragem', label: 'Quilometragem', field: 'quilometragem', align: 'left' },
    ]

    // Computed
    const telemetryStatus = computed(() => {
      const ultimoStatus = telemetryData.value.ultimo_status?.status || {}
      const veiculo = telemetryData.value.veiculo || {}

      return {
        status: ultimoStatus.status || veiculo.status_atual || 'N/A',
        statusColor: getStatusColor(ultimoStatus.status || veiculo.status_atual),
        nivelCombustivel: ultimoStatus.nivel_combustivel,
        nivelBateria: ultimoStatus.nivel_bateria,
        quilometragem: ultimoStatus.quilometragem || veiculo.quilometragem,
        mensagem: ultimoStatus.mensagem,
        ultimaAtualizacao: telemetryData.value.ultimo_status?.timestamp,
      }
    })

    const vehicleDetails = computed(() => {
      if (!vehicle.value) return []

      return [
        { label: 'Placa', value: vehicle.value.placa },
        { label: 'Modelo', value: vehicle.value.modelo },
        { label: 'Marca', value: vehicle.value.marca },
        { label: 'Ano', value: vehicle.value.ano },
        { label: 'Cor', value: vehicle.value.cor },
        { label: 'Chassi', value: vehicle.value.chassi },
        {
          label: 'Quilometragem',
          value: telemetryStatus.value.quilometragem
            ? `${telemetryStatus.value.quilometragem.toLocaleString()} km`
            : 'N/A',
        },
        {
          label: 'Status',
          value: telemetryStatus.value.status,
          color: telemetryStatus.value.statusColor,
        },
        {
          label: 'Motorista',
          value: vehicle.value.motorista_atual
            ? `${vehicle.value.motorista_atual.nome} (${vehicle.value.motorista_atual.cpf})`
            : 'Nenhum motorista atribuído',
        },
        {
          label: 'Última Atualização',
          value: telemetryStatus.value.ultimaAtualizacao
            ? formatDate(telemetryStatus.value.ultimaAtualizacao)
            : 'N/A',
        },
      ]
    })

    const telemetryLocation = computed(() => {
      const localizacao = telemetryData.value.ultima_localizacao?.localizacao || {}
      const endereco =
        typeof localizacao.endereco === 'string'
          ? localizacao.endereco.split('\n')[0]
          : 'Endereço não disponível'

      return {
        latitude: localizacao.latitude,
        longitude: localizacao.longitude,
        velocidade: localizacao.velocidade,
        endereco: endereco,
        timestamp: telemetryData.value.ultima_localizacao?.timestamp,
      }
    })

    const formattedLocations = computed(() => {
      return (telemetryData.value.historico_localizacoes || []).map((loc) => ({
        ...loc,
        endereco:
          typeof loc.localizacao?.endereco === 'string'
            ? loc.localizacao.endereco.split('\n')[0]
            : 'Endereço não disponível',
      }))
    })

    const formattedStatus = computed(() => {
      return (telemetryData.value.historico_status || []).map((stat) => ({
        ...stat,
        status: stat.status?.status || stat.status,
        descricao: stat.status?.descricao || stat.descricao,
        nivel_bateria: stat.status?.nivel_bateria ?? stat.nivel_bateria,
        nivel_combustivel: stat.status?.nivel_combustivel ?? stat.nivel_combustivel,
        mensagem: stat.status?.mensagem || stat.mensagem || 'Sem mensagem',
        quilometragem: stat.status?.quilometragem ?? stat.quilometragem,
      }))
    })

    const hasLocationData = computed(
      () => !!telemetryLocation.value.latitude && !!telemetryLocation.value.longitude,
    )

    const openStreetMapUrl = computed(() => {
      if (!hasLocationData.value) return '#'
      const { latitude, longitude } = telemetryLocation.value
      return `https://www.openstreetmap.org/?mlat=${latitude}&mlon=${longitude}#map=15/${latitude}/${longitude}`
    })

    const map = ref(null)
    const mapInitialized = ref(false)
    const mapContainer = ref(null)

    const initMap = () => {
      if (!hasLocationData.value || mapInitialized.value || !mapContainer.value) return

      nextTick(() => {
        try {
          const { latitude, longitude } = telemetryLocation.value

          // Inicializa o mapa
          map.value = L.map('map', {
            zoomControl: false,
          }).setView([latitude, longitude], 15)

          // Adiciona o tile layer do OpenStreetMap
          L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19,
          }).addTo(map.value)

          // Adiciona o marcador
          L.marker([latitude, longitude])
            .addTo(map.value)
            .bindPopup('Localização atual do veículo')
            .openPopup()

          // Adiciona controle de zoom
          L.control
            .zoom({
              position: 'topright',
            })
            .addTo(map.value)

          mapInitialized.value = true
        } catch (error) {
          console.error('Erro ao inicializar o mapa:', error)
        }
      })
    }

    const updateMap = () => {
      if (!map.value || !hasLocationData.value) return

      try {
        const { latitude, longitude } = telemetryLocation.value
        map.value.setView([latitude, longitude], 15)

        // Limpa marcadores existentes
        map.value.eachLayer((layer) => {
          if (layer instanceof L.Marker) {
            map.value.removeLayer(layer)
          }
        })

        // Adiciona novo marcador
        L.marker([latitude, longitude])
          .addTo(map.value)
          .bindPopup('Localização atual do veículo')
          .openPopup()
      } catch (error) {
        console.error('Erro ao atualizar o mapa:', error)
      }
    }

    // Observa mudanças na localização
    watch(
      telemetryLocation,
      () => {
        if (mapInitialized.value) {
          updateMap()
        } else if (hasLocationData.value) {
          initMap()
        }
      },
      { immediate: true },
    )

    // Inicializa o mapa quando o componente for montado
    onMounted(() => {
      if (hasLocationData.value) {
        initMap()
      }
    })

    // Limpa o mapa quando o componente for desmontado
    onBeforeUnmount(() => {
      if (map.value) {
        map.value.remove()
        map.value = null
        mapInitialized.value = false
      }
    })

    // Métodos
    const formatDate = (dateString) => {
      if (!dateString) return 'N/A'
      return date.formatDate(dateString, 'DD/MM/YYYY HH:mm:ss')
    }

    const getStatusColor = (status) => {
      if (!status) return 'grey'

      const statusLower = status.toString().toLowerCase()

      if (statusLower.includes('em_manutencao') || statusLower.includes('manutenção')) {
        return 'negative'
      } else if (statusLower.includes('disponivel') || statusLower.includes('disponível')) {
        return 'positive'
      } else if (statusLower.includes('em_uso') || statusLower.includes('em uso')) {
        return 'primary'
      } else if (statusLower.includes('desligado')) {
        return 'grey'
      } else if (statusLower.includes('manutencao') || statusLower.includes('manutenção')) {
        return 'negative'
      } else if (
        statusLower.includes('alerta') ||
        statusLower.includes('atenção') ||
        statusLower.includes('erro')
      ) {
        return 'negative'
      }
      return 'grey'
    }

    const getFuelColor = (level) => {
      if (level > 50) return 'positive'
      if (level > 20) return 'warning'
      return 'negative'
    }

    const getBatteryColor = (level) => {
      if (level > 70) return 'positive'
      if (level > 30) return 'warning'
      return 'negative'
    }

    // Buscar dados do veículo
    const fetchVehicleData = async () => {
      try {
        loading.value = true
        const response = await api.get(`/veiculos/${props.id}`)
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

    // Buscar dados de telemetria
    const fetchTelemetryData = async () => {
      try {
        loading.value = true
        const response = await api.get(`/telemetria/veiculos/${props.id}`, {
          params: {
            data_inicio: dateRange.value.from,
            data_fim: dateRange.value.to,
          },
        })

        if (response.data.success && response.data.data) {
          telemetryData.value = {
            ...telemetryData.value,
            ...response.data.data,
          }
        } else {
          throw new Error('Dados de telemetria não encontrados')
        }
      } catch (error) {
        console.error('Erro ao carregar dados de telemetria:', error)
        $q.notify({
          color: 'negative',
          message: error.response?.data?.message || 'Erro ao carregar dados de telemetria',
          icon: 'report_problem',
        })
      } finally {
        loading.value = false
      }
    }

    // Aplicar filtros
    const applyFilters = () => {
      fetchTelemetryData()
    }

    // Inicialização
    onMounted(async () => {
      await Promise.all([fetchVehicleData(), fetchTelemetryData()])
    })

    return {
      vehicle,
      telemetryData,
      telemetryStatus,
      telemetryLocation,
      formattedLocations,
      formattedStatus,
      loading,
      activeTab,
      historyTab,
      pagination,
      locationColumns,
      statusColumns,
      vehicleDetails,
      hasLocationData,
      openStreetMapUrl,
      mapContainer,
      initMap,
      updateMap,
      formatDate,
      getStatusColor,
      getFuelColor,
      getBatteryColor,
      applyFilters,
      fetchTelemetryData,
      router,
    }
  },
}
</script>

<style scoped>
.map-container {
  height: 70vh;
  width: 100%;
  border-radius: 4px;
  overflow: hidden;
  position: relative;
}

#map {
  height: 100%;
  width: 100%;
  min-height: 400px;
  background-color: #f5f5f5;
}

/* Estilo para o botão flutuante */
.absolute-top-right {
  position: absolute;
  top: 10px;
  right: 10px;
  z-index: 1000;
}

.q-tab-panel {
  padding: 16px 0;
}

.q-table__title {
  font-size: 1.1rem;
  font-weight: 500;
  color: #1976d2;
}

.q-badge {
  font-size: 0.75rem;
  padding: 4px 8px;
}
</style>
