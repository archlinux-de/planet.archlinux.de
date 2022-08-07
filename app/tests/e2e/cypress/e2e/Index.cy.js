describe('Index page', () => {
  beforeEach(() => {
    cy.intercept({ method: 'GET', pathname: /^\/api\/items$/ }).as('api-items')
    cy.intercept({ method: 'GET', pathname: /^\/api\/feeds$/ }).as('api-feeds')

    cy.visit('/')

    cy.wait('@api-feeds')
    cy.wait('@api-items')
  })

  it('shows title', () => {
    cy.contains('h1', 'Arch Linux Planet')
  })

  it('shows item', () => {
    cy.get('[data-test~=item-1]').should('be.visible')

    cy.get('[data-test~=item-1] h2 a').invoke('text').should('not.be.empty')
    cy.get('[data-test~=item-1] h2 a').invoke('attr', 'href').should('match', /^https?:\/\//)

    cy.get('[data-test~=item-1] [data-test~=item-description]').should('be.visible')
    cy.get('[data-test~=item-1] [data-test~=item-description]').invoke('text').should('not.be.empty')
  })

  it('loads additional items when scrolling', () => {
    const itemsLimit = 10
    const itemHeight = 250

    cy.get('[data-test~=item]').should('have.length', itemsLimit)
    cy.scrollTo(0, itemHeight * itemsLimit)
    cy.wait('@api-items')

    cy.get('[data-test~=item]').should('have.length', itemsLimit * 2)
  })
})
