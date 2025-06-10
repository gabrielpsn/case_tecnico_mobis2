<template>
  <q-page padding>
    <div class="q-pa-md">
      <div class="row q-mb-md justify-between items-center">
        <div class="text-h5">Gerenciamento de Usuários</div>
        <q-btn color="primary" icon="add" label="Novo Usuário" @click="openUserForm()" />
      </div>

      <!-- Filtros -->
      <q-card class="q-mb-md">
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-4">
              <q-input
                v-model="filters.search"
                label="Pesquisar"
                outlined
                dense
                clearable
                @update:model-value="fetchUsers"
              >
                <template v-slot:prepend>
                  <q-icon name="search" />
                </template>
              </q-input>
            </div>
            <div class="col-12 col-md-3">
              <q-select
                v-model="filters.status"
                :options="statusOptions"
                label="Status"
                outlined
                dense
                clearable
                emit-value
                map-options
                @update:model-value="fetchUsers"
              />
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- Tabela de usuários -->
      <q-card>
        <q-table
          :rows="users"
          :columns="columns"
          :loading="loading"
          :pagination.sync="pagination"
          @request="onRequest"
          row-key="id"
          flat
          bordered
        >
          <template v-slot:body-cell-avatar="props">
            <q-td :props="props">
              <q-avatar size="40px" class="q-mr-sm">
                <img :src="props.row.avatar || 'https://cdn.quasar.dev/img/avatar.png'" />
              </q-avatar>
            </q-td>
          </template>

          <template v-slot:body-cell-status="props">
            <q-td :props="props">
              <q-badge
                :color="props.row.status === 'active' ? 'positive' : 'grey'"
                text-color="white"
              >
                {{ props.row.status === 'active' ? 'Ativo' : 'Inativo' }}
              </q-badge>
            </q-td>
          </template>

          <template v-slot:body-cell-actions="props">
            <q-td :props="props">
              <q-btn
                flat
                round
                color="primary"
                icon="edit"
                @click="openUserForm(props.row)"
                size="sm"
              >
                <q-tooltip>Editar</q-tooltip>
              </q-btn>
              <q-btn
                flat
                round
                :color="props.row.status === 'active' ? 'negative' : 'positive'"
                :icon="props.row.status === 'active' ? 'block' : 'check_circle'"
                @click="toggleStatus(props.row)"
                size="sm"
              >
                <q-tooltip>{{ props.row.status === 'active' ? 'Desativar' : 'Ativar' }}</q-tooltip>
              </q-btn>
            </q-td>
          </template>
        </q-table>
      </q-card>
    </div>

    <!-- Diálogo de formulário de usuário -->
    <q-dialog v-model="showUserForm" persistent>
      <q-card style="min-width: 400px">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6">{{ form.id ? 'Editar' : 'Novo' }} Usuário</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section>
          <q-form @submit="saveUser" class="q-gutter-md">
            <div class="row q-col-gutter-md">
              <div class="col-12">
                <q-input
                  v-model="form.name"
                  label="Nome completo"
                  outlined
                  dense
                  :rules="[(val) => !!val || 'Campo obrigatório']"
                />
              </div>
              <div class="col-12 col-md-6">
                <q-input
                  v-model="form.email"
                  label="E-mail"
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
                  v-model="form.phone"
                  label="Telefone"
                  outlined
                  dense
                  mask="(##) #####-####"
                  unmasked-value
                />
              </div>
              <div class="col-12 col-md-6">
                <q-select
                  v-model="form.role"
                  :options="roleOptions"
                  label="Perfil"
                  outlined
                  dense
                  emit-value
                  map-options
                  :rules="[(val) => !!val || 'Selecione um perfil']"
                />
              </div>
              <div class="col-12 col-md-6">
                <q-select
                  v-model="form.status"
                  :options="statusOptions"
                  label="Status"
                  outlined
                  dense
                  emit-value
                  map-options
                />
              </div>
              <div class="col-12" v-if="!form.id">
                <q-input
                  v-model="form.password"
                  label="Senha"
                  :type="showPassword ? 'text' : 'password'"
                  outlined
                  dense
                  :rules="[(val) => !form.id || val.length >= 6 || 'Mínimo de 6 caracteres']"
                >
                  <template v-slot:append>
                    <q-icon
                      :name="showPassword ? 'visibility_off' : 'visibility'"
                      class="cursor-pointer"
                      @click="showPassword = !showPassword"
                    />
                  </template>
                </q-input>
              </div>
            </div>

            <div class="row justify-end q-mt-md">
              <q-btn flat label="Cancelar" color="grey-7" v-close-popup class="q-mr-sm" />
              <q-btn
                type="submit"
                :label="form.id ? 'Atualizar' : 'Salvar'"
                color="primary"
                :loading="saving"
              />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { api } from 'src/boot/axios'

const $q = useQuasar()

// Dados da tabela
const users = ref([])
const loading = ref(false)
const pagination = ref({
  sortBy: 'name',
  descending: false,
  page: 1,
  rowsPerPage: 10,
  rowsNumber: 0,
})

// Filtros
const filters = ref({
  search: '',
  status: 'active',
})

const statusOptions = [
  { label: 'Ativo', value: 'active' },
  { label: 'Inativo', value: 'inactive' },
]

const roleOptions = [
  { label: 'Administrador', value: 'admin' },
  { label: 'Motorista', value: 'driver' },
  { label: 'Operador', value: 'operator' },
]

// Colunas da tabela
const columns = [
  {
    name: 'avatar',
    label: '',
    field: 'avatar',
    align: 'left',
    sortable: false,
  },
  {
    name: 'name',
    label: 'Nome',
    field: 'name',
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
    name: 'role',
    label: 'Perfil',
    field: 'role',
    align: 'left',
    sortable: true,
    format: (val) => {
      const role = roleOptions.find((r) => r.value === val)
      return role ? role.label : val
    },
  },
  {
    name: 'status',
    label: 'Status',
    field: 'status',
    align: 'center',
    sortable: true,
  },
  {
    name: 'actions',
    label: 'Ações',
    field: 'actions',
    align: 'right',
    sortable: false,
  },
]

// Formulário
const showUserForm = ref(false)
const showPassword = ref(false)
const saving = ref(false)
const form = ref({
  id: null,
  name: '',
  email: '',
  phone: '',
  role: '',
  status: 'active',
  password: '',
})

// Carrega os usuários
async function fetchUsers() {
  try {
    loading.value = true
    const { page, rowsPerPage, sortBy, descending } = pagination.value

    const params = {
      page,
      per_page: rowsPerPage,
      sort_by: sortBy,
      order: descending ? 'desc' : 'asc',
      ...filters.value,
    }

    const response = await api.get('/users', { params })
    users.value = response.data.data
    pagination.value.rowsNumber = response.data.total
  } catch (error) {
    console.error('Erro ao carregar usuários:', error)
    $q.notify({
      type: 'negative',
      message: 'Erro ao carregar usuários',
      position: 'top',
    })
  } finally {
    loading.value = false
  }
}

// Manipula a paginação/ordenação da tabela
function onRequest(props) {
  pagination.value = props.pagination
  fetchUsers()
}

// Abre o formulário para edição
function openUserForm(user = null) {
  if (user) {
    form.value = { ...user, password: '' }
  } else {
    form.value = {
      id: null,
      name: '',
      email: '',
      phone: '',
      role: '',
      status: 'active',
      password: '',
    }
  }
  showUserForm.value = true
}

// Salva o usuário
async function saveUser() {
  try {
    saving.value = true

    if (form.value.id) {
      await api.put(`/users/${form.value.id}`, form.value)
    } else {
      await api.post('/users', form.value)
    }

    $q.notify({
      type: 'positive',
      message: `Usuário ${form.value.id ? 'atualizado' : 'criado'} com sucesso!`,
      position: 'top',
    })

    showUserForm.value = false
    fetchUsers()
  } catch (error) {
    console.error('Erro ao salvar usuário:', error)

    let message = 'Erro ao salvar usuário'
    if (error.response?.data?.message) {
      message = error.response.data.message
    } else if (error.response?.data?.errors) {
      // Pega a primeira mensagem de erro da validação
      const firstError = Object.values(error.response.data.errors)[0][0]
      message = firstError
    }

    $q.notify({
      type: 'negative',
      message,
      position: 'top',
    })
  } finally {
    saving.value = false
  }
}

// Altera o status do usuário
async function toggleStatus(user) {
  try {
    $q.dialog({
      title: 'Confirmar',
      message: `Tem certeza que deseja ${user.status === 'active' ? 'desativar' : 'ativar'} o usuário ${user.name}?`,
      cancel: true,
      persistent: true,
    }).onOk(async () => {
      const newStatus = user.status === 'active' ? 'inactive' : 'active'
      await api.put(`/users/${user.id}/status`, { status: newStatus })

      $q.notify({
        type: 'positive',
        message: `Usuário ${newStatus === 'active' ? 'ativado' : 'desativado'} com sucesso!`,
        position: 'top',
      })

      fetchUsers()
    })
  } catch (error) {
    console.error('Erro ao alterar status do usuário:', error)
    $q.notify({
      type: 'negative',
      message: 'Erro ao alterar status do usuário',
      position: 'top',
    })
  }
}

// Inicialização
onMounted(() => {
  fetchUsers()
})
</script>

<style scoped>
/* Estilos específicos da página */
.q-table__title {
  font-size: 1.25rem;
  font-weight: 500;
}

/* Ajustes para dispositivos móveis */
@media (max-width: 600px) {
  .q-table__top {
    flex-direction: column;
    align-items: flex-start;
  }

  .q-table__control {
    width: 100%;
    margin-top: 8px;
  }
}
</style>
