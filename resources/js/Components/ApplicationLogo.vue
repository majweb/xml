
<template>
<div>
    <h2 :class="'font-bold text-indigo-500 text-center' + ( route().current('dashboard') || route().current('orders') ? ' text-1xl' : ' text-4xl')">NEUCA</h2>
     <p class="text-center text-gray-500 font-bold">
     zamówienia
     </p>
</div>
</template>
