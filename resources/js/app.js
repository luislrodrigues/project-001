import './bootstrap';
import 'select2/dist/css/select2.css';
import '../css/app.css';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from './router';
import App from './App.vue';

const app = createApp(App);

app.use(createPinia());
app.use(router);

app.mount('#app');
