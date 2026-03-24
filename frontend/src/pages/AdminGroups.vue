<template>
  <v-app>
    <v-main class="admin-groups-page">
      <div class="admin-groups-shell" :class="{ 'admin-groups-shell-dark': darkMode }">
        <v-navigation-drawer
          v-if="isCompactNav && mobileMenuOpen"
          v-model="mobileMenuOpen"
          temporary
          location="left"
          width="286"
          class="mobile-drawer"
          :class="{ 'mobile-drawer-dark': darkMode }"
        >
          <div class="mobile-drawer-inner">
            <div class="brand-block mobile-drawer-brand">
              <div class="brand-icon">
                <v-icon color="white">mdi-school-outline</v-icon>
              </div>
              <div class="brand-text">
                <div class="brand-name">SportSystem</div>
                <div class="brand-caption">Admin workspace</div>
              </div>
            </div>

            <nav class="mobile-nav-list">
              <v-btn
                v-for="item in navItems"
                :key="`mobile-${item.label}`"
                :to="item.to || undefined"
                variant="text"
                class="nav-item"
                :class="{ 'nav-item-active': item.to === '/manage-groups' }"
                block
                @click="mobileMenuOpen = false"
              >
                <template #prepend>
                  <v-icon>{{ item.icon }}</v-icon>
                </template>
                {{ item.label }}
              </v-btn>
            </nav>

            <div class="mobile-drawer-profile">
              <v-avatar size="44">
                <img :src="avatarFor(profileSeed, profileName)" alt="Admin profile">
              </v-avatar>
              <div>
                <div class="profile-name">{{ profileName }}</div>
                <div class="profile-email">{{ profileEmail }}</div>
              </div>
            </div>

            <v-btn
              variant="outlined"
              class="mobile-logout-btn"
              prepend-icon="mdi-logout"
              @click="handleMobileLogout"
            >
              Log out
            </v-btn>
          </div>
        </v-navigation-drawer>

        <div class="admin-groups-panel">
          <aside class="sidebar-card">
            <div class="brand-block">
              <div class="brand-icon">
                <v-icon color="white">mdi-school-outline</v-icon>
              </div>
              <div class="brand-text">
                <div class="brand-name">SportSystem</div>
                <div class="brand-caption">Admin workspace</div>
              </div>
            </div>

            <nav class="nav-list">
              <v-btn
                v-for="item in navItems"
                :key="item.label"
                :to="item.to || undefined"
                variant="text"
                class="nav-item"
                :class="{ 'nav-item-active': item.to === '/manage-groups' }"
                block
              >
                <template #prepend>
                  <v-icon>{{ item.icon }}</v-icon>
                </template>
                {{ item.label }}
              </v-btn>
            </nav>
          </aside>

          <section class="content-shell">
            <div class="mobile-header-card">
              <button type="button" class="mobile-menu-btn" @click="mobileMenuOpen = true">
                <v-icon size="24">mdi-menu</v-icon>
              </button>

              <div class="mobile-brand-inline">
                <div class="brand-icon mobile-brand-icon">
                  <v-icon color="white">mdi-school-outline</v-icon>
                </div>
                <div class="mobile-brand-copy">
                  <div class="brand-name">SportSystem</div>
                  <div class="brand-caption">Groups</div>
                </div>
              </div>

              <div class="mobile-header-actions">
                <v-btn
                  icon
                  variant="text"
                  class="top-icon-btn mobile-theme-btn"
                  :class="{ 'top-icon-btn-active': darkMode }"
                  @click="darkMode = !darkMode"
                >
                  <v-icon>{{ darkMode ? 'mdi-weather-night' : 'mdi-white-balance-sunny' }}</v-icon>
                </v-btn>

                <div class="icon-badge-wrap">
                  <v-btn icon variant="text" class="top-icon-btn" @click="notificationsDialog = true">
                    <v-icon>mdi-bell-outline</v-icon>
                  </v-btn>
                  <span class="icon-badge">{{ unreadCount }}</span>
                </div>
              </div>
            </div>

            <div class="mobile-utility-card">
              <div class="mobile-profile-row">
                <div class="profile-pill mobile-profile-pill">
                  <v-avatar size="42">
                    <img :src="avatarFor(profileSeed, profileName)" alt="Admin profile">
                  </v-avatar>
                  <div>
                    <div class="profile-name">{{ profileName }}</div>
                    <div class="profile-email">{{ profileEmail }}</div>
                  </div>
                </div>

                <v-btn color="primary" class="mobile-create-btn" prepend-icon="mdi-plus" @click="openCreateDialog">
                  Create group
                </v-btn>
              </div>
            </div>

            <div class="topbar-card">
              <div class="search-wrap">
                <div class="search-shell">
                  <v-icon size="20" class="search-shell-icon">mdi-magnify</v-icon>
                  <v-text-field
                    v-model="search"
                    placeholder="Search groups"
                    variant="plain"
                    hide-details
                    density="comfortable"
                    class="search-field"
                  />
                </div>
              </div>

              <div class="topbar-tools">
                <div class="icon-badge-wrap">
                  <v-btn
                    icon
                    variant="text"
                    class="top-icon-btn"
                    :class="{ 'top-icon-btn-active': darkMode }"
                    @click="darkMode = !darkMode"
                  >
                    <v-icon>{{ darkMode ? 'mdi-weather-night' : 'mdi-white-balance-sunny' }}</v-icon>
                  </v-btn>
                </div>

                <div class="icon-badge-wrap">
                  <v-btn icon variant="text" class="top-icon-btn" @click="notificationsDialog = true">
                    <v-icon>mdi-bell-outline</v-icon>
                  </v-btn>
                  <span class="icon-badge">{{ unreadCount }}</span>
                </div>

                <v-btn
                  icon
                  variant="text"
                  class="top-icon-btn logout-btn"
                  @click="handleLogout"
                >
                  <v-icon>mdi-logout</v-icon>
                </v-btn>

                <div class="profile-pill">
                  <v-avatar size="48">
                    <img :src="avatarFor(profileSeed, profileName)" alt="Admin profile">
                  </v-avatar>
                  <div>
                    <div class="profile-name">{{ profileName }}</div>
                    <div class="profile-email">{{ profileEmail }}</div>
                  </div>
                </div>
              </div>
            </div>

            <div class="groups-card">
              <div class="groups-header">
                <div>
                  <h1 class="groups-title">Manage Groups</h1>
                  <div class="groups-subtitle">
                    Create, edit and organize all sports groups from the admin panel.
                  </div>
                </div>

                <v-btn color="primary" class="desktop-only-btn" prepend-icon="mdi-plus" @click="openCreateDialog">
                  Create group
                </v-btn>
              </div>

              <div class="overview-stats-grid">
                <article v-for="item in overviewStats" :key="item.label" class="overview-stat-card">
                  <div class="summary-label">{{ item.label }}</div>
                  <div class="summary-value">{{ item.value }}</div>
                </article>
              </div>

              <div v-if="errorMessage" class="state-wrap">
                <v-alert type="error" variant="tonal" border="start">
                  {{ errorMessage }}
                </v-alert>
              </div>

              <div v-else-if="loading" class="state-wrap loading-state">
                <v-progress-circular indeterminate color="primary" size="28" />
                <span>Loading groups...</span>
              </div>

              <div v-else-if="!filteredGroups.length" class="state-wrap empty-state">
                No groups found for the current search.
              </div>

              <div v-else class="groups-grid">
                <article
                  v-for="group in filteredGroups"
                  :key="group.id"
                  class="group-card"
                >
                  <div class="group-card-top">
                    <div class="group-card-main">
                      <v-avatar size="56" class="group-avatar">
                        <img :src="avatarFor(`group-${group.id}-${group.section}`, group.section)" :alt="group.section">
                      </v-avatar>

                      <div class="group-copy">
                        <div class="group-name">{{ group.section }}</div>
                        <div class="group-trainer">{{ group.trainer || 'Coach not assigned' }}</div>
                      </div>
                    </div>

                    <v-chip size="small" class="group-chip">
                      {{ group.age_category ? `Age ${group.age_category}` : 'No age category' }}
                    </v-chip>
                  </div>

                  <div class="group-info-grid">
                    <div class="info-item">
                      <span class="info-label">Students</span>
                      <span class="info-value">{{ group.students }}</span>
                    </div>

                    <div class="info-item">
                      <span class="info-label">Days</span>
                      <span class="info-value">{{ group.days || 'Not set' }}</span>
                    </div>

                    <div class="info-item">
                      <span class="info-label">Time</span>
                      <span class="info-value">{{ group.time || 'Not set' }}</span>
                    </div>

                    <div class="info-item">
                      <span class="info-label">Price</span>
                      <span class="info-value">{{ formatCurrency(group.price) }} / month</span>
                    </div>
                  </div>

                  <div class="group-linked">
                    <div class="linked-title">Linked children</div>
                    <div v-if="group.child_ids?.length" class="children-chips">
                      <v-chip
                        v-for="childId in group.child_ids"
                        :key="childId"
                        size="small"
                        variant="tonal"
                        class="child-chip"
                      >
                        {{ childNameById(childId) }}
                      </v-chip>
                    </div>
                    <div v-else class="linked-empty">No children linked</div>
                  </div>

                  <div class="group-card-actions">
                    <v-btn
                      color="primary"
                      class="action-btn action-btn-edit"
                      prepend-icon="mdi-pencil-outline"
                      @click="openEditDialog(group)"
                    >
                      Edit
                    </v-btn>
                    <v-btn
                      variant="outlined"
                      color="error"
                      class="action-btn action-btn-delete"
                      @click="deleteGroup(group)"
                    >
                      Delete
                    </v-btn>
                  </div>
                </article>
              </div>
            </div>
          </section>
        </div>

        <v-dialog v-model="groupDialog" max-width="760">
          <v-card class="dialog-card create-dialog-card">
            <div class="create-dialog-header">
              <div>
                <div class="create-dialog-title">{{ editingGroupId ? 'Edit Group' : 'Create Group' }}</div>
                <div class="create-dialog-subtitle">
                  Manage group details, assign a coach and link children from the admin panel.
                </div>
              </div>

              <v-btn icon variant="text" @click="closeDialog">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </div>

            <v-card-text class="create-dialog-content">
              <v-alert
                v-if="formError"
                type="error"
                variant="tonal"
                border="start"
                class="dialog-alert"
              >
                {{ formError }}
              </v-alert>

              <div class="create-fields-grid">
                <v-text-field v-model="form.name" label="Group name" variant="outlined" class="create-field" />
                <div class="age-range-fields">
                  <v-select
                    v-model="form.age_from"
                    :items="ageOptions"
                    item-title="label"
                    item-value="value"
                    label="Age from"
                    variant="outlined"
                    class="create-field"
                    :menu-props="selectMenuProps"
                  />
                  <v-select
                    v-model="form.age_to"
                    :items="ageOptions"
                    item-title="label"
                    item-value="value"
                    label="Age to"
                    variant="outlined"
                    class="create-field"
                    :menu-props="selectMenuProps"
                  />
                </div>
                <v-select
                  v-model="form.schedule_days"
                  :items="weekdayOptions"
                  item-title="label"
                  item-value="value"
                  label="Schedule days"
                  variant="outlined"
                  class="create-field"
                  :menu-props="selectMenuProps"
                  chips
                  multiple
                  closable-chips
                />
                <v-text-field
                  v-model="form.default_time"
                  label="Default time"
                  type="time"
                  variant="outlined"
                  class="create-field"
                />
                <v-text-field
                  v-model="form.price"
                  label="Price"
                  type="number"
                  min="0"
                  prepend-inner-icon="mdi-currency-eur"
                  variant="outlined"
                  class="create-field create-field-no-spin price-field"
                />

                <v-autocomplete
                  v-model="form.coach_id"
                  :items="coachOptions"
                  item-title="label"
                  item-value="value"
                  label="Coach"
                  variant="outlined"
                  class="create-field"
                  :menu-props="selectMenuProps"
                  placeholder="Search and select coach"
                  clearable
                />

                <v-autocomplete
                  v-model="form.child_ids"
                  :items="childOptions"
                  item-title="label"
                  item-value="value"
                  label="Children"
                  variant="outlined"
                  class="create-field create-field-full children-autocomplete-field"
                  :menu-props="selectMenuProps"
                  placeholder="Search and select children"
                  chips
                  multiple
                  closable-chips
                  clearable
                />
              </div>
            </v-card-text>

            <v-card-actions class="create-dialog-actions">
              <v-spacer></v-spacer>
              <v-btn color="primary" class="apply-filter-btn" :loading="saving" @click="saveGroup">
                {{ editingGroupId ? 'Save changes' : 'Create group' }}
              </v-btn>
              <v-btn variant="text" class="reset-filter-btn" @click="closeDialog">Cancel</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <AppNotificationsDialog
          v-model="notificationsDialog"
          :dark-mode="darkMode"
          :notifications="notificationItems"
          :loading="notificationsLoading"
          @notification-click="handleNotificationClick"
        />
      </div>
    </v-main>
  </v-app>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import AppNotificationsDialog from '../components/AppNotificationsDialog.vue'
import { useNotifications } from '../composables/useNotifications'
import { groupsApi, usersApi } from '../services/api'
import { logout, useAuth } from '../services/auth'
import { createAvatarDataUri } from '../utils/avatar'

const router = useRouter()
const search = ref('')
const darkMode = ref(false)
const groupDialog = ref(false)
const notificationsDialog = ref(false)
const mobileMenuOpen = ref(false)
const isCompactNav = ref(false)
const loading = ref(false)
const saving = ref(false)
const errorMessage = ref('')
const formError = ref('')
const editingGroupId = ref(null)
const darkModeStorageKey = 'app-dark-mode'

const navItems = [
  { label: 'Admin Panel', icon: 'mdi-shield-crown-outline', to: '/admin' },
  { label: 'Users', icon: 'mdi-account-multiple-outline', to: '/users' },
  { label: 'Groups', icon: 'mdi-account-group-outline', to: '/manage-groups' },
  { label: 'Sessions', icon: 'mdi-calendar-clock-outline', to: '/manage-sessions' }
]

const weekdayOptions = [
  { label: 'Monday', value: 'Mon' },
  { label: 'Tuesday', value: 'Tue' },
  { label: 'Wednesday', value: 'Wed' },
  { label: 'Thursday', value: 'Thu' },
  { label: 'Friday', value: 'Fri' },
  { label: 'Saturday', value: 'Sat' },
  { label: 'Sunday', value: 'Sun' }
]

const ageOptions = Array.from({ length: 15 }, (_, index) => {
  const age = index + 4
  return {
    label: String(age),
    value: age
  }
})

const selectMenuProps = computed(() => ({
  contentClass: darkMode.value ? 'admin-groups-select-menu admin-groups-select-menu-dark' : 'admin-groups-select-menu',
  theme: darkMode.value ? 'dark' : 'light'
}))

const avatarFor = (seed, label = seed) => createAvatarDataUri(seed, label)
const { user } = useAuth()
const {
  items: notificationItems,
  loading: notificationsLoading,
  unreadCount,
  loadNotifications,
  markNotificationRead
} = useNotifications()

const groups = ref([])
const users = ref([])
const form = ref(getDefaultForm())

const profileName = computed(() => {
  if (!user.value) return 'Admin User'
  return `${user.value.name} ${user.value.surname}`.trim()
})
const profileEmail = computed(() => user.value?.email ?? 'admin@sportsystem.app')
const profileSeed = computed(() => user.value?.email ?? profileName.value)

const coachOptions = computed(() =>
  users.value
    .filter((item) => item.role === 'coach')
    .map((item) => ({
      label: item.full_name,
      value: item.id
    }))
)

const childOptions = computed(() =>
  users.value
    .filter((item) => item.role === 'child')
    .map((item) => ({
      label: item.full_name,
      value: item.id
    }))
)

const filteredGroups = computed(() => {
  const query = search.value.trim().toLowerCase()

  return groups.value.filter((group) => {
    if (!query) return true

    return [
      group.section,
      group.trainer,
      group.days,
      group.age_category
    ]
      .filter(Boolean)
      .some((value) => value.toLowerCase().includes(query))
  })
})

const overviewStats = computed(() => [
  { label: 'Total groups', value: groups.value.length },
  { label: 'Assigned coaches', value: new Set(groups.value.map((item) => item.trainer).filter(Boolean)).size },
  { label: 'Children linked (total links, one child can be in multiple groups)', value: groups.value.reduce((acc, item) => acc + (item.students || 0), 0) },
  { label: 'Average price', value: formatCurrency(averagePrice.value) }
])

const averagePrice = computed(() => {
  if (!groups.value.length) return 0
  return groups.value.reduce((acc, item) => acc + Number(item.price || 0), 0) / groups.value.length
})

onMounted(() => {
  darkMode.value = localStorage.getItem(darkModeStorageKey) === 'true'
  updateViewportState()
  window.addEventListener('resize', updateViewportState)
  initializePage()
  loadNotifications()
})

watch(darkMode, (value) => {
  localStorage.setItem(darkModeStorageKey, String(value))
})

watch(isCompactNav, (value) => {
  if (!value) mobileMenuOpen.value = false
})

watch(notificationsDialog, (value) => {
  if (value) {
    loadNotifications(true)
  }
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', updateViewportState)
})

function getDefaultForm() {
  return {
    name: '',
    age_from: null,
    age_to: null,
    schedule_days: [],
    default_time: '',
    price: 0,
    coach_id: null,
    child_ids: []
  }
}

function updateViewportState() {
  isCompactNav.value = window.innerWidth <= 1024
}

function formatCurrency(value) {
  return `€${Number(value ?? 0).toFixed(0)}`
}

function childNameById(childId) {
  return users.value.find((item) => item.id === childId)?.full_name ?? `Child #${childId}`
}

function closeDialog() {
  groupDialog.value = false
  editingGroupId.value = null
  formError.value = ''
  form.value = getDefaultForm()
}

function openCreateDialog() {
  editingGroupId.value = null
  formError.value = ''
  form.value = getDefaultForm()
  groupDialog.value = true
}

function openEditDialog(group) {
  editingGroupId.value = group.id
  formError.value = ''
  form.value = {
    name: group.section ?? '',
    ...parseAgeCategory(group.age_category),
    schedule_days: parseScheduleDays(group.days),
    default_time: group.time ?? '',
    price: group.price ?? 0,
    coach_id: coachIdByName(group.trainer),
    child_ids: Array.isArray(group.child_ids) ? [...group.child_ids] : []
  }
  groupDialog.value = true
}

function coachIdByName(name) {
  const coach = users.value.find((item) => item.role === 'coach' && item.full_name === name)
  return coach?.id ?? null
}

function parseScheduleDays(value) {
  if (!value) return []

  return String(value)
    .split('/')
    .map((item) => item.trim())
    .filter(Boolean)
}

function parseAgeCategory(value) {
  if (!value) {
    return {
      age_from: null,
      age_to: null
    }
  }

  const matches = String(value).match(/(\d+)\D+(\d+)/)
  if (!matches) {
    return {
      age_from: null,
      age_to: null
    }
  }

  return {
    age_from: Number(matches[1]),
    age_to: Number(matches[2])
  }
}

function extractErrorMessage(error, fallback) {
  const validationErrors = error?.response?.data?.errors

  if (validationErrors && typeof validationErrors === 'object') {
    const firstError = Object.values(validationErrors).flat()[0]
    if (firstError) return firstError
  }

  return error?.response?.data?.message || fallback
}

async function initializePage() {
  loading.value = true
  errorMessage.value = ''

  try {
    const [groupsResponse, usersResponse] = await Promise.all([
      groupsApi.list(),
      usersApi.list()
    ])

    groups.value = groupsResponse
    users.value = usersResponse
  } catch (error) {
    errorMessage.value = extractErrorMessage(error, 'Failed to load groups.')
  } finally {
    loading.value = false
  }
}

function buildPayload() {
  return {
    name: form.value.name.trim(),
    age_category: form.value.age_from && form.value.age_to
      ? `${form.value.age_from}-${form.value.age_to}`
      : null,
    schedule_days: Array.isArray(form.value.schedule_days) && form.value.schedule_days.length
      ? form.value.schedule_days.join(' / ')
      : null,
    default_time: form.value.default_time?.trim() || null,
    price: form.value.price === '' || form.value.price == null ? 0 : Number(form.value.price),
    coach_id: form.value.coach_id || null,
    child_ids: Array.isArray(form.value.child_ids) ? form.value.child_ids : []
  }
}

async function saveGroup() {
  saving.value = true
  formError.value = ''

  try {
    const payload = buildPayload()

    if (!payload.name) {
      formError.value = 'Group name is required.'
      return
    }

    if (!payload.coach_id) {
      formError.value = 'Please assign a coach to the group.'
      return
    }

    if ((form.value.age_from && !form.value.age_to) || (!form.value.age_from && form.value.age_to)) {
      formError.value = 'Please select both age limits for the age category.'
      return
    }

    if (form.value.age_from && form.value.age_to && form.value.age_from > form.value.age_to) {
      formError.value = 'Age from cannot be greater than age to.'
      return
    }

    if (editingGroupId.value) {
      await groupsApi.update(editingGroupId.value, payload)
    } else {
      await groupsApi.create(payload)
    }

    await initializePage()
    closeDialog()
  } catch (error) {
    formError.value = extractErrorMessage(error, 'Failed to save group.')
  } finally {
    saving.value = false
  }
}

async function deleteGroup(group) {
  const confirmed = window.confirm(`Delete ${group.section}? This action cannot be undone.`)
  if (!confirmed) return

  try {
    await groupsApi.remove(group.id)
    await initializePage()
  } catch (error) {
    errorMessage.value = extractErrorMessage(error, 'Failed to delete group.')
  }
}

async function handleNotificationClick(item) {
  if (item?.unread) {
    await markNotificationRead(item.id)
  }
}

async function handleLogout() {
  await logout()
  router.push('/login')
}

async function handleMobileLogout() {
  mobileMenuOpen.value = false
  await handleLogout()
}
</script>

<style scoped>
.admin-groups-page {
  padding: 24px;
}

.admin-groups-shell {
  position: relative;
  max-width: 1440px;
  margin: 0 auto;
  border-radius: 34px;
  overflow: visible;
  border: 1px solid rgba(255, 255, 255, 0.45);
  background: linear-gradient(135deg, rgba(236, 244, 255, 0.82), rgba(245, 237, 248, 0.78));
  box-shadow: 0 24px 70px rgba(95, 122, 168, 0.18);
  backdrop-filter: blur(18px);
}

.admin-groups-shell-dark {
  border-color: rgba(91, 109, 145, 0.4);
  background: linear-gradient(135deg, rgba(17, 24, 39, 0.96), rgba(28, 36, 54, 0.94));
  box-shadow: 0 28px 80px rgba(4, 10, 24, 0.45);
}

.admin-groups-panel {
  display: grid;
  grid-template-columns: 232px minmax(0, 1fr);
  gap: 22px;
  padding: 22px;
}

.mobile-header-card,
.mobile-utility-card {
  display: none;
}

.mobile-drawer-inner {
  display: flex;
  flex-direction: column;
  height: 100%;
  padding: 18px;
  background: linear-gradient(180deg, rgba(247, 251, 255, 0.98), rgba(238, 245, 255, 0.96));
}

.mobile-drawer :deep(.v-navigation-drawer__scrim),
.mobile-drawer :deep(.v-navigation-drawer__content),
.mobile-drawer :deep(.v-navigation-drawer__prepend),
.mobile-drawer :deep(.v-navigation-drawer__append) {
  background: transparent;
}

.mobile-drawer-dark {
  background: linear-gradient(180deg, rgba(17, 24, 39, 0.98), rgba(22, 31, 48, 0.98)) !important;
  color: #eef4ff;
}

.mobile-drawer-dark :deep(.v-navigation-drawer__content),
.mobile-drawer-dark .mobile-drawer-inner {
  background: linear-gradient(180deg, rgba(17, 24, 39, 0.98), rgba(22, 31, 48, 0.98));
}

.mobile-drawer-brand {
  margin-bottom: 20px;
}

.mobile-nav-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.mobile-drawer-profile {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: 14px;
  padding: 14px;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.78);
}

.admin-groups-shell-dark .mobile-drawer-profile {
  background: rgba(18, 27, 43, 0.92);
  border: 1px solid rgba(74, 92, 126, 0.42);
}

.mobile-logout-btn {
  justify-content: flex-start;
  margin-top: 12px;
  min-height: 56px;
  border-radius: 18px;
  text-transform: none;
  letter-spacing: 0;
  font-weight: 600;
}

.sidebar-card {
  display: flex;
  flex-direction: column;
  min-height: 100%;
  padding: 18px;
  border-radius: 28px;
  background: rgba(245, 250, 255, 0.8);
  border: 1px solid rgba(255, 255, 255, 0.62);
}

.admin-groups-shell-dark .sidebar-card {
  background: rgba(18, 27, 43, 0.88);
  border-color: rgba(74, 92, 126, 0.42);
}

.brand-block {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-bottom: 26px;
  padding: 10px 8px;
  min-width: 0;
}

.brand-icon {
  display: grid;
  place-items: center;
  width: 54px;
  height: 54px;
  flex-shrink: 0;
  border-radius: 18px;
  background: linear-gradient(180deg, #1677ff 0%, #0f5fe3 100%);
  box-shadow: 0 16px 30px rgba(22, 119, 255, 0.28);
}

.brand-text {
  min-width: 0;
  flex: 1;
}

.brand-name {
  font-size: 1.22rem;
  font-weight: 700;
  color: #172033;
  line-height: 1.1;
}

.admin-groups-shell-dark .brand-name {
  color: #f3f7ff;
}

.brand-caption {
  margin-top: 2px;
  font-size: 0.82rem;
  color: #7b8798;
}

.admin-groups-shell-dark .brand-caption {
  color: #94a6c4;
}

.nav-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.nav-item {
  justify-content: flex-start;
  min-height: 58px;
  border-radius: 18px;
  color: #172033;
  text-transform: none;
  letter-spacing: 0;
  font-size: 1rem;
  font-weight: 500;
}

.admin-groups-shell-dark .nav-item {
  color: #d8e2f2;
}

:deep(.nav-item .v-btn__content) {
  justify-content: flex-start;
}

.nav-item-active {
  color: white;
  background: linear-gradient(180deg, #1677ff 0%, #0f5fe3 100%);
  box-shadow: 0 16px 34px rgba(22, 119, 255, 0.28);
}

.content-shell {
  display: flex;
  flex-direction: column;
  gap: 22px;
}

.mobile-header-card {
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 14px 16px;
  border-radius: 22px;
  background: rgba(255, 255, 255, 0.62);
  border: 1px solid rgba(255, 255, 255, 0.62);
}

.admin-groups-shell-dark .mobile-header-card {
  background: rgba(22, 31, 48, 0.82);
  border-color: rgba(74, 92, 126, 0.42);
}

.mobile-menu-btn {
  display: grid;
  place-items: center;
  width: 46px;
  height: 46px;
  padding: 0;
  border: 1px solid rgba(223, 231, 243, 0.92);
  border-radius: 14px;
  color: #111827;
  background: rgba(255, 255, 255, 0.92);
  cursor: pointer;
  flex-shrink: 0;
}

.admin-groups-shell-dark .mobile-menu-btn {
  color: #eef4ff;
  background: rgba(13, 20, 34, 0.88);
  border-color: rgba(63, 80, 114, 0.58);
}

.mobile-brand-inline {
  display: flex;
  align-items: center;
  gap: 12px;
  min-width: 0;
}

.mobile-brand-icon {
  width: 46px;
  height: 46px;
  border-radius: 15px;
}

.mobile-brand-copy {
  min-width: 0;
}

.mobile-header-actions {
  display: flex;
  align-items: center;
  gap: 8px;
}

.mobile-utility-card {
  padding: 16px;
  border-radius: 24px;
  background: rgba(255, 255, 255, 0.62);
  border: 1px solid rgba(255, 255, 255, 0.62);
}

.admin-groups-shell-dark .mobile-utility-card {
  background: rgba(22, 31, 48, 0.82);
  border-color: rgba(74, 92, 126, 0.42);
}

.mobile-profile-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 14px;
}

.topbar-card {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 18px;
  padding: 18px 22px;
  border-radius: 28px;
  background: rgba(255, 255, 255, 0.62);
  border: 1px solid rgba(255, 255, 255, 0.62);
}

.admin-groups-shell-dark .topbar-card {
  background: rgba(22, 31, 48, 0.82);
  border-color: rgba(74, 92, 126, 0.42);
}

.search-wrap {
  flex: 1;
  min-width: 0;
}

.search-shell {
  display: flex;
  align-items: center;
  gap: 12px;
  min-height: 58px;
  padding: 0 18px;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.92);
  border: 1px solid rgba(230, 237, 246, 0.94);
}

.admin-groups-shell-dark .search-shell {
  background: rgba(13, 20, 34, 0.88);
  border-color: rgba(63, 80, 114, 0.58);
}

.search-shell-icon {
  color: #111827;
}

.admin-groups-shell-dark .search-shell-icon {
  color: #edf4ff;
}

.search-field {
  flex: 1;
}

.search-field :deep(input) {
  color: #111827;
  opacity: 1;
}

.admin-groups-shell-dark .search-field :deep(input) {
  color: #edf4ff;
}

.search-field :deep(input::placeholder) {
  color: #111827;
  opacity: 1;
}

.admin-groups-shell-dark .search-field :deep(input::placeholder) {
  color: #edf4ff;
}

.search-field :deep(.v-field__input) {
  padding-top: 0;
  padding-bottom: 0;
  min-height: auto;
}

.topbar-tools {
  display: flex;
  align-items: center;
  gap: 12px;
}

.icon-badge-wrap {
  position: relative;
}

.top-icon-btn {
  width: 54px;
  height: 54px;
  border-radius: 18px;
  color: #111827;
  background: rgba(255, 255, 255, 0.92);
  border: 1px solid rgba(230, 237, 246, 0.94);
}

.admin-groups-shell-dark .top-icon-btn {
  color: #eef4ff;
  background: rgba(13, 20, 34, 0.88);
  border-color: rgba(63, 80, 114, 0.58);
}

.top-icon-btn-active {
  color: #0f5fe3;
}

.logout-btn {
  color: #111827;
}

.admin-groups-shell-dark .logout-btn {
  color: #eef4ff;
}

.icon-badge {
  position: absolute;
  top: 2px;
  right: 2px;
  min-width: 20px;
  height: 20px;
  padding: 0 6px;
  display: grid;
  place-items: center;
  border-radius: 999px;
  background: #1677ff;
  color: white;
  font-size: 0.72rem;
  font-weight: 700;
}

.profile-pill {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 12px;
  border-radius: 22px;
  background: rgba(255, 255, 255, 0.88);
  border: 1px solid rgba(230, 237, 246, 0.94);
}

.admin-groups-shell-dark .profile-pill {
  background: rgba(13, 20, 34, 0.88);
  border-color: rgba(63, 80, 114, 0.58);
}

.mobile-profile-pill {
  flex: 1;
}

.profile-name {
  font-size: 0.98rem;
  font-weight: 700;
  color: #1c2438;
}

.admin-groups-shell-dark .profile-name {
  color: #f3f7ff;
}

.profile-email {
  margin-top: 2px;
  font-size: 0.86rem;
  color: #78859a;
}

.admin-groups-shell-dark .profile-email {
  color: #94a6c4;
}

.groups-card {
  padding: 26px;
  border-radius: 30px;
  background: rgba(249, 252, 255, 0.72);
  border: 1px solid rgba(255, 255, 255, 0.72);
}

.admin-groups-shell-dark .groups-card {
  background: rgba(18, 27, 43, 0.86);
  border-color: rgba(74, 92, 126, 0.42);
}

.groups-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 18px;
  margin-bottom: 24px;
}

.groups-title {
  margin: 0;
  font-size: 2.1rem;
  line-height: 1.1;
  color: #172033;
}

.admin-groups-shell-dark .groups-title {
  color: #f3f7ff;
}

.groups-subtitle,
.summary-label,
.info-label,
.linked-title {
  color: #7b8798;
}

.admin-groups-shell-dark .groups-subtitle,
.admin-groups-shell-dark .summary-label,
.admin-groups-shell-dark .info-label,
.admin-groups-shell-dark .linked-title {
  color: #94a6c4;
}

.overview-stats-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 16px;
  margin-bottom: 22px;
}

.overview-stat-card {
  padding: 20px;
  border-radius: 22px;
  background: rgba(255, 255, 255, 0.88);
  border: 1px solid rgba(229, 236, 246, 0.94);
}

.admin-groups-shell-dark .overview-stat-card {
  background: rgba(13, 20, 34, 0.88);
  border-color: rgba(63, 80, 114, 0.58);
}

.summary-value {
  margin-top: 8px;
  font-size: 1.9rem;
  font-weight: 700;
  color: #172033;
}

.admin-groups-shell-dark .summary-value {
  color: #f3f7ff;
}

.state-wrap {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 180px;
  padding: 18px;
  border-radius: 22px;
  background: rgba(255, 255, 255, 0.75);
  border: 1px dashed rgba(194, 206, 225, 0.92);
  color: #5f6f88;
}

.admin-groups-shell-dark .state-wrap {
  background: rgba(13, 20, 34, 0.72);
  border-color: rgba(78, 97, 132, 0.58);
  color: #aac0df;
}

.loading-state {
  flex-direction: column;
  gap: 14px;
}

.groups-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 18px;
}

.group-card {
  display: flex;
  flex-direction: column;
  gap: 16px;
  height: 100%;
  padding: 22px;
  border-radius: 26px;
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.94), rgba(245, 249, 255, 0.9));
  border: 1px solid rgba(229, 236, 246, 0.94);
  box-shadow: 0 14px 34px rgba(125, 148, 190, 0.09);
}

.admin-groups-shell-dark .group-card {
  background: linear-gradient(180deg, rgba(13, 20, 34, 0.94), rgba(18, 27, 43, 0.92));
  border-color: rgba(63, 80, 114, 0.58);
  box-shadow: 0 18px 38px rgba(4, 10, 24, 0.3);
}

.group-card-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 14px;
}

.group-card-main {
  display: flex;
  align-items: center;
  gap: 14px;
  min-width: 0;
}

.group-copy {
  min-width: 0;
}

.group-name {
  font-size: 1.08rem;
  font-weight: 700;
  color: #172033;
}

.admin-groups-shell-dark .group-name,
.admin-groups-shell-dark .info-value {
  color: #f3f7ff;
}

.group-trainer {
  margin-top: 4px;
  color: #7b8798;
}

.admin-groups-shell-dark .group-trainer {
  color: #94a6c4;
}

.group-chip {
  border-radius: 999px;
  font-weight: 700;
  color: #0f5fe3;
  background: rgba(194, 225, 255, 0.5);
}

.group-info-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 12px;
  padding: 14px;
  border-radius: 20px;
  background: rgba(241, 246, 255, 0.8);
  border: 1px solid rgba(223, 232, 246, 0.95);
}

.admin-groups-shell-dark .group-info-grid {
  background: rgba(17, 25, 40, 0.88);
  border-color: rgba(58, 75, 108, 0.62);
}

.info-item {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.info-value {
  font-weight: 600;
  color: #172033;
}

.group-linked {
  display: flex;
  flex-direction: column;
  gap: 10px;
  padding-top: 14px;
  border-top: 1px solid rgba(211, 221, 236, 0.95);
}

.admin-groups-shell-dark .group-linked {
  border-top-color: rgba(61, 78, 111, 0.72);
}

.children-chips {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.child-chip {
  border-radius: 999px;
  color: #1f4fa3;
  background: rgba(198, 223, 255, 0.7);
  border: 1px solid rgba(147, 184, 243, 0.78);
}

.admin-groups-shell-dark .child-chip {
  color: #dce9ff;
  background: rgba(31, 72, 133, 0.42);
  border-color: rgba(83, 122, 188, 0.62);
}

.linked-empty {
  color: #7b8798;
}

.admin-groups-shell-dark .linked-empty {
  color: #94a6c4;
}

.group-card-actions {
  display: flex;
  gap: 10px;
  margin-top: auto;
  padding-top: 4px;
}

.action-btn {
  flex: 1;
  min-height: 48px;
  border-radius: 18px;
  text-transform: none;
  letter-spacing: 0;
  font-weight: 700;
}

.action-btn-edit {
  box-shadow: 0 14px 28px rgba(22, 119, 255, 0.16);
}

.action-btn-delete {
  background: rgba(255, 255, 255, 0.68);
}

.admin-groups-shell-dark .action-btn-delete {
  background: rgba(17, 25, 40, 0.56);
}

.dialog-card {
  border-radius: 28px;
  overflow: hidden;
  max-height: min(88vh, 820px);
}

.create-dialog-card {
  border: 1px solid rgba(223, 232, 246, 0.95);
  background:
    radial-gradient(circle at top left, rgba(147, 197, 253, 0.22), transparent 34%),
    radial-gradient(circle at top right, rgba(244, 200, 255, 0.18), transparent 30%),
    linear-gradient(180deg, rgba(247, 251, 255, 0.99), rgba(235, 243, 255, 0.98));
  box-shadow: 0 28px 70px rgba(79, 106, 154, 0.22);
}

.admin-groups-shell-dark :deep(.v-overlay__content .create-dialog-card) {
  border-color: rgba(66, 84, 118, 0.64);
  background:
    radial-gradient(circle at top left, rgba(55, 116, 255, 0.18), transparent 34%),
    radial-gradient(circle at top right, rgba(190, 120, 255, 0.12), transparent 30%),
    linear-gradient(180deg, rgba(16, 23, 37, 0.99), rgba(22, 31, 48, 0.98));
  box-shadow: 0 28px 80px rgba(3, 8, 20, 0.55);
  color: #eef4ff;
}

.create-dialog-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
  padding: 24px 24px 18px;
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.38), rgba(255, 255, 255, 0.08));
  border-bottom: 1px solid rgba(219, 230, 246, 0.78);
}

.admin-groups-shell-dark .create-dialog-header {
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.03), rgba(255, 255, 255, 0));
  border-bottom-color: rgba(64, 82, 116, 0.68);
}

.create-dialog-title {
  font-size: 1.55rem;
  font-weight: 700;
  color: #172033;
}

.admin-groups-shell-dark .create-dialog-title {
  color: #f3f7ff;
}

.create-dialog-subtitle {
  margin-top: 6px;
  color: #74849a;
  max-width: 430px;
}

.admin-groups-shell-dark .create-dialog-subtitle {
  color: #94a6c4;
}

.create-dialog-content {
  padding: 18px 24px 14px !important;
  overflow-y: auto;
}

.dialog-alert {
  margin-bottom: 16px;
}

.create-fields-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 16px;
}

.age-range-fields {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 16px;
}

.create-field-full {
  grid-column: 1 / -1;
}

.create-dialog-actions {
  padding: 18px 24px 24px;
  border-top: 1px solid rgba(219, 230, 246, 0.72);
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0.28));
}

.admin-groups-shell-dark .create-dialog-actions {
  border-top-color: rgba(64, 82, 116, 0.62);
  background: linear-gradient(180deg, rgba(255, 255, 255, 0), rgba(255, 255, 255, 0.03));
}

.reset-filter-btn,
.apply-filter-btn,
.desktop-only-btn,
.mobile-create-btn {
  min-height: 46px;
  border-radius: 16px;
  text-transform: none;
  letter-spacing: 0;
}

.desktop-only-btn {
  min-height: 52px;
  font-weight: 600;
}

.reset-filter-btn {
  color: #6e7f97;
  background: transparent !important;
}

.admin-groups-shell-dark .reset-filter-btn {
  color: #9eb1cf;
}

.create-dialog-card :deep(.v-btn--icon) {
  color: #172033;
  background: rgba(255, 255, 255, 0.66);
  border: 1px solid rgba(219, 230, 246, 0.92);
}

.admin-groups-shell-dark .create-dialog-card :deep(.v-btn--icon) {
  color: #eef4ff;
  background: rgba(18, 27, 43, 0.72);
  border-color: rgba(64, 82, 116, 0.62);
}

.create-dialog-card :deep(.create-field .v-field) {
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.7);
  box-shadow: inset 0 0 0 1px rgba(223, 232, 246, 0.95);
}

.create-dialog-card :deep(.create-field .v-field--focused) {
  background: rgba(255, 255, 255, 0.92);
  box-shadow:
    inset 0 0 0 1px rgba(74, 144, 255, 0.72),
    0 0 0 4px rgba(22, 119, 255, 0.08);
}

.create-dialog-card :deep(.create-field .v-field__outline) {
  --v-field-border-opacity: 0;
}

.create-dialog-card :deep(.create-field-no-spin input[type='number']) {
  -moz-appearance: textfield;
}

.create-dialog-card :deep(.create-field-no-spin input[type='number']::-webkit-outer-spin-button),
.create-dialog-card :deep(.create-field-no-spin input[type='number']::-webkit-inner-spin-button) {
  margin: 0;
  -webkit-appearance: none;
}

.create-dialog-card :deep(.children-autocomplete-field .v-field__input) {
  align-items: flex-start;
  padding-top: 14px;
  padding-bottom: 10px;
}

.create-dialog-card :deep(.children-autocomplete-field input) {
  align-self: flex-start;
}

.create-dialog-card :deep(.price-field .v-field__prepend-inner) {
  color: #172033;
  font-size: 1rem;
}

.create-dialog-card :deep(.price-field .v-field__prepend-inner .v-icon) {
  font-size: 18px;
}

.create-dialog-card :deep(.children-autocomplete-field .v-chip) {
  color: #1f4fa3;
  background: rgba(198, 223, 255, 0.78);
  border: 1px solid rgba(147, 184, 243, 0.82);
}

.create-dialog-card :deep(.children-autocomplete-field .v-chip .v-chip__close) {
  color: #1f4fa3;
  opacity: 0.82;
}

.create-dialog-card :deep(.create-field input),
.create-dialog-card :deep(.create-field textarea),
.create-dialog-card :deep(.create-field .v-select__selection-text),
.create-dialog-card :deep(.create-field .v-select__selection) {
  color: #172033;
}

.create-dialog-card :deep(.create-field .v-label),
.create-dialog-card :deep(.create-field .v-field__append-inner) {
  color: #6f7f96;
}

.admin-groups-shell-dark .create-dialog-card :deep(.create-field .v-field) {
  background: rgba(17, 25, 40, 0.86);
  box-shadow: inset 0 0 0 1px rgba(64, 82, 116, 0.72);
}

.admin-groups-shell-dark .create-dialog-card :deep(.create-field .v-field--focused) {
  background: rgba(22, 31, 48, 0.98);
  box-shadow:
    inset 0 0 0 1px rgba(97, 155, 255, 0.74),
    0 0 0 4px rgba(22, 119, 255, 0.12);
}

.admin-groups-shell-dark .create-dialog-card :deep(.price-field .v-field__prepend-inner) {
  color: #eef4ff;
}

.admin-groups-shell-dark .create-dialog-card :deep(.children-autocomplete-field .v-chip) {
  color: #dce9ff;
  background: rgba(31, 72, 133, 0.42);
  border-color: rgba(83, 122, 188, 0.62);
}

.admin-groups-shell-dark .create-dialog-card :deep(.children-autocomplete-field .v-chip .v-chip__close) {
  color: #dce9ff;
}

.admin-groups-shell-dark .create-dialog-card :deep(.create-field input),
.admin-groups-shell-dark .create-dialog-card :deep(.create-field textarea),
.admin-groups-shell-dark .create-dialog-card :deep(.create-field .v-select__selection-text),
.admin-groups-shell-dark .create-dialog-card :deep(.create-field .v-select__selection) {
  color: #eef4ff;
}

.admin-groups-shell-dark .create-dialog-card :deep(.create-field .v-label),
.admin-groups-shell-dark .create-dialog-card :deep(.create-field .v-field__append-inner) {
  color: #94a6c4;
}

:deep(.admin-groups-select-menu) {
  border-radius: 20px;
  overflow: hidden;
  border: 1px solid rgba(223, 232, 246, 0.94);
  background: linear-gradient(180deg, rgba(250, 252, 255, 0.98), rgba(239, 246, 255, 0.98));
  box-shadow: 0 22px 48px rgba(79, 106, 154, 0.22);
}

:deep(.admin-groups-select-menu .v-list) {
  padding: 8px;
  background: transparent;
}

:deep(.admin-groups-select-menu .v-list-item) {
  min-height: 46px;
  border-radius: 14px;
  color: #172033;
}

:deep(.admin-groups-select-menu .v-list-item:hover) {
  background: rgba(207, 226, 252, 0.56);
}

:deep(.admin-groups-select-menu .v-list-item--active) {
  background: linear-gradient(180deg, #1677ff 0%, #0f5fe3 100%);
  color: white;
}

:deep(.admin-groups-select-menu-dark) {
  border-color: rgba(64, 82, 116, 0.62);
  background: linear-gradient(180deg, rgba(16, 23, 37, 0.99), rgba(22, 31, 48, 0.98));
  box-shadow: 0 24px 56px rgba(3, 8, 20, 0.55);
}

:deep(.admin-groups-select-menu-dark .v-list) {
  background: transparent;
}

:deep(.admin-groups-select-menu-dark .v-list-item) {
  color: #eef4ff;
}

:deep(.admin-groups-select-menu-dark .v-list-item:hover) {
  background: rgba(36, 52, 79, 0.78);
}

:deep(.admin-groups-select-menu-dark .v-list-item--active) {
  background: linear-gradient(180deg, #1677ff 0%, #0f5fe3 100%);
  color: white;
}

@media (max-width: 1180px) {
  .groups-grid,
  .overview-stats-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 1024px) {
  .admin-groups-page {
    padding: 16px;
  }

  .admin-groups-shell {
    overflow: hidden;
  }

  .admin-groups-panel {
    display: block;
    padding: 16px;
  }

  .sidebar-card,
  .topbar-card .search-wrap {
    display: none;
  }

  .mobile-header-card,
  .mobile-utility-card {
    display: block;
  }

  .mobile-header-card {
    display: flex;
  }

  .topbar-card {
    justify-content: flex-end;
    padding: 16px;
  }

  .topbar-tools .profile-pill,
  .logout-btn {
    display: none;
  }
}

@media (max-width: 768px) {
  .groups-card {
    padding: 20px;
  }

  .groups-header,
  .mobile-profile-row {
    flex-direction: column;
    align-items: stretch;
  }

  .overview-stats-grid,
  .groups-grid,
  .create-fields-grid,
  .age-range-fields,
  .group-info-grid {
    grid-template-columns: 1fr;
  }

  .desktop-only-btn {
    display: none;
  }

  .group-card-top,
  .group-card-actions {
    flex-direction: column;
    align-items: stretch;
  }

  .group-chip {
    align-self: flex-start;
  }
}

@media (max-width: 560px) {
  .admin-groups-page {
    padding: 10px;
  }

  .admin-groups-panel {
    padding: 10px;
  }

  .groups-card {
    padding: 16px;
    border-radius: 24px;
  }

  .topbar-card,
  .mobile-header-card,
  .mobile-utility-card {
    border-radius: 20px;
  }

  .groups-title {
    font-size: 1.75rem;
  }

  .summary-value {
    font-size: 1.6rem;
  }
}
</style>
