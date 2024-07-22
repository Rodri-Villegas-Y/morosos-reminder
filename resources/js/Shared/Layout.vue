<template>
  <div class="min-h-screen bg-indigo-800 p-2">
    <div class="">
      <Button @click="toggle" :label="auth.user.first_name" icon="fa-solid fa-chevron-down" iconPos="right" aria-controls="overlay_tmenu" ></Button>
      <Menu ref="menu" id="overlay_menu" :model="items" :popup="true" />
    </div>
    <div class=" md:flex-1 md:p-12 md:overflow-y-auto" scroll-region>
      <slot />
    </div>
  </div>

  <Toast position="bottom-center">
      <template #message="slotProps">
          <div class="w-full text-center items-center">
              <i v-if="slotProps.message.severity == 'success'" class="pi pi-check-circle animate-bounce" style="font-size: 2rem"></i>
              <i v-if="slotProps.message.severity == 'error'" class="pi pi-exclamation-triangle animate-bounce" style="font-size: 2rem"></i>
              <p class="font-semibold">{{slotProps.message.detail}}</p>
          </div>
      </template>
  </Toast>
</template>

<script>
import { ref } from "vue";
import Icon from '@/Shared/Icon.vue'
import Logo from '@/Shared/Logo.vue'
import Dropdown from '@/Shared/Dropdown.vue'
import MainMenu from '@/Shared/MainMenu.vue'
import Toast from 'primevue/toast'
import Menu from 'primevue/menu'
import moment from 'moment'
import Button from 'primevue/button'

export default {
  components: {
    Dropdown,
    Icon,
    Logo,
    MainMenu,
    Toast,
    Menu,
    Button
  },
  props: {
    auth: Object,
  },
  data() {
    return {
      items: [
        {
          label: 'Reporte',
          icon: 'fa-solid fa-calendar-days',
          command: () => {
            const year = moment().format('yyyy')
            const month = moment().format('MM')
            this.$inertia.get(route('reports', { year, month }))
          }
        },
        {
          label: 'Mi Perfil',
          icon: 'fa-solid fa-user',
          command: () => {
            const user = this.auth.user.id
            this.$inertia.get(route('users.edit', { user }))
          }
        },
        {
          label: 'Salir',
          icon: 'fas fa-sign-out-alt',
          command: () => {
            this.$inertia.delete(route('logout'))
          }
        }
      ]
    }
  },
  methods: {
    changeToSpanish() {
        this.$primevue.config.locale.monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']
        this.$primevue.config.locale.monthNamesShort = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']
        this.$primevue.config.locale.dayNamesMin = ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa']
        this.$primevue.config.locale.dayNamesMin = ['Dom', 'Lun', 'Mar', 'mie', 'jue', 'Vie', 'Sab']
        this.$primevue.config.locale.today = 'Hoy'
        this.$primevue.config.locale.clear = 'Limpiar'
    }
  },
  mounted () {
      this.changeToSpanish()
  },
  setup() {
    const menu = ref()

    const toggle = (event) => {
      menu.value.toggle(event)
    }

    return {
        menu,
        toggle,
    }
  }
  

}
</script>
