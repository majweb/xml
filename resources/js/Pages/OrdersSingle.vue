<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head } from '@inertiajs/inertia-vue3';
import BreezeButton from '@/Components/Button.vue';

export default {
  components: {
    BreezeAuthenticatedLayout,Head
  },
  props: {
    order: Object
  },
  methods:{
    back() {
      window.history.back();
    },
  }
};
</script>
<template>
    <Head title="Zamówienia" />
    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Zamówienia 213123
            </h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <button class="bg-indigo-400 transition delay-75 border-2 border-indigo=100 p-2 hover:bg-indigo-300 rounded-lg text-white" type="button"  @click="back" >
                    Powrót
                </button>
                    {{order.id}}
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
