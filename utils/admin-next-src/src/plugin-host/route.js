export function findAppRoute(apps, path) {
  for (const app of apps) {
    const prefix = `/plugin/${app.appId}`
    if (path === prefix || path.startsWith(`${prefix}/`)) {
      return {
        app,
        route: path.slice(prefix.length) || "/",
        menu: app.menu.find((item) => item.route === path) ?? null,
      }
    }
  }
  return null
}

export function canLoadPluginRoute(matched, checkRight) {
  return matched !== null && matched.menu !== null && checkRight(matched.menu.require)
}
