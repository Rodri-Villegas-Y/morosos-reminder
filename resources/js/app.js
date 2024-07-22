import '../css/app.css'
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import PrimeVue from 'primevue/config'
import { ZiggyVue } from '../../vendor/tightenco/ziggy'
import Tooltip from 'primevue/tooltip'

import ToastService from 'primevue/toastservice';
import ConfirmationService from 'primevue/confirmationservice';

createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    return pages[`./Pages/${name}.vue`]
  },
  title: title => title ? `${title} - Morosos Reminder` : 'Morosos Reminder',
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(PrimeVue)
      .use(ToastService)
      .use(ConfirmationService)
      .use(ZiggyVue)
      .directive('tooltip', Tooltip)
      .mount(el)
  },
})
