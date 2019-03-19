import Vue from 'vue'
import App from './App'
import router from './router'
import fastClick from 'fastclick'
import store from './store'
import 'babel-polyfill'
import '@/assets/css/reset.sim.css'
import '@/assets/css/border.css'
import '@/assets/css/iconfont.css'
import apiurl from '@/assets/js/apiurl.config.js'
import axios from 'axios'
import { Menu, Submenu, MenuItem, Progress, Popover, Container, Aside, Header, Main, Input,
  InputNumber, ButtonGroup, Button, Checkbox, CheckboxButton, CheckboxGroup, Loading, Dialog, Transfer,
  Form, FormItem, Card, Row, Col, Switch, Tag, Table, TableColumn, Autocomplete, Message, Scrollbar } from 'element-ui'

Vue.config.productionTip = false
Vue.prototype.$apis = apiurl
Vue.prototype.$axios = axios
fastClick.attach(document.body)
Vue.prototype.$message = Message
Vue.use(Menu)
Vue.use(Submenu)
Vue.use(MenuItem)
Vue.use(Progress)
Vue.use(Popover)
Vue.use(Container)
Vue.use(Aside)
Vue.use(Header)
Vue.use(Main)
Vue.use(Form)
Vue.use(FormItem)
Vue.use(Input)
Vue.use(InputNumber)
Vue.use(Button)
Vue.use(ButtonGroup)
Vue.use(Checkbox)
Vue.use(CheckboxButton)
Vue.use(CheckboxGroup)
Vue.use(Autocomplete)
Vue.use(Card)
Vue.use(Row)
Vue.use(Col)
Vue.use(Switch)
Vue.use(Tag)
Vue.use(Table)
Vue.use(TableColumn)
Vue.use(Loading.directive)
Vue.use(Scrollbar)
Vue.use(Dialog)
Vue.use(Transfer)

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  store,
  components: { App },
  template: '<App/>'
})
