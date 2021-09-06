<template>
  <main class="container" role="main">
    <div class="row">
      <div class="col col-12 col-xl-8">
        <h1 class="mb-4">Arch Linux Planet</h1>
        <items :limit="10"></items>
      </div>

      <div class="col col-12 col-xl-4">
        <div class="card mb-4">
          <h3 class="card-title card-header">Feeds</h3>
          <div class="card-body p-1 p-lg-3">
            <feeds :limit="100"></feeds>
          </div>
          <div class="card-footer">
            <a class="btn btn-primary" role="button" href="/atom.xml">
              <strong>ATOM</strong> Feed
            </a>
            <a class="btn btn-outline-secondary" role="button" href="/rss.xml">
              <strong>RSS</strong> Feed
            </a>
          </div>
        </div>

        <div class="card">
          <h3 class="card-title card-header">Dein Blog</h3>
          <div class="card-body">
            Wenn auch Du Dein Blog bei archlinux.de hinzufügen möchtest, melde Dich einfach bei
            <a href="mailto:pierre@archlinux.de">pierre@archlinux.de</a>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script>
import Feeds from '../components/Feeds'
import Items from '../components/Items'

export default {
  name: 'Index',
  components: {
    Feeds,
    Items
  },
  metaInfo: { titleTemplate: null }
}
</script>
