<template>
  <main class="container" role="main">
    <div class="alert alert-danger" role="alert">
      <h4 class="alert-heading">Datei nicht gefunden</h4>
      <p>Die angefragte Datei <strong>{{ currentUrl }}</strong> wurde nicht gefunden.</p>
      <hr>
      <p class="mb-0">Sollte das Problem weiterhin bestehen, melde Dich bei uns. Wir werden es so
        schnell wie möglich beheben.</p>
    </div>
  </main>
</template>

<script setup>
import { useHead } from '@vueuse/head'

useHead({
  meta: [{ name: 'robots', content: 'noindex,nofollow' }]
})

const currentUrl = window.location.pathname
</script>
