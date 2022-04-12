<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head, Link } from '@inertiajs/inertia-vue3';
import Pagination from '../Components/Pagination';
import Notification from '@/Components/Notification.vue';
import { pickBy, throttle } from 'lodash';

export default {
  components: {
    Pagination,BreezeAuthenticatedLayout,Head,Link,Notification
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
        typ: this.filters.typ,
      },
    };
  },
 
  methods: {
    sort(field) {
      this.params.pole = field;
      this.params.sortowanie = this.params.sortowanie === 'asc' ? 'desc' : 'asc';
    },
    changeType(event) {
    let selected = event.target.value;
      this.params.typ = selected;
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
        <div v-if="$page.props.flash.message">
            <Notification :message="$page.props.flash.message" />
        </div>

            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="lg:text-center">
                                <h2 class="text-base text-indigo-600 font-semibold tracking-wide uppercase mb-3">Zamówienia</h2>
                                <p class="mb-2">Wszytskie {{orders.total}}
                                    <span class="inset-0 rounded-full bg-green-200 py-1 px-3 mr-2">
                                    Nowe <strong>{{newOrders}}</strong>
                                    </span>
                                    <span class="inset-0 rounded-full bg-blue-200 py-1 px-3 mr-2">
                                    Potwierdzone <strong>{{ConfirmOrders}}</strong>
                                    </span>
                                    <span class="inset-0 rounded-full bg-red-200 py-1 px-3 mr-2">
                                    Zafakturowane <strong>{{InvoiceOrders}}</strong>
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center justify-end pb-6">
                            <div class="form-check form-check-inline mr-2">
                                <input id="all" v-model="params.typ" @change="changeType($event)" checked value="Wszystkie" class="form-check-input form-check-input appearance-none rounded-full h-4 w-4 border border-gray-300 bg-white checked:bg-indigo-600 checked:border-indigo-600 focus:bg-indigo-600 checked:focus:border-indigo-600 checked:focus:bg-indigo-600 checked:focus:border-indigo-600 checked:focus:ring-indigo-600 transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2 cursor-pointer" type="radio" name="typ">
                                <label class="form-check-label inline-block text-gray-800" for="all">Wszystkie</label>
                            </div>
                            <div class="form-check form-check-inline mr-2">
                                <input id="new" v-model="params.typ" @change="changeType($event)" value="Nowe" class="form-check-input form-check-input appearance-none rounded-full h-4 w-4 border border-gray-300 bg-white checked:bg-indigo-600 checked:border-indigo-600 focus:bg-indigo-600 checked:focus:border-indigo-600 checked:focus:bg-indigo-600 checked:focus:border-indigo-600 checked:focus:ring-indigo-600 transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2 cursor-pointer" type="radio" name="typ" >
                                <label class="form-check-label inline-block text-gray-800" for="new">Nowe</label>
                            </div>
                            <div class="form-check form-check-inline mr-2">
                                <input id="issue" v-model="params.typ" @change="changeType($event)" value="Potwierdzone" class="form-check-input form-check-input appearance-none rounded-full h-4 w-4 border border-gray-300 bg-white checked:bg-indigo-600 checked:border-indigo-600 focus:bg-indigo-600 checked:focus:border-indigo-600 checked:focus:bg-indigo-600 checked:focus:border-indigo-600 checked:focus:ring-indigo-600 transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2 cursor-pointer" type="radio" name="typ" >
                                <label class="form-check-label inline-block text-gray-800" for="issue">Potwierdzone</label>
                            </div>
                            <div class="form-check form-check-inline mr-4">
                                <input id="invoice" v-model="params.typ" @change="changeType($event)" value="Zafakturowane" class="form-check-input form-check-input appearance-none rounded-full h-4 w-4 border border-gray-300 bg-white checked:bg-indigo-600 checked:border-indigo-600 focus:bg-indigo-600 checked:focus:border-indigo-600 checked:focus:bg-indigo-600 checked:focus:border-indigo-600 checked:focus:ring-indigo-600 transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2 cursor-pointer" type="radio" name="typ">
                                <label class="form-check-label inline-block text-gray-800" for="invoice">Zafakturowane</label>
                            </div>
                            <input type="search" v-model="params.szukaj" aria-label="Search" class="bg-gray-50 rounded-lg border-2 hover:bg-indigo-50 outline-none border-indigo-300 block focus:outline-none  focus:ring focus:border-indigo-400 focus:ring-indigo-100" name="szukaj" id="search" placeholder="Szukaj produktów...">
                        </div>
                        <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
                            <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                                <p v-if="orders.data.length === 0" class="rounded-md bg-indigo-100 p-5 text-center w-1/3 mx-auto my-4">Brak zamówień</p>
                                <div v-else>
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
                                                    Nr. Zamówienia
                                                </th>
                                                <th scope="col" class="hover:cursor-pointer px-5 py-3 border-b-2 border-indigo-200 bg-indigo-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                    <span class="inline-flex py-3 px-6 w-full justify-between" @click="sort('date_of_issue')">Data pobrania pliku
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
                                                    Data zamówienia
                                                </th>
                                                <th
                                                    class="px-5 py-3 border-b-2 border-indigo-200 bg-indigo-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                    Ilość produktów
                                                </th>
                                                <th
                                                    class="px-5 py-3 border-b-2 border-indigo-200 bg-indigo-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                    Status
                                                </th>
                                                <th
                                                    class="px-5 py-3 border-b-2 border-indigo-200 bg-indigo-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                    Akcja
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
                                                        {{order.order_date}}
                                                    </p>
                                                </td>
                                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                    <p class="text-gray-900 whitespace-no-wrap">
                                                        {{order.products_count}}
                                                    </p>
                                                </td>
                                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                    <span
                                                        class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight">
                                                        <span aria-hidden
                                                            :class="'absolute inset-0 rounded-full ' +  colorStatus(order.status) "></span>
                                                    <span class="relative">{{order.status}}</span>
                                                    </span>
                                                </td>
                                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                    <p class="text-gray-900 whitespace-no-wrap">
                                                                <Link :href="route('orders.single',order)" class="cursor-pointer text-sm hover:text-indigo-700 transition duration-75 text-gray-700 underline text-center flex justify-center">
                                                                Szczegóły
                                                            </Link>
                                                    </p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <Pagination class="my-10 flex justify-center" :links="orders.links" />
                                </div>
                            </div>
                        </div>  
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
