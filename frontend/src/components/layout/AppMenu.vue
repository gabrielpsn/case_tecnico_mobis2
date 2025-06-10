<template>
  <q-list>
    <template v-for="(item, index) in menuItems" :key="index">
      <!-- Itens de menu sem subitens -->
      <q-item
        v-if="!item.children && !item.group"
        v-ripple
        :to="{ name: item.name }"
        clickable
        exact
        active-class="text-primary"
      >
        <q-item-section avatar>
          <q-icon :name="item.meta?.icon || 'label'" />
        </q-item-section>
        <q-item-section>
          <q-item-label>{{ item.meta?.title || item.name }}</q-item-label>
        </q-item-section>
      </q-item>

      <!-- Itens de menu expansíveis (com subitens) -->
      <q-expansion-item
        v-else-if="item.children && item.children.length > 0"
        :icon="item.meta?.icon || 'folder'"
        :label="item.meta?.title || item.name"
        :default-opened="isExpanded(item)"
        expand-icon-class="text-grey-6"
        header-class="text-weight-medium"
      >
        <q-list>
          <q-item
            v-for="(child, childIndex) in item.children"
            :key="childIndex"
            v-ripple
            :to="{ name: child.name }"
            clickable
            exact
            active-class="text-primary"
            class="q-pl-xl"
          >
            <q-item-section avatar>
              <q-icon :name="child.meta?.icon || 'label'" size="xs" />
            </q-item-section>
            <q-item-section>
              <q-item-label>{{ child.meta?.title || child.name }}</q-item-label>
            </q-item-section>
          </q-item>
        </q-list>
      </q-expansion-item>

      <!-- Grupos de menu (como Cadastros, Frota, etc.) -->
      <div v-else-if="item.group">
        <q-separator class="q-my-md" />
        <q-item-label header class="text-weight-bold text-uppercase text-grey-7">
          {{ item.meta?.title || item.name }}
        </q-item-label>

        <q-item
          v-for="(child, childIndex) in item.children"
          :key="`group-${childIndex}`"
          v-ripple
          :to="{ name: child.name }"
          clickable
          exact
          active-class="text-primary"
          class="q-pl-md"
        >
          <q-item-section avatar>
            <q-icon :name="child.meta?.icon || 'label'" size="xs" />
          </q-item-section>
          <q-item-section>
            <q-item-label>{{ child.meta?.title || child.name }}</q-item-label>
          </q-item-section>
        </q-item>
      </div>
    </template>
  </q-list>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const props = defineProps({
  // Filtro para exibir apenas itens de menu de um grupo específico
  group: {
    type: String,
    default: null,
  },
})

const route = useRoute()

// Filtra as rotas para exibir no menu
const menuItems = computed(() => {
  const routes = route.matched[0]?.children || []

  // Filtra as rotas que devem aparecer no menu
  return routes.filter((route) => {
    // Remove rotas sem título (como rota raiz)
    if (!route.meta?.title) return false

    // Filtra por grupo, se especificado
    if (props.group) {
      return route.meta.group === props.group
    }

    // Remove rotas que pertencem a um grupo (elas serão exibidas dentro do grupo)
    if (route.meta.group) {
      return false
    }

    return true
  })
})

// Verifica se um item de menu expansível deve estar expandido por padrão
function isExpanded(item) {
  if (!item.children) return false

  // Verifica se alguma das rotas filhas corresponde à rota atual
  return item.children.some((child) => {
    return (
      route.name === child.name ||
      (child.children && child.children.some((subChild) => route.name === subChild.name))
    )
  })
}
</script>

<style scoped>
/* Estilo para itens de menu ativos */
.q-item--active {
  background-color: rgba(25, 118, 210, 0.08);
  color: var(--q-primary);
}

/* Estilo para itens de menu ao passar o mouse */
.q-item {
  transition: background-color 0.2s ease-in-out;
}

.q-item:hover {
  background-color: rgba(0, 0, 0, 0.04);
}

/* Estilo para itens de menu ativos ao passar o mouse */
.q-item--active:hover {
  background-color: rgba(25, 118, 210, 0.12);
}

/* Ajuste de espaçamento para subitens */
.q-item.q-pl-xl {
  padding-left: 48px !important;
}

/* Estilo para cabeçalhos de grupo */
.q-item__section--header {
  font-size: 0.75rem;
  letter-spacing: 0.5px;
  padding: 8px 16px;
}
</style>
