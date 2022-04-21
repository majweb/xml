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
    order: Object,
    invoices: Object,
    filters: String,
  },

    data() {
    return {
      params: {
        szukaj: this.filters,
      },
      selected:null,
      
      form: this.$inertia.form({
        pdf: null
      }),
    };
  },
  methods: {
      saveSelection(invoice){
          this.selected = invoice;
      },
      removeSelection(){
          this.selected = null;
      },
      submit(){
            this.form.transform((data) => (
                {
                ...data,
                invoice:this.selected
                }
            ))
            .post(route('orders.single.invoice.post',this.order),
                {
                onSuccess: () => {
                    form.reset();
                    this.selected = null;
                }
        }
        )
      }
  },
    watch: {
    params: {
      handler: throttle(function () {
        let params = pickBy(this.params);
        this.$inertia.get(this.route('orders.single.invoice',this.order), params, { replace: true, preserveState: true });
      }, 150),
      deep: true,
    },
  },

};
</script>


<template>
    <Head title="Faktura" />
    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Faktura do zamówienia {{order.id}}
            </h2>
        </template>
        <div class="py-12">
        <div v-if="$page.props.flash.message">
            <Notification :message="$page.props.flash.message" />
        </div>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex items-center justify-between pb-6">
                            <Link :href="route('orders.single',order)" class="px-4 py-2 mb-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-600 focus:outline-none focus:border-indigo-600 focus:shadow-outline-gray transition ease-in-out duration-150 mr-2">Powrót</Link>
                            <p v-if="selected" class="bg-indigo-100 p-3 text-center rounded-md">Obecnie wybrany numer faktury: {{selected.dok_NrPelny}}</p>
                            <input v-if="!order.invoice" type="search" v-model="params.szukaj" aria-label="Search" class="bg-gray-50 rounded-lg border-2 hover:bg-indigo-50 outline-none border-indigo-300 block focus:outline-none  focus:ring focus:border-indigo-400 focus:ring-indigo-100" name="szukaj" id="search" placeholder="Szukaj faktury">
                        </div>
                        <div v-if="selected" class="flex items-center justify-center mt-2 pb-6 flex-column">
                            <hr>
                            <form enctype="multipart/form-data" @submit.prevent="submit">
                                <input type="file" @input="form.pdf = $event.target.files[0]" />
                                <button class="px-4 py-2 mb-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-600 focus:outline-none focus:border-green-600 focus:shadow-outline-gray transition ease-in-out duration-150 mr-2" type="submit" :loading="form.processing" :disabled="form.processing">Wyślij fakture {{form.progress ? form.progress.percentage + '%...'  :null}}</button>
                                <p class="bg-red-400 my-2 p-2 text-center text-white" v-if="form.errors.pdf">{{ form.errors.pdf }}</p>
                            </form>
                        </div>
                        <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
                            <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                                <div v-if="!order.invoice">
                                    <p v-if="invoices === null || invoices.length == 0" class="rounded-md bg-indigo-100 p-5 text-center w-1/3 mx-auto my-4">Brak faktury lub brak szukanej frazy</p>
                                    <div v-else>
                                        <table class="min-w-full leading-normal">
                                            <thead>
                                                <tr>
                                                    <th scope="col" class="hover:cursor-pointer px-5 py-3 border-b-2 border-indigo-200 bg-indigo-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                        <span class="inline-flex py-3 px-6 w-full justify-between">
                                                            Id
                                                        </span>
                                                    </th>
                                                    <th
                                                        class="px-5 py-3 border-b-2 border-indigo-200 bg-indigo-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                        Nr. Faktury
                                                    </th>
                                                    <th
                                                        class="px-5 py-3 border-b-2 border-indigo-200 bg-indigo-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                        Wartość zamowienie Netto
                                                    </th>
                                                    <th
                                                        class="px-5 py-3 border-b-2 border-indigo-200 bg-indigo-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                        Wartość zamowienie Brutto
                                                    </th>
                                                    <th
                                                        class="px-5 py-3 border-b-2 border-indigo-200 bg-indigo-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                        Ilość produktów
                                                    </th>
                                                    <th
                                                        class="px-5 py-3 border-b-2 border-indigo-200 bg-indigo-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                        Akcja
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-for="(invoice,index) in invoices.data" :key="index">
                                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{invoice.dok_Id ?? null}}</td>
                                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                        <div class="flex items-center">
                                                                <div class="ml-3">
                                                                    <p class="text-gray-900 whitespace-no-wrap">
                                                                        {{index}}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                    </td>
                                    
                                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                        <p class="text-gray-900 whitespace-no-wrap">
                                                            {{invoices.data[index].ob_WartNetto.toFixed(3)}} zł
                                                        </p>
                                                    </td>
                                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                        <p class="text-gray-900 whitespace-no-wrap">
                                                            {{invoices.data[index].ob_WartBrutto.toFixed(3)}} zł
                                                        </p>
                                                    </td>
                                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                        <p class="text-gray-900 whitespace-no-wrap">
                                                            {{invoice.reszta.length}}
                                                        </p>
                                                    </td>
                                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                        <button v-if="selected == null" @click="saveSelection(invoice)" class="px-4 py-2 mb-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-600 focus:outline-none focus:border-green-600 focus:shadow-outline-gray transition ease-in-out duration-150 mr-2">Wybierz</button>
                                                        <button v-else-if="selected.dok_Id === invoice.dok_Id" @click="removeSelection" class="px-4 py-2 mb-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-600 focus:outline-none focus:border-red-600 focus:shadow-outline-gray transition ease-in-out duration-150 mr-2">Usuń</button>
                                                        <button v-else-if="selected.dok_Id !== invoice.dok_Id" class="px-4 py-2 mb-2 bg-gray-600 border opacity-10 border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-600 focus:outline-none focus:border-gray-600 focus:shadow-outline-gray transition ease-in-out duration-150 mr-2" disabled>Usuń</button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <Pagination v-if="!selected" class="my-10 flex justify-center" :links="invoices.links" />
                                    </div>
                                </div>
                                <div v-else>
                                    <dl
                                    class="my-5
                                        space-y-10
                                        md:space-y-0 md:grid md:grid-cols-3 md:gap-x-8 md:gap-y-10
                                    "
                                    >
                                    <div class="relative">
                                        <dt>
                                        <p class="ml-16 text-lg leading-6 font-medium text-gray-900">
                                            Id
                                        </p>
                                        </dt>
                                        <dd class="mt-2 ml-16 text-base text-gray-500">
                                        {{order.invoice.dok_id}}
                                        </dd>
                                    </div>

                                    <div class="relative">
                                        <dt>
                                        <p class="ml-16 text-lg leading-6 font-medium text-gray-900">
                                        Nr. Faktury
                                        </p>
                                        </dt>
                                        <dd class="mt-2 ml-16 text-base text-gray-500">
                                        {{order.invoice.nr_pelny_oryg}}
                                        </dd>
                                    </div>
                                    <div class="relative">
                                        <dt>
                                        <p class="ml-16 text-lg leading-6 font-medium text-gray-900">
                                            Wartość zamowienie Netto
                                        </p>
                                        </dt>
                                        <dd class="mt-2 ml-16 text-base text-gray-500">
                                        {{order.invoice.wartosc_netto}} zł
                                        </dd>
                                    </div>

                                    <div class="relative">
                                        <dt>
                                        <p class="ml-16 text-lg leading-6 font-medium text-gray-900">
                                            Wartość zamowienie Brutto
                                        </p>
                                        </dt>
                                        <dd class="mt-2 ml-16 text-base text-gray-500">
                                        {{order.invoice.wartosc_brutto}} zł
                                        </dd>
                                    </div>
                                    <div class="relative">
                                        <dt>
                                        <p class="ml-16 text-lg leading-6 font-medium text-gray-900">
                                            Ilość produktów
                                        </p>
                                        </dt>
                                        <dd class="mt-2 ml-16 text-base text-gray-500">
                                        {{order.invoice.ilosc_produktow}}
                                        </dd>
                                    </div>

                                    <div class="relative" v-if="order.invoice.faktura">
                                        <dt>
                                        <p class="ml-16 text-lg leading-6 font-medium text-gray-900">
                        
                                        <a :href="route('orders.single.invoice.show',order)" class="px-4 py-2 mb-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-600 focus:outline-none focus:border-indigo-600 focus:shadow-outline-gray transition ease-in-out duration-150 mr-2">Pobierz fakturę</a>
                                        </p>
                                        </dt>
                                    </div>
                                    </dl>
                                </div>
                            </div>
                        </div>  
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
