/*jshint esversion: 6 */

/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import VueRouter from 'vue-router';
import VueSocketio from 'vue-socket.io';

Vue.use(VueRouter);

Vue.use(VueSocketio, 'http://127.0.0.1:8080');

const user = Vue.component('user', require('./components/user.vue'));
const login = Vue.component('login', require('./components/login.vue'));
const account = Vue.component('account', require('./components/account.vue'));
const statistics = Vue.component('statistics', require('./components/statistics.vue'));
const multiplayerGame = Vue.component('multiplayergame', require('./components/multiplayer_blackjack.vue'));

const routes = [
  { path: '/', redirect: '/login' },
  { path: '/login', component: login },
  { path: '/users', component: user },
  { path: '/account', component: account },
  { path: '/statistics', component: statistics },
  { path: '/multiblackjack', component: multiplayerGame }
];

const router = new VueRouter({
  routes:routes
});

const app = new Vue({
  router,
  data:{
    isAdmin: false,
    isAuthenticated: false,
    own: { nickname: 'none' },
	  playersList: []
  }
}).$mount('#app');

