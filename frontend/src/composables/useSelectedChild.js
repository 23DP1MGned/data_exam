import { computed, reactive } from 'vue'

const STORAGE_KEY = 'app-selected-child-id'

const readStoredChildId = () => {
  const raw = localStorage.getItem(STORAGE_KEY)
  if (!raw) return null

  const parsed = Number(raw)
  return Number.isFinite(parsed) ? parsed : null
}

const state = reactive({
  selectedChildId: readStoredChildId()
})

const persistSelectedChildId = () => {
  if (state.selectedChildId == null) {
    localStorage.removeItem(STORAGE_KEY)
    return
  }

  localStorage.setItem(STORAGE_KEY, String(state.selectedChildId))
}

const setSelectedChildId = (childId) => {
  const normalizedChildId = childId == null ? null : Number(childId)
  state.selectedChildId = Number.isFinite(normalizedChildId) ? normalizedChildId : null
  persistSelectedChildId()
}

const syncSelectedChild = (availableChildIds = [], options = {}) => {
  const { preserveExisting = false } = options
  const normalizedIds = availableChildIds
    .map((value) => Number(value))
    .filter((value) => Number.isFinite(value))

  if (!normalizedIds.length) {
    if (!preserveExisting) {
      setSelectedChildId(null)
    }
    return null
  }

  if (preserveExisting && state.selectedChildId != null) {
    return state.selectedChildId
  }

  if (!normalizedIds.includes(state.selectedChildId)) {
    setSelectedChildId(normalizedIds[0])
  }

  return state.selectedChildId
}

export const useSelectedChild = () => ({
  selectedChildId: computed(() => state.selectedChildId),
  setSelectedChildId,
  syncSelectedChild
})
