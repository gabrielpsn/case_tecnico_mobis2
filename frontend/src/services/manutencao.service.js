import { api } from 'src/boot/axios'

const ManutencaoService = {
  async listar(params = {}) {
    try {
      // Garante que o parâmetro include está presente para trazer os dados do veículo
      const paramsWithInclude = {
        ...params,
        include: 'veiculo',
      }
      const response = await api.get('/manutencoes', { params: paramsWithInclude })
      return response.data
    } catch (error) {
      console.error('Erro ao listar manutenções:', error)
      throw error
    }
  },

  async buscarPorId(id) {
    try {
      const response = await api.get(`/manutencoes/${id}`)
      return response.data
    } catch (error) {
      console.error(`Erro ao buscar manutenção ${id}:`, error)
      throw error
    }
  },

  async criar(dados) {
    try {
      const response = await api.post('/manutencoes', dados)
      return response.data
    } catch (error) {
      console.error('Erro ao criar manutenção:', error)
      throw error
    }
  },

  async atualizar(id, dados) {
    try {
      const response = await api.put(`/manutencoes/${id}`, dados)
      return response.data
    } catch (error) {
      console.error(`Erro ao atualizar manutenção ${id}:`, error)
      throw error
    }
  },

  async excluir(id) {
    try {
      await api.delete(`/manutencoes/${id}`)
    } catch (error) {
      console.error(`Erro ao excluir manutenção ${id}:`, error)
      throw error
    }
  },

  async buscarVeiculos() {
    try {
      const response = await api.get('/veiculos/disponiveis')
      return response.data
    } catch (error) {
      console.error('Erro ao buscar veículos:', error)
      throw error
    }
  },
}

export default ManutencaoService
