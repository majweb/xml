<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head } from '@inertiajs/inertia-vue3';
import Pagination from '../Components/Pagination';
import { pickBy, throttle } from 'lodash';

export default {
  components: {
    Pagination,BreezeAuthenticatedLayout,Head
  },
  props: {
    orders: Object,
    filters: Object,
    newOrders: Number,
    ConfirmOrders: Number,
    InvoiceOrders: Number,
  },
  data() {
    return {
      params: {
        szukaj: this.filters.szukaj,
        pole: this.filters.pole,
        sortowanie: this.filters.sortowanie,
      },
    };
  },
 
  methods: {
    sort(field) {
      this.params.pole = field;
      this.params.sortowanie = this.params.sortowanie === 'asc' ? 'desc' : 'asc';
    },
      colorStatus(value) {
          switch (value) {
              case 'Nowe':
                  return 'bg-green-200'
                  break;
              case 'Potwierdzone':
                  return 'bg-blue-200'
                  break;
              case 'Zafakturowane':
                  return 'bg-red-200'
                  break;
          }
      }
  },
  watch: {
    params: {
      handler: throttle(function () {
        let params = pickBy(this.params);
        this.$inertia.get(this.route('orders'), params, { replace: true, preserveState: true });
      }, 150),
      deep: true,
    },
  },
};
</script>


<template>
    <Head title="Zamówienia" />
    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Zamówienia
            </h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="lg:text-center">
                                <h2 class="text-base text-indigo-600 font-semibold tracking-wide uppercase">Zamówienia</h2>
                                <p>Ilość wszytskich {{orders.total}}
                                    <span class="inset-0 opacity-50 rounded-full bg-green-200 p-1 mr-2">
                                    {{newOrders}}
                                    </span>
                                    <span class="inset-0 opacity-50 rounded-full bg-blue-200 p-1 mr-2">
                                    {{ConfirmOrders}}
                                    </span>
                                    <span class="inset-0 opacity-50 rounded-full bg-red-200 p-1 mr-2">
                                    {{InvoiceOrders}}
                                    </span>

                                </p>
                            </div>
                        </div>
                        <div class="flex items-center justify-end pb-6">
                            <input type="search" v-model="params.szukaj" aria-label="Search" class="bg-gray-50 rounded-lg border-2 hover:bg-indigo-50 outline-none border-indigo-300 block focus:outline-none  focus:ring focus:border-indigo-400 focus:ring-indigo-100" name="szukaj" id="search" placeholder="Szukaj produktów...">
                        </div>
                        <div>
                            <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
                                <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                                    <table class="min-w-full leading-normal">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="hover:cursor-pointer px-5 py-3 border-b-2 border-indigo-200 bg-indigo-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                    <span class="inline-flex py-3 px-6 w-full justify-between" @click="sort('id')">Id
                                                        <svg v-if="params.pole === 'id' && params.sortowanie === 'asc'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M3 3a1 1 0 000 2h11a1 1 0 100-2H3zM3 7a1 1 0 000 2h5a1 1 0 000-2H3zM3 11a1 1 0 100 2h4a1 1 0 100-2H3zM13 16a1 1 0 102 0v-5.586l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 101.414 1.414L13 10.414V16z"/>
                                                        </svg>
                                
                                                        <svg v-if="params.pole === 'id' && params.sortowanie === 'desc'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M3 3a1 1 0 000 2h11a1 1 0 100-2H3zM3 7a1 1 0 000 2h7a1 1 0 100-2H3zM3 11a1 1 0 100 2h4a1 1 0 100-2H3zM15 8a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L15 13.586V8z"/>
                                                        </svg>
                                                    </span>
                                                </th>
                                                <th
                                                    class="px-5 py-3 border-b-2 border-indigo-200 bg-indigo-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                    Zamówienie
                                                </th>
                                                <th scope="col" class="hover:cursor-pointer px-5 py-3 border-b-2 border-indigo-200 bg-indigo-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                    <span class="inline-flex py-3 px-6 w-full justify-between" @click="sort('date_of_issue')">Data pobranie
                                                        <svg v-if="params.pole === 'date_of_issue' && params.sortowanie === 'asc'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M3 3a1 1 0 000 2h11a1 1 0 100-2H3zM3 7a1 1 0 000 2h5a1 1 0 000-2H3zM3 11a1 1 0 100 2h4a1 1 0 100-2H3zM13 16a1 1 0 102 0v-5.586l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 101.414 1.414L13 10.414V16z"/>
                                                        </svg>
                                
                                                        <svg v-if="params.pole === 'date_of_issue' && params.sortowanie === 'desc'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M3 3a1 1 0 000 2h11a1 1 0 100-2H3zM3 7a1 1 0 000 2h7a1 1 0 100-2H3zM3 11a1 1 0 100 2h4a1 1 0 100-2H3zM15 8a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L15 13.586V8z"/>
                                                        </svg>
                                                    </span>
                                                </th>
                                                <th
                                                    class="px-5 py-3 border-b-2 border-indigo-200 bg-indigo-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                    Ilość produktów
                                                </th>
                                                <th
                                                    class="px-5 py-3 border-b-2 border-indigo-200 bg-indigo-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                    Status
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="order in orders.data" :key="order.id">
                                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{order.id}}</td>
                                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                    <div class="flex items-center">
                                                            <div class="ml-3">
                                                                <p class="text-gray-900 whitespace-no-wrap">
                                                                        {{ order.order_number }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                </td>
                              
                                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                    <p class="text-gray-900 whitespace-no-wrap">
                                                        {{ order.date_of_issue }}
                                                    </p>
                                                </td>
                                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                    <p class="text-gray-900 whitespace-no-wrap">
                                                        {{order.products.length}}
                                                    </p>
                                                </td>
                                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                    <span
                                                        class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight">
                                                        <span aria-hidden
                                                            :class="'absolute inset-0 opacity-50 rounded-full ' +  colorStatus(order.status) "></span>
                                                    <span class="relative">{{order.status}}</span>
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <Pagination class="my-10 flex justify-center" :links="orders.links"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
