import { apiClient } from './http'

const unwrap = async (request) => {
  const response = await request
  return response.data?.data
}

export const authApi = {
  login(payload) {
    return unwrap(apiClient.post('/login', payload))
  },
  register(payload) {
    return unwrap(apiClient.post('/register', payload))
  },
  me() {
    return unwrap(apiClient.get('/me'))
  },
  logout() {
    return unwrap(apiClient.post('/logout'))
  }
}

export const dashboardApi = {
  get() {
    return unwrap(apiClient.get('/dashboard'))
  }
}

export const groupsApi = {
  list() {
    return unwrap(apiClient.get('/groups'))
  },
  create(payload) {
    return unwrap(apiClient.post('/groups', payload))
  },
  update(id, payload) {
    return unwrap(apiClient.put(`/groups/${id}`, payload))
  },
  remove(id) {
    return unwrap(apiClient.delete(`/groups/${id}`))
  }
}

export const sessionsApi = {
  list(params = {}) {
    return unwrap(apiClient.get('/sessions', { params }))
  },
  create(payload) {
    return unwrap(apiClient.post('/sessions', payload))
  },
  update(id, payload) {
    return unwrap(apiClient.put(`/sessions/${id}`, payload))
  },
  updateStatus(id, payload) {
    return unwrap(apiClient.patch(`/sessions/${id}/status`, payload))
  },
  remove(id) {
    return unwrap(apiClient.delete(`/sessions/${id}`))
  }
}

export const attendanceApi = {
  list(params = {}) {
    return unwrap(apiClient.get('/attendance', { params }))
  },
  create(payload) {
    return unwrap(apiClient.post('/attendance', payload))
  },
  update(id, payload) {
    return unwrap(apiClient.put(`/attendance/${id}`, payload))
  }
}

export const paymentsApi = {
  list() {
    return unwrap(apiClient.get('/payments'))
  },
  create(payload) {
    return unwrap(apiClient.post('/payments', payload))
  },
  get(id) {
    return unwrap(apiClient.get(`/payments/${id}`))
  },
  refund(id) {
    return unwrap(apiClient.post(`/payments/${id}/refund`))
  }
}

export const notificationsApi = {
  list() {
    return unwrap(apiClient.get('/notifications'))
  },
  update(id, payload) {
    return unwrap(apiClient.patch(`/notifications/${id}`, payload))
  }
}

export const usersApi = {
  list(params = {}) {
    return unwrap(apiClient.get('/users', { params }))
  },
  create(payload) {
    return unwrap(apiClient.post('/users', payload))
  },
  update(id, payload) {
    return unwrap(apiClient.put(`/users/${id}`, payload))
  },
  remove(id) {
    return unwrap(apiClient.delete(`/users/${id}`))
  }
}
