<template>
  <v-app>
    <v-main class="dashboard-page">
      <div class="dashboard-shell">
        <div class="dashboard-panel">
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
                :class="{ 'nav-item-active': item.to === '/dashboard' }"
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
                    label="Search schedule"
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

            <div class="schedule-card">
              <div class="schedule-header">
                <div>
                  <h1 class="schedule-title">Schedule</h1>
                  <div class="schedule-subtitle">Today {{ todayLabel }}</div>
                </div>

                <v-btn
                  color="primary"
                  class="create-btn"
                  prepend-icon="mdi-plus"
                  @click="openCreate"
                >
                  Create training
                </v-btn>
              </div>

              <div class="toolbar-row">
                <div class="pill-tabs">
                  <button
                    v-for="item in tabs"
                    :key="item.value"
                    type="button"
                    class="pill-tab"
                    :class="{ 'pill-tab-active': tab === item.value }"
                    @click="tab = item.value"
                  >
                    {{ item.label }}
                  </button>
                </div>

                <div class="toolbar-actions">
                  <v-btn variant="outlined" class="toolbar-btn" @click="filterDialog = true">
                    <v-icon start size="18">mdi-tune-variant</v-icon>
                    Filters
                  </v-btn>
                </div>
              </div>

              <div
                v-for="group in groupedTrainings"
                :key="group.label"
                class="day-group"
              >
                <div class="day-label">
                  <span class="day-primary">{{ group.title }}</span>
                  <span class="day-secondary">{{ group.label }}</span>
                </div>

                <div
                  v-for="training in group.items"
                  :key="training.id"
                  class="schedule-item"
                  :class="{ 'schedule-item-expanded': expandedId === training.id }"
                >
                  <div class="schedule-grid">
                    <div class="slot-block">
                      <div class="meta-label">Date</div>
                      <div class="slot-value">{{ formatCardDate(training.date) }}</div>
                    </div>

                    <div class="slot-block">
                      <div class="meta-label">From</div>
                      <div class="slot-value">{{ training.start }}</div>
                    </div>

                    <div class="slot-block">
                      <div class="meta-label">To</div>
                      <div class="slot-value">{{ training.end }}</div>
                    </div>

                    <div class="course-block">
                      <div class="meta-label">Training</div>
                      <div class="course-title">{{ training.title }}</div>

                      <template v-if="expandedId === training.id">
                        <div class="meta-label section-gap">Description</div>
                        <div class="course-subtitle">{{ training.description }}</div>

                        <div class="meta-label section-gap">Group</div>
                        <div class="meeting-link">{{ training.group }}</div>
                      </template>
                    </div>

                    <div class="teacher-block">
                      <div class="teacher-summary">
                        <v-avatar size="46" class="teacher-avatar">
                          <img :src="training.avatar" :alt="training.trainer">
                        </v-avatar>
                        <div>
                          <div class="meta-label">Trainer</div>
                          <div class="teacher-name">{{ training.trainer }}</div>
                        </div>
                      </div>
                    </div>

                    <div class="expand-block">
                      <button
                        type="button"
                        class="expand-link"
                        @click="toggleExpanded(training.id)"
                      >
                        {{ expandedId === training.id ? 'Show less' : 'Show more' }}
                      </button>
                    </div>
                  </div>

                  <div v-if="expandedId === training.id" class="schedule-footer">
                    <div class="students-block">
                      <div class="meta-label">Students</div>
                      <div class="students-row">
                        <v-avatar
                          v-for="student in training.students.slice(0, 4)"
                          :key="student.id"
                          size="44"
                          class="student-avatar"
                        >
                          <img :src="student.avatar" :alt="student.name">
                        </v-avatar>
                        <div class="student-more">+{{ training.students.length }}</div>
                      </div>
                    </div>

                    <div class="action-row">
                      <v-btn variant="outlined" color="error" class="action-btn">
                        Cancel
                      </v-btn>
                      <v-btn
                        variant="outlined"
                        color="primary"
                        class="action-btn"
                        @click="openEdit(training)"
                      >
                        Edit
                      </v-btn>
                      <v-btn color="primary" class="action-btn">
                        Attendance
                      </v-btn>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>
        </div>

        <v-dialog v-model="dialog" max-width="560">
          <v-card class="dialog-card">
            <v-card-title>
              {{ isEditing ? 'Edit Training' : 'Create Training' }}
            </v-card-title>

            <v-card-text>
              <v-text-field label="Title" v-model="newTraining.title" />
              <v-text-field label="Date (YYYY-MM-DD)" v-model="newTraining.date" />
              <v-text-field label="Start time" v-model="newTraining.start" />
              <v-text-field label="End time" v-model="newTraining.end" />
              <v-text-field label="Trainer" v-model="newTraining.trainer" />
              <v-textarea label="Description" v-model="newTraining.description" />
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn @click="dialog = false">Cancel</v-btn>
              <v-btn color="primary" @click="saveTraining">
                {{ isEditing ? 'Update' : 'Save' }}
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <v-dialog v-model="filterDialog" max-width="520">
          <v-card class="dialog-card filter-dialog-card">
            <div class="filter-dialog-header">
              <div>
                <div class="filter-dialog-title">Filters</div>
                <div class="filter-dialog-subtitle">
                  Sort trainings by time or title without leaving the dashboard view.
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
                :class="{ 'filter-option-active': selectedSort === 'time' }"
                @click="selectedSort = 'time'"
              >
                <span class="filter-option-icon">
                  <v-icon size="18">mdi-clock-outline</v-icon>
                </span>
                <span>
                  <span class="filter-option-title">By time</span>
                  <span class="filter-option-text">Show trainings from the earliest start time.</span>
                </span>
              </button>

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
                  <span class="filter-option-text">Order trainings alphabetically from A to Z.</span>
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
                  <span class="filter-option-text">Order trainings alphabetically from Z to A.</span>
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
const tab = ref('upcoming')
const dialog = ref(false)
const filterDialog = ref(false)
const isEditing = ref(false)
const editingId = ref(null)
const expandedId = ref(1)
const selectedSort = ref('time')
const darkMode = ref(false)

const tabs = [
  { value: 'upcoming', label: 'Upcoming' },
  { value: 'past', label: 'Past' }
]

const navItems = [
  { label: 'Home', icon: 'mdi-home-outline', to: '/dashboard' },
  { label: 'Schedule', icon: 'mdi-calendar-month-outline', to: '/schedule' },
  { label: 'Groups', icon: 'mdi-account-group-outline', to: '/groups' },
  { label: 'Attendance', icon: 'mdi-check-circle-outline' },
  { label: 'Payments', icon: 'mdi-credit-card-outline', to: '/payments' }
]

const trainings = ref([
  {
    id: 1,
    title: 'Football Training U14',
    description: 'Speed and coordination practice',
    trainer: 'Jānis Ozols',
    start: '09:00 AM',
    end: '10:20 AM',
    date: '2026-03-23',
    status: 'upcoming',
    group: 'Football U14',
    avatar: 'https://i.pravatar.cc/100?img=32',
    students: [
      { id: 1, name: 'Anna', avatar: 'https://i.pravatar.cc/80?img=5' },
      { id: 2, name: 'Liam', avatar: 'https://i.pravatar.cc/80?img=6' },
      { id: 3, name: 'Noah', avatar: 'https://i.pravatar.cc/80?img=7' },
      { id: 4, name: 'Emma', avatar: 'https://i.pravatar.cc/80?img=8' }
    ]
  },
  {
    id: 2,
    title: 'Basketball Training',
    description: 'Passing combinations and finishing drills',
    trainer: 'Alex Johnson',
    start: '01:20 PM',
    end: '03:00 PM',
    date: '2026-03-23',
    status: 'upcoming',
    group: 'Basketball Juniors',
    avatar: 'https://i.pravatar.cc/100?img=44',
    students: [
      { id: 5, name: 'Ethan', avatar: 'https://i.pravatar.cc/80?img=9' },
      { id: 6, name: 'Sofia', avatar: 'https://i.pravatar.cc/80?img=10' }
    ]
  },
  {
    id: 3,
    title: 'Running Practice',
    description: 'Stamina blocks and sprint intervals',
    trainer: 'Mike Smith',
    start: '03:30 PM',
    end: '04:30 PM',
    date: '2026-03-23',
    status: 'upcoming',
    group: 'Running Club',
    avatar: 'https://i.pravatar.cc/100?img=47',
    students: [
      { id: 7, name: 'Mia', avatar: 'https://i.pravatar.cc/80?img=11' },
      { id: 8, name: 'Lucas', avatar: 'https://i.pravatar.cc/80?img=12' }
    ]
  },
  {
    id: 4,
    title: 'Swimming Session',
    description: 'Technique work and lap pacing',
    trainer: 'Anna Petrova',
    start: '09:20 AM',
    end: '11:00 AM',
    date: '2026-03-24',
    status: 'past',
    group: 'Swimming Beginners',
    avatar: 'https://i.pravatar.cc/100?img=53',
    students: [
      { id: 9, name: 'Oliver', avatar: 'https://i.pravatar.cc/80?img=13' },
      { id: 10, name: 'Isabella', avatar: 'https://i.pravatar.cc/80?img=14' }
    ]
  }
])

const newTraining = ref({
  title: '',
  date: '',
  start: '',
  end: '',
  trainer: '',
  description: ''
})

const todayLabel = computed(() =>
  new Date('2026-03-23').toLocaleDateString('en-US', {
    month: 'long',
    day: 'numeric',
    year: 'numeric'
  })
)

const filteredTrainings = computed(() => {
  const query = search.value.trim().toLowerCase()

  return trainings.value.filter((training) => {
    const matchesTab = tab.value === 'upcoming'
      ? training.status !== 'past'
      : training.status === tab.value

    if (!matchesTab) return false
    if (!query) return true

    return [
      training.title,
      training.description,
      training.trainer,
      training.group
    ].some((value) => value.toLowerCase().includes(query))
  })
})

const groupedTrainings = computed(() => {
  const groups = new Map()

  filteredTrainings.value.forEach((training) => {
    if (!groups.has(training.date)) groups.set(training.date, [])
    groups.get(training.date).push(training)
  })

  return Array.from(groups.entries()).map(([date, items], index) => ({
    title: index === 0 ? 'Today' : 'Tomorrow',
    label: new Date(date).toLocaleDateString('en-US', {
      month: 'long',
      day: 'numeric',
      year: 'numeric'
    }),
    items
  }))
})

function toggleExpanded(id) {
  expandedId.value = expandedId.value === id ? null : id
}

function openCreate() {
  isEditing.value = false
  editingId.value = null
  newTraining.value = {
    title: '',
    date: '',
    start: '',
    end: '',
    trainer: '',
    description: ''
  }
  dialog.value = true
}

function openEdit(training) {
  isEditing.value = true
  editingId.value = training.id
  newTraining.value = {
    title: training.title,
    date: training.date,
    start: training.start,
    end: training.end,
    trainer: training.trainer,
    description: training.description
  }
  dialog.value = true
}

function saveTraining() {
  if (isEditing.value) {
    const index = trainings.value.findIndex((item) => item.id === editingId.value)

    if (index !== -1) {
      trainings.value[index] = {
        ...trainings.value[index],
        ...newTraining.value
      }
    }
  } else {
    trainings.value.push({
      id: Date.now(),
      ...newTraining.value,
      status: 'upcoming',
      group: 'New Group',
      avatar: 'https://i.pravatar.cc/100?img=25',
      students: []
    })
  }

  dialog.value = false
}

function parseTime(value) {
  return new Date(`1970-01-01 ${value}`).getTime()
}

function formatCardDate(value) {
  return new Date(value).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric'
  })
}

function sortAZ() {
  trainings.value.sort((a, b) => a.title.localeCompare(b.title))
}

function sortZA() {
  trainings.value.sort((a, b) => b.title.localeCompare(a.title))
}

function sortTime() {
  trainings.value.sort((a, b) => parseTime(a.start) - parseTime(b.start))
}

function applySelectedSort() {
  if (selectedSort.value === 'time') sortTime()
  if (selectedSort.value === 'az') sortAZ()
  if (selectedSort.value === 'za') sortZA()

  filterDialog.value = false
}

function resetSort() {
  selectedSort.value = 'time'
  sortTime()
  filterDialog.value = false
}
</script>

<style scoped>
.dashboard-page {
  padding: 24px;
}

.dashboard-shell {
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

.dashboard-panel {
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

.nav-item-settings {
  margin-top: auto;
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

.schedule-card {
  min-width: 0;
  padding: 26px;
  border-radius: 28px;
  background: rgba(249, 251, 255, 0.58);
  border: 1px solid rgba(255, 255, 255, 0.62);
}

.schedule-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 18px;
  margin-bottom: 24px;
}

.schedule-title {
  margin: 0;
  font-size: 2.3rem;
  line-height: 1.1;
  font-weight: 700;
  color: #121826;
}

.schedule-subtitle {
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

.pill-tabs {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.pill-tab {
  min-height: 48px;
  padding: 0 18px;
  border: 1px solid rgba(191, 203, 224, 0.84);
  border-radius: 999px;
  color: #6b7280;
  background: rgba(255, 255, 255, 0.9);
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.pill-tab-active {
  border-color: transparent;
  color: white;
  background: linear-gradient(180deg, #1677ff 0%, #0f5fe3 100%);
  box-shadow: 0 14px 26px rgba(22, 119, 255, 0.24);
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

.day-group + .day-group {
  margin-top: 34px;
}

.day-label {
  margin-bottom: 16px;
  font-size: 1.22rem;
}

.day-primary {
  margin-right: 10px;
  font-weight: 700;
  color: #111827;
}

.day-secondary {
  color: #6b7280;
}

.schedule-item {
  margin-bottom: 16px;
  padding: 24px 26px;
  border-radius: 24px;
  background: rgba(255, 255, 255, 0.92);
  border: 1px solid transparent;
  box-shadow: 0 12px 28px rgba(110, 136, 173, 0.08);
}

.schedule-item-expanded {
  border-color: rgba(22, 119, 255, 0.7);
  box-shadow: 0 18px 36px rgba(22, 119, 255, 0.1);
}

.schedule-grid {
  display: grid;
  grid-template-columns: 90px 90px minmax(220px, 1fr) minmax(180px, 220px) 100px;
  gap: 18px;
  align-items: start;
}

.meta-label {
  margin-bottom: 6px;
  font-size: 0.95rem;
  color: #7d8899;
}

.slot-value,
.course-title,
.teacher-name {
  font-size: 1.05rem;
  font-weight: 600;
  color: #111827;
}

.course-title {
  font-size: 1.1rem;
}

.course-subtitle,
.meeting-link {
  color: #172033;
  font-size: 0.96rem;
}

.meeting-link {
  word-break: break-word;
}

.section-gap {
  margin-top: 22px;
}

.teacher-summary {
  display: flex;
  align-items: center;
  gap: 12px;
}

.teacher-avatar {
  border: 2px solid rgba(255, 255, 255, 0.82);
  box-shadow: 0 10px 20px rgba(110, 136, 173, 0.18);
}

.expand-block {
  display: flex;
  justify-content: flex-end;
}

.expand-link {
  padding: 0;
  border: none;
  background: transparent;
  color: #1677ff;
  font-size: 1.02rem;
  font-weight: 600;
  cursor: pointer;
}

.schedule-footer {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 24px;
  margin-top: 26px;
}

.students-row {
  display: flex;
  align-items: center;
  margin-top: 10px;
}

.student-avatar {
  margin-right: -10px;
  border: 3px solid white;
}

.student-more {
  display: grid;
  place-items: center;
  width: 44px;
  height: 44px;
  margin-left: 8px;
  border-radius: 999px;
  font-weight: 700;
  color: white;
  background: #1677ff;
  border: 3px solid white;
}

.action-row {
  display: flex;
  flex-wrap: wrap;
  justify-content: flex-end;
  gap: 12px;
}

.action-btn {
  min-height: 50px;
  padding: 0 20px;
  border-radius: 16px;
  text-transform: none;
  letter-spacing: 0;
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
  .dashboard-panel {
    grid-template-columns: 210px minmax(0, 1fr);
  }

  .brand-name {
    font-size: 1.08rem;
  }

  .brand-caption {
    font-size: 0.76rem;
  }

  .schedule-grid {
    grid-template-columns: repeat(2, minmax(90px, 110px)) minmax(220px, 1fr);
  }

  .teacher-block,
  .expand-block {
    grid-column: span 3;
  }

  .teacher-block {
    margin-top: 6px;
  }

  .expand-block {
    justify-content: flex-start;
  }

  .schedule-footer {
    flex-direction: column;
    align-items: flex-start;
  }

  .action-row {
    justify-content: flex-start;
  }
}

@media (max-width: 1024px) {
  .dashboard-panel {
    grid-template-columns: 1fr;
  }

  .sidebar-card {
    min-height: auto;
  }

  .nav-list {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .nav-item-settings {
    margin-top: 12px;
  }

  .topbar-card {
    flex-direction: column;
    align-items: stretch;
  }

  .search-wrap {
    max-width: none;
  }

  .topbar-tools,
  .schedule-header,
  .toolbar-row {
    flex-direction: column;
    align-items: stretch;
  }

  .toolbar-actions {
    flex-wrap: wrap;
  }

  .profile-pill {
    width: 100%;
  }

  .schedule-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .course-block,
  .teacher-block,
  .expand-block {
    grid-column: 1 / -1;
  }
}

@media (max-width: 768px) {
  .dashboard-page {
    padding: 12px;
  }

  .schedule-card {
    padding: 18px;
  }

  .schedule-title {
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

  .schedule-grid {
    grid-template-columns: 1fr;
  }

  .expand-block {
    justify-content: flex-start;
  }

  .schedule-item {
    padding: 18px;
  }

  .action-row {
    width: 100%;
  }

  .action-btn {
    flex: 1 1 100%;
  }
}

@media (max-width: 560px) {
  .dashboard-shell {
    border-radius: 24px;
  }

  .dashboard-panel {
    padding: 14px;
    gap: 14px;
  }

  .sidebar-card,
  .topbar-card,
  .schedule-card {
    border-radius: 20px;
  }

  .brand-block {
    align-items: flex-start;
  }

  .brand-name {
    font-size: 1rem;
  }

  .brand-caption {
    font-size: 0.72rem;
  }

  .schedule-header {
    margin-bottom: 18px;
  }

  .toolbar-row {
    margin-bottom: 20px;
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
