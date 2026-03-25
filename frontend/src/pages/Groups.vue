<template>
  <v-app>
    <v-main class="groups-page">
      <div class="groups-shell" :class="{ 'groups-shell-dark': darkMode }">
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
                <div class="brand-caption">Coach workspace</div>
              </div>
            </div>

            <nav class="mobile-nav-list">
              <v-btn
                v-for="item in navItems"
                :key="`mobile-${item.label}`"
                :to="item.to || undefined"
                variant="text"
                class="nav-item"
                :class="{ 'nav-item-active': item.to === '/groups' }"
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
                <img :src="avatarFor(profileSeed, profileName)" alt="Coach profile">
              </v-avatar>
              <div>
                <div class="profile-name">{{ profileName }}</div>
                <div class="profile-email">{{ profileEmail }}</div>
              </div>
            </div>
          </div>
        </v-navigation-drawer>

        <div class="groups-panel">
          <aside class="sidebar-card">
            <div class="brand-block">
              <div class="brand-icon">
                <v-icon color="white">mdi-school-outline</v-icon>
              </div>
              <div class="brand-text">
                <div class="brand-name">SportSystem</div>
                <div class="brand-caption">Coach workspace</div>
              </div>
            </div>

            <nav class="nav-list">
              <v-btn
                v-for="item in navItems"
                :key="item.label"
                :to="item.to || undefined"
                variant="text"
                class="nav-item"
                :class="{ 'nav-item-active': item.to === '/groups' }"
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
                    <img :src="avatarFor(profileSeed, profileName)" alt="Coach profile">
                  </v-avatar>
                  <div>
                    <div class="profile-name">{{ profileName }}</div>
                    <div class="profile-email">{{ profileEmail }}</div>
                  </div>
                </div>
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

                <div class="profile-pill">
                  <v-avatar size="48">
                    <img :src="avatarFor(profileSeed, profileName)" alt="Coach profile">
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
                  <h1 class="groups-title">My Groups</h1>
                  <div class="groups-subtitle">Groups you manage and their key training details</div>
                </div>
              </div>

              <div class="toolbar-row">
                <div class="toolbar-label">Groups overview</div>

                <div class="toolbar-actions">
                  <v-btn variant="outlined" class="toolbar-btn" @click="filterDialog = true">
                    <v-icon start size="18">mdi-tune-variant</v-icon>
                    Filters
                  </v-btn>
                </div>
              </div>

              <div class="groups-grid">
                <article
                  v-for="group in filteredGroups"
                  :key="group.id"
                  class="group-card"
                >
                  <div class="group-card-top">
                    <v-avatar size="54" class="group-avatar">
                      <img :src="group.avatar" :alt="group.trainer">
                    </v-avatar>

                    <div>
                      <div class="section">{{ group.section }}</div>
                      <div class="trainer">
                        {{ isChild ? `Group #${group.group_number}` : `Group #${group.group_number} • ${group.trainer}` }}
                      </div>
                    </div>
                  </div>

                  <div class="group-info-grid">
                    <div class="info-item">
                      <div class="label">Students</div>
                      <div class="value">{{ group.students }}</div>
                    </div>

                    <div class="info-item">
                      <div class="label">Days</div>
                      <div class="value">{{ group.days }}</div>
                    </div>

                    <div class="info-item">
                      <div class="label">Time</div>
                      <div class="value">{{ group.time }}</div>
                    </div>

                    <div class="info-item">
                      <div class="label">{{ isChild ? 'Coach' : 'Price' }}</div>
                      <div class="value">
                        {{ isChild ? group.trainer : `${group.price} € / month` }}
                      </div>
                    </div>
                  </div>

                  <div class="attendance-block">
                    <div class="label">Attendance</div>
                    <div class="attendance-row">
                      <div class="attendance-bar">
                        <div class="attendance-fill" :style="{ width: `${group.attendance}%` }"></div>
                      </div>
                      <div class="attendance-value">{{ group.attendance }}%</div>
                    </div>
                  </div>
                </article>
              </div>
            </div>
          </section>
        </div>

        <v-dialog v-model="filterDialog" max-width="520">
          <v-card class="dialog-card filter-dialog-card">
            <div class="filter-dialog-header">
              <div>
                <div class="filter-dialog-title">Filters</div>
                <div class="filter-dialog-subtitle">
                  Sort groups by name without leaving the groups view.
                </div>
              </div>

              <v-btn icon variant="text" @click="filterDialog = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </div>

            <div class="filter-options">
              <button
                type="button"
                class="filter-option"
                :class="{ 'filter-option-active': selectedSort === 'az' }"
                @click="selectedSort = 'az'"
              >
                <span class="filter-option-icon">
                  <v-icon size="18">mdi-sort-alphabetical-ascending</v-icon>
                </span>
                <span>
                  <span class="filter-option-title">A to Z</span>
                  <span class="filter-option-text">Order groups alphabetically from A to Z.</span>
                </span>
              </button>

              <button
                type="button"
                class="filter-option"
                :class="{ 'filter-option-active': selectedSort === 'za' }"
                @click="selectedSort = 'za'"
              >
                <span class="filter-option-icon">
                  <v-icon size="18">mdi-sort-alphabetical-descending</v-icon>
                </span>
                <span>
                  <span class="filter-option-title">Z to A</span>
                  <span class="filter-option-text">Order groups alphabetically from Z to A.</span>
                </span>
              </button>
            </div>

            <div class="filter-dialog-actions">
              <v-btn variant="outlined" class="reset-filter-btn" @click="resetSort">
                Reset
              </v-btn>
              <v-btn color="primary" class="apply-filter-btn" @click="applySelectedSort">
                Apply filters
              </v-btn>
            </div>
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
import AppNotificationsDialog from '../components/AppNotificationsDialog.vue'
import { useNotifications } from '../composables/useNotifications'
import { groupsApi } from '../services/api'
import { useAuth } from '../services/auth'
import { createAvatarDataUri } from '../utils/avatar'

const search = ref('')
const darkMode = ref(false)
const filterDialog = ref(false)
const notificationsDialog = ref(false)
const selectedSort = ref('az')
const mobileMenuOpen = ref(false)
const isCompactNav = ref(false)
const darkModeStorageKey = 'app-dark-mode'
const loading = ref(false)

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
const profileName = computed(() => {
  if (!user.value) return 'SportSystem User'
  return `${user.value.name} ${user.value.surname}`.trim()
})
const isChild = computed(() => user.value?.role === 'child')
const navItems = computed(() => [
  { label: 'Home', icon: 'mdi-home-outline', to: '/home' },
  { label: 'Schedule', icon: 'mdi-calendar-month-outline', to: '/schedule' },
  { label: 'Groups', icon: 'mdi-account-group-outline', to: '/groups' },
  { label: 'Attendance', icon: 'mdi-check-circle-outline', to: '/attendance' },
  ...(user.value?.role === 'child'
    ? []
    : [{ label: 'Payments', icon: 'mdi-credit-card-outline', to: '/payments' }])
])
const profileEmail = computed(() => user.value?.email ?? 'user@sportsystem.app')
const profileSeed = computed(() => user.value?.email ?? profileName.value)

const filteredGroups = computed(() =>
  groups.value.filter((group) =>
    [group.section, String(group.group_number ?? ''), group.trainer, group.days].some((value) =>
      value.toLowerCase().includes(search.value.toLowerCase())
    )
  )
)

onMounted(() => {
  darkMode.value = localStorage.getItem(darkModeStorageKey) === 'true'
  updateViewportState()
  window.addEventListener('resize', updateViewportState)
  loadGroups()
  loadNotifications()
})

watch(darkMode, (value) => {
  localStorage.setItem(darkModeStorageKey, String(value))
})

watch(isCompactNav, (value) => {
  if (!value) mobileMenuOpen.value = false
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', updateViewportState)
})

watch(notificationsDialog, (value) => {
  if (value) {
    loadNotifications(true)
  }
})

function sortAZ() {
  groups.value.sort((a, b) => a.section.localeCompare(b.section))
}

function sortZA() {
  groups.value.sort((a, b) => b.section.localeCompare(a.section))
}

async function loadGroups() {
  loading.value = true

  try {
    const response = await groupsApi.list()
    groups.value = response.map(withGroupAvatar)
    if (selectedSort.value === 'az') sortAZ()
    if (selectedSort.value === 'za') sortZA()
  } finally {
    loading.value = false
  }
}

function calculateAttendance(studentsCount) {
  if (studentsCount <= 0) return 0
  if (studentsCount <= 8) return 92
  if (studentsCount <= 12) return 85
  return 78
}

function applySelectedSort() {
  if (selectedSort.value === 'az') sortAZ()
  if (selectedSort.value === 'za') sortZA()

  filterDialog.value = false
}

function resetSort() {
  selectedSort.value = 'az'
  sortAZ()
  filterDialog.value = false
}

function updateViewportState() {
  isCompactNav.value = window.innerWidth <= 1024
}

function withGroupAvatar(group) {
  return {
    ...group,
    avatar: avatarFor(`group-${group.section}-${group.trainer}`, group.section)
  }
}

async function handleNotificationClick(item) {
  if (item?.unread) {
    await markNotificationRead(item.id)
  }
}
</script>

<style scoped>
.groups-page {
  padding: 24px;
}

.groups-shell {
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

.groups-shell-dark {
  border-color: rgba(91, 109, 145, 0.4);
  background: linear-gradient(135deg, rgba(17, 24, 39, 0.96), rgba(28, 36, 54, 0.94));
  box-shadow: 0 28px 80px rgba(4, 10, 24, 0.45);
}

.groups-panel {
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
  margin-top: auto;
  padding: 14px;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.78);
}

.groups-shell-dark .mobile-drawer-profile {
  background: rgba(18, 27, 43, 0.92);
  border: 1px solid rgba(74, 92, 126, 0.42);
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

.groups-shell-dark .sidebar-card {
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

.groups-shell-dark .brand-name {
  color: #f3f7ff;
}

.brand-caption {
  margin-top: 2px;
  font-size: 0.82rem;
  color: #7b8798;
}

.groups-shell-dark .brand-caption {
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

.groups-shell-dark .nav-item {
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

.groups-shell-dark .mobile-header-card {
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

.groups-shell-dark .mobile-menu-btn {
  color: #eef4ff;
  background: rgba(13, 20, 34, 0.88);
  border-color: rgba(63, 80, 114, 0.58);
}

.mobile-brand-inline {
  display: flex;
  align-items: center;
  gap: 12px;
  min-width: 0;
  flex: 1;
}

.mobile-brand-icon {
  width: 44px;
  height: 44px;
  border-radius: 14px;
}

.mobile-brand-copy {
  min-width: 0;
}

.mobile-brand-copy .brand-name,
.mobile-brand-copy .brand-caption {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.mobile-header-actions {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-shrink: 0;
}

.mobile-theme-btn {
  color: #111827;
}

.mobile-utility-card {
  flex-direction: column;
  gap: 0;
  padding: 14px 16px;
  border-radius: 22px;
  background: rgba(255, 255, 255, 0.62);
  border: 1px solid rgba(255, 255, 255, 0.62);
}

.groups-shell-dark .mobile-utility-card {
  background: rgba(22, 31, 48, 0.82);
  border-color: rgba(74, 92, 126, 0.42);
}

.mobile-profile-row {
  display: flex;
  align-items: center;
  gap: 10px;
}

.mobile-profile-pill {
  flex: 1;
  min-width: 0;
  min-height: 56px;
  padding: 10px 12px;
}

.profile-pill > div {
  min-width: 0;
}

.mobile-create-btn {
  min-height: 56px;
  padding: 0 18px;
  border-radius: 18px;
  text-transform: none;
  letter-spacing: 0;
  box-shadow: 0 18px 34px rgba(22, 119, 255, 0.22);
}

.topbar-card {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 18px;
  padding: 18px 20px;
  border-radius: 26px;
  background: rgba(255, 255, 255, 0.58);
  border: 1px solid rgba(255, 255, 255, 0.6);
}

.groups-shell-dark .topbar-card {
  background: rgba(22, 31, 48, 0.82);
  border-color: rgba(74, 92, 126, 0.42);
}

.search-wrap {
  flex: 1;
  min-width: 0;
  max-width: 420px;
}

.search-shell {
  display: flex;
  align-items: center;
  gap: 12px;
  min-height: 58px;
  padding: 0 18px;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.92);
  border: 1px solid rgba(223, 231, 243, 0.92);
}

.groups-shell-dark .search-shell {
  background: rgba(13, 20, 34, 0.88);
  border-color: rgba(63, 80, 114, 0.58);
}

.search-shell-icon {
  color: #6b7280;
  flex-shrink: 0;
}

.groups-shell-dark .search-shell-icon {
  color: #8ea3c7;
}

.search-field {
  flex: 1;
}

:deep(.search-field .v-input__control),
:deep(.search-field .v-field),
:deep(.search-field .v-field__field) {
  min-height: auto;
  background: transparent;
  box-shadow: none;
}

.search-field :deep(.v-field__input) {
  min-height: 58px;
  padding-top: 0;
  padding-bottom: 0;
  display: flex;
  align-items: center;
}

.search-field :deep(input) {
  color: #172033;
}

.search-field :deep(.v-label),
.search-field :deep(input::placeholder) {
  color: #111827;
  opacity: 1;
}

.groups-shell-dark .search-field :deep(input),
.groups-shell-dark .search-field :deep(.v-label),
.groups-shell-dark .search-field :deep(input::placeholder) {
  color: #e7eefb;
}

.topbar-tools {
  display: flex;
  align-items: center;
  gap: 14px;
  min-width: 0;
}

.top-icon-btn {
  width: 54px;
  height: 54px;
  border: 1px solid rgba(148, 163, 184, 0.18);
  background: rgba(255, 255, 255, 0.75);
}

.groups-shell-dark .top-icon-btn {
  color: #dce6f7;
  background: rgba(18, 27, 43, 0.92);
  border-color: rgba(74, 92, 126, 0.46);
}

.top-icon-btn-active {
  color: #1677ff;
  background: rgba(232, 242, 255, 0.96);
  border-color: rgba(22, 119, 255, 0.28);
}

.groups-shell-dark .top-icon-btn-active {
  color: #7fbcff;
  background: rgba(22, 43, 76, 0.96);
  border-color: rgba(82, 156, 255, 0.44);
}

.icon-badge-wrap {
  position: relative;
  color: #111827;
}

.icon-badge {
  position: absolute;
  top: 2px;
  right: 2px;
  display: grid;
  place-items: center;
  min-width: 21px;
  height: 21px;
  padding: 0 6px;
  border-radius: 999px;
  font-size: 0.72rem;
  font-weight: 700;
  color: white;
  background: #1677ff;
}

.profile-pill {
  display: flex;
  align-items: center;
  gap: 14px;
  min-width: 0;
  padding: 10px 16px;
  border-radius: 20px;
  background: rgba(255, 255, 255, 0.82);
}

.groups-shell-dark .profile-pill {
  background: rgba(18, 27, 43, 0.92);
  border: 1px solid rgba(74, 92, 126, 0.42);
}

.profile-name {
  font-size: 0.98rem;
  font-weight: 600;
  color: #172033;
}

.groups-shell-dark .profile-name {
  color: #f2f7ff;
}

.profile-email {
  font-size: 0.9rem;
  color: #7b8798;
  word-break: break-word;
}

.groups-shell-dark .profile-email {
  color: #93a5c3;
}

.groups-card {
  min-width: 0;
  padding: 26px;
  border-radius: 28px;
  background: rgba(249, 251, 255, 0.58);
  border: 1px solid rgba(255, 255, 255, 0.62);
}

.groups-shell-dark .groups-card {
  background: rgba(22, 31, 48, 0.72);
  border-color: rgba(74, 92, 126, 0.42);
}

.groups-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 18px;
  margin-bottom: 24px;
}

.groups-title {
  margin: 0;
  font-size: 2.3rem;
  line-height: 1.1;
  font-weight: 700;
  color: #121826;
}

.groups-shell-dark .groups-title {
  color: #f3f7ff;
}

.groups-subtitle {
  margin-top: 10px;
  font-size: 1rem;
  color: #66748a;
}

.groups-shell-dark .groups-subtitle {
  color: #8fa3c1;
}

.create-btn {
  min-height: 54px;
  padding: 0 24px;
  border-radius: 18px;
  text-transform: none;
  letter-spacing: 0;
  font-size: 1rem;
  box-shadow: 0 18px 34px rgba(22, 119, 255, 0.28);
}

.desktop-only-btn {
  display: inline-flex;
}

.toolbar-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 18px;
  margin-bottom: 28px;
}

.toolbar-label {
  font-size: 1rem;
  font-weight: 700;
  color: #172033;
}

.groups-shell-dark .toolbar-label {
  color: #eef4ff;
}

.toolbar-actions {
  display: flex;
  gap: 12px;
}

.toolbar-btn {
  min-height: 48px;
  padding: 0 18px;
  border-radius: 18px;
  text-transform: none;
  letter-spacing: 0;
  color: #162033;
  background: rgba(255, 255, 255, 0.8);
}

.groups-shell-dark .toolbar-btn {
  color: #7fbcff;
  background: rgba(18, 27, 43, 0.92);
  border-color: rgba(82, 156, 255, 0.32);
}

.groups-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 18px;
}

.group-card {
  padding: 20px;
  border-radius: 24px;
  background: rgba(255, 255, 255, 0.92);
  box-shadow: 0 12px 28px rgba(110, 136, 173, 0.08);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.groups-shell-dark .group-card {
  background: rgba(18, 27, 43, 0.9);
  box-shadow: 0 18px 38px rgba(4, 10, 24, 0.26);
}

.group-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 18px 36px rgba(110, 136, 173, 0.12);
}

.dialog-card {
  border-radius: 24px;
}

.groups-shell-dark :deep(.v-overlay__content .dialog-card) {
  background: linear-gradient(180deg, rgba(22, 31, 48, 0.98), rgba(16, 24, 38, 0.96));
  color: #eff5ff;
  border: 1px solid rgba(74, 92, 126, 0.42);
}

.create-dialog-card {
  padding: 26px;
  background: linear-gradient(180deg, rgba(247, 251, 255, 0.96), rgba(238, 245, 255, 0.92));
  border: 1px solid rgba(255, 255, 255, 0.72);
  box-shadow: 0 20px 45px rgba(76, 104, 148, 0.18);
}

.groups-shell-dark :deep(.v-overlay__content .create-dialog-card),
.groups-shell-dark :deep(.v-overlay__content .filter-dialog-card) {
  background: linear-gradient(180deg, rgba(22, 31, 48, 0.98), rgba(16, 24, 38, 0.96));
  box-shadow: 0 24px 48px rgba(4, 10, 24, 0.42);
}

.create-dialog-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 18px;
  margin-bottom: 20px;
}

.create-dialog-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #172033;
}

.groups-shell-dark .create-dialog-title,
.groups-shell-dark .filter-dialog-title {
  color: #f3f7ff;
}

.create-dialog-subtitle {
  margin-top: 8px;
  color: #64748b;
  line-height: 1.5;
}

.groups-shell-dark .create-dialog-subtitle,
.groups-shell-dark .filter-dialog-subtitle {
  color: #8fa3c1;
}

.create-dialog-content {
  padding: 0 !important;
}

.create-fields-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 14px;
}

.create-field-full {
  grid-column: 1 / -1;
}

.create-dialog-actions {
  padding: 22px 0 0 !important;
}

.create-field :deep(.v-field) {
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.74);
  backdrop-filter: blur(10px);
}

.groups-shell-dark .create-field :deep(.v-field) {
  background: rgba(12, 19, 32, 0.88);
}

.create-field :deep(.v-field__outline) {
  color: rgba(190, 202, 222, 0.95);
}

.groups-shell-dark .create-field :deep(.v-field__outline) {
  color: rgba(74, 92, 126, 0.62);
}

.create-field :deep(.v-label),
.create-field :deep(input) {
  color: #172033;
}

.groups-shell-dark .create-field :deep(.v-label),
.groups-shell-dark .create-field :deep(input) {
  color: #eef4ff;
}

.create-field-no-spin :deep(input[type='number']) {
  appearance: textfield;
  -moz-appearance: textfield;
}

.create-field-no-spin :deep(input[type='number']::-webkit-outer-spin-button),
.create-field-no-spin :deep(input[type='number']::-webkit-inner-spin-button) {
  margin: 0;
  -webkit-appearance: none;
}

.group-card-top {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-bottom: 18px;
}

.group-avatar {
  border: 2px solid rgba(255, 255, 255, 0.82);
  box-shadow: 0 10px 20px rgba(110, 136, 173, 0.18);
}

.trainer {
  margin-top: 4px;
  font-weight: 500;
  font-size: 0.9rem;
  color: #66748a;
}

.groups-shell-dark .trainer {
  color: #8ea3c7;
}

.section {
  font-size: 1.08rem;
  font-weight: 700;
  color: #172033;
}

.groups-shell-dark .section,
.groups-shell-dark .value,
.groups-shell-dark .attendance-value {
  color: #eef4ff;
}

.group-info-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px 16px;
}

.info-item {
  padding: 14px;
  border-radius: 18px;
  background: rgba(245, 249, 255, 0.88);
}

.groups-shell-dark .info-item {
  background: rgba(12, 19, 32, 0.88);
}

.label {
  font-size: 0.78rem;
  color: #7b8798;
}

.groups-shell-dark .label {
  color: #8fa3c1;
}

.value {
  margin-top: 6px;
  font-size: 0.96rem;
  font-weight: 600;
  color: #172033;
}

.attendance-block {
  margin-top: 18px;
}

.attendance-row {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: 8px;
}

.attendance-bar {
  flex: 1;
  height: 10px;
  border-radius: 999px;
  overflow: hidden;
  background: rgba(209, 218, 230, 0.7);
}

.groups-shell-dark .attendance-bar {
  background: rgba(63, 80, 114, 0.58);
}

.attendance-fill {
  height: 100%;
  border-radius: 999px;
  background: linear-gradient(180deg, #1677ff 0%, #0f5fe3 100%);
}

.attendance-value {
  min-width: 44px;
  font-weight: 700;
  color: #172033;
}

.dialog-card {
  border-radius: 24px;
}

.filter-dialog-card {
  padding: 26px;
  background: linear-gradient(180deg, rgba(247, 251, 255, 0.96), rgba(238, 245, 255, 0.92));
  border: 1px solid rgba(255, 255, 255, 0.72);
  box-shadow: 0 20px 45px rgba(76, 104, 148, 0.18);
}

.filter-dialog-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 18px;
  margin-bottom: 20px;
}

.filter-dialog-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #172033;
}

.filter-dialog-subtitle {
  margin-top: 8px;
  color: #64748b;
  line-height: 1.5;
}

.filter-options {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.filter-option {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  width: 100%;
  padding: 16px 18px;
  border: 1px solid rgba(199, 210, 224, 0.9);
  border-radius: 20px;
  text-align: left;
  background: rgba(255, 255, 255, 0.84);
  cursor: pointer;
  transition: all 0.2s ease;
}

.groups-shell-dark .filter-option {
  background: rgba(12, 19, 32, 0.88);
  border-color: rgba(63, 80, 114, 0.58);
}

.filter-option:hover {
  transform: translateY(-1px);
  border-color: rgba(22, 119, 255, 0.35);
}

.filter-option-active {
  border-color: rgba(22, 119, 255, 0.55);
  background: linear-gradient(180deg, rgba(236, 244, 255, 0.98), rgba(227, 238, 255, 0.96));
  box-shadow: 0 14px 28px rgba(22, 119, 255, 0.12);
}

.filter-option-icon {
  display: grid;
  place-items: center;
  width: 38px;
  height: 38px;
  border-radius: 12px;
  color: #1677ff;
  background: rgba(22, 119, 255, 0.1);
  flex-shrink: 0;
}

.filter-option-title {
  display: block;
  font-size: 1rem;
  font-weight: 600;
  color: #172033;
}

.groups-shell-dark .filter-option-title {
  color: #eef4ff;
}

.filter-option-text {
  display: block;
  margin-top: 4px;
  color: #6b7280;
  line-height: 1.45;
}

.groups-shell-dark .filter-option-text {
  color: #8ea3c7;
}

.filter-dialog-actions {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 22px;
}

.reset-filter-btn {
  min-height: 46px;
  padding: 0 18px;
  border-radius: 16px;
  text-transform: none;
  letter-spacing: 0;
  color: #1677ff;
  background: rgba(255, 255, 255, 0.82);
  border-color: rgba(22, 119, 255, 0.28);
  font-weight: 600;
}

.groups-shell-dark .reset-filter-btn {
  background: rgba(18, 27, 43, 0.92);
  color: #7fbcff;
  border-color: rgba(82, 156, 255, 0.32);
}

.apply-filter-btn {
  min-height: 46px;
  padding: 0 18px;
  border-radius: 16px;
  text-transform: none;
  letter-spacing: 0;
}

@media (max-width: 1280px) {
  .groups-panel {
    grid-template-columns: 210px minmax(0, 1fr);
  }

  .brand-name {
    font-size: 1.08rem;
  }

  .brand-caption {
    font-size: 0.76rem;
  }

  .groups-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 1024px) {
  .desktop-only-btn {
    display: none !important;
  }

  .groups-shell {
    width: 100%;
    max-width: none;
    overflow: hidden;
  }

  .groups-panel {
    grid-template-columns: 1fr;
    padding: 18px;
    gap: 18px;
  }

  .sidebar-card {
    display: none;
  }

  .mobile-header-card,
  .mobile-utility-card {
    display: flex;
  }

  .topbar-card {
    display: none;
  }

  .groups-header,
  .groups-header,
  .toolbar-row {
    flex-direction: column;
    align-items: stretch;
  }

  .groups-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .toolbar-actions,
  .toolbar-btn {
    width: 100%;
  }
}

@media (max-width: 768px) {
  .groups-page {
    padding: 10px;
  }

  .groups-panel {
    padding: 12px;
    gap: 12px;
  }

  .groups-card {
    padding: 18px;
  }

  .groups-title {
    font-size: 1.9rem;
  }

  .groups-grid {
    grid-template-columns: 1fr;
  }

  .group-card {
    padding: 16px;
    border-radius: 20px;
  }

  .group-card-top {
    gap: 12px;
    margin-bottom: 14px;
  }

  .group-avatar {
    width: 46px !important;
    height: 46px !important;
  }

  .group-info-grid {
    grid-template-columns: 1fr;
    gap: 10px;
  }

  .info-item {
    padding: 12px;
  }

  .mobile-profile-row {
    flex-direction: column;
    align-items: stretch;
  }

  .mobile-create-btn {
    width: 100%;
    min-height: 50px;
  }
}

@media (max-width: 560px) {
  .groups-shell {
    border-radius: 24px;
    width: 100%;
    max-width: none;
    overflow: hidden;
  }

  .groups-panel {
    padding: 10px;
    gap: 10px;
  }

  .sidebar-card,
  .topbar-card,
  .groups-card {
    border-radius: 20px;
  }

  .mobile-header-card,
  .mobile-utility-card,
  .groups-card {
    padding: 14px;
  }

  .mobile-menu-btn,
  .mobile-header-actions .top-icon-btn {
    width: 42px;
    height: 42px;
    min-width: 42px;
  }

  .mobile-brand-icon {
    width: 40px;
    height: 40px;
  }

  .mobile-header-actions {
    gap: 8px;
  }

  .brand-name {
    font-size: 1rem;
  }

  .brand-caption {
    font-size: 0.72rem;
  }

  .groups-title {
    font-size: 1.65rem;
  }

  .groups-subtitle {
    font-size: 0.92rem;
  }

  .group-card {
    padding: 14px;
    border-radius: 18px;
  }

  .group-card-top {
    margin-bottom: 12px;
  }

  .group-avatar {
    width: 42px !important;
    height: 42px !important;
  }

  .section {
    font-size: 0.98rem;
  }

  .trainer,
  .value,
  .attendance-value {
    font-size: 0.88rem;
  }

  .info-item {
    padding: 10px;
    border-radius: 16px;
  }

  .attendance-block {
    margin-top: 14px;
  }

  .filter-dialog-card {
    padding: 18px;
  }

  .create-dialog-card {
    padding: 18px;
  }

  .create-fields-grid {
    grid-template-columns: 1fr;
  }

  .filter-dialog-header,
  .filter-dialog-actions {
    align-items: stretch;
    flex-direction: column;
  }

  .create-dialog-header,
  .create-dialog-actions {
    align-items: stretch;
    flex-direction: column;
  }
}
</style>
