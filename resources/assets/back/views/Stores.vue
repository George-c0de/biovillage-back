<template>
	<v-container fluid>
		
		<v-data-table
			:headers="headers"
			:items="stores"
			item-key="id"
			must-sort
			:sort-by="['order']"
			:loading="loading"
			fixed-header
		>
			<template #top>
				<v-row class="align-center mb-6">
					<v-col class="col-auto">
						<h1>{{ $tc('titles.stores') }}</h1>
					</v-col>
					<v-col class="col-auto">
						<v-btn color="success" @click="dialogCreate = true">
							<v-icon left>add</v-icon>{{ $tc('add') }}
						</v-btn>
					</v-col>
				</v-row>
			</template>

			<template #item.id="{ item }">{{ item.id }}</template>

			<template #item.type="{ item }">{{ $tc(item.type, 2) }}</template>

			<template #item.name="{ item }">
				<router-link :to="{name: 'StoreDetails', params: {id: item.id}}">{{item.name}}</router-link>
			</template>
			
			<template #item.actions="{ item }">
				<div class="d-inline-flex">
					<v-btn icon class="mr-1" color="primary" :to="{name: 'StoreOperationNewPut', params: {storeId: item.id}}">
						<v-icon :title="$tc('storeOperations.put')">add_circle</v-icon>
					</v-btn>
					<v-btn icon class="mr-4" color="primary" :to="{name: 'StoreOperationNewTake', params: {storeId: item.id}}">
						<v-icon :title="$tc('storeOperations.take')">remove_circle</v-icon>
					</v-btn>
					<v-btn icon class="mr-1" outlined color="success" :to="{name: 'StoreDetails', params: {id: item.id}}">
						<v-icon :title="$tc('edit')">edit</v-icon>
					</v-btn>
					<v-btn icon color="error" outlined @click="deleteStore(item)">
						<v-icon :title="$tc('delete')">delete</v-icon>
					</v-btn>
				</div>
			</template>
			
		</v-data-table>

		<ConfirmDialog
			:show="dialogDelete"
			:title="$tc('deletion')"
			agreeIcon="delete"
			@agree="confirmStoreDelete"
			@disagree="dialogDelete = false"
		>
			<div v-if="delitingStore">
				{{ $tc('areYouSure') + ' ' + $tc('delete').toLowerCase() + ' ' +  $tc('store').toLowerCase() }} <br /> 
				<b>{{ delitingStore.name }}</b>?
			</div>
		</ConfirmDialog>

		<v-dialog v-model="dialogCreate" max-width="500px">
			<v-card>
				<v-card-title class="headline justify-center mb-6">
					{{ $tc('titles.createNewStore') }}
				</v-card-title>
				<v-card-text>
					<v-form @submit.prevent="createStore()">
						<v-text-field
							dense outlined filled
							v-model="newStore.name"
							name="name"
							:label="$tc('title')"
						></v-text-field>
						<v-select
							dense outlined filled
							v-model="newStore.type"
							:items="storeTypes"
							name="type"
							:label="$tc('storeType')"
						></v-select>
						<v-text-field
							dense outlined filled
							v-model="newStore.order"
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
				<v-btn icon absolute color="grey" top right @click="dialogCreate = false">
					<v-icon>close</v-icon>
				</v-btn>
			</v-card>
		</v-dialog>
		
	</v-container>
</template>

<script>
	import ConfirmDialog from '@bo/components/ConfirmDialog'

	export default {

		components: {
			ConfirmDialog,
		},

		created() {
			this.getStores(false)
		},

		watch: {
			'$store.state.stores.stores': {
				immediate: true,
				handler(v) {
					this.stores = JSON.parse(JSON.stringify(v))
				}
			},
		},

		data() { 
			return {
				stores: [],
				loading: false,
				force: false,
				dialogCreate: false,
				dialogDelete: false,
				delitingStore: undefined,
				headers: [
					{
						text: this.$tc('id'),
						value: 'id',
						width: '80px'
					},
					{
						text: this.$tc('storeType'),
						value: 'type',
						width: '120px'
					},
					{
						text: this.$tc('storeName'),
						value: 'name',
					},
					{
						text: this.$tc('priority'),
						value: 'order',
					},
					{
						text: this.$tc('actions'),
						value: 'actions',
						sortable: false,
						width: '180px',
					},
				],
				newStore: {
					name: '',
					order: 100,
					type: 'product'
				},
				storeTypes: [
					{ text: this.$tc('product', 2), value: 'product' },
					{ text: this.$tc('gift', 2), value: 'gift' },
				]
			}
		},
		
		methods: {
			getStores(force = true) {
				setTimeout(() => {
					this.$store.dispatch('stores/getStores', {
						force,
						start: () => this.loading = true,
						finish: () => this.loading = false,
					})
					this.force = true
				})
			},

			deleteStore(item) {
				this.delitingStore = item
				this.dialogDelete = true
			},

			confirmStoreDelete() {
				this.$store.dispatch('stores/deleteStore', {
					id: this.delitingStore.id,
					then: () => this.dialogDelete = false,
				})
			},

			createStore() {
				this.$store.dispatch('stores/createStore', {
					newStore: this.newStore,
					then: () => {
						this.dialogCreate = false;
						this.newStore = {
							name: '',
							order: 100,
						}
					}
				})
			},
		},

	}
</script>
