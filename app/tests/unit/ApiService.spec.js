import createApiService from '@/services/ApiService'

describe('Testing fetchItems', () => {
  it('Items can be fetched', async () => {
    const fetchMock = jest.fn().mockReturnValue(Promise.resolve({
      ok: true,
      json: () => Promise.resolve({ count: 1, items: [] })
    }))

    expect(await createApiService(fetchMock).fetchItems())
      .toEqual({ count: 1, items: [] })

    expect(fetchMock).toBeCalledWith(
      'http://localhost/api/items',
      {
        credentials: 'omit',
        headers: { Accept: 'application/json' },
        signal: (new AbortController()).signal
      }
    )
  })

  it('Fetching items fails on server error', async () => {
    const fetchMock = jest.fn().mockReturnValue(Promise.resolve({
      ok: false,
      statusText: 'Server is down'
    }))

    expect.assertions(1)
    await createApiService(fetchMock).fetchItems()
      .catch(error => { expect(error.toString()).toBeTruthy() })
  })
})
