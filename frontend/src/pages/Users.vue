<template>
  <v-app>
    <v-main class="users-page">
      <div class="users-shell" :class="{ 'users-shell-dark': darkMode }">
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
                :class="{ 'nav-item-active': item.to === '/users' }"
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

        <div class="users-panel">
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
                :class="{ 'nav-item-active': item.to === '/users' }"
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
                  <div class="brand-caption">Users</div>
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
                  Create user
                </v-btn>
              </div>
            </div>

            <div class="topbar-card">
              <div class="search-wrap">
                <div class="search-shell">
                  <v-icon size="20" class="search-shell-icon">mdi-magnify</v-icon>
                  <v-text-field
                    v-model="search"
                    placeholder="Search users"
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

            <div class="users-card">
              <div class="users-header">
                <div>
                  <h1 class="users-title">Users</h1>
                  <div class="users-subtitle">
                    Create, update and manage all system users from one admin panel.
                  </div>
                </div>

                <v-btn color="primary" class="desktop-only-btn" prepend-icon="mdi-plus" @click="openCreateDialog">
                  Create user
                </v-btn>
              </div>

              <div class="overview-stats-grid">
                <article v-for="item in overviewStats" :key="item.label" class="overview-stat-card">
                  <div class="summary-label">{{ item.label }}</div>
                  <div class="summary-value">{{ item.value }}</div>
                </article>
              </div>

              <div class="toolbar-row">
                <div class="toolbar-label">User overview</div>

                <div class="toolbar-actions role-filter-group">
                  <v-btn
                    v-for="option in roleFilters"
                    :key="option.value"
                    variant="outlined"
                    class="toolbar-btn role-filter-btn"
                    :class="{ 'role-filter-btn-active': selectedRole === option.value }"
                    @click="selectedRole = option.value"
                  >
                    {{ option.label }}
                  </v-btn>
                </div>
              </div>

              <div v-if="errorMessage" class="state-wrap">
                <v-alert type="error" variant="tonal" border="start">
                  {{ errorMessage }}
                </v-alert>
              </div>

              <div v-else-if="loading" class="state-wrap loading-state">
                <v-progress-circular indeterminate color="primary" size="28" />
                <span>Loading users...</span>
              </div>

              <div v-else-if="!filteredUsers.length" class="state-wrap empty-state">
                No users found for the current search or role filter.
              </div>

              <div v-else class="users-grid">
                <article
                  v-for="item in filteredUsers"
                  :key="item.id"
                  class="user-card"
                >
                  <div class="user-card-top">
                    <div class="user-card-main">
                      <v-avatar size="56" class="user-avatar">
                        <img :src="avatarFor(item.email, item.full_name)" :alt="item.full_name">
                      </v-avatar>

                      <div class="user-copy">
                        <div class="user-name">{{ item.full_name }}</div>
                        <div class="user-email">{{ item.email }}</div>
                      </div>
                    </div>

                    <v-chip size="small" class="role-chip" :class="roleChipClass(item.role)">
                      {{ formatRole(item.role) }}
                    </v-chip>
                  </div>

                  <div class="user-card-body">
                    <div class="user-meta-list">
                      <div class="meta-row">
                        <span class="meta-label">Role</span>
                        <span class="meta-value">{{ formatRole(item.role) }}</span>
                      </div>

                      <div class="meta-row">
                        <span class="meta-label">Email</span>
                        <span class="meta-value">{{ item.email || 'Not set' }}</span>
                      </div>

                      <div v-if="item.role !== 'admin'" class="meta-row">
                        <span class="meta-label">Phone</span>
                        <span class="meta-value">{{ item.phone || 'Not set' }}</span>
                      </div>

                      <div v-if="item.role !== 'admin'" class="meta-row">
                        <span class="meta-label">Birth date</span>
                        <span class="meta-value">{{ item.birth_date || 'Not set' }}</span>
                      </div>

                      <div v-if="item.role === 'coach'" class="meta-row">
                        <span class="meta-label">Specialization</span>
                        <span class="meta-value">{{ item.specialization || 'Not set' }}</span>
                      </div>

                      <div v-if="item.role !== 'admin'" class="meta-row">
                        <span class="meta-label">Personal code</span>
                        <span class="meta-value">{{ item.personal_code || 'Not set' }}</span>
                      </div>

                      <div v-if="item.role !== 'admin' && item.role !== 'child'" class="meta-row">
                        <span class="meta-label">Balance</span>
                        <span class="meta-value">
                          {{ item.role === 'parent' ? formatCurrency(item.account_balance) : 'Not set' }}
                        </span>
                      </div>

                      <div v-if="item.role === 'parent'" class="meta-row meta-row-span linked-relatives-row">
                        <span class="meta-label">Linked children</span>
                        <div v-if="item.children?.length" class="children-chips">
                          <v-chip
                            v-for="child in item.children"
                            :key="child.id"
                            size="small"
                            variant="tonal"
                            class="child-chip"
                          >
                            {{ child.name }}
                          </v-chip>
                        </div>
                        <span v-else class="meta-value">Not set</span>
                      </div>

                      <div v-if="item.role === 'child'" class="meta-row meta-row-span linked-relatives-row">
                        <span class="meta-label">Linked parents</span>
                        <div v-if="item.parents?.length" class="children-chips">
                          <v-chip
                            v-for="parent in item.parents"
                            :key="parent.id"
                            size="small"
                            variant="tonal"
                            class="child-chip"
                          >
                            {{ parent.name }}
                          </v-chip>
                        </div>
                        <span v-else class="meta-value">Not set</span>
                      </div>
                    </div>
                  </div>

                  <div class="user-card-actions">
                    <v-btn
                      color="primary"
                      class="action-btn action-btn-edit"
                      prepend-icon="mdi-pencil-outline"
                      @click="openEditDialog(item)"
                    >
                      Edit
                    </v-btn>
                    <v-btn
                      variant="outlined"
                      color="error"
                      class="action-btn action-btn-delete"
                      :disabled="item.id === currentUserId"
                      @click="deleteUser(item)"
                    >
                      {{ item.id === currentUserId ? 'Current account' : 'Delete' }}
                    </v-btn>
                  </div>
                </article>
              </div>
            </div>
          </section>
        </div>

        <v-dialog v-model="userDialog" max-width="720">
          <v-card class="dialog-card create-dialog-card">
            <div class="create-dialog-header">
              <div>
                <div class="create-dialog-title">{{ editingUserId ? 'Edit User' : 'Create User' }}</div>
                <div class="create-dialog-subtitle">
                  Manage account details and role-specific profile fields without leaving the admin panel.
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
                <v-text-field v-model="form.name" label="Name" variant="outlined" class="create-field" />
                <v-text-field v-model="form.surname" label="Surname" variant="outlined" class="create-field" />
                <v-text-field v-model="form.email" label="Email" variant="outlined" class="create-field" />
                <v-text-field
                  v-model="form.password"
                  :label="editingUserId ? 'Password (leave blank to keep current)' : 'Password'"
                  :type="showPassword ? 'text' : 'password'"
                  :append-inner-icon="showPassword ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
                  variant="outlined"
                  class="create-field"
                  @click:append-inner="showPassword = !showPassword"
                />

                <v-select
                  v-model="form.role"
                  :items="roleOptions"
                  item-title="label"
                  item-value="value"
                  label="Role"
                  variant="outlined"
                  class="create-field create-field-full role-select-field"
                  :menu-props="roleMenuProps"
                />

                <template v-if="form.role === 'coach'">
                  <v-text-field v-model="form.phone" label="Phone" variant="outlined" class="create-field" />
                  <v-text-field v-model="form.birth_date" label="Birth date" type="date" variant="outlined" class="create-field" />
                  <v-text-field v-model="form.specialization" label="Specialization" variant="outlined" class="create-field" />
                </template>

                <template v-if="form.role === 'parent'">
                  <v-text-field v-model="form.phone" label="Phone" variant="outlined" class="create-field" />
                  <v-text-field v-model="form.birth_date" label="Birth date" type="date" variant="outlined" class="create-field" />
                  <v-text-field
                    v-model="form.account_balance"
                    label="Account balance"
                    type="number"
                    min="0"
                    variant="outlined"
                    class="create-field"
                  />
                  <v-text-field
                    v-model="form.child_identifier"
                    label="Child email or personal code"
                    variant="outlined"
                    class="create-field"
                  />
                </template>

                <template v-if="form.role === 'child'">
                  <v-text-field v-model="form.birth_date" label="Birth date" type="date" variant="outlined" class="create-field" />
                  <v-text-field v-model="form.personal_code" label="Personal code" variant="outlined" class="create-field" />
                </template>
              </div>
            </v-card-text>

            <v-card-actions class="create-dialog-actions">
              <v-spacer></v-spacer>
              <v-btn color="primary" class="apply-filter-btn" :loading="saving" @click="saveUser">
                {{ editingUserId ? 'Save changes' : 'Create user' }}
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
import { usersApi } from '../services/api'
import { logout, useAuth } from '../services/auth'
import { createAvatarDataUri } from '../utils/avatar'

const router = useRouter()
const search = ref('')
const selectedRole = ref('all')
const darkMode = ref(false)
const userDialog = ref(false)
const notificationsDialog = ref(false)
const showPassword = ref(false)
const mobileMenuOpen = ref(false)
const isCompactNav = ref(false)
const loading = ref(false)
const saving = ref(false)
const errorMessage = ref('')
const formError = ref('')
const editingUserId = ref(null)
const darkModeStorageKey = 'app-dark-mode'

const navItems = [
  { label: 'Admin Panel', icon: 'mdi-shield-crown-outline', to: '/admin' },
  { label: 'Users', icon: 'mdi-account-multiple-outline', to: '/users' },
  { label: 'Groups', icon: 'mdi-account-group-outline', to: '/manage-groups' },
  { label: 'Sessions', icon: 'mdi-calendar-clock-outline', to: '/manage-sessions' }
]

const roleFilters = [
  { label: 'All', value: 'all' },
  { label: 'Admins', value: 'admin' },
  { label: 'Coaches', value: 'coach' },
  { label: 'Parents', value: 'parent' },
  { label: 'Children', value: 'child' }
]

const roleOptions = [
  { label: 'Admin', value: 'admin' },
  { label: 'Coach', value: 'coach' },
  { label: 'Parent', value: 'parent' },
  { label: 'Child', value: 'child' }
]

const roleMenuProps = computed(() => ({
  contentClass: darkMode.value ? 'users-select-menu users-select-menu-dark' : 'users-select-menu',
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

const users = ref([])
const form = ref(getDefaultForm())

const currentUserId = computed(() => user.value?.id ?? null)
const profileName = computed(() => {
  if (!user.value) return 'Admin User'
  return `${user.value.name} ${user.value.surname}`.trim()
})
const profileEmail = computed(() => user.value?.email ?? 'admin@sportsystem.app')
const profileSeed = computed(() => user.value?.email ?? profileName.value)

const filteredUsers = computed(() => {
  const query = search.value.trim().toLowerCase()

  return users.value.filter((item) => {
    if (selectedRole.value !== 'all' && item.role !== selectedRole.value) {
      return false
    }

    if (!query) return true

    return [
      item.full_name,
      item.email,
      item.role,
      item.phone,
      item.specialization,
      item.personal_code
    ]
      .filter(Boolean)
      .some((value) => value.toLowerCase().includes(query))
  })
})

const overviewStats = computed(() => {
  const totals = {
    total: users.value.length,
    admin: users.value.filter((item) => item.role === 'admin').length,
    coach: users.value.filter((item) => item.role === 'coach').length,
    parent: users.value.filter((item) => item.role === 'parent').length,
    child: users.value.filter((item) => item.role === 'child').length
  }

  return [
    { label: 'Total users', value: totals.total },
    { label: 'Admins & coaches', value: totals.admin + totals.coach },
    { label: 'Parents', value: totals.parent },
    { label: 'Children', value: totals.child }
  ]
})

onMounted(() => {
  darkMode.value = localStorage.getItem(darkModeStorageKey) === 'true'
  updateViewportState()
  window.addEventListener('resize', updateViewportState)
  loadUsers()
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

watch(() => form.value.role, () => {
  formError.value = ''
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', updateViewportState)
})

function getDefaultForm() {
  return {
    name: '',
    surname: '',
    email: '',
    password: '',
    role: 'child',
    phone: '',
    birth_date: '',
    specialization: '',
    personal_code: '',
    child_identifier: '',
    account_balance: 0
  }
}

function updateViewportState() {
  isCompactNav.value = window.innerWidth <= 1024
}

function formatRole(role) {
  return role.charAt(0).toUpperCase() + role.slice(1)
}

function formatCurrency(value) {
  return `€${Number(value ?? 0).toFixed(0)}`
}

function roleChipClass(role) {
  return `role-chip-${role}`
}

function closeDialog() {
  userDialog.value = false
  editingUserId.value = null
  showPassword.value = false
  formError.value = ''
  form.value = getDefaultForm()
}

function openCreateDialog() {
  editingUserId.value = null
  formError.value = ''
  showPassword.value = false
  form.value = getDefaultForm()
  userDialog.value = true
}

function openEditDialog(item) {
  editingUserId.value = item.id
  formError.value = ''
  showPassword.value = false
  form.value = {
    name: item.name ?? '',
    surname: item.surname ?? '',
    email: item.email ?? '',
    password: '',
    role: item.role ?? 'child',
    phone: item.phone ?? '',
    birth_date: item.birth_date ?? '',
    specialization: item.specialization ?? '',
    personal_code: item.personal_code ?? '',
    child_identifier: item.role === 'parent' ? (item.children?.[0]?.email ?? '') : '',
    account_balance: item.account_balance ?? 0
  }
  userDialog.value = true
}

function buildPayload() {
  const payload = {
    name: form.value.name.trim(),
    surname: form.value.surname.trim(),
    email: form.value.email.trim(),
    role: form.value.role,
    phone: form.value.phone?.trim() || null,
    birth_date: form.value.birth_date || null,
    specialization: form.value.specialization?.trim() || null,
    personal_code: form.value.personal_code?.trim() || null,
    child_identifier: form.value.child_identifier?.trim() || null,
    account_balance: form.value.account_balance === '' || form.value.account_balance == null
      ? 0
      : Number(form.value.account_balance)
  }

  if (form.value.password.trim()) {
    payload.password = form.value.password.trim()
  }

  if (payload.role !== 'parent') {
    payload.account_balance = null
    payload.child_identifier = null
  }

  if (payload.role !== 'coach') {
    payload.specialization = null
  }

  if (payload.role !== 'coach' && payload.role !== 'parent') {
    payload.phone = null
  }

  if (payload.role !== 'child') {
    payload.personal_code = null
  }

  if (!['child', 'parent', 'coach'].includes(payload.role)) {
    payload.birth_date = null
  }

  return payload
}

function extractErrorMessage(error, fallback) {
  const validationErrors = error?.response?.data?.errors

  if (validationErrors && typeof validationErrors === 'object') {
    const firstError = Object.values(validationErrors).flat()[0]
    if (firstError) return firstError
  }

  return error?.response?.data?.message || fallback
}

async function loadUsers() {
  loading.value = true
  errorMessage.value = ''

  try {
    users.value = await usersApi.list()
  } catch (error) {
    errorMessage.value = extractErrorMessage(error, 'Failed to load users.')
  } finally {
    loading.value = false
  }
}

async function saveUser() {
  saving.value = true
  formError.value = ''

  try {
    const payload = buildPayload()

    if (!editingUserId.value && !payload.password) {
      formError.value = 'Password is required when creating a user.'
      return
    }

    if (editingUserId.value) {
      await usersApi.update(editingUserId.value, payload)
    } else {
      await usersApi.create(payload)
    }

    await loadUsers()
    closeDialog()
  } catch (error) {
    formError.value = extractErrorMessage(error, 'Failed to save user.')
  } finally {
    saving.value = false
  }
}

async function deleteUser(item) {
  if (item.id === currentUserId.value) return

  const confirmed = window.confirm(`Delete ${item.full_name}? This action cannot be undone.`)
  if (!confirmed) return

  try {
    await usersApi.remove(item.id)
    await loadUsers()
  } catch (error) {
    errorMessage.value = extractErrorMessage(error, 'Failed to delete user.')
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
.users-page {
  padding: 24px;
}

.users-shell {
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

.users-shell-dark {
  border-color: rgba(91, 109, 145, 0.4);
  background: linear-gradient(135deg, rgba(17, 24, 39, 0.96), rgba(28, 36, 54, 0.94));
  box-shadow: 0 28px 80px rgba(4, 10, 24, 0.45);
}

.users-panel {
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

.users-shell-dark .mobile-drawer-profile {
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

.users-shell-dark .sidebar-card {
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

.users-shell-dark .brand-name {
  color: #f3f7ff;
}

.brand-caption {
  margin-top: 2px;
  font-size: 0.82rem;
  color: #7b8798;
}

.users-shell-dark .brand-caption {
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

.users-shell-dark .nav-item {
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

.users-shell-dark .mobile-header-card {
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

.users-shell-dark .mobile-menu-btn {
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

.users-shell-dark .mobile-utility-card {
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

.users-shell-dark .topbar-card {
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

.users-shell-dark .search-shell {
  background: rgba(13, 20, 34, 0.88);
  border-color: rgba(63, 80, 114, 0.58);
}

.search-shell-icon {
  color: #111827;
}

.users-shell-dark .search-shell-icon {
  color: #edf4ff;
}

.search-field {
  flex: 1;
}

.search-field :deep(input) {
  color: #111827;
  opacity: 1;
}

.users-shell-dark .search-field :deep(input) {
  color: #edf4ff;
}

.search-field :deep(input::placeholder) {
  color: #111827;
  opacity: 1;
}

.users-shell-dark .search-field :deep(input::placeholder) {
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

.users-shell-dark .top-icon-btn {
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

.users-shell-dark .logout-btn {
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

.users-shell-dark .profile-pill {
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

.users-shell-dark .profile-name {
  color: #f3f7ff;
}

.profile-email {
  margin-top: 2px;
  font-size: 0.86rem;
  color: #78859a;
}

.users-shell-dark .profile-email {
  color: #94a6c4;
}

.users-card {
  padding: 26px;
  border-radius: 30px;
  background: rgba(249, 252, 255, 0.72);
  border: 1px solid rgba(255, 255, 255, 0.72);
}

.users-shell-dark .users-card {
  background: rgba(18, 27, 43, 0.86);
  border-color: rgba(74, 92, 126, 0.42);
}

.users-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 18px;
  margin-bottom: 24px;
}

.users-title {
  margin: 0;
  font-size: 2.1rem;
  line-height: 1.1;
  color: #172033;
}

.users-shell-dark .users-title {
  color: #f3f7ff;
}

.users-subtitle,
.summary-label,
.toolbar-label,
.meta-label {
  color: #7b8798;
}

.users-shell-dark .users-subtitle,
.users-shell-dark .summary-label,
.users-shell-dark .toolbar-label,
.users-shell-dark .meta-label {
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

.users-shell-dark .overview-stat-card {
  background: rgba(13, 20, 34, 0.88);
  border-color: rgba(63, 80, 114, 0.58);
}

.summary-value {
  margin-top: 8px;
  font-size: 1.9rem;
  font-weight: 700;
  color: #172033;
}

.users-shell-dark .summary-value {
  color: #f3f7ff;
}

.toolbar-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 22px;
}

.toolbar-actions {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.toolbar-btn {
  min-height: 44px;
  border-radius: 16px;
  text-transform: none;
  letter-spacing: 0;
  color: #5f6f88;
  background: rgba(255, 255, 255, 0.82);
  border-color: rgba(223, 232, 246, 0.94);
}

.users-shell-dark .toolbar-btn {
  color: #b7c7df;
  background: rgba(17, 25, 40, 0.82);
  border-color: rgba(58, 75, 108, 0.62);
}

.role-filter-btn-active {
  color: white !important;
  border-color: transparent !important;
  background: linear-gradient(180deg, #1677ff 0%, #0f5fe3 100%);
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

.users-shell-dark .state-wrap {
  background: rgba(13, 20, 34, 0.72);
  border-color: rgba(78, 97, 132, 0.58);
  color: #aac0df;
}

.loading-state {
  flex-direction: column;
  gap: 14px;
}

.users-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 18px;
}

.user-card {
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

.users-shell-dark .user-card {
  background: linear-gradient(180deg, rgba(13, 20, 34, 0.94), rgba(18, 27, 43, 0.92));
  border-color: rgba(63, 80, 114, 0.58);
  box-shadow: 0 18px 38px rgba(4, 10, 24, 0.3);
}

.user-card-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 14px;
}

.user-card-main {
  display: flex;
  align-items: center;
  gap: 14px;
  min-width: 0;
}

.user-avatar {
  flex-shrink: 0;
}

.user-copy {
  min-width: 0;
}

.user-name {
  font-size: 1.06rem;
  font-weight: 700;
  color: #172033;
}

.users-shell-dark .user-name,
.users-shell-dark .meta-value {
  color: #f3f7ff;
}

.user-email {
  margin-top: 4px;
  color: #7b8798;
  word-break: break-word;
}

.users-shell-dark .user-email {
  color: #94a6c4;
}

.role-chip {
  border-radius: 999px;
  padding-inline: 10px;
  font-weight: 700;
}

.role-chip-admin {
  background: rgba(241, 226, 185, 0.38);
  color: #946200;
}

.role-chip-coach {
  background: rgba(194, 225, 255, 0.5);
  color: #0f5fe3;
}

.role-chip-parent {
  background: rgba(203, 241, 223, 0.52);
  color: #22764d;
}

.role-chip-child {
  background: rgba(240, 221, 252, 0.55);
  color: #7a36b0;
}

.user-card-body {
  display: flex;
  flex-direction: column;
  gap: 14px;
  flex: 1;
}

.user-meta-list {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 12px;
  padding: 14px;
  border-radius: 20px;
  background: rgba(241, 246, 255, 0.8);
  border: 1px solid rgba(223, 232, 246, 0.95);
}

.users-shell-dark .user-meta-list {
  background: rgba(17, 25, 40, 0.88);
  border-color: rgba(58, 75, 108, 0.62);
}

.meta-row {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 6px;
  min-width: 0;
}

.meta-row-span {
  grid-column: 1 / -1;
}

.linked-relatives-row {
  padding-top: 14px;
  border-top: 1px solid rgba(211, 221, 236, 0.95);
}

.users-shell-dark .linked-relatives-row {
  border-top-color: rgba(61, 78, 111, 0.72);
}

.meta-label {
  font-size: 0.92rem;
}

.meta-value {
  color: #172033;
  text-align: left;
  font-weight: 600;
  word-break: break-word;
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

.users-shell-dark .child-chip {
  color: #dce9ff;
  background: rgba(31, 72, 133, 0.42);
  border-color: rgba(83, 122, 188, 0.62);
}

.user-card-actions {
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

.users-shell-dark .action-btn-delete {
  background: rgba(17, 25, 40, 0.56);
}

.action-btn-delete:disabled {
  opacity: 1;
  color: #9aa8bf !important;
  border-color: rgba(180, 193, 216, 0.7) !important;
}

.users-shell-dark .action-btn-delete:disabled {
  color: #7d8ca5 !important;
  border-color: rgba(82, 101, 136, 0.72) !important;
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

.users-shell-dark :deep(.v-overlay__content .create-dialog-card) {
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

.users-shell-dark .create-dialog-header {
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.03), rgba(255, 255, 255, 0));
  border-bottom-color: rgba(64, 82, 116, 0.68);
}

.create-dialog-title {
  font-size: 1.55rem;
  font-weight: 700;
  color: #172033;
}

.users-shell-dark .create-dialog-title {
  color: #f3f7ff;
}

.create-dialog-subtitle {
  margin-top: 6px;
  color: #74849a;
  max-width: 430px;
}

.users-shell-dark .create-dialog-subtitle {
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

.create-field-full {
  grid-column: 1 / -1;
}

.create-dialog-actions {
  padding: 18px 24px 24px;
  border-top: 1px solid rgba(219, 230, 246, 0.72);
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0.28));
}

.users-shell-dark .create-dialog-actions {
  border-top-color: rgba(64, 82, 116, 0.62);
  background: linear-gradient(180deg, rgba(255, 255, 255, 0), rgba(255, 255, 255, 0.03));
}

.reset-filter-btn,
.apply-filter-btn {
  min-height: 46px;
  border-radius: 16px;
  text-transform: none;
  letter-spacing: 0;
  padding: 0 18px;
}

.reset-filter-btn {
  color: #6e7f97;
  background: transparent !important;
}

.users-shell-dark .reset-filter-btn {
  color: #9eb1cf;
}

.desktop-only-btn {
  min-height: 52px;
  border-radius: 18px;
  text-transform: none;
  letter-spacing: 0;
  font-weight: 600;
}

.mobile-create-btn {
  min-height: 48px;
  border-radius: 16px;
  text-transform: none;
  letter-spacing: 0;
}

.create-dialog-card :deep(.v-btn--icon) {
  color: #172033;
  background: rgba(255, 255, 255, 0.66);
  border: 1px solid rgba(219, 230, 246, 0.92);
}

.users-shell-dark .create-dialog-card :deep(.v-btn--icon) {
  color: #eef4ff;
  background: rgba(18, 27, 43, 0.72);
  border-color: rgba(64, 82, 116, 0.62);
}

.create-dialog-card :deep(.create-field .v-field) {
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.7);
  box-shadow: inset 0 0 0 1px rgba(223, 232, 246, 0.95);
}

.create-dialog-card :deep(.create-field .v-field:hover) {
  background: rgba(255, 255, 255, 0.82);
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

.create-dialog-card :deep(.role-select-field .v-field) {
  background:
    linear-gradient(180deg, rgba(255, 255, 255, 0.88), rgba(246, 250, 255, 0.82));
}

.create-dialog-card :deep(.role-select-field .v-field__append-inner) {
  color: #5f7aa6;
}

.create-dialog-card :deep(.role-select-field .v-select__selection) {
  color: #172033;
  font-weight: 600;
}

.create-dialog-card :deep(.role-select-field .v-field--focused) {
  background:
    linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(248, 251, 255, 0.92));
}

.create-dialog-card :deep(.create-field input),
.create-dialog-card :deep(.create-field textarea),
.create-dialog-card :deep(.create-field .v-select__selection-text) {
  color: #172033;
}

.create-dialog-card :deep(.create-field .v-label) {
  color: #6f7f96;
}

.users-shell-dark .create-dialog-card :deep(.create-field .v-field) {
  background: rgba(17, 25, 40, 0.86);
  box-shadow: inset 0 0 0 1px rgba(64, 82, 116, 0.72);
}

.users-shell-dark .create-dialog-card :deep(.role-select-field .v-field) {
  background:
    linear-gradient(180deg, rgba(17, 25, 40, 0.94), rgba(20, 29, 46, 0.9));
}

.users-shell-dark .create-dialog-card :deep(.create-field .v-field:hover) {
  background: rgba(20, 29, 46, 0.94);
}

.users-shell-dark .create-dialog-card :deep(.create-field .v-field--focused) {
  background: rgba(22, 31, 48, 0.98);
  box-shadow:
    inset 0 0 0 1px rgba(97, 155, 255, 0.74),
    0 0 0 4px rgba(22, 119, 255, 0.12);
}

.users-shell-dark .create-dialog-card :deep(.create-field input),
.users-shell-dark .create-dialog-card :deep(.create-field textarea),
.users-shell-dark .create-dialog-card :deep(.create-field .v-select__selection-text) {
  color: #eef4ff;
}

.users-shell-dark .create-dialog-card :deep(.role-select-field .v-field__append-inner) {
  color: #a7bbdc;
}

.users-shell-dark .create-dialog-card :deep(.create-field .v-label),
.users-shell-dark .create-dialog-card :deep(.create-field .v-field__append-inner) {
  color: #94a6c4;
}

:deep(.users-select-menu) {
  border-radius: 20px;
  overflow: hidden;
  border: 1px solid rgba(223, 232, 246, 0.94);
  background: linear-gradient(180deg, rgba(250, 252, 255, 0.98), rgba(239, 246, 255, 0.98));
  box-shadow: 0 22px 48px rgba(79, 106, 154, 0.22);
}

:deep(.users-select-menu .v-list) {
  padding: 8px;
  background: transparent;
}

:deep(.users-select-menu .v-list-item) {
  min-height: 46px;
  border-radius: 14px;
  color: #172033;
}

:deep(.users-select-menu .v-list-item-title) {
  color: inherit;
  font-weight: 600;
}

:deep(.users-select-menu .v-list-item:hover) {
  background: rgba(207, 226, 252, 0.56);
}

:deep(.users-select-menu .v-list-item--active) {
  background: linear-gradient(180deg, #1677ff 0%, #0f5fe3 100%);
  color: white;
}

:deep(.users-select-menu .v-list-item--active .v-list-item__overlay) {
  opacity: 0 !important;
}

:deep(.users-select-menu-dark) {
  border-color: rgba(64, 82, 116, 0.62);
  background: linear-gradient(180deg, rgba(16, 23, 37, 0.99), rgba(22, 31, 48, 0.98));
  box-shadow: 0 24px 56px rgba(3, 8, 20, 0.55);
}

:deep(.users-select-menu-dark .v-list) {
  background: transparent;
}

:deep(.users-select-menu-dark .v-list-item) {
  color: #eef4ff;
}

:deep(.users-select-menu-dark .v-list-item-title) {
  color: inherit;
}

:deep(.users-select-menu-dark .v-list-item:hover) {
  background: rgba(36, 52, 79, 0.78);
}

:deep(.users-select-menu-dark .v-list-item--active) {
  background: linear-gradient(180deg, #1677ff 0%, #0f5fe3 100%);
  color: white;
}

:deep(.users-select-menu-dark .v-list-item--active .v-list-item__overlay) {
  opacity: 0 !important;
}

@media (max-width: 1180px) {
  .users-grid,
  .overview-stats-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 1024px) {
  .users-page {
    padding: 16px;
  }

  .users-shell {
    overflow: hidden;
  }

  .users-panel {
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
  .users-card {
    padding: 20px;
  }

  .users-header,
  .toolbar-row,
  .mobile-profile-row {
    flex-direction: column;
    align-items: stretch;
  }

  .overview-stats-grid,
  .users-grid,
  .create-fields-grid {
    grid-template-columns: 1fr;
  }

  .desktop-only-btn {
    display: none;
  }

  .user-card-top,
  .user-card-actions {
    flex-direction: column;
    align-items: stretch;
  }

  .user-meta-list {
    grid-template-columns: 1fr;
  }

  .meta-row-span {
    grid-column: auto;
  }

  .meta-row {
    align-items: flex-start;
  }

  .role-chip {
    align-self: flex-start;
  }
}

@media (max-width: 560px) {
  .users-page {
    padding: 10px;
  }

  .users-panel {
    padding: 10px;
  }

  .users-card {
    padding: 16px;
    border-radius: 24px;
  }

  .topbar-card,
  .mobile-header-card,
  .mobile-utility-card {
    border-radius: 20px;
  }

  .users-title {
    font-size: 1.75rem;
  }

  .summary-value {
    font-size: 1.6rem;
  }
}
</style>
