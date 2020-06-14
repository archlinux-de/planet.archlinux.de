<template>
  <b-container fluid role="main" tag="main">
    <b-row>
      <b-col cols="12" xl="9">
        <h1 class="mb-4">Arch Linux Planet</h1>
        <items :limit="10"></items>
      </b-col>

      <b-col cols="12" xl="3">
        <div class="card mb-4">
          <h3 class="card-title card-header">Dein Blog bei archlinux.de</h3>
          <div class="card-body">
            Wenn auch Du Dein Blog bei archlinux.de hinzufügen möchtest, melde Dich einfach bei
            <a href="mailto:pierre@archlinux.de">pierre@archlinux.de</a>
          </div>
        </div>

        <div class="card">
          <h3 class="card-title card-header">Feeds</h3>
          <div class="card-body p-1 p-lg-3">
            <feeds :limit="100"></feeds>
          </div>
          <div class="card-footer">
            <a class="btn btn-primary" href="/atom.xml">
              <strong>ATOM</strong> Feed
            </a>
            <a class="btn btn-outline-secondary" href="/rss.xml">
              <strong>RSS</strong> Feed
            </a>
          </div>
        </div>
      </b-col>
    </b-row>
  </b-container>
</template>

<script>
import Feeds from '@/components/Feeds'
import Items from '@/components/Items'

export default {
  name: 'Index',
  components: {
    Feeds,
    Items
  },
  metaInfo: { titleTemplate: null }
}
</script>
