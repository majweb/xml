<script setup>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import { Head, Link } from "@inertiajs/inertia-vue3";
import Notification from "../Components/Notification";

// onMounted(async () => {
//       let response = await axios.get(route('orders.get'))
// });
</script>

<template>
  <Head title="Panel" />
  <BreezeAuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Panel</h2>
    </template>
    <div v-if="$page.props.flash.message">
      <Notification :message="$page.props.flash.message" />
    </div>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
              <div class="lg:text-center">
                <h2
                  class="
                    text-base text-indigo-600
                    font-semibold
                    tracking-wide
                    uppercase
                  "
                >
                  Zamówienia
                </h2>
                <p
                  class="
                    mt-2
                    text-3xl
                    leading-8
                    font-extrabold
                    tracking-tight
                    text-gray-900
                    sm:text-4xl
                  "
                >
                  Statystyki
                </p>
              </div>
              <div class="mt-10">
                <dl
                  class="
                    space-y-10
                    md:space-y-0 md:grid md:grid-cols-3 md:gap-x-8 md:gap-y-10
                  "
                >
                  <div class="relative">
                    <dt>
                      <div
                        class="
                          absolute
                          flex
                          items-center
                          justify-center
                          h-12
                          w-12
                          rounded-md
                          bg-indigo-500
                          text-white
                        "
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-6 w-6"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke="currentColor"
                          stroke-width="2"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 4v16m8-8H4"
                          />
                        </svg>
                      </div>
                      <p
                        class="
                          ml-16
                          text-lg
                          leading-6
                          font-medium
                          text-gray-900
                        "
                      >
                        Nowe zamówienia
                      </p>
                    </dt>
                    <dd
                      class="
                        mt-2
                        ml-16
                        text-base text-gray-500
                        font-bold
                        bg-indigo-50
                        border-slate-50
                        text-center
                        py-1
                      "
                    >
                      {{ $page.props.newOrders }}
                    </dd>
                  </div>
                  <div class="relative">
                    <dt>
                      <div
                        class="
                          absolute
                          flex
                          items-center
                          justify-center
                          h-12
                          w-12
                          rounded-md
                          bg-indigo-500
                          text-white
                        "
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-6 w-6"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke="currentColor"
                          stroke-width="2"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"
                          />
                        </svg>
                      </div>
                      <p
                        class="
                          ml-16
                          text-lg
                          leading-6
                          font-medium
                          text-gray-900
                        "
                      >
                        Potwierdzone
                      </p>
                    </dt>
                    <dd
                      class="
                        mt-2
                        ml-16
                        text-base text-gray-500
                        font-bold
                        bg-indigo-50
                        border-slate-50
                        text-center
                        py-1
                      "
                    >
                      {{ $page.props.ConfirmOrders }}
                    </dd>
                  </div>

                  <div class="relative">
                    <dt>
                      <div
                        class="
                          absolute
                          flex
                          items-center
                          justify-center
                          h-12
                          w-12
                          rounded-md
                          bg-indigo-500
                          text-white
                        "
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-6 w-6"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke="currentColor"
                          stroke-width="2"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M5 13l4 4L19 7"
                          />
                        </svg>
                      </div>
                      <p
                        class="
                          ml-16
                          text-lg
                          leading-6
                          font-medium
                          text-gray-900
                        "
                      >
                        Zafakturowane
                      </p>
                    </dt>
                    <dd
                      class="
                        mt-2
                        ml-16
                        text-base text-gray-500
                        font-bold
                        bg-indigo-50
                        border-slate-50
                        text-center
                        py-1
                      "
                    >
                      {{ $page.props.InvoiceOrders }}
                    </dd>
                  </div>
                </dl>
              </div>
            </div>
            <div class="flex justify-center items-center mt-3">
              <Link
                v-if="$page.props.auth.user"
                :href="route('orders')"
                class="
                  mr-2
                  text-sm
                  hover:text-indigo-700
                  transition
                  duration-75
                  text-gray-700
                  underline
                  text-center
                "
              >
                Zamówienia
              </Link>
              <Link
                :href="route('orders.get')"
                method="get"
                as="button"
                type="button"
                class="
                  p-2
                  bg-indigo-600
                  border border-transparent
                  rounded-md
                  font-semibold
                  text-xs text-white
                  uppercase
                  tracking-widest
                  hover:bg-indigo-700
                  active:bg-indigo-600
                  focus:outline-none
                  focus:border-indigo-600
                  focus:shadow-outline-gray
                  transition
                  ease-in-out
                  duration-150
                "
                >Sprawdź zamówienia</Link
              >
            </div>
            <div v-if="$page.props.newest.newest !== null || $page.props.newest.length > 0">
              <p v-if="$page.props.newest.newest && $page.props.newest.newest.length > 0" class="text-indigo-800 font-bold text-center mt-3">
                Nowo dodane
              </p>
              <div v-if="$page.props.newest.newest && $page.props.newest.newest.length > 0" class="flex flex-col justify-center items-center mt-3">
                <div
                  v-for="added in $page.props.newest.newest"
                  :key="added.id"
                  class="mb-2"
                >
                  {{ added.order_number }}
                  <Link
                    :href="route('orders.single', added.id)"
                    class="
                      mr-2
                      text-sm
                      hover:text-indigo-700
                      transition
                      duration-75
                      text-gray-700
                      underline
                      text-center
                    "
                  >
                    Przejdź
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </BreezeAuthenticatedLayout>
</template>
