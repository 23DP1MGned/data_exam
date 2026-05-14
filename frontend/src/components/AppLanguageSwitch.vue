<template>
  <v-menu location="bottom end" offset="10">
    <template #activator="{ props }">
      <v-btn
        v-bind="props"
        variant="text"
        class="language-switch-btn"
        :class="{
          'language-switch-btn-dark': darkMode,
          'language-switch-btn-admin': accent === 'admin',
          'language-switch-btn-coach': accent === 'coach'
        }"
      >
        <span class="language-switch-code">{{ currentLocale.toUpperCase() }}</span>
        <v-icon size="16">mdi-chevron-down</v-icon>
      </v-btn>
    </template>

    <v-list
      class="language-switch-menu"
      :class="{
        'language-switch-menu-dark': darkMode,
        'language-switch-menu-admin': accent === 'admin',
        'language-switch-menu-coach': accent === 'coach'
      }"
    >
      <v-list-item
        v-for="item in languageOptions"
        :key="item.value"
        :active="item.value === currentLocale"
        @click="setLocale(item.value)"
      >
        <v-list-item-title>{{ item.label }}</v-list-item-title>
      </v-list-item>
    </v-list>
  </v-menu>
</template>

<script setup>
import { useLocale } from '../i18n'

defineProps({
  darkMode: {
    type: Boolean,
    default: false
  },
  accent: {
    type: String,
    default: 'default'
  }
})

const { currentLocale, languageOptions, setLocale } = useLocale()
</script>

<style scoped>
.language-switch-btn {
  min-width: 68px;
  min-height: 56px;
  border-radius: 20px;
  padding: 0 14px;
  text-transform: none;
  color: #172033;
  background: rgba(255, 255, 255, 0.72);
  border: 1px solid rgba(214, 226, 247, 0.9);
  box-shadow: 0 12px 24px rgba(104, 128, 164, 0.12);
}

.language-switch-btn-dark {
  color: #eef4ff;
  background: rgba(27, 38, 59, 0.82);
  border-color: rgba(74, 92, 126, 0.42);
  box-shadow: 0 14px 28px rgba(4, 10, 24, 0.26);
}

.language-switch-btn-admin.language-switch-btn-dark {
  border-color: rgba(242, 140, 40, 0.32);
}

.language-switch-btn-coach.language-switch-btn-dark {
  border-color: rgba(156, 124, 255, 0.32);
}

.language-switch-code {
  font-size: 0.92rem;
  font-weight: 700;
  letter-spacing: 0.08em;
}

.language-switch-menu {
  min-width: 164px;
  border-radius: 18px !important;
  padding: 8px !important;
  background: rgba(248, 251, 255, 0.98) !important;
  border: 1px solid rgba(214, 226, 247, 0.92);
  box-shadow: 0 24px 44px rgba(76, 104, 148, 0.18);
}

.language-switch-menu-dark {
  background: rgba(16, 23, 37, 0.98) !important;
  border-color: rgba(74, 92, 126, 0.42);
  box-shadow: 0 24px 54px rgba(4, 10, 24, 0.34);
}

.language-switch-menu :deep(.v-list-item) {
  min-height: 44px;
  border-radius: 14px;
  color: #172033;
}

.language-switch-menu-dark :deep(.v-list-item) {
  color: #eef4ff;
}

.language-switch-menu :deep(.v-list-item--active) {
  background: rgba(22, 119, 255, 0.12);
  color: #1677ff;
}

.language-switch-menu-admin :deep(.v-list-item--active) {
  background: rgba(242, 140, 40, 0.14);
  color: #f28c28;
}

.language-switch-menu-coach :deep(.v-list-item--active) {
  background: rgba(156, 124, 255, 0.14);
  color: #9c7cff;
}

@media (max-width: 640px) {
  .language-switch-btn {
    min-width: 58px;
    min-height: 48px;
    padding: 0 12px;
    border-radius: 18px;
  }

  .language-switch-code {
    font-size: 0.84rem;
  }
}
</style>
