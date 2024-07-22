<template>

<Card>
  <template #title>
    <span class="inline-flex uppercase items-center justify-left px-1 py-1 mx-1 text-lg font-bold leading-none text-white rounded">Modifica Usuario</span>
  </template>
    <template #content>
      
      <div class="grid xl:grid-cols-2 lg:grid-cols-2 md:grid-cols-2 grid-cols-1 gap-3">
        <div class="mb-4">
          <FloatLabel class="w-full md:w-14rem">
            <InputText 
              id="form-first-name" 
              v-model="form.first_name" 
              class="w-full"
              :class="{'p-invalid': !form.first_name || errors['first_name']}"
            />
            <label for="first">Nombre <span class="text-red-500 opacity-40">(requerido)</span></label>
          </FloatLabel>
          <small v-if="errors['first_name']" class="p-error text-xs flex">{{ errors['first_name'] }}</small>
        </div>
  
        <div class="mb-4">
          <FloatLabel class="w-full md:w-14rem">
            <InputText 
              id="form-email" 
              v-model="form.email" 
              class="w-full"
              :class="{'p-invalid': !form.email || errors['email']}"
            />
            <label for="first">Email <span class="text-red-500 opacity-40">(requerido)</span></label>
          </FloatLabel>
          <small v-if="errors['email']" class="p-error text-xs flex">{{ errors['email'] }}</small>
        </div>

        <div class="mb-4">
          <FloatLabel class="w-full md:w-14rem">
            <Password 
              id="form-password" 
              v-model="form.password" 
              class="w-full"
              toggleMask
              weakLabel="Débil"
              mediumLabel="Medio"
              strongLabel="Fuerte"
              promptLabel="Ingresa Contraseña"
              :class="{'p-invalid': !form.password || errors['password']}"
            />
            <label for="first">Contraseña <span class="text-red-500 opacity-40">(requerido)</span></label>
          </FloatLabel>
          <small v-if="errors['password']" class="p-error text-xs flex">{{ errors['password'] }}</small>
        </div>

        <div class="mb-4">
          <FloatLabel class="w-full md:w-14rem">
            <Password 
              id="form-password" 
              v-model="form.password_confirmation" 
              class="w-full"
              toggleMask
              weakLabel="Débil"
              mediumLabel="Medio"
              strongLabel="Fuerte"
              promptLabel="Ingresa Contraseña"
              :class="{'p-invalid': !form.password_confirmation || errors['password_confirmation']}"
            />
            <label for="first">Confirma Contraseña <span class="text-red-500 opacity-40">(requerido)</span></label>
          </FloatLabel>
          <small v-if="errors['password_confirmation']" class="p-error text-xs flex">{{ errors['password_confirmation'] }}</small>
        </div>
      </div>

      <Button class="mx-2" label='Actualizar' icon="fa-solid fa-database" :loading="processing" @click.prevent="update($event)" />

    </template>
  </Card>

  <ConfirmPopup></ConfirmPopup>
</template>

<script>
import { Head } from '@inertiajs/vue3'
import Layout from '@/Shared/Layout.vue'
import InputText from 'primevue/inputtext'
import Card from 'primevue/card'
import FloatLabel from 'primevue/floatlabel'
import Password from 'primevue/password'
import Button from 'primevue/button'
import ConfirmPopup from 'primevue/confirmpopup'

export default {
  components: {
    Head,
    Card,
    InputText,
    FloatLabel,
    Password,
    Button,
    ConfirmPopup
  },
  layout: Layout,
  props: {
    user: Object,
    flash: Object,
    errors: Object,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        _method: 'put',
        id: this.user.id,
        first_name: this.user.first_name,
        last_name: this.user.last_name,
        email: this.user.email,
        password: null,
        password_confirmation: null
      }),
      processing: false
    }
  },
  methods: {
    update(e) {
      this.$confirm.require({
          target: e.currentTarget,
          message: `Actualizar Usuario?`,
          icon: 'fas fa-question-circle',
          acceptLabel: 'Si',
          rejectLabel: 'No',
          accept: () => {
            this.processing = true
            this.form.post(route('users.update'), {
              onSuccess: () => {

              }
            })
          },
          reject: () => {

          }
      })
    },
    addClass() {
      const inputPassword = document.querySelectorAll('.p-password-input')
      inputPassword.forEach(element => {
          element.classList.add('w-full')
      });
    },
  },
  watch: {

    flash: {
      handler: function(value) {
        this.processing = false
        if (value.success) {
          this.$toast.removeAllGroups();
          this.$toast.add({
            severity:'success', 
            summary: 'Mensaje', 
            detail: value.success, 
            life: 3000
          })
        }
      } 
    },
    errors: {
      handler: function(value) {
        this.processing = false
        if (value.error_controller) {
          this.$toast.removeAllGroups();
          this.$toast.add({
            severity:'error', 
            summary: 'Mensaje', 
            detail: value.error_controller, 
            life: 3000
          })
        }
      }
    },
  },
  mounted () {
      this.addClass()
  }
}
</script>
