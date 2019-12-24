Nova.booting((Vue, router, store) => {
  router.addRoutes([
    {
      name: 'nova-opcache',
      path: '/nova-opcache',
      component: require('./components/Tool'),
    },
  ])
})
