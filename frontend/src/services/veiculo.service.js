import { api } from 'boot/axios'

const VeiculoService = {
  /**
   * Lista todos os veículos
   * @param {Object} params - Parâmetros de filtro e paginação
   * @returns {Promise<Array>} Lista de veículos
   */
  async listar(params = {}) {
    try {
      const response = await api.get('/veiculos', { params })
      return response.data
    } catch (error) {
      console.error('Erro ao listar veículos:', error)
      throw error
    }
  },

  /**
   * Busca um veículo pelo ID
   * @param {number|string} id - ID do veículo
   * @returns {Promise<Object>} Dados do veículo
   */
  async buscarPorId(id) {
    try {
      const response = await api.get(`/veiculos/${id}`)
      return response.data
    } catch (error) {
      console.error(`Erro ao buscar veículo com ID ${id}:`, error)
      throw error
    }
  },

  /**
   * Cria um novo veículo
   * @param {Object} veiculo - Dados do veículo a ser criado
   * @returns {Promise<Object>} Veículo criado
   */
  async criar(veiculo) {
    try {
      const response = await api.post('/veiculos', veiculo)
      return response.data
    } catch (error) {
      console.error('Erro ao criar veículo:', error)
      throw error
    }
  },

  /**
   * Atualiza um veículo existente
   * @param {number|string} id - ID do veículo a ser atualizado
   * @param {Object} veiculo - Dados atualizados do veículo
   * @returns {Promise<Object>} Veículo atualizado
   */
  async atualizar(id, veiculo) {
    try {
      const response = await api.put(`/veiculos/${id}`, veiculo)
      return response.data
    } catch (error) {
      console.error(`Erro ao atualizar veículo com ID ${id}:`, error)
      throw error
    }
  },

  /**
   * Remove um veículo
   * @param {number|string} id - ID do veículo a ser removido
   * @returns {Promise<void>}
   */
  async remover(id) {
    try {
      await api.delete(`/veiculos/${id}`)
    } catch (error) {
      console.error(`Erro ao remover veículo com ID ${id}:`, error)
      throw error
    }
  },

  /**
   * Lista veículos disponíveis para alocação
   * @returns {Promise<Array>} Lista de veículos disponíveis
   */
  async listarDisponiveis() {
    try {
      const response = await api.get('/veiculos/disponiveis')
      return response.data
    } catch (error) {
      console.error('Erro ao listar veículos disponíveis:', error)
      throw error
    }
  },

  /**
   * Atualiza o status de um veículo
   * @param {number|string} id - ID do veículo
   * @param {string} status - Novo status
   * @returns {Promise<Object>} Veículo atualizado
   */
  async atualizarStatus(id, status) {
    try {
      const response = await api.patch(`/veiculos/${id}/status`, { status })
      return response.data
    } catch (error) {
      console.error(`Erro ao atualizar status do veículo ${id}:`, error)
      throw error
    }
  },
}

export default VeiculoService
