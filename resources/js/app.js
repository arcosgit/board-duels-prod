import './bootstrap';
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
function truncate(str, number, symbol = ''){
   return str.length > number ? str.substring(0, number) + symbol : str
};
createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    return pages[`./Pages/${name}.vue`]
  },
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) });
    app.config.globalProperties.$truncate = truncate;
    app.use(plugin)
      .use(ZiggyVue)
      .mount(el);
  },
  progress:{
    color: '#1e1e1e'
  }
})
