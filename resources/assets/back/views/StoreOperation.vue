<template>
	<v-container fluid>

		<v-row class="mb-3" align="center">
			<v-col cols="auto">
				<h1>
					<router-link
						:to="{name: 'StoreDetails', params: {id: this.storeId}}"
						class="navigation-icon navigation-icon--back"
					>
						<v-icon>arrow_back_ios</v-icon>
					</router-link>
					{{ pageTitle }}
				</h1>
			</v-col>
			<v-col cols="auto" v-if="['details', 'failed'].indexOf(mode) > -1">
				<v-btn icon outlined color="primary" @click="getInfo()" :loading="loadingDetails">
					<v-icon>refresh</v-icon>
				</v-btn>
			</v-col>
		</v-row>

		<div v-if="operation && operation.id" class="body-1">
			<p class="mb-2" v-if="operation.createdAt"><b>{{ $tc('createdDate') }}:</b> {{ new Date(operation.createdAt).toLocaleDateString() }} {{ new Date(operation.createdAt).toLocaleTimeString() }}</p>
			<p class="mb-2" v-if="operation.updatedAt"><b>{{ $tc('updatedDate') }}:</b> {{ new Date(operation.updatedAt).toLocaleDateString() }} {{ new Date(operation.updatedAt).toLocaleTimeString() }}</p>
			<p class="mb-2" v-if="operation.adminId"><b>{{ $tc('admin') }}:</b> <router-link :to="{name: 'AdminAccountEdit', params: {id: operation.adminId}}">{{ operation.adminName || `[${operation.adminId}]` }}</router-link></p>
			<p class="mb-2" v-if="operation.clientId"><b>{{ $tc('client') }}:</b> <router-link :to="{name: 'ClientAccount', params: {id: operation.clientId}}">{{ operation.clientName || `[${operation.clientId}]` }}</router-link></p>
			<p class="mb-2" v-if="operation.orderId"><b>{{ $tc('orderId') }}:</b> <router-link :to="{name: 'OrderDetails', params: {id: operation.orderId}}">{{ operation.orderId }}</router-link></p>
			<p class="mb-2" v-if="operation.status">
				<b>{{ $tc('status') }}: </b> {{ $tc('storeOperationStatus.' + operation.status) }}
				<v-icon v-if="operation.status == 'completed'" color="primary" class="mt-n1">check</v-icon>
				<v-icon v-else-if="operation.status == 'reserve'" color="primary" class="mt-n1">inventory</v-icon>
			</p>
		</div>

		<v-form v-if="operation && storeDetails" @submit.prevent="saveOperation()">

			<v-data-table
				:headers="productsHeaders"
				:items="operation.contents"
				class="th-nowrap"
			>
				<template #top>
					<v-row align="center" class="mt-4 mb-2">
						<v-col cols="auto">
							<h2>{{ $tc('operationContents') }}</h2>
						</v-col>
						<v-col cols="auto" v-if="mode != 'details'">
							<v-btn v-if=" storeDetails.type == 'product'" color="primary" @click="dialogSearchProduct = true">
								<v-icon :title="$tc('add')">add</v-icon> {{ $tc('addProduct') }} 
							</v-btn>
							<v-btn v-else color="primary" @click="openDialogSearchGift()">
								<v-icon :title="$tc('add')">add</v-icon> {{$tc('addGift') }} 
							</v-btn>
						</v-col>
					</v-row>
				</template>
				
				<template #item.name="{ item }">
					<template v-if="item.productId">
						[<router-link :to="{name: 'CatalogProductEdit', params: {id: item.productId }}">{{ item.productId }}</router-link>]
						{{ item.productName }}
						[{{ item.unitStep }} {{item.unitShortName}} / {{ numberByThousands(item.price) + ' ' + $store.state.general.settings.paymentCurrencySign }}]
					</template>
					<template v-else-if="item.giftId">
						[<router-link :to="{name: 'Gifts', params: {giftId: item.giftId }}">{{ item.giftId }}</router-link>]
						{{ item.giftName }}
						[{{ numberByThousands(item.price) + ' ' + $tc('bonuses_short') }}]
					</template>
				</template>

				<template #item.storePlaceId="{ item }">
					<div v-if="mode == 'details'">
						[{{ item.storePlaceId }}] {{ item.storePlaceName }}
					</div>
					<v-select
						v-else
						v-model="item.storePlaceId"
						:loading="loadingStoresPlaces"
						:disabled="loadingStoresPlaces"
						:items="storesPlaces"
						item-value="id"
						item-text="name"
						style="width: 240px"
						class="mt-1 mb-n2"
						dense
						:error="!item.storePlaceId"
					>
						<template slot="selection" slot-scope="data">
							[{{ data.item.id }}]  {{ data.item.name }}
						</template>
						<template slot="item" slot-scope="data">
							[{{ data.item.id }}]  {{ data.item.name }}
						</template>
					</v-select>
				</template>

				<template #item.realUnits="{ item }">
					<div class="d-flex justify-end">
						<div v-if="mode == 'details'">
							{{ item.giftId ? item.realUnits + ' ' + $tc('pc') : autoFormatUnits({ item }) }}
						</div>
						<div v-else class="d-inline-flex mt-2 mb-1">
							<v-text-field
								dense
								type="number"
								class="no-input-arrows"
								v-model="item.realUnits"
								persistent-hint
								:error="item.realUnits == ''"
								reverse
								style="max-width: 90px"
								:prefix="storeDetails.type == 'product' ? null : ' ' + $tc('pc')"
							></v-text-field>
							<v-select
								v-if="storeDetails.type == 'product'"
								v-model="item.unitType"
								hide-details="auto"
								dense
								:items="[{'value': 'base', 'text': item.unitShortName}, {'value': 'der', 'text': item.unitShortDerName}]"
								class="pt-0 ml-2 text-right hint-text-right"
								style="width: 90px; max-width: 90px"
								@change="changeUnitType($event, item)"
							></v-select>
						</div>
					</div>
				</template>

				<template #item.netCost="{ item }">
					<div class="d-flex justify-end mt-2">
						<div v-if="mode == 'details'">
							{{ numberByThousands(item.netCost / 100) }} 
							{{ item.productId ? $store.state.general.settings.paymentCurrencySign : $tc('bonuses_short') }}
						</div>
						<v-text-field
							v-else
							v-model="item.netCost"
							:suffix="$store.state.general.settings.paymentCurrencySign"
							type="number"
							style="max-width: 100px"
							size="8"
							class="pt-0 text-right no-input-arrows input-sm-padding"
							:error="!item.netCost || item.netCost <= 0"
						></v-text-field>
					</div>
				</template>

				<template #item.actions="{ item }">
					<v-btn icon color="error" @click="deleteOperationProduct(item)">
						<v-icon	color="error" :title="$tc('delete')">delete</v-icon>
					</v-btn>
				</template>
			</v-data-table>

			<v-data-table
				:headers="filesHeaders"
				:items="operation.files"
				class="th-nowrap"
			>
				<template #top>
					<v-row align="center" class="mt-4 mb-2">
						<v-col cols="auto">
							<h2>{{ $tc('attachedFile', 2) }}</h2>
						</v-col>
						<v-col cols="auto" v-if="mode != 'details'">
							<v-btn color="primary" @click="openFileToUpload()">
								<v-icon :title="$tc('add')">add</v-icon> {{ $tc('addFile') }}
								<div style="visibility: hidden; width: 0; height: 0">
									<v-file-input ref="fileToUpload" @change="addOperationFile($event)"></v-file-input>
								</div>
							</v-btn>
						</v-col>
					</v-row>
					<div v-if="mode != 'details'" class="body-2 my-4">
						<b>{{ $tc('allowedFileTypes') }}:</b> jpeg, jpg, png  doc, docx, pdf, xls, xlsx, txt <br>
						<b>{{ $tc('maxFileSize') }}</b> 5 {{ $tc('mb') }}
					</div>
				</template>

				<template #item.name="{ item }">
					<div v-if="mode == 'details'">
						{{ item.name }}
					</div>
					<v-text-field
						v-else
						dense
						class="mt-2"
						v-model="item.name"
						:error="!item.name"
					></v-text-field>
				</template>

				<template #item.src="{ item }">
					<div v-if="item.src" class="d-flex flex-wrap justify-end m-n2">
						<v-btn
							small text
							color="primary"
							:href="buildFileLink(item.src)"
							target="_blank"
							class="m-1"
						>
							<v-icon small left>visibility</v-icon> {{ $tc('openFile') }}
						</v-btn>
						<v-btn
							small text
							color="success"
							:href="buildFileLink(item.src)"
							download
							class="m-1"
						>
							<v-icon small left>file_download</v-icon> {{ $tc('downloadFile') }}
						</v-btn>
					</div>
					
					<v-file-input
						v-else
						dense
						class="mb-1"
						hide-details
						v-model="item.file"
						:error="!item.file"
						truncate-length="40"
					></v-file-input>
				</template>

				<template #item.actions="{ item }">
					<v-btn icon color="error" @click="deleteOperationFile(item)">
						<v-icon	color="error" :title="$tc('delete')">delete</v-icon>
					</v-btn>
				</template>
				
			</v-data-table>

			<v-row class="mt-5" v-if="mode != 'details'">
				<v-col cols="auto">
					<v-btn type="submit" color="success">
						<v-icon left>save</v-icon> {{ $tc('save') }}
					</v-btn>
				</v-col>
			</v-row>

		</v-form>

		<v-dialog v-model="dialogSearchProduct" max-width="700px">
			<v-card>
				<v-card-title class="headline justify-center mb-4">
					{{ $tc('addingProduct') }}
				</v-card-title>
				<v-card-text>
					<v-text-field
						dense outlined filled
						v-model="searchProductQuery"
						:label="$tc('productSearch')"
						:hint="$tc('startEnterProductName')"
						prepend-inner-icon="search"
						clearable
						class="pt-0 mt-0"
						:loading="searchProductsLoading"
						@input="searchProducts"
					></v-text-field> 

					<v-data-table
						:headers="searchProductsHeaders"
						:items="searchProductsResults"
						item-key="id"
						must-sort
						:sort-by="['order']"
						:loading="searchProductsLoading"
						fixed-header
					>
						<template #item.unitStep="{ item }">
							{{ item.unitStep }} {{ item.unitShortName }}
						</template>

						<template #item.price="{ item }">
							{{ numberByThousands(item.price) + ' ' + $store.state.general.settings.paymentCurrencySign }}
						</template>

						<template #item.actions="{ item }">
							<v-btn 
								outlined 
								icon 
								color="success" 
								:loading="saveLoading"
								@click="addOperationProduct(item)"
								:disabled="operation.contents.findIndex(v => v.productId === item.id) > -1"
							>
								<v-icon :title="$tc('add')">add</v-icon>
							</v-btn>
						</template>
					</v-data-table>
				</v-card-text>
				<v-btn icon absolute color="grey" top right @click="dialogSearchProduct = false">
					<v-icon>close</v-icon>
				</v-btn>
			</v-card>
		</v-dialog>

		<v-dialog v-model="dialogSearchGift" max-width="700px">
			<v-card>
				<v-card-title class="headline justify-center mb-4">
					{{ $tc('addingGift') }}
				</v-card-title>
				<v-card-text>
					<v-text-field
						dense outlined filled
						v-model="giftsSearch"
						:label="$tc('giftsSearch')"
						:hint="$tc('startEnterGiftName')"
						prepend-inner-icon="search"
						clearable
						class="pt-0 mt-0"
					></v-text-field> 

					<v-data-table
						:headers="searchGiftsHeaders"
						:items="gifts"
						:search="giftsSearch"
						item-key="id"
						must-sort
						:sort-by="['order']"
						:loading="!gifts"
						fixed-header
					>
						<template #item.price="{ item }">
							{{ numberByThousands(item.price) }} {{ $tc('bonuses_short') }}
						</template>

						<template #item.actions="{ item }">
							<v-btn 
								outlined 
								icon 
								color="success" 
								:loading="saveLoading"
								@click="addOperationGift(item)"
								:disabled="operation.contents.findIndex(v => v.giftId === item.id) > -1"
							>
								<v-icon :title="$tc('add')">add</v-icon>
							</v-btn>
						</template>
					</v-data-table>
				</v-card-text>
				<v-btn icon absolute color="grey" top right @click="dialogSearchGift = false">
					<v-icon>close</v-icon>
				</v-btn>
			</v-card>
		</v-dialog>

		<ConfirmDialog
			:show="dialogShortageForce"
			:title="$tc('confirmation')"
			agreeIcon="check"
			@agree="saveOperation(true)"
			@disagree="dialogShortageForce = false"
			maxWidth="480px"
		>
			<div class="black--text mb-3">{{ $tc('shortageProductPositions') }}:</div>
			<div 
				v-for="product in shortageForceProducts" 
				:key="product.id" 
				class="my-1" 
				v-html="buildShortageText(product)"
			></div>
		</ConfirmDialog>

	</v-container>
</template>

<script>
	import ConfirmDialog from '@bo/components/ConfirmDialog'
	import { numberByThousands, formatUnits } from '@bo/mixins.js'

	export default {

		props: ['storeId', 'id'],

		mixins: [numberByThousands, formatUnits],

		components: {
			ConfirmDialog
		},

		created() {
			this.getInfo()
		},
		
		data() { 
			return {
				mode: 'loading',
				operation: undefined,
				loadingDetails: false,
				saveLoading: false,
				fileToUpload: undefined,

				loadingStoresPlaces: false,
				dialogSearchProduct: false,
				dialogSearchGift: false,

				dialogShortageForce: false,
				shortageForceProducts: [],

				giftsSearch: '',

				searchProductQuery: '',
				searchProductsLoading: false,
				searchThrottleCounter: 0,
				searchProductsHeaders: [
					{
						text: this.$tc('id'),
						value: 'id',
						sortable: false,
					},
					{
						text: this.$tc('productName'),
						value: 'name',
						sortable: false,
					},
					{
						text: this.$tc('quantity'),
						value: 'unitStep',
						sortable: false,
					},
					{
						text: this.$tc('price'),
						value: 'price',
						sortable: false,
					},
					{
						value: 'actions',
						sortable: false,
						align: 'end',
					},
				],

				searchGiftsHeaders: [
					{
						text: this.$tc('id'),
						value: 'id',
						sortable: false,
					},
					{
						text: this.$tc('giftName'),
						value: 'name',
						sortable: false,
					},
					{
						text: this.$tc('price') + ', ' + this.$tc('bonus', 2),
						value: 'price',
						sortable: false,
					},
					{
						value: 'actions',
						sortable: false,
						align: 'end',
					},
				],
			}
		},

		computed: {

			storeDetails() {
				return this.$store.state.stores.storeDetails
			},

			pageTitle() {
				switch (this.mode) {
					case 'loading':
						return this.$tc('loading') + '...'
					case 'details':
						let titleDetails = this.$tc('storeOperationTitle')
						if (!this.operation) return titleDetails
						if (this.operation.id) titleDetails += ' №' + this.operation.id
						if (this.operation.type) titleDetails += ': ' + this.$tc(`storeOperations.${this.operation.type}`)
						return titleDetails
					case 'newPut':
						let titlePut = this.$tc('storeOperations.put', 2)
						if (this.storeDetails) titlePut += `. ${this.$tc('store')} "${this.storeDetails.name}"`
						return titlePut
					case 'newTake':
						let titleTake = this.$tc('storeOperations.take', 2)
						if (this.storeDetails) titleTake += `. ${this.$tc('store')} "${this.storeDetails.name}"`
						return titleTake
					case 'newCorrection':
						let titleCorrection = this.$tc('storeOperations.correction', 2)
						if (this.storeDetails) titleCorrection += `. ${this.$tc('store')} "${this.storeDetails.name}"`
						return titleCorrection
					default:
						return this.$tc('error') + '...'
				}
			},

			productsHeaders() {
				let isTake = this.mode == 'newTake' || (this.mode == 'details' && this.operation && this.operation.type == 'take')
				let hasNetCost;
				this.operation.contents.forEach((item) => {
					if (item.netCost === null && !hasNetCost) hasNetCost = false
					else if (item.netCost !== null) hasNetCost = true
				})
				let isGifts = this.storeDetails.type == 'gift'
				let headers = []
				headers.push({ text: this.$tc(this.storeDetails.type), value: 'name', sortable: false })
				headers.push({ text: this.$tc('storePlace'), value: 'storePlaceId', sortable: false })
				headers.push({ text: this.$tc('quantity'), value: 'realUnits', sortable: false, align: 'end' })
				if (hasNetCost && !isTake && !isGifts) headers.push({
					text: this.$tc('costPrice'),
					value: 'netCost',
					sortable: false,
					align: 'end',
				})
				if (this.mode != 'details') headers.push({ value: 'actions', sortable: false, align: 'end', })
				return headers
			},

			filesHeaders() {
				let headers = [
					{ text: this.$tc('title'), value: 'name', sortable: false },
					{ text: this.$tc('file'), value: 'src', sortable: false, align: this.mode == 'details' ? 'end' : 'start' },
				]
				if (this.mode != 'details') headers.push({ value: 'actions', sortable: false, align: 'end' })
				return headers
			},

			storesPlaces() {
				return this.$store.getters['stores/getStorePlacesByStoreId'](this.storeId) || []
			},

			gifts() {
				return this.$store.state.catalog.gifts
			},

			searchProductsResults() {
				return this.$store.state.catalog.searchProductsResults
			}
		},
		
		methods: {
			getInfo() {
				if (!this.storeId) return this.$router.push({ name: 'Stores' })
				this.$store.dispatch('stores/getStoreDetails', {id: this.storeId})
				let rName = this.$route.name
				if (rName === 'StoreOperationDetails') {
					if (!this.id) return this.$router.push({ name: 'StoreDetails', params: { id: this.storeId } })
					this.$store.dispatch('stores/getStoreOperationDetails', {
						id: this.id,
						start: () => this.loadingDetails = true,
						finish: () => {
							let operation = this.$store.state.stores.storeOperationDetails
							if (!operation) {
								this.mode = 'failed'
								return
							}
							operation = JSON.parse(JSON.stringify(operation))
							operation.contents.forEach((item) => {
								item.unitType = item.realUnits >= item.unitFactor ? 'der' : 'base'
							})
							this.operation = operation
							this.mode = 'details'
							this.loadingDetails = false
						}
					})
					return
				} else {
					if (!this.storeDetails || this.storeDetails.id != this.id) {
						this.$store.dispatch('stores/getStoreDetails', { force: true, id: this.storeId })
					}
					this.operation =  {
						storeId: this.storeId,
						contents: [],
						files: [],
					},
					this.$store.dispatch('stores/getStoresPlaces', {
						storeId: this.storeId,
						start: () => this.loadingStoresPlaces = true,
						finish: () => this.loadingStoresPlaces = false,
					})
				}
				if (rName === 'StoreOperationNewPut') this.mode = 'newPut'
				else if (rName === 'StoreOperationNewTake') this.mode = 'newTake'
				else if (rName === 'StoreOperationNewCorrection') this.mode = 'newCorrection'
				else return this.$router.push({ name: 'StoreDetails', params: { id: this.storeId } })
			},

			searchProducts(q) {	
				if (!q || !q.length) {
					this.$store.dispatch('catalog/searchProducts', { q: q })
					return
				}
				let currentThrottleVal = ++this.searchThrottleCounter
				this.searchProductsLoading = true
				setTimeout(() => {
					if (currentThrottleVal != this.searchThrottleCounter) return
					this.$store.dispatch('catalog/searchProducts', {
						q: q,
						then: () => {
							this.searchProductsLoading = false
						}
					})
				}, 500)
			},

			addOperationProduct(product) {
				if (this.operation.contents.findIndex(v => v.productId === product.id) > -1) return
				let storePlaceId
				if (this.storesPlaces[1]) storePlaceId = this.storesPlaces[1].id
				this.operation.contents.push({
					productId: product.id,
					storePlaceId: storePlaceId,
					netCost: undefined,
					realUnits: '',
					productName: product.name,
					unitStep: product.unitStep,
					unitFactor: product.unitFactor,
					unitShortName: product.unitShortName,
					unitShortDerName: product.unitShortDerName,
					price: product.price,
					unitType: 'der',
				})
			},

			addOperationGift(gift) {
				if (this.operation.contents.findIndex(v => v.giftId === gift.id) > -1) return
				let storePlaceId
				if (this.storesPlaces[1]) storePlaceId = this.storesPlaces[1].id
				this.operation.contents.push({
					giftId: gift.id,
					storePlaceId: storePlaceId,
					realUnits: '',
					giftName: gift.name,
					price: gift.price,
				})
			},

			deleteOperationProduct(item) {
				let i = this.operation.contents.findIndex(v => v.productId === item.productId)
				this.operation.contents.splice(i, 1)
			},

			changeUnitType(val, item) {
				if (val == 'der') {
					item.realUnits = this.unitsToDer(item.realUnits, item.unitFactor) || ''
				} else if (val == 'base') {
					item.realUnits = this.unitsToBase(item.realUnits, item.unitFactor) || ''
				}
			},

			openFileToUpload() {
				this.$refs.fileToUpload.$el.children[1].children[0].children[0].children[0].click()
			},

			addOperationFile(file) {				
				this.operation.files.push({ name: file.name, file: file })
			},

			deleteOperationFile(item) {
				let i = this.operation.files.findIndex(v => v.name === item.name && v.src === item.src && v.file === item.file)
				this.operation.files.splice(i, 1)
			},

			saveOperation(force = false) {
				this.saveLoading = true
				let isGifts = this.storeDetails.type == 'gift'
				let formData = new FormData
				if (!force) this.shortageForceProducts = []
				else formData.append('force', true)
				formData.append('storeId', this.storeId)
				for (let i = 0; i < this.operation.contents.length; i++) {
					let item = this.operation.contents[i]
					formData.append(`contents[${i}][storePlaceId]`, item.storePlaceId)
					if (isGifts) {
						formData.append(`contents[${i}][giftId]`, item.giftId)
						formData.append(`contents[${i}][realUnits]`, item.realUnits)
					} else {
						formData.append(`contents[${i}][productId]`, item.productId)
						formData.append(`contents[${i}][realUnits]`, item.unitType == 'der' ? this.unitsToBase(item.realUnits, item.unitFactor) : item.realUnits)
						formData.append(`contents[${i}][netCost]`, item.netCost * 100)
					}
				}
				for (let i = 0; i < this.operation.files.length; i++) {
					let item = this.operation.files[i]
					formData.append(`files[${i}][name]`, item.name)
					formData.append(`files[${i}][src]`, item.file)
				}

				let methodName
				if (this.mode == 'newPut') methodName = 'storePutOperation'
				else if (this.mode == 'newTake') methodName = 'storeTakeOperation'
				else if (this.mode == 'newCorrection') methodName = 'storeCorrectionOperation'
				if (!methodName) {
					this.saveLoading = false
					return
				}
				this.$store.dispatch('stores/' + methodName, {
					isGifts,
					formData, 
					then: () => {
						this.saveLoading = false
						this.$router.push({ name: 'StoreDetails', params: { id: this.storeId } })
					},
					force,
					error: (errors) => {
						if (Array.isArray(errors)) {
							errors.forEach((err) => {
								if (!err.productId && !err.giftId) return
								let i = this.operation.contents.findIndex(v => v.productId === err.productId || v.giftId === err.giftId)
								if (i < 0) return
								let item = JSON.parse(JSON.stringify(this.operation.contents[i]))
								item.shortage = Math.abs(err.shortage)
								this.shortageForceProducts.push(item)
							})
							this.dialogShortageForce = true
						}
					}
				})
			},

			buildFileLink(src) {
				return `${document.location.protocol}//${document.location.host}${src}`
			},

			buildShortageText(product) {
				let units = product.shortage + ' ' + (product.unitShortName || this.$tc('pc'))
				if (this.storeDetails.type == 'product' && product.shortage >= product.unitFactor) {
					units = this.unitsToDer(product.shortage, product.unitFactor) + ' ' + product.unitShortDerName
				}
				return `<b>${product.name}</b>: ${this.$tc('shortage').toLowerCase()} ${units}`
			},

			openDialogSearchGift() {
				this.$store.dispatch('catalog/getGifts')
				this.dialogSearchGift = true
			}
		},
	}
</script>
