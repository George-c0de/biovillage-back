<template>
	<v-container fluid v-if="product.id || !id">
		<h1 class="mb-3">{{ product.name || $tc('new') + ' ' + $tc('product').toLowerCase() }}</h1>
		
		<v-form @submit.prevent="saveProduct($event.target)">
			
			<v-row>
				<v-col cols="12" lg="" order-lg="1">
					
					<ImagesUploader
						:uploaded="product.imageSrc || undefined"
						:toUpload.sync="product.image"
						:uploadTitle="$tc('cover')"
						@changed="imageChanged = true"
						class="mb-3"
					/>

					<ImagesUploader
						v-if="$vuetify.breakpoint.lgAndUp"
						:multiple="true"
						:uploaded="certs"
						:toUpload.sync="newCerts"
						:uploadTitle="$tc('cert', 2)"
						class="mb-3"
						@changed="certsChanged = true"
					/>
				
				</v-col>
				<v-col
					cols="12" lg="8"
					class="d-flex flex-column flex-children-grow-0"
				>

					<v-row align="center" class="my-0 mb-5">
						<v-col class="col-auto py-0 ">
							<v-checkbox
								v-model="product.active"
								name="active"
								:label="$tc('activeM')"
								hide-details
							></v-checkbox>
						</v-col>
						<v-col class="col-auto py-0 ">
							<v-text-field
								dense outlined filled
								hide-details
								v-model="product.order"
								name="order"
								:label="$tc('sortOrder')"
								style="width: 140px; max-width: 100%"
							></v-text-field>
						</v-col>
					</v-row>
					
					<v-text-field
						dense outlined filled
						v-model="product.name"
						name="name"
						:label="$tc('title')"
					></v-text-field>
					
					<v-autocomplete
						dense outlined filled
						v-model="product.groupId"
						:items="sections"
						item-value="id"
						item-text="name"
						:label="$tc('section')"
					></v-autocomplete>
					
					<v-textarea
						outlined filled
						v-model="product.description"
						name="description"
						:label="$tc('description')"
						rows="3"
					></v-textarea>
					
					<v-textarea
						outlined filled
						v-model="product.composition"
						name="composition"
						:label="$tc('composition')"
						rows="3"
					></v-textarea>

					<v-textarea
						outlined filled
						v-model="product.nutrition"
						name="nutrition"
						:label="$tc('nutrition')"
						rows="1"
					></v-textarea>
					
					<v-textarea
						outlined filled
						v-model="product.shelfLife"
						name="shelfLife"
						:label="$tc('shelfLife')"
						rows="2"
					></v-textarea>


					<v-row class="my-0" align="center">
						<v-col cols="12" class="col-lg-4 py-0">
							<v-text-field
								dense outlined filled
								v-model="product.price"
								name="price"
								:label="$tc('price')"
								:suffix="$store.state.general.settings.paymentCurrencySign"
							></v-text-field>
						</v-col>
						<v-col cols="12" class="col-lg-4 py-0">
							<v-text-field
								dense outlined filled
								v-model="product.netCostPerStep"
								name="netCostPerStep"
								:label="$tc('costPrice')"
								:suffix="$store.state.general.settings.paymentCurrencySign"
							></v-text-field>
						</v-col>
						<v-col class="col-lg-auto py-0" v-if="id">
							<v-btn
								color="primary" 
								text small 
								class="mb-7" 
								@click="dialogPriceHistory = true"
							>
								<v-icon left>history</v-icon> {{ $tc('historyCostPrice') }}
							</v-btn>
						</v-col>
					</v-row>

					<v-row class="my-0">
						<v-col cols="12" lg="4" class="py-0">
							
							<v-autocomplete
								dense outlined filled
								v-model="product.unitId"
								:items="units"
								item-value="id"
								item-text="fullName"
								:label="$tc('unit')"
							></v-autocomplete>
							
						</v-col>

						<v-col cols="12" lg="4" class="py-0">

							<v-select
								v-model="unitType"
								dense outlined filled
								:disabled="!product.unitId"
								:label="$tc('unitType')"
								:items="unitTypes"
								class="v-select--size"
							></v-select>

						</v-col>
						
						<v-col cols="12" lg="2" class="py-0">
							
							<v-text-field
								dense outlined filled
								type="number"
								class="no-input-arrows"
								v-model="product.unitStep"
								name="unitStep"
								:label="$tc('unitStepOfChange')"
								:disabled="!product.unitId"
							></v-text-field>
							
						</v-col>

						<v-col cols="12" class="mt-n3 mb-6" v-if="id">
							<v-btn text color="primary" outlined @click="showRemains = !showRemains">
								<v-icon left>inventory</v-icon>
								{{ $tc('productRemains') }}
								<v-icon right class="ml-3">{{ showRemains ? 'keyboard_arrow_up' : 'keyboard_arrow_down' }}</v-icon>
							</v-btn>
							<v-expand-transition>
								<div v-if="showRemains">
									<div v-if="!productRemains" class="d-flex align-center justify-center py-4">
										<v-progress-circular indeterminate color="primary" width="2" size="50"></v-progress-circular>
									</div>
									<v-data-table
										v-else
										dense
										:headers="productRemainsHeaders"
										:items="productRemains"
										fixed-header
										hide-default-footer
										class="mt-3"
									>
										<template #item.realUnits="{ item }">{{ autoFormatUnits({ item }) }}</template>
									</v-data-table>
								</div>
							</v-expand-transition>
						</v-col>

					</v-row>

					<v-autocomplete
						outlined filled
						v-model="product.tags"
						:items="tags"
						item-value="id"
						item-text="name"
						:label="$tc('tag', 2)"
						multiple
						clearable
						chips
						deletable-chips
					></v-autocomplete>

					<ImagesUploader
						v-if="!$vuetify.breakpoint.lgAndUp"
						:multiple="true"
						:uploaded="certs"
						:toUpload.sync="newCerts"
						:uploadTitle="$tc('cert', 2)"
						class="mb-3"
						@changed="certsChanged = true"
					/>

					<v-radio-group
						v-model="product.promotion"
						name="promotion"
						mandatory
						row
					>
						<v-radio
							v-for="item in promo"
							:key="item.value"
							:value="item.value"
							:label="item.label"
							hide-details
						></v-radio>
					</v-radio-group>

					<v-row class="justify-start my-0">
						<v-col cols="auto">
							<v-btn
								type="submit"
								color="success"
							>
								<v-icon left>
									save
								</v-icon>
								{{ $tc('save') }}
							</v-btn>
						</v-col>
						
						<v-col v-if="product.id" cols="auto">
							<v-btn
								color="error"
								@click="deleteProduct"
							>
								<v-icon left>
									delete
								</v-icon>
								{{ $tc('delete') }}
							</v-btn>
						</v-col>
					</v-row>
					
				</v-col>
			</v-row>
			
		</v-form>

		<v-dialog v-model="dialogPriceHistory" max-width="700px">
			<v-card>
				<v-card-title class="headline justify-center mb-6">
					{{ $tc('historyCostPrice') }}
				</v-card-title>
				<v-card-text>
					<div v-if="!productPriceHistoty" class="d-flex align-center justify-center py-4">
						<v-progress-circular indeterminate color="primary" width="2" size="50"></v-progress-circular>
					</div>
					<v-data-table
						v-else 
						dense
						:headers="productPriceHistoryHeaders"
						:items="productPriceHistoty"
						fixed-header
						hide-default-footer
					>
						<template #item.updatedAt="{ item }">
							{{ new Date(item.updatedAt).toLocaleDateString() }} {{ new Date(item.updatedAt).toLocaleTimeString() }}
						</template>
						<template #item.netCostPerStep="{ item }">
							{{ numberByThousands(item.netCostPerStep / 100) + ' ' + $store.state.general.settings.paymentCurrencySign }}
						</template>
					</v-data-table>
				</v-card-text>
				<v-btn icon absolute color="grey" top right @click="dialogPriceHistory = false">
					<v-icon>close</v-icon>
				</v-btn>
			</v-card>
		</v-dialog>

		<ConfirmDialog
			:show="productDeleteDialog"
			:title="$tc('deletion')"
			agreeIcon="delete"
			@agree="confirmProductDeleteDialog"
			@disagree="productDeleteDialog = false"
		>
			{{ $tc('areYouSure') + ' ' + $tc('delete').toLowerCase() + ' ' +  $tc('productEdit') }} <br /> <b>{{ product.name }}</b>?
		</ConfirmDialog>
		
	</v-container>
</template>

<script>
import ImagesUploader from '@bo/components/ImagesUploader'
import ConfirmDialog from '@bo/components/ConfirmDialog'
import { formatUnits, numberByThousands } from '@bo/mixins'
	

export default {

	mixins: [formatUnits, numberByThousands],
	
	components: {
		ImagesUploader,
		ConfirmDialog,
	},
	
	props: ['id'],
	
	data() { return {
		product: {},
		newProduct: {
			active: true,
			order: 100,
			tags: [],
			units: [],
		},
		unitTypes: [
			{
				'text': this.$tc('unitShortTitle'),
				'value': 'base',
			},
			{
				'text': this.$tc('unitShortDerTitle'), 
				'value': 'der',
			},
		],
		unitType: 'der',
		currentUnit: undefined,
		promo: [
			{
				label: this.$tc('withoutPromotion'),
				value: '',
			},
			{
				label: this.$tc('productOfTheDay'),
				value: 'DP',
			},
			{
				label: this.$tc('productOfTheWeek'),
				value: 'WP',
			},
		],
		imageChanged: false,
		productDeleteDialog: false,
		certs: [],
		newCerts: [],
		certsChanged: false,
		newCert: null,

		dialogPriceHistory: false,
		productPriceHistoryHeaders: [
			{
				text: this.$tc('updatedDate'),
				value: 'updatedAt',
				sortable: true,
			},
			{
				text: this.$tc('costPrice'),
				value: 'netCostPerStep',
				sortable: false,
			},
		],

		productRemainsHeaders: [
			{
				text: this.$tc('store'),
				value: 'storeName',
				sortable: false,
			},
			{
				text: this.$tc('storePlace'),
				value: 'storePlaceName',
				sortable: false,
			},
			{
				text: this.$tc('remains'),
				value: 'realUnits',
				sortable: false,
			},
		],

		showRemains: false,
	
	}},
	
	computed: {
		sections() {
			return JSON.parse(JSON.stringify(this.$store.state.catalog.sections)).sort((a, b) => a.order - b.order)
		},
		
		units() {
			return this.$store.state.general.units
		},

		factor() {
			if (!this.currentUnit) return 0
			return this.currentUnit.factor
		},
		
		tags() {
			return JSON.parse(JSON.stringify(this.$store.state.general.tags)).sort((a, b) => a.order - b.order)
		},

		productPriceHistoty() {
			return this.$store.state.catalog.productPriceHistoty
		},

		productRemains() {
			return this.$store.state.catalog.productRemains
		},
		
	},
	
	methods: {
		saveProduct(form) {
			let data = new FormData(form),
				newCerts = this.certsChanged ? this.newCerts : [],
				oldCerts = this.certsChanged ? this.certs : []
			data.set('active', form.active.checked ? '1' : '0')
			data.set('groupId', this.product.groupId)
			data.set('unitId', this.product.unitId)

			let netCost = this.product.netCostPerStep
			if (netCost == undefined) netCost = ''
			netCost = parseFloat(netCost.toString().replace(',','.'))
			netCost = parseInt(netCost  * 100)
			data.set('netCostPerStep', netCost)

			// Форматируем ед. изм.:
			if (this.unitType == 'der') data.set('unitStep', this.unitsToBase(this.product.unitStep, this.factor))

			for (let tag of this.product.tags) data.append('tags[]', tag)
			
			if (this.imageChanged && this.product.image[0]) data.set('image', this.product.image[0].file)
			
			this.id
			? this.$store.dispatch('catalog/updateProduct', {id: this.id, data, oldCerts, newCerts})
			: this.$store.dispatch('catalog/createProduct', {data, newCerts})
		},
		
		deleteProduct() {
			this.productDeleteDialog = true
		},
		
		confirmProductDeleteDialog() {
			this.$store.dispatch('catalog/deleteProduct', {id: this.product.id, oldCerts: this.certs})
			this.productDeleteDialog = false
		},

		updateUnitInfo() {
			this.currentUnit = this.$store.getters['general/getUnitById'](this.product.unitId)
			if (!this.currentUnit) return
			// Приводим ед. измерения к производным при первой загрузке:
			if (!this.product.unitsAreSet) {
				this.product.unitsAreSet = true
				this.product.unitStep = this.unitsToDer(this.product.unitStep, this.factor)
			}
			// Обновляем подсказки:
			this.unitTypes[0].text = this.currentUnit.shortName
			this.unitTypes[1].text = this.currentUnit.shortDerName
		}
	},
	
	watch: {
		id: {
			immediate: true,
			handler(v) {
				this.$store.dispatch('catalog/getProduct', {id: v})
				this.$store.dispatch('catalog/getCerts', {id: v})
			},
		},

		'product.unitId'() {
			this.updateUnitInfo()
		},

		units() {
			this.updateUnitInfo()
		},

		unitType(val) {
			let step = this.product.unitStep
			if (val == 'der') this.product.unitStep = this.unitsToDer(step, this.factor)
			else if (val == 'base') this.product.unitStep = this.unitsToBase(step, this.factor)
		},

		'$store.state.catalog.product': {
			immediate: true,
			handler(v) {
				let product = JSON.parse(JSON.stringify(v.id ? v : this.newProduct))				
				// преобразование тегов в нужный для компонента вид
				if (product.tags.length) {
					for (let i in product.tags) {
						product.tags[i] = product.tags[i][0]
					}
				}
				if (product.netCostPerStep) product.netCostPerStep /= 100
				product.unitsAreSet = false
				this.product = product
			},
		},

		'$store.state.catalog.certs': {
			immediate: true,
			handler(v) {
				this.certs = JSON.parse(JSON.stringify(v)).sort((a, b) => a.order - b.order)
			},
		},
		
	},
	
	created() {
		this.$store.dispatch('catalog/getSections')
		this.$store.dispatch('general/getUnits')
		this.$store.dispatch('general/getTags')
		if (this.id) {
			this.$store.dispatch('catalog/getProductPriceHistory', this.id)
			this.$store.dispatch('catalog/getProductRemains', this.id)
		}
	},
	
}
</script>