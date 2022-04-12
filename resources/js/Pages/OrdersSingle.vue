	<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import { Head,Link } from "@inertiajs/inertia-vue3";
import BreezeButton from "@/Components/Button";
import BreezeInput from "@/Components/Input";
import { pickBy, throttle } from "lodash";

export default {
  components: {
    BreezeAuthenticatedLayout,
    Head,
		BreezeButton,
		BreezeInput,
    Link
  },
  props: {
    order: Object,
    orginal: Object,
    filters: Object,
  },
  data() {
    return {
      params: {
        pole: this.filters.pole,
        sortowanie: this.filters.sortowanie,
      },
      form: this.$inertia.form({
        products: this.order.products,
      }),
    };
  },

  methods: {
    back() {
      window.history.back();
    },

    sort(field) {
      this.params.pole = field;
      this.params.sortowanie =
        this.params.sortowanie === "asc" ? "desc" : "asc";
    },
    colorStatus(value) {
      switch (value) {
        case "Nowe":
          return "text-green-200";
          break;
        case "Potwierdzone":
          return "text-blue-200";
          break;
        case "Zafakturowane":
          return "text-red-200";
          break;
      }
    },
    submit() {
      this.$inertia.patch(
        `/zamowienie/aktualizacja/${this.order.id}`,
        this.order.products
      );
    },
    zero() {
      this.form.products = this.order.products.map((el) => {
        return {
          id: el.id,
          line_number: el.line_number,
          unit_of_measure: el.unit_of_measure,
          item_description: el.item_description,
          ordered_quantity: el.ordered_quantity,
          ordered_quantity_updated: 0,
        };
      });
    },
    maximum() {
      this.form.products = this.orginal.products.map((el) => {
        return {
          id: el.id,
          line_number: el.line_number,
          unit_of_measure: el.unit_of_measure,
          item_description: el.item_description,
          ordered_quantity: el.ordered_quantity,
          ordered_quantity_updated: el.ordered_quantity_updated,
        };
      });
    },
  },
  watch: {
    params: {
      handler: throttle(function () {
        let params = pickBy(this.params);
        this.$inertia.get(this.route("orders.single", this.order), params, {
          replace: true,
        });
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
      <h2 class="font-semibold text-xl text-gray-500 leading-tight">
        Zamówienia {{ order.order_number }}
      </h2>
    </template>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="flex justify-between items-center">
            <a role="button"
            @click="back" class="inline-flex items-center px-4 py-2 mb-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-600 focus:outline-none focus:border-indigo-600 focus:shadow-outline-gray transition ease-in-out duration-150 mr-2">Powrót do listy</a>
            <Link v-if="(order.date_of_return && order.status != 'Nowe')" :href="route('orders.single.invoice',order)" class="px-4 py-2 mb-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-600 focus:outline-none focus:border-indigo-600 focus:shadow-outline-gray transition ease-in-out duration-150 mr-2">Faktura</Link>
          </div>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="my-5">
            <dl
              class="
                space-y-10
                md:space-y-0 md:grid md:grid-cols-4 md:gap-x-8 md:gap-y-10
              "
            >
              <div class="relative">
                <dt>
                  <p class="ml-16 text-lg leading-6 font-medium text-gray-900">
                    Numer zamówienia
                  </p>
                </dt>
                <dd class="mt-2 ml-16 text-base text-gray-500">
                  {{ order.order_number }}
                </dd>
              </div>

              <div class="relative">
                <dt>
                  <p class="ml-16 text-lg leading-6 font-medium text-gray-900">
                    Data pobrania zamówienia
                  </p>
                </dt>
                <dd class="mt-2 ml-16 text-base text-gray-500">
                  {{ order.date_of_issue }}
                </dd>
              </div>
              <div class="relative">
                <dt>
                  <p class="ml-16 text-lg leading-6 font-medium text-gray-900">
                    Data zamówienia
                  </p>
                </dt>
                <dd class="mt-2 ml-16 text-base text-gray-500">
                  {{ order.order_date }}
                </dd>
              </div>

              <div class="relative">
                <dt>
                  <p class="ml-16 text-lg leading-6 font-medium text-gray-900">
                    Kupujący ILN
                  </p>
                </dt>
                <dd class="mt-2 ml-16 text-base text-gray-500">
                  {{ order.buyer_iln }}
                </dd>
              </div>
              <div class="relative">
                <dt>
                  <p class="ml-16 text-lg leading-6 font-medium text-gray-900">
                    Ilość produktów
                  </p>
                </dt>
                <dd class="mt-2 ml-16 text-base text-gray-500">
                  {{ order.products_count }}
                </dd>
              </div>

              <div class="relative">
                <dt>
                  <p class="ml-16 text-lg leading-6 font-medium text-gray-900">
                    Status
                  </p>
                </dt>
                <dd
                  :class="'mt-2 ml-16 text-base ' + colorStatus(order.status)"
                >
                  {{ order.status }}
                </dd>
              </div>
              <div class="relative" v-if="(order.date_of_return && order.status != 'Nowe')">
                <dt>
                  <p class="ml-16 text-lg leading-6 font-medium text-gray-900">
                    Data potwierdzenia
                  </p>
                </dt>
                <dd
                  class="mt-2 ml-16 text-base"
                >
                  {{ order.date_of_return }}
                </dd>
              </div>
              <div class="relative" v-if="order.date_of_invoice && order.status == 'Zafakturowane'">
                <dt>
                  <p class="ml-16 text-lg leading-6 font-medium text-gray-900">
                    Data faktury
                  </p>
                </dt>
                <dd
                  class="mt-2 ml-16 text-base"
                >
                  {{ order.date_of_invoice }}

                </dd>
              </div>
            </dl>
          </div>
          <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
            <div
              class="inline-block min-w-full shadow rounded-lg overflow-hidden"
            >
              <form
                @submit.prevent="
                  form.patch(route('orders.single.update', { id: order.id }))
                "
              >
                <table class="min-w-full leading-normal">
                  <thead>
                    <tr>
                      <th
                        scope="col"
                        class="
                          hover:cursor-pointer
                          px-5
                          py-3
                          border-b-2 border-indigo-200
                          bg-indigo-100
                          text-left text-xs
                          font-semibold
                          text-gray-600
                          uppercase
                          tracking-wider
                        "
                      >
                        <span
                          class="inline-flex py-3 px-6 w-full justify-between"
                          @click="sort('line_number')"
                          >Numer
                          <svg
                            v-if="
                              params.pole === 'line_number' &&
                              params.sortowanie === 'asc'
                            "
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              d="M3 3a1 1 0 000 2h11a1 1 0 100-2H3zM3 7a1 1 0 000 2h5a1 1 0 000-2H3zM3 11a1 1 0 100 2h4a1 1 0 100-2H3zM13 16a1 1 0 102 0v-5.586l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 101.414 1.414L13 10.414V16z"
                            />
                          </svg>

                          <svg
                            v-if="
                              params.pole === 'line_number' &&
                              params.sortowanie === 'desc'
                            "
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              d="M3 3a1 1 0 000 2h11a1 1 0 100-2H3zM3 7a1 1 0 000 2h7a1 1 0 100-2H3zM3 11a1 1 0 100 2h4a1 1 0 100-2H3zM15 8a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L15 13.586V8z"
                            />
                          </svg>
                        </span>
                      </th>
                      <th
                        class="
                          px-5
                          py-3
                          border-b-2 border-indigo-200
                          bg-indigo-100
                          text-left text-xs
                          font-semibold
                          text-gray-600
                          uppercase
                          tracking-wider
                        "
                      >
                        EAN
                      </th>
                      <th
                        class="
                          px-5
                          py-3
                          border-b-2 border-indigo-200
                          bg-indigo-100
                          text-left text-xs
                          font-semibold
                          text-gray-600
                          uppercase
                          tracking-wider
                        "
                      >
                        Nazwa
                      </th>
                      <th
                        class="
                          px-5
                          py-3
                          border-b-2 border-indigo-200
                          bg-indigo-100
                          text-left text-xs
                          font-semibold
                          text-gray-600
                          uppercase
                          tracking-wider
                        "
                      >
                        Jm.
                      </th>
                      <th
                        scope="col"
                        class="
                          hover:cursor-pointer
                          px-5
                          py-3
                          border-b-2 border-indigo-200
                          bg-indigo-100
                          text-left text-xs
                          font-semibold
                          text-gray-600
                          uppercase
                          tracking-wider
                        "
                      >
                        <span
                          class="inline-flex py-3 px-6 w-full justify-between"
                          @click="sort('ordered_quantity')"
                          >Ilość
                          <svg
                            v-if="
                              params.pole === 'ordered_quantity' &&
                              params.sortowanie === 'asc'
                            "
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              d="M3 3a1 1 0 000 2h11a1 1 0 100-2H3zM3 7a1 1 0 000 2h5a1 1 0 000-2H3zM3 11a1 1 0 100 2h4a1 1 0 100-2H3zM13 16a1 1 0 102 0v-5.586l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 101.414 1.414L13 10.414V16z"
                            />
                          </svg>

                          <svg
                            v-if="
                              params.pole === 'ordered_quantity' &&
                              params.sortowanie === 'desc'
                            "
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              d="M3 3a1 1 0 000 2h11a1 1 0 100-2H3zM3 7a1 1 0 000 2h7a1 1 0 100-2H3zM3 11a1 1 0 100 2h4a1 1 0 100-2H3zM15 8a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L15 13.586V8z"
                            />
                          </svg>
                        </span>
                      </th>
                      <th
                        class="
                          px-5
                          py-3
                          border-b-2 border-indigo-200
                          bg-indigo-100
                          text-left text-xs
                          font-semibold
                          text-gray-600
                          uppercase
                          tracking-wider
                        "
                      >
                        Aktualizacja
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="(product, index) in form.products"
                      :key="product.id"
                    >
                      <td
                        class="
                          px-5
                          py-5
                          border-b border-gray-200
                          bg-white
                          text-sm
                        "
                      >
                        {{ product.line_number }}
                      </td>
                      <td
                        class="
                          px-5
                          py-5
                          border-b border-gray-200
                          bg-white
                          text-sm
                        "
                      >
                        {{ product.ean }}
                      </td>
                      <td
                        class="
                          px-5
                          py-5
                          border-b border-gray-200
                          bg-white
                          text-sm
                        "
                      >
                        <div class="flex items-center">
                          <div class="ml-3">
                            <p class="text-gray-900 whitespace-no-wrap">
                              {{ product.item_description }}
                            </p>
                          </div>
                        </div>
                      </td>

                      <td
                        class="
                          px-5
                          py-5
                          border-b border-gray-200
                          bg-white
                          text-sm
                        "
                      >
                        <p class="text-gray-900 whitespace-no-wrap">
                          {{ product.unit_of_measure }}
                        </p>
                      </td>
                      <td
                        class="
                          px-5
                          py-5
                          border-b border-gray-200
                          bg-white
                          text-sm
                        "
                      >
                        <p class="text-gray-900 whitespace-no-wrap">
                          {{ product.ordered_quantity }}
                        </p>
                      </td>
                      <td
                        class="
                          px-5
                          py-5
                          border-b border-gray-200
                          bg-white
                          text-sm
                        "
                      >
                        <span class="text-gray-900 whitespace-no-wrap flex flex-col">
                          <BreezeInput v-if="order.status == 'Nowe'"
                            v-model="form.products[index].ordered_quantity_updated"
                            type="number"
                            min="0"
                            :max="product.ordered_quantity"
                            name="products"
                            :id="`products${product.id}`"
                            class="
                              appearance-none
                              transition
                              duration-200
                              mt-1
                              align-top
                              float-left
                              mr-2
                              cursor-pointer
                            "
                          />
                          <span v-else
                            class="
                              mt-1
                              align-top
                              float-left
                              mr-2
                            "
                          >
													{{form.products[index].ordered_quantity_updated}}
													</span>
                          <p class="text-sm mt-1 text-red-600" v-if="form.errors[`products.${index}.ordered_quantity_updated`]">{{form.errors[`products.${index}.ordered_quantity_updated`]}}</p>
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </table>
								<div class="flex justify-end mt-4 mr-2" v-if="order.status == 'Nowe'">
									<BreezeButton class="bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-500 focus:outline-none focus:border-indigo-500 focus:shadow-outline-gray transition ease-in-out duration-150" type="submit" :disabled="form.processing">
										Wyślij/Zmień
									</BreezeButton>
								</div>
              </form>
						<div class="flex justify-end my-4 mr-2" v-if="order.status == 'Nowe'">
              <BreezeButton class="bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:shadow-outline-gray transition ease-in-out duration-150 mr-2"  @click="zero">Resetuj</BreezeButton>
              <BreezeButton class="bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:shadow-outline-gray transition ease-in-out duration-150" @click="maximum">Maksimum</BreezeButton>
            </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </BreezeAuthenticatedLayout>
</template>
