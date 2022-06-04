module.exports = {
  fixturesFolder: false,
  screenshotOnRunFailure: false,
  video: false,
  numTestsKeptInMemory: 0,
  e2e: {
    setupNodeEvents (on, config) {},
    supportFile: false
  },
  component: {
    setupNodeEvents (on, config) {},
    specPattern: '**/*.cy.{js,jsx,ts,tsx}'
  }
}
