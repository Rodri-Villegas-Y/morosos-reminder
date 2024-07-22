<template>
  <Head title="Login" />

  <div class="flex items-center justify-center min-h-screen bg-indigo-800">
    <div class="w-full max-w-md">
      <logo class="block w-full text-center" height="50" />
      <Card>
        <template #content> 
          <form @submit.prevent="login">
            <div class="py-4">
              <FloatLabel class="w-full md:w-14rem">
                <InputText 
                    id="email"
                    class="w-full" 
                    type="email" 
                    required 
                    autofocus
                    v-model="form.email" 
                />
                <label for="email" class="dark:text-gray-300">Email</label>
              </FloatLabel>
              <div class="text-center">
                  <small v-if="errors['email']" class="p-error text-xs text-center">{{ errors['email'] }}</small>
              </div>
            </div>
                    
            <div class="py-4">
              <FloatLabel class="w-full md:w-14rem">
                <InputText 
                    id="password"
                    required 
                    class="w-full" 
                    type="password" 
                    v-model="form.password" 
                />
                <label for="password" class="dark:text-gray-300">Contraseña</label>
              </FloatLabel>
            </div>
            
            <div id="reCaptcha" class="block flex justify-center"></div>
                                    
            <div class="flex justify-center mt-8">
                <Button
                    class="w-full p-button-raised" 
                    type="submit" 
                    label="Ingresar" 
                    :loading="processing"
                    loadingIcon="animate-spin fas fa-spinner"
                    icon="fas fa-sign-in-alt"
                />
            </div>
          </form>   
        </template>
      </Card>
    </div>
  </div>
</template>

<script>
import { Head } from '@inertiajs/vue3'
import Logo from '@/Shared/Logo.vue'
import InputText from 'primevue/inputtext'
import Card from 'primevue/card'
import Button from 'primevue/button'
import Checkbox from 'primevue/checkbox'
import FloatLabel from 'primevue/floatlabel'

export default {
  components: {
    Head,
    Logo,
    InputText,
    Card,
    Button,
    Checkbox,
    FloatLabel
  },
  props: {
      errors: Object,
  },
  data() {
    return {
      form: this.$inertia.form({
        email: 'johndoe@example.com',
        password: 'secret',
        remember: false,
      }),
      processing: false
    }
  },
  methods: {
    login() {
      this.form.post(route('login.store'))
    },
  },
}
</script>