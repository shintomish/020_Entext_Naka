// import './bootstrap';
require('./bootstrap');

// import Vue from 'vue';
window.Vue = require('vue').default;

var app = new Vue({
    el: '#app',
});