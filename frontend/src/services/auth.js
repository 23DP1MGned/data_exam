import { computed, reactive } from 'vue'
import { authApi } from './api'

const TOKEN_KEY = 'app-auth-token'
const USER_KEY = 'app-auth-user'
const SELECTED_CHILD_KEY = 'app-selected-child-id'
const SELECTED_COACH_GROUP_KEY = 'app-selected-coach-group-id'

const parseStoredUser = () => {
  const raw = localStorage.getItem(USER_KEY)

  if (!raw) return null

  try {
    return JSON.parse(raw)
  } catch {
    localStorage.removeItem(USER_KEY)
    return null
  }
}

const state = reactive({
  token: localStorage.getItem(TOKEN_KEY) || '',
  user: parseStoredUser(),
  ready: false,
  bootstrapping: null
})

const persistAuth = () => {
  if (state.token) {
    localStorage.setItem(TOKEN_KEY, state.token)
  } else {
    localStorage.removeItem(TOKEN_KEY)
  }

  if (state.user) {
    localStorage.setItem(USER_KEY, JSON.stringify(state.user))
  } else {
    localStorage.removeItem(USER_KEY)
  }
}

export const setAuth = ({ token, user }) => {
  state.token = token
  state.user = user
  localStorage.removeItem(SELECTED_CHILD_KEY)
  localStorage.removeItem(SELECTED_COACH_GROUP_KEY)
  persistAuth()
}

export const clearAuth = () => {
  state.token = ''
  state.user = null
  localStorage.removeItem(SELECTED_CHILD_KEY)
  localStorage.removeItem(SELECTED_COACH_GROUP_KEY)
  persistAuth()
}

export const bootstrapAuth = async () => {
  if (state.bootstrapping) return state.bootstrapping

  state.bootstrapping = (async () => {
    if (!state.token) {
      state.ready = true
      return null
    }

    try {
      const user = await authApi.me()
      state.user = user
      persistAuth()
      state.ready = true
      return user
    } catch (error) {
      clearAuth()
      state.ready = true
      return null
    } finally {
      state.bootstrapping = null
    }
  })()

  return state.bootstrapping
}

export const login = async (payload) => {
  const result = await authApi.login(payload)
  setAuth(result)
  return result
}

export const register = async (payload) => {
  const result = await authApi.register(payload)
  setAuth(result)
  return result
}

export const logout = async () => {
  try {
    await authApi.logout()
  } finally {
    clearAuth()
  }
}

export const useAuth = () => ({
  state,
  user: computed(() => state.user),
  token: computed(() => state.token),
  isAuthenticated: computed(() => Boolean(state.token)),
  ready: computed(() => state.ready)
})
