import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createPinia } from 'pinia';
import { ApiClient } from '@/lib/axios-client';

const pages = import.meta.glob<Record<string, any>>('./pages/**/*.vue');
const pinia = createPinia();

createInertiaApp({
    resolve: (name: string) => resolvePageComponent<DefineComponent>(
        `./pages/${name}.vue`,
        Object.fromEntries(
          Object.entries(pages).map(([key, resolver]) => [
            key,
            async () => {
              const mod = (await resolver()) as { default: DefineComponent };
              return mod.default;
            },
          ])
        )
      ),
    setup({ el, App, props, plugin }) {
      const app = createApp({ render: () => h(App, props) });
      app.use(plugin);
      app.use(pinia);

      // Initialize token from localStorage on app startup
      const token = localStorage.getItem('api_token');
      if (token) {
        ApiClient.setToken(token);
      }

      app.mount(el);
    },
});
