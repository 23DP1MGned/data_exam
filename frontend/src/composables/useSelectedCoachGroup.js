import { computed, reactive } from 'vue'

const STORAGE_KEY = 'app-selected-coach-group-id'

const readStoredGroupId = () => {
  const raw = localStorage.getItem(STORAGE_KEY)
  if (!raw) return null

  const parsed = Number(raw)
  return Number.isFinite(parsed) ? parsed : null
}

const state = reactive({
  selectedCoachGroupId: readStoredGroupId()
})

const persistSelectedCoachGroupId = () => {
  if (state.selectedCoachGroupId == null) {
    localStorage.removeItem(STORAGE_KEY)
    return
  }

  localStorage.setItem(STORAGE_KEY, String(state.selectedCoachGroupId))
}

const setSelectedCoachGroupId = (groupId) => {
  const normalizedGroupId = groupId == null ? null : Number(groupId)
  state.selectedCoachGroupId = Number.isFinite(normalizedGroupId) ? normalizedGroupId : null
  persistSelectedCoachGroupId()
}

const syncSelectedCoachGroup = (availableGroupIds = []) => {
  const normalizedIds = availableGroupIds
    .map((value) => Number(value))
    .filter((value) => Number.isFinite(value))

  if (!normalizedIds.length) {
    setSelectedCoachGroupId(null)
    return null
  }

  if (!normalizedIds.includes(state.selectedCoachGroupId)) {
    setSelectedCoachGroupId(normalizedIds[0])
  }

  return state.selectedCoachGroupId
}

export const useSelectedCoachGroup = () => ({
  selectedCoachGroupId: computed(() => state.selectedCoachGroupId),
  setSelectedCoachGroupId,
  syncSelectedCoachGroup
})
