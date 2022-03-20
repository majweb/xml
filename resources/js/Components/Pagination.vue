<template>
  <div v-show="links.length > 3">
    <div class="flex flex-wrap -mb-1">
      <template v-for="(link, key) in links" :key="key">
        <div v-if="link.url === null" :key="`${key}-disabled`" class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded" v-html="link.label"/>
        <Link v-else :key="key"  class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-indigo-50 hover:border-indigo-500 focus:border-indigo-500 focus:text-indigo-500" :class="{ 'bg-white border-indigo-500': link.active }" :href="link.url">
              <span v-html="link.label"></span>
        </Link>
      </template>
    </div>
  </div>
</template>
 
<script setup>
import { Link } from '@inertiajs/inertia-vue3';

defineProps({
  links: Array,
})
</script>