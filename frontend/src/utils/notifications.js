export function resolveNotificationTarget(notification, role) {
  const payload = notification?.payload ?? {}

  if (payload?.route) {
    return typeof payload.route === 'string'
      ? { path: payload.route, query: payload.query ?? {} }
      : payload.route
  }

  switch (role) {
    case 'admin':
      if (notification?.type === 'payment') return '/admin-payments'
      if (notification?.type === 'group') return '/manage-groups'
      return '/manage-sessions'
    case 'coach':
      if (notification?.type === 'attendance') return '/coach-attendance'
      if (notification?.type === 'group') return '/groups'
      return '/schedule'
    case 'parent':
      if (notification?.type === 'payment') return '/payments'
      if (notification?.type === 'attendance') return '/attendance'
      if (notification?.type === 'group') return '/groups'
      return '/schedule'
    case 'child':
      return notification?.type === 'attendance' ? '/attendance' : '/schedule'
    default:
      return '/home'
  }
}

export function isSameNotificationTarget(currentRoute, target) {
  if (!target || !currentRoute) return false

  if (typeof target === 'string') {
    return currentRoute.path === target
  }

  const currentQuery = JSON.stringify(currentRoute.query ?? {})
  const targetQuery = JSON.stringify(target.query ?? {})

  return currentRoute.path === target.path && currentQuery === targetQuery
}
