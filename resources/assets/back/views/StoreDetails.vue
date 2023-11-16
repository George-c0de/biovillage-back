<template>
	<v-container fluid v-if="store">

		<v-row class="align-center">
			<v-col class="col-auto">
				<h1>
					<router-link :to="{name: 'Stores'}" class="navigation-icon navigation-icon--back">
						<v-icon>arrow_back_ios</v-icon>
					</router-link>
					{{ $tc('store') + ': ' + store.name }}
				</h1>
			</v-col>
			<v-col class="col-auto">
				<v-btn small color="primary" @click="dialogStoreEdit = true">
					<v-icon small left>edit</v-icon> {{ $tc('edit') }}
				</v-btn>
			</v-col>
		</v-row>
		<div class="subtitle-2"><b>{{ $tc('storeType') }}</b>: {{ $tc(store.type, 2) }}</div>

		<v-tabs v-model="tab" class="mt-10 mb-6">
			<v-tabs-slider color="primary"></v-tabs-slider>
			<v-tab>{{ $tc('titles.storeOperations') }}</v-tab>
			<v-tab>{{ $tc(store.type, 2) }}</v-tab>
			<v-tab>{{ $tc('titles.storePlaces') }}</v-tab>
		</v-tabs>

		<v-tabs-items v-model="tab">

			<v-tab-item>
				<v-data-table
					:headers="storeHistoryHeaders"
					:items="storeHistory"
					item-key="id"
					:server-items-length="historyPager.total"
					:options.sync="historyFilterOptions"
					:loading="storeHistoryLoading"
					fixed-header
					:footer-props="{'items-per-page-options': [10, 15, 25]}"
					@update:options="getStoreHistory()"
				>
					<template #top>
						<v-row class="mb-3" align="center">
							<v-col cols="auto">
								<h1>{{ $tc('titles.storeOperations') }}</h1>
							</v-col>
							<v-col cols="auto">
								<v-btn icon outlined color="primary" @click="getStoreHistory()">
									<v-icon>refresh</v-icon>
								</v-btn>
							</v-col>
						</v-row>
						<v-row class="mb-4">
							<v-col cols="auto">
								<v-btn color="primary" :to="{ name: 'StoreOperationNewPut', params: { storeId: id } }">
									<v-icon left>add</v-icon> {{ $tc('storeOperations.put') }}
								</v-btn>
							</v-col>
							<v-col cols="auto">
								<v-btn color="primary" :to="{ name: 'StoreOperationNewTake', params: { storeId: id } }">
									<v-icon left>remove</v-icon> {{ $tc('storeOperations.take') }}
								</v-btn>
							</v-col>
							<v-spacer></v-spacer>
							<v-col cols="auto">
								<v-menu offset-y nudge-bottom="5">
									<template v-slot:activator="{ on }">
										<v-btn icon v-on="on" outlined color="primary">
											<v-icon>more_vert</v-icon>
										</v-btn>
									</template>
									<v-btn color="error" :to="{ name: 'StoreOperationNewCorrection', params: { storeId: id } }">
										<v-icon left>settings</v-icon> {{ $tc('storeOperations.correction') }}
									</v-btn>
								</v-menu>
							</v-col>
						</v-row>
						<v-row class="table-filters">

							<v-col cols="auto">
								<div class="v-label theme--light">
									<v-icon class="pb-1">date_range</v-icon> {{ $tc('created') }}
								</div>
								<v-row align="end" class="mt-n1">
									<v-col cols="auto" class="mr-n3">
										<DateInput
											v-model="historyFilter.createdAtBegin"
											dense outlined filled
											:label="$tc('dateStarts')"
											clearable
											hide-details
											@input="getStoreHistory()"
											@clear="getStoreHistory()"
										/>
									</v-col>
									<v-col cols="auto">
										<DateInput
											v-model="historyFilter.createdAtEnd"
											dense outlined filled
											:label="$tc('dateEnds')"
											clearable
											hide-details
											@input="getStoreHistory()"
											@clear="getStoreHistory()"
										/>
									</v-col>
								</v-row>
								
							</v-col>

							<v-col cols="auto">
								<div class="v-label theme--light">
									<v-icon class="pb-1">perm_identity</v-icon> {{ $tc('user') }}
								</div>
								<v-row align="end" class="mt-n1">
									<v-col cols="auto" class="mr-n3">
										<v-text-field
											v-model="historyFilter.clientId"
											dense outlined filled
											:label="$tc('clientId')"
											hide-details
											clearable
											size="12"
											@change.native="getStoreHistory()"
											@click:clear="getStoreHistory()"
										></v-text-field>
									</v-col>
									<v-col cols="auto">
										<v-text-field
											v-model="historyFilter.adminId"
											dense outlined filled
											:label="$tc('adminId')"
											hide-details
											clearable
											size="12"
											@change.native="getStoreHistory()"
											@click:clear="getStoreHistory()"
										></v-text-field>
									</v-col>
								</v-row>
							</v-col>

						</v-row>
					</template>

					<template #item.createdAt="{ item }">
						{{ new Date(item.createdAt).toLocaleDateString() }} {{ new Date(item.createdAt).toLocaleTimeString() }}
					</template>

					<template #item.type="{ item }">
						{{ $tc('storeOperations.'+item.type) }}
					</template>

					<template #item.status="{ item }">
						<v-icon v-if="item.status == 'completed'" color="primary" class="mt-n1">check</v-icon>
						<v-icon v-else-if="item.status == 'reserve'" color="primary" class="mt-n1">inventory</v-icon>
						{{ $tc('storeOperationStatus.' + item.status) }}
					</template>

					<template #item.user="{ item }">
						<span v-if="item.adminId">{{ $tc('admin') }} [<router-link :to="{name: 'AdminAccountEdit', params: {id: item.adminId }}">{{ item.adminId }}</router-link>]</span>
						<span v-else-if="item.clientId">{{ $tc('client') }} [<router-link :to="{name: 'ClientAccount', params: {id: item.clientId }}">{{ item.clientId }}</router-link>]</span>
						<span v-else>--</span>
					</template>

					<template #item.actions="{ item }">
						<div class="d-flex justify-end">
							<v-icon v-if="item.isFiles" :title=" $tc('hasAttachedFiles')" color="primary" class="mr-2">attach_file</v-icon>
							<v-btn 
								small color="primary" 
								:to="{ name: 'StoreOperationDetails', params: { storeId: id, id: item.id } }"
							>{{ $tc('moreDetailed') }}</v-btn>
						</div>
					</template>
	
				</v-data-table>
			</v-tab-item>

			<v-tab-item>
				<v-data-table
					:headers="storeProductsHeaders"
					:items="storeProducts"
					:server-items-length="productsPager.total"
					:options.sync="productsFilterOptions"
					:loading="storeProductsLoading"
					fixed-header
					:footer-props="{'items-per-page-options': [10, 15, 25]}"
					@update:options="getStoreProducts()"
				>
					<template #top>
						<v-row class="mb-4" align="center">
							<v-col cols="auto">
								<h1>{{ $tc(store.type, 2) }}</h1>
							</v-col>
							<v-col cols="auto">
								<v-btn icon outlined color="primary" @click="getStoreProducts()">
									<v-icon>refresh</v-icon>
								</v-btn>
							</v-col>
						</v-row>
					</template>

					<template #item.name="{ item }">
						<template v-if="item.productId">
							[<router-link :to="{name: 'CatalogProductEdit', params: {id: item.productId }}">{{ item.productId }}</router-link>]
							{{ item.productName }}
						</template>
						<template v-else-if="item.giftId">
							[<router-link :to="{name: 'Gifts', params: {giftId: item.giftId }}">{{ item.giftId }}</router-link>]
							{{ item.giftName }}
						</template>
					</template>

					<template #item.storePlaceId="{ item }">
						[{{ item.storePlaceId }}] {{ item.storePlaceName }}
					</template>

					<template #item.realUnits="{ item }">
						{{ item.giftId ? item.realUnits + ' ' + $tc('pc') : autoFormatUnits({ item }) }}
					</template>
	
				</v-data-table>
			</v-tab-item>

			<v-tab-item>
				<v-data-table
					:headers="storesPlacesHeaders"
					:items="storesPlaces"
					item-key="id"
					must-sort
					:sort-by="['order']"
					:loading="loadingStoresPlaces"
					fixed-header
				>
					<template #top>
						<v-row class="align-center mb-6">
							<v-col class="col-auto">
								<h1>{{ $tc('titles.storePlaces') }}</h1>
							</v-col>
							<v-col class="col-auto">
								<v-btn color="success" @click="openCreateStorePlaceDialog()">
									<v-icon left>add</v-icon>{{ $tc('add') }}
								</v-btn>
							</v-col>
						</v-row>
					</template>

					<template #item.id="{ item }">{{ item.id }}</template>
					
					<template #item.name="{ item }">{{item.name}}</template>
					
					<template #item.actions="{ item }">
						<div class="d-inline-flex">
							<v-btn icon class="mr-1" color="primary" @click="openEditStorePlaceDialog(item)">
								<v-icon color="primary" :title="$tc('edit')">edit</v-icon>
							</v-btn>
							<v-btn icon color="error" @click="deleteStorePlace(item)">
								<v-icon	color="error" :title="$tc('delete')">delete</v-icon>
							</v-btn>
						</div>
					</template>
					
				</v-data-table>
			</v-tab-item>

		</v-tabs-items>

		<ConfirmDialog
			:show="dialogDeleteStorePlace"
			:title="$tc('deletion')"
			agreeIcon="delete"
			@agree="confirmDeleteStorePlace"
			@disagree="dialogDeleteStorePlace = false"
		>
			<div v-if="delitingStorePlace">
				{{ $tc('areYouSure') + ' ' + $tc('delete').toLowerCase() + ' ' +  $tc('storePlace').toLowerCase() }} <br /> 
				<b>{{ delitingStorePlace.name }}</b>?
			</div>
		</ConfirmDialog>

		<v-dialog v-model="dialogStoreEdit" max-width="560px">
			<v-card>
				<v-card-title class="headline justify-center mb-6">
					{{ $tc('store') + ': ' + store.name }}
				</v-card-title>
				<v-card-text>
					<v-form @submit.prevent="saveStore()">
						<v-text-field
							dense outlined filled
							v-model="store.name"
							name="name"
							:label="$tc('title')"
						></v-text-field>
						<v-text-field
							dense outlined filled
							v-model="store.order"
							name="order"
							:label="$tc('sortOrder')"
						></v-text-field>
						<v-row>
							<v-col class="col-auto">
								<v-btn type="submit" color="success" :loading="updateLoading">
									<v-icon left>save</v-icon> {{ $tc('save') }}
								</v-btn>
							</v-col>
						</v-row>
					</v-form>
				</v-card-text>
				<v-btn icon absolute color="grey" top right @click="dialogStoreEdit = false">
					<v-icon>close</v-icon>
				</v-btn>
			</v-card>
		</v-dialog>

		<v-dialog v-model="dialogStorePlace" max-width="560px">
			<v-card v-if="editingStorePlace">
				<v-card-title class="headline justify-center mb-6">
					{{ editingStorePlace.id ? $tc('titles.updateStorePlace') : $tc('titles.createNewStorePlace') }}
				</v-card-title>
				<v-card-text>
					<v-form @submit.prevent="createUpdateStorePlace()">
						<v-text-field
							dense outlined filled
							v-model="editingStorePlace.name"
							name="name"
							:label="$tc('title')"
						></v-text-field>
						<v-text-field
							dense outlined filled
							v-model="editingStorePlace.order"
							name="order"
							:label="$tc('sortOrder')"
						></v-text-field>
						<div class="d-flex mt-2">
							<v-btn type="submit" color="success">
								<v-icon left>save</v-icon>
								{{ $tc('save') }}
							</v-btn>
						</div>
					</v-form>
				</v-card-text>
				<v-btn icon absolute color="grey" top right @click="dialogStorePlace = false">
					<v-icon>close</v-icon>
				</v-btn>
			</v-card>
		</v-dialog>

	</v-container>
</template>

<script>
import ConfirmDialog from '@bo/components/ConfirmDialog'
import DateInput from '@bo/components/DateInput'
import {numberByThousands, formatUnits} from '@bo/mixins.js'

export default {

	props: ['id'],
	
	components: {
		ConfirmDialog,
		DateInput,
	},

	mixins: [numberByThousands, formatUnits],

	created() {
		this.$store.dispatch('stores/getStoreDetails', {id: this.id})
		this.$store.dispatch('stores/getStoresPlaces', {
			storeId: this.id,
			start: () => this.loadingStoresPlaces = true,
			finish: () => this.loadingStoresPlaces = false,
		})
	},

	watch: {
		'$store.state.stores.storeDetails': {
			immediate: true,
			handler(v) {
				this.store = v ? JSON.parse(JSON.stringify(v)) : undefined
			},
		},

		tab(v) {
			if (v == 0) this.getStoreHistory()
			else if (v == 1) this.getStoreProducts()
		}
	},
	
	data() { 
		return {
			store: undefined,
			updateLoading: false,
			dialogStoreEdit: false,
			tab: 0,

			loadingStoresPlaces: false,
			storesPlacesHeaders: [
				{
					text: this.$tc('id'),
					value: 'id',
				},
				{
					text: this.$tc('storePlaceName'),
					value: 'name',
				},
				{
					text: this.$tc('actions'),
					value: 'actions',
					sortable: false,
					align: 'end',
				},
			],
			delitingStorePlace: undefined,
			dialogDeleteStorePlace: false,
			dialogStorePlace: false,
			editingStorePlace: undefined,
			newStorePlace: {
				name: '',
				order: 100,
			},

			storeHistoryForce: false,
			storeHistoryLoading: false,
			storeHistoryHeaders: [
				{
					text: this.$tc('id'),
					value: 'id',
				},
				{
					text: this.$tc('date'),
					value: 'createdAt',
				},
				{
					text: this.$tc('user'),
					value: 'user',
					sortable: false,
				},
				{
					text: this.$tc('storeOperationType'),
					value: 'type',
					sortable: false,
				},
				{
					text: this.$tc('status'),
					value: 'status',
					sortable: false,
				},
				{
					value: 'actions',
					sortable: false,
					align: 'end',
				},
			],

			storeProductsHeaders: [
				{
					text: this.$tc('title'),
					value: 'name',
					sortable: false,
				},
				{
					text: this.$tc('storePlace'),
					value: 'storePlaceId',
					sortable: false,
				},
				{
					text: this.$tc('quantity'),
					value: 'realUnits',
					sortable: false,
					align: 'end',
				}
			],
			storeProductsLoading: false,
			storeProductsForce: false,

		}
	},

	computed: {
		storesPlaces() {
			return this.$store.getters['stores/getStorePlacesByStoreId'](this.id)
		},

		historyPager() {
			return this.$store.state.stores.storeHistoryPager
		},

		historyFilter() {
			return this.$store.state.stores.storeHistoryFilter
		},

		storeHistory() {
			return this.$store.state.stores.storeHistory
		},

		historyFilterOptions: {
			get() {
				let f = this.historyFilter
				return {
					page: f.page,
					itemsPerPage: f.perPage,
					sortBy: [f.sort],
					sortDesc: [f.sortDirect === 'desc' ? true : false],
				}
			},

			set(v) {
				let f = this.historyFilter
				f.page = v.page
				f.perPage = v.itemsPerPage,
				f.sort = v.sortBy[0],
				f.sortDirect = v.sortDesc[0] ? 'desc' : 'asc'
			},
		},

		productsPager() {
			return this.$store.state.stores.storeProductsPager
		},

		productsFilter() {
			return this.$store.state.stores.storeHistoryFilter
		},

		storeProducts() {
			return this.$store.state.stores.storeProducts
		},

		productsFilterOptions: {
			get() {
				let f = this.productsFilter
				return {
					page: f.page,
					itemsPerPage: f.perPage,
					sortBy: [f.sort],
					sortDesc: [f.sortDirect === 'desc' ? true : false],
				}
			},

			set(v) {
				let f = this.productsFilter
				f.page = v.page
				f.perPage = v.itemsPerPage,
				f.sort = v.sortBy[0],
				f.sortDirect = v.sortDesc[0] ? 'desc' : 'asc'
			},

		},
	},
	
	methods: {
		saveStore() {
			this.updateLoading = true
			this.$store.dispatch('stores/updateStore', {
				store: this.store,
				then: () => {
					this.updateLoading = false
					this.dialogStoreEdit = false
				}
			})
		},

		deleteStorePlace(item) {
			this.delitingStorePlace = item
			this.dialogDeleteStorePlace = true
		},

		confirmDeleteStorePlace() {
			this.$store.dispatch('stores/deleteStorePlace', {
				id: this.delitingStorePlace.id,
				then: () => this.dialogDeleteStorePlace = false,
			})
		},

		createUpdateStorePlace() {
			if (this.editingStorePlace.id) {
				this.$store.dispatch('stores/updateStorePlace', {
					storePlace: this.editingStorePlace,
					then: () => {
						this.dialogStorePlace = false
					}
				})
			} else {
				this.$store.dispatch('stores/createStorePlace', {
					storePlace: this.editingStorePlace,
					storeId: this.id,
					then: () => {
						this.dialogStorePlace = false
					}
				})
			}
		},

		openCreateStorePlaceDialog() {
			this.editingStorePlace = JSON.parse(JSON.stringify(this.newStorePlace))
			this.dialogStorePlace = true
		},

		openEditStorePlaceDialog(storePlace) {
			this.editingStorePlace = JSON.parse(JSON.stringify(storePlace))
			this.dialogStorePlace = true
		},

		getStoreHistory() {
			setTimeout(() => {
				this.$store.dispatch('stores/getStoreHistory', {
					force: this.storeHistoryForce,
					storeId: this.id,
					params: this.historyFilter,
					start: () => this.storeHistoryLoading = true,
					finish: () => this.storeHistoryLoading = false,
				})
				this.storeHistoryForce = true
			})
		},

		getStoreProducts() {
			setTimeout(() => {
				this.$store.dispatch('stores/getStoreProducts', {
					id: this.id,
					force: this.storeProductsForce,
					params: this.productsFilter,
					start: () => this.storeProductsLoading = true,
					finish: () => this.storeProductsLoading = false,
				})
				this.storeProductsForce = true
			})
		}
	}
}
</script>
