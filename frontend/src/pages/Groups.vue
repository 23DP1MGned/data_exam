<template>
  <v-app>
    <v-main class="groups-page">
      <div class="groups-shell">
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
            <div class="topbar-card">
              <div class="search-wrap">
                <div class="search-shell">
                  <v-icon size="20" class="search-shell-icon">mdi-magnify</v-icon>
                  <v-text-field
                    v-model="search"
                    label="Search groups"
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
                  <v-btn icon variant="text" class="top-icon-btn">
                    <v-icon>mdi-bell-outline</v-icon>
                  </v-btn>
                  <span class="icon-badge">6</span>
                </div>

                <div class="profile-pill">
                  <v-avatar size="48">
                    <img src="https://i.pravatar.cc/80?img=12" alt="Coach profile">
                  </v-avatar>
                  <div>
                    <div class="profile-name">Maksims Richards</div>
                    <div class="profile-email">maksims@sportsystem.app</div>
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

                <v-btn color="primary" class="create-btn" prepend-icon="mdi-plus">
                  Create group
                </v-btn>
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
                      <div class="trainer">{{ group.trainer }}</div>
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
                      <div class="label">Price</div>
                      <div class="value">{{ group.price }} € / month</div>
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
      </div>
    </v-main>
  </v-app>
</template>

<script setup>
import { computed, ref } from 'vue'

const search = ref('')
const darkMode = ref(false)
const filterDialog = ref(false)
const selectedSort = ref('az')

const navItems = [
  { label: 'Home', icon: 'mdi-home-outline', to: '/dashboard' },
  { label: 'Schedule', icon: 'mdi-calendar-month-outline', to: '/schedule' },
  { label: 'Groups', icon: 'mdi-account-group-outline', to: '/groups' },
  { label: 'Attendance', icon: 'mdi-check-circle-outline', to: '/attendance' },
  { label: 'Payments', icon: 'mdi-credit-card-outline', to: '/payments' }
]

const groups = ref([
  { id: 1, trainer: 'Jānis Ozols', section: 'Football U14', days: 'Mon / Wed', time: '17:00', students: 12, price: 50, attendance: 85, avatar: 'https://i.pravatar.cc/100?img=1' },
  { id: 2, trainer: 'Alex Johnson', section: 'Basketball', days: 'Tue / Thu', time: '19:00', students: 10, price: 60, attendance: 78, avatar: 'https://i.pravatar.cc/100?img=2' },
  { id: 3, trainer: 'Mike Smith', section: 'Running', days: 'Mon / Fri', time: '15:00', students: 8, price: 40, attendance: 92, avatar: 'https://i.pravatar.cc/100?img=3' },
  { id: 4, trainer: 'Anna Petrova', section: 'Dance', days: 'Sat', time: '16:00', students: 14, price: 45, attendance: 88, avatar: 'https://i.pravatar.cc/100?img=4' },
  { id: 5, trainer: 'David Brown', section: 'Boxing', days: 'Mon / Thu', time: '18:00', students: 9, price: 55, attendance: 80, avatar: 'https://i.pravatar.cc/100?img=5' },
  { id: 6, trainer: 'Olga Ivanova', section: 'Yoga', days: 'Tue / Sat', time: '10:00', students: 11, price: 35, attendance: 95, avatar: 'https://i.pravatar.cc/100?img=6' }
])

const filteredGroups = computed(() =>
  groups.value.filter((group) =>
    [group.section, group.trainer, group.days].some((value) =>
      value.toLowerCase().includes(search.value.toLowerCase())
    )
  )
)

function sortAZ() {
  groups.value.sort((a, b) => a.section.localeCompare(b.section))
}

function sortZA() {
  groups.value.sort((a, b) => b.section.localeCompare(a.section))
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

.groups-panel {
  display: grid;
  grid-template-columns: 232px minmax(0, 1fr);
  gap: 22px;
  padding: 22px;
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

.brand-caption {
  margin-top: 2px;
  font-size: 0.82rem;
  color: #7b8798;
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

.search-shell-icon {
  color: #6b7280;
  flex-shrink: 0;
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

.top-icon-btn-active {
  color: #1677ff;
  background: rgba(232, 242, 255, 0.96);
  border-color: rgba(22, 119, 255, 0.28);
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

.profile-name {
  font-size: 0.98rem;
  font-weight: 600;
  color: #172033;
}

.profile-email {
  font-size: 0.9rem;
  color: #7b8798;
  word-break: break-word;
}

.groups-card {
  min-width: 0;
  padding: 26px;
  border-radius: 28px;
  background: rgba(249, 251, 255, 0.58);
  border: 1px solid rgba(255, 255, 255, 0.62);
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

.groups-subtitle {
  margin-top: 10px;
  font-size: 1rem;
  color: #66748a;
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

.group-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 18px 36px rgba(110, 136, 173, 0.12);
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

.section {
  font-size: 1.08rem;
  font-weight: 700;
  color: #172033;
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

.label {
  font-size: 0.78rem;
  color: #7b8798;
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

.filter-option-text {
  display: block;
  margin-top: 4px;
  color: #6b7280;
  line-height: 1.45;
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
  .groups-panel {
    grid-template-columns: 1fr;
  }

  .nav-list {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .topbar-card,
  .groups-header,
  .toolbar-row {
    flex-direction: column;
    align-items: stretch;
  }

  .search-wrap {
    max-width: none;
  }

  .topbar-tools {
    flex-direction: column;
    align-items: stretch;
  }

  .profile-pill {
    width: 100%;
  }
}

@media (max-width: 768px) {
  .groups-page {
    padding: 12px;
  }

  .groups-card {
    padding: 18px;
  }

  .groups-title {
    font-size: 1.9rem;
  }

  .nav-list {
    grid-template-columns: 1fr;
  }

  .topbar-tools {
    gap: 10px;
  }

  .icon-badge-wrap {
    flex: 1;
  }

  .top-icon-btn {
    width: 100%;
  }

  .groups-grid {
    grid-template-columns: 1fr;
  }

  .group-info-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 560px) {
  .groups-shell {
    border-radius: 24px;
  }

  .groups-panel {
    padding: 14px;
    gap: 14px;
  }

  .sidebar-card,
  .topbar-card,
  .groups-card {
    border-radius: 20px;
  }

  .brand-name {
    font-size: 1rem;
  }

  .brand-caption {
    font-size: 0.72rem;
  }

  .filter-dialog-card {
    padding: 18px;
  }

  .filter-dialog-header,
  .filter-dialog-actions {
    align-items: stretch;
    flex-direction: column;
  }
}
</style>
