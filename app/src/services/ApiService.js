const createApiService = fetch => {
  /**
   * @param {string} url
   * @returns {Promise<any>}
   */
  const fetchJson = url => {
    const controller = new AbortController()
    setTimeout(() => { controller.abort() }, 5000)

    return fetch(url, {
      credentials: 'omit',
      headers: { Accept: 'application/json' },
      signal: controller.signal
    }).then(response => {
      if (response.ok) {
        return response.json()
      }
      throw new Error(response.statusText)
    }).catch(error => {
      throw new Error(`Fetching URL "${url}" failed with "${error.message}"`)
    })
  }

  /**
   * @param {string} path
   * @param {Object} options
   * @returns {string}
   */
  const createUrl = (path, options = {}) => {
    const url = new URL(path, location.toString())
    Object.entries(options)
      .filter((entry) => typeof entry[1] !== 'undefined' && entry[1] !== null && entry[1].toString().length > 0)
      .forEach(entry => { url.searchParams.set(entry[0], entry[1]) })
    url.searchParams.sort()
    return url.toString()
  }

  return {
    /**
     * @param {limit, offset } options
     * @returns {Promise<any>}
     */
    fetchItems (options) {
      return fetchJson(createUrl('/api/items', options))
    },

    /**
     * @param {limit, offset } options
     * @returns {Promise<any>}
     */
    fetchFeeds (options) {
      return fetchJson(createUrl('/api/feeds', options))
    }
  }
}

export default createApiService
