<template>
  <div>
    <Head title="Reports" />
  </div>

  <div>
    <Calendar 
        v-model="form.month" 
        view="month" 
        :manualInput="false" 
        dateFormat="MM yy"
        @date-select="changeMonth()"
    />
    <Button v-if="auth.user.role=='admin'" class="mx-2" :label="width >= 1024 ? 'Crear Item' : null" icon="fas fa-plus-circle" @click="visible = true" />
    <Button v-if="auth.user.role=='admin'" class="mx-2" :label="width >= 1024 ? 'Duplicar Mes' : null" icon="fas fa-copy" @click="duplicateMonth($event)" />
    </div>

  <div class="grid xl:grid-cols-2 lg:grid-cols-2 md:grid-cols-2 grid-cols-1 gap-3">
    <Card v-for="(section, index) in items" class="my-2">
      <template #title>
        <span class="inline-flex uppercase items-center justify-left px-1 py-1 mx-1 text-lg font-bold leading-none text-white rounded" :style="{ backgroundColor: sectionColor[index] }">{{index}}</span>
      </template>
      <template #content>
          <p class="m-0">
            <DataTable :value="section"
              :rows="10" 
              breakpoint="960px"
              stripedRows
              :responsiveLayout="(width < 1024) ? 'stack' : 'scroll'"
              sortField="id"
              size="Small"
            >
                <Column field="item_name" header="Item">
                  <template #body="{data}">
                    <div>
                      <span class="lowercase">{{ data.item_name }}</span>
                      <i v-if="data.icon" class="mx-1 " :class="data.icon"></i>
                      <span v-if="data.quota" class="inline-flex items-center justify-left px-1 py-1 mx-1 text-xs font-bold leading-none text-white bg-green-500 rounded">{{ data.quota }}</span>
                      <span v-for="user in data.users" class="inline-flex items-center justify-left px-1 py-1 mx-1 text-xs font-bold leading-none text-white bg-indigo-500 rounded">{{ user.name }}</span>
                    </div>
                  </template>
                </Column>
                <Column field="amount" header="Monto" bodyStyle="text-align: right">
                  <template #body="{data}">
                    <div>
                      {{ priceFormat(data.amount) }}
                      <Button v-if="auth.user.role=='admin'" class="m-0 p-0 ml-2" @click="editItem(data.item_id, index)" text ><i class="fa-solid fa-pen text-sm opacity-70"></i></Button>
                      <Button v-if="auth.user.role=='admin'" class="m-0 p-0 ml-2" @click="removeItem($event, data.item_id, data.item_name)" text ><i class="fas fa-trash-alt text-red-500 text-sm opacity-70"></i></Button>
                    </div>
                  </template>
                </Column>
                <Column field="payed" header="Pagado" bodyStyle="text-align: center" :style="{ width: width >= 1024 ? '50px' : null }">
                  <template #body="{data}">
                    <div>
                      <i v-if="data.payed" class="fas fa-check-circle text-green-400 text-xl"></i>
                      <Button v-else class="m-0 p-0" @click="payItem($event, data.item_id, data.item_name)" text ><i class="far fa-check-circle text-gray-400 text-xl opacity-40"></i></Button>
                    </div>
                  </template>
                </Column>
                <ColumnGroup type="footer">
                  <Row>
                    <Column :footer="`Total ${index}:`" footerStyle="text-align:right" />
                    <Column :footer="priceFormat(getTotal(index))" footerStyle="text-align:right" />
                  </Row>
                  <div v-if="Object.keys(usersSections[index]).length > 1">
                    <Row v-for="users in usersSections[index]">
                        <Column :footer="`Total ${index} - ${users.name}:`" footerStyle="text-align:right" />
                        <Column :footer="priceFormat(getTotalSplit(index, users.user_id))" footerStyle="text-align:right" />
                    </Row>
                  </div>
                </ColumnGroup>
            </DataTable>
          </p>
      </template>
    </Card>
  </div>

  <Dialog v-model:visible="visible" modal header="Edit Profile" :style="{ width: '25rem' }"  @hide="saveCancel">
      <template #header>
        <span v-if="form.id" class="inline-flex uppercase items-center justify-left px-1 py-1 mx-1 text-lg font-bold leading-none text-white rounded">Editar Item</span>
        <span v-else class="inline-flex uppercase items-center justify-left px-1 py-1 mx-1 text-lg font-bold leading-none text-white rounded">Crear Item</span>
      </template>

      <div class="grid xl:grid-cols-1 lg:grid-cols-1 md:grid-cols-1 grid-cols-1 gap-3">
        <div class="mb-4">
          <FloatLabel class="w-full md:w-14rem">
            <Dropdown 
              v-model="form.section" 
              id="form-section" 
              :options="sectionsSelect" 
              optionLabel="name" 
              class="w-full"
              :class="{'p-invalid': !form.section || errors['section']}" 
            />
            <label for="form-section">Sección <span class="text-red-500 opacity-40">(requerido)</span></label>
          </FloatLabel>
          <small v-if="errors['section']" class="p-error text-xs flex">{{ errors['item'] }}</small>
        </div>
        <div class="mb-4">
          <FloatLabel class="w-full md:w-14rem">
            <InputText 
              id="form-item" 
              v-model="form.item" 
              class="w-full"
              :class="{'p-invalid': !form.item || errors['item']}"
            />
            <label for="form-item">Item <span class="text-red-500 opacity-40">(requerido)</span></label>
          </FloatLabel>
          <small v-if="errors['item']" class="p-error text-xs flex">{{ errors['item'] }}</small>
        </div>
        <div class="mb-4">
          <FloatLabel class="w-full md:w-14rem">
            <InputNumber 
              id="form-amount" 
              v-model="form.amount" 
              class="w-full" 
              :class="{'p-invalid': !form.amount || errors['amount']}"
            />
            <label for="form-amount">Monto <span class="text-red-500 opacity-40">(requerido)</span></label>
          </FloatLabel>
          <small v-if="errors['amount']" class="p-error text-xs flex">{{ errors['amount'] }}</small>
        </div>
        <div class="mb-4">
          <FloatLabel class="w-full md:w-14rem">
            <InputMask id="form-quota" v-model="form.quota" mask="99/99" placeholder="01/03" class="w-full" />
            <label for="form-quota">Cuota <span class="opacity-40">(opcional)</span></label>
          </FloatLabel>
        </div>
        <div class="mb-4">
          <FloatLabel class="w-full md:w-14rem">
            <InputText id="form-icon" v-model="form.icon" class="w-full" />
            <label for="form-icon">Icono <span class="opacity-40">(opcional)</span></label>
          </FloatLabel>
        </div>
        <div class="mb-4">
          <FloatLabel class="w-full md:w-20rem">
              <MultiSelect 
                id="form-users" 
                v-model="form.users" 
                :options="userSelect" 
                optionLabel="name" 
                :maxSelectedLabels="3" 
                class="w-full"
                :class="{'p-invalid': !form.users || errors['users']}"
              />
              <label for="form-users">Asignado <span class="text-red-500 opacity-40">(requerido)</span></label>
          </FloatLabel>
          <small v-if="errors['users']" class="p-error text-xs flex">{{ errors['users'] }}</small>
        </div>
      </div>
      <template #footer>
          <Button label="Cancelar" text severity="secondary" @click="saveCancel()" />
          <Button label="Guardar" outlined severity="secondary" @click="saveItem()" autofocus />
      </template>
  </Dialog>

  <ConfirmPopup></ConfirmPopup>

</template>

<script>
import { Head } from '@inertiajs/vue3'
import Layout from '@/Shared/Layout.vue'
import Card from 'primevue/card'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import ColumnGroup from 'primevue/columngroup'
import Row from 'primevue/row'
import Calendar from 'primevue/calendar'
import InputText from 'primevue/inputtext'
import moment from 'moment'
import 'moment/locale/es.js';
import Button from 'primevue/button'
import Dialog from 'primevue/dialog'
import Dropdown from 'primevue/dropdown'
import FloatLabel from 'primevue/floatlabel'
import InputMask from 'primevue/inputmask'
import InputNumber from 'primevue/inputnumber'
import mapValues from 'lodash/mapValues'
import MultiSelect from 'primevue/multiselect'
import ConfirmPopup from 'primevue/confirmpopup'

moment.locale('es')

export default {
  components: {
    Head,
    Card,
    DataTable,
    Column,
    ColumnGroup,
    Row,
    Calendar,
    InputText,
    Button,
    Dialog,
    Dropdown,
    FloatLabel,
    InputMask,
    InputNumber,
    MultiSelect,
    ConfirmPopup
  },
  layout: Layout,
  props: {
    items: Object,
    usersSections: Object,
    sectionColor: Array,
    month: Object,
    sectionsSelect: Object,
    userSelect: Object,
    errors: Object,
    flash: Object,
    auth: Object,
  },
  data () {
    return {  
      form: {
          month: (this.month) ? moment(this.month, 'YYYY-MM').toDate() : moment().toDate(),
          section: null,
          item: null,
          amount: null,
          quota: null,
          users: null,
          id: null
      },
      visible: false,
      width: null,
    }
  },
  methods: {
    getTotal(index) {
      return this.items[index].reduce((total, item) => total + item.amount, 0);
    },
    getTotalSplit(index, user_id) {
      const filteredItems = this.items[index].filter(item => {
        return item.users.some(user => user.id === user_id)
      })

      return filteredItems.reduce((total, item) => total + item.split, 0);  
    },
    priceFormat(amount) {
        let total = amount
        const decimal = ","
        const thousands = "."
        let decimalCount = 0
        
        decimalCount = Math.abs(decimalCount)
        decimalCount = isNaN(decimalCount) ? 2 : decimalCount

        const negativeSign = total < 0 ? "-" : ""

        let i = parseInt(total = Math.abs(Number(total) || 0).toFixed(decimalCount)).toString()
        let j = (i.length > 3) ? i.length % 3 : 0

        return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(total - i).toFixed(decimalCount).slice(2) : "")
    },
    changeMonth() {
        let year  = moment(this.form.month).format('YYYY')
        let month = moment(this.form.month).format('MM')

        this.$inertia.get(`/reports/${year}/${month}`)
    },
    saveItem() {
      this.$inertia.post(this.form.id ? '/reports/update' : '/reports/store', this.form)
    },
    saveCancel() {
      this.visible = false
      this.form.id = false
      this.form = mapValues(this.form, (value, key) => (key === 'month' ? value : null));
    },
    payItem(e, id, name) {
      this.$confirm.require({
          target: e.currentTarget,
          message: `Pagaste ${name}?`,
          icon: 'fas fa-question-circle',
          acceptLabel: 'Si',
          rejectLabel: 'No',
          accept: () => {
            this.$inertia.post('/reports/pay', {
              id: id,
              month: this.form.month
            })
          },
          reject: () => {
          }
      })
    },
    removeItem(e, id, name) {
      this.$confirm.require({
          target: e.currentTarget,
          message: `Eliminar ${name}?`,
          icon: 'fas fa-question-circle',
          acceptLabel: 'Si, Eliminar',
          rejectLabel: 'No',
          accept: () => {
            this.$inertia.post('/reports/remove', {
              id: id,
              month: this.form.month
            }, {
              preserveScroll: true
            })
          },
          reject: () => {
          }
      })
    },
    editItem(id, section) {
      const findItem = Object.values(this.items[section]).find(obj => obj.item_id === id)
      const findSection = this.sectionsSelect.find(obj => obj.name === section)
      const findUsers = this.userSelect.filter(objUser => {
          return findItem.users.some(objUserSelect => objUserSelect.id === objUser.id)
      })
      this.form.section = findSection
      this.form.item = findItem.item_name
      this.form.amount = findItem.amount
      this.form.quota = findItem.quota
      this.form.icon = findItem.icon
      this.form.users = findUsers
      this.form.id = id

      this.visible = true
    },
    duplicateMonth(e) {
      this.$confirm.require({
          target: e.currentTarget,
          message: `Duplicar Mes para ${moment(this.month, 'YYYY-MM').add(1, 'month').format('MMMM YYYY')}?`,
          icon: 'fas fa-question-circle',
          acceptLabel: 'Si, Duplicar',
          rejectLabel: 'No',
          accept: () => {
            this.$inertia.post('/reports/duplicate', {
              month: this.form.month,
            }, {
              preserveScroll: true,
              onSuccess: () => {
                console.log('hola')
              },
            })
          },
          reject: () => {
          }
      }) 
    },
    handleResize() {
        this.width = window.innerWidth
    },
  },
  computed: {

  },
  watch: {
    flash: {
      handler: function(value) {
        if (value.success) {
          this.saveCancel()
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
    }
  },
  mounted() {
    window.addEventListener('resize', this.handleResize)
    this.handleResize()
  }

}
</script>

