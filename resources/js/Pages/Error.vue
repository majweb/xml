<template>
  <div>
    <H1>{{ title }}</H1>
    <div>{{ description }}</div>
  </div>
</template>

<script>
      export default {
      props: {
      status: Number,
      },
      computed: {
      title() {
            return {
            503: '503: Serwis niedostępny',
            500: '500: Błąd serwera',
            404: '404: Strona nie znaleziona',
            403: '403: Zakazana',
            }[this.status]
      },
      description() {
            return {
            503: 'Przepraszamy, wykonujemy pewne prace konserwacyjne. Sprawdź ponownie wkrótce.',
            500: 'Ups, coś poszło nie tak na naszych serwerach.',
            404: 'Przepraszamy, nie można znaleźć strony, której szukasz.',
            403: 'Przepraszamy, nie masz dostępu do tej strony.',
            }[this.status]
      },
      },
      }
</script>