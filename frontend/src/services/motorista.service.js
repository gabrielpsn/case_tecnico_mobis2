import { api } from 'src/boot/axios'
import { useQuasar } from 'quasar'

const $q = useQuasar()

const motoristaService = {
  /**
   * Busca motoristas com paginação e filtros
   * @param {Object} params - Parâmetros da requisição
   * @param {number} params.page - Página atual
   * @param {number} params.per_page - Itens por página
   * @param {string} params.search - Termo de busca
   * @param {string} params.sort_by - Campo para ordenação
   * @param {string} params.sort_order - Ordem (asc/desc)
   * @returns {Promise<Object>} Dados paginados dos motoristas
   */
  async getMotoristas(params = {}) {
    try {
      const response = await api.get('/motoristas', { params })
      return {
        data: response.data.data,
        meta: {
          currentPage: response.data.meta.current_page,
          lastPage: response.data.meta.last_page,
          perPage: response.data.meta.per_page,
          total: response.data.meta.total,
        },
      }
    } catch (error) {
      console.error('Erro ao buscar motoristas:', error)
      $q.notify({
        type: 'negative',
        message: 'Erro ao carregar a lista de motoristas',
      })
      throw error
    }
  },

  /**
   * Busca um motorista pelo ID
   * @param {number} id - ID do motorista
   * @returns {Promise<Object>} Dados do motorista
   */
  async getMotorista(id) {
    try {
      const response = await api.get(`/motoristas/${id}`)
      return response
    } catch (error) {
      console.error('Erro ao buscar motorista:', error)
      throw error
    }
  },

  /**
   * Cria um novo motorista
   * @param {Object} data - Dados do motorista
   * @returns {Promise<Object>} Motorista criado
   */
  async createMotorista(data) {
    try {
      const response = await api.post('/motoristas', data)
      $q.notify({
        type: 'positive',
        message: 'Motorista cadastrado com sucesso!',
      })
      return response.data
    } catch (error) {
      console.error('Erro ao criar motorista:', error)
      const message = error.response?.data?.message || 'Erro ao cadastrar o motorista'
      $q.notify({
        type: 'negative',
        message,
      })
      throw error
    }
  },

  /**
   * Atualiza um motorista existente
   * @param {number} id - ID do motorista
   * @param {Object} data - Dados atualizados do motorista
   * @returns {Promise<Object>} Motorista atualizado
   */
  async updateMotorista(id, data) {
    try {
      const response = await api.put(`/motoristas/${id}`, data)
      $q.notify({
        type: 'positive',
        message: 'Motorista atualizado com sucesso!',
      })
      return response.data
    } catch (error) {
      console.error('Erro ao atualizar motorista:', error)
      const message = error.response?.data?.message || 'Erro ao atualizar o motorista'
      $q.notify({
        type: 'negative',
        message,
      })
      throw error
    }
  },

  /**
   * Remove um motorista
   * @param {number} id - ID do motorista
   * @returns {Promise<void>}
   */
  async deleteMotorista(id) {
    try {
      await api.delete(`/motoristas/${id}`)
    } catch (error) {
      console.error('Erro ao excluir motorista:', error)
      throw error
    }
  },
}

export default motoristaService
