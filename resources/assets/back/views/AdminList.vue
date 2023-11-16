<template>
	<v-container fluid>
		
		<v-data-table
			:headers="headers"
			:items="adminList"
			item-key="id"
			sort-by="id"
			must-sort
			disable-pagination
			:loading="loading"
			:search="filter.search"
			:custom-filter="customFilter"
			hide-default-footer
			fixed-header
		>
			<template #top>

				<v-row justify="space-between" align="end" class="mb-0">
					<v-col cols="auto">

						<h1> {{ $tc('titles.adminList') }}</h1>

					</v-col>
					<v-col cols="auto">

						<v-btn
							color="success"
							:to="{name: 'AdminAccountNew'}"
						>
							<v-icon left>
								add
							</v-icon>
							{{ $tc('add') }}
						</v-btn>

					</v-col>
				</v-row>

				<v-row class="table-filters">
					<v-col cols="auto">
								
						<v-text-field
							v-model="filter.search"
							dense outlined filled
							prepend-inner-icon="search"
							:label="$tc('search')"
							size="30"
							single-line
							hide-details
							clearable
						></v-text-field>
						
					</v-col>
				</v-row>
			</template>

			<template #item.id="{ item }">

				<router-link :to="{name: 'AdminAccountEdit', params: {id: item.id}}">
					{{ item.id }}
				</router-link>

			</template>
			<template #item.roles="{ item }">

				<v-chip
					v-for="role in item.roles"
					:key="role"
					:color="adminRoles.find(v => v.value === role).color"
					:ripple="false"
					label
					class="ma-1"
				>{{ adminRoles.find(v => v.value === role).text }}</v-chip>

			</template>
			<template #item.actions="{ item }">

				<v-icon
					color="error"
					@click="deleteAdmin(item)"
				>
					delete
				</v-icon>

			</template>
		</v-data-table>

		<ConfirmDialog
			:show="deleteDialog"
			:title="$tc('deletion')"
			agreeIcon="delete"
			@agree="confirmDeleteDialog"
			@disagree="deleteDialog = false"
		>
		{{ $tc('areYouSure') + ' ' + $tc('delete').toLowerCase() + ' ' +  $tc('adminEdit') }} <br /> <b>{{ editedAdmin.name }}</b>?
		</ConfirmDialog>

	</v-container>
</template>

<script>
import ConfirmDialog from '@bo/components/ConfirmDialog'
import {adminRoles} from '@bo/mixins'

export default {
	components: {
		ConfirmDialog,
	},

	mixins: [adminRoles],

	data() { return {
		adminList: [],
		loading: false,
		deleteDialog: false,
		editedAdmin: {},

		headers: [
			{
				text: this.$tc('id'),
				value: 'id',
			},
			{
				text: this.$tc('name'),
				value: 'name',
			},
			{
				text: this.$tc('phone'),
				value: 'phone',
			},
			{
				text: this.$tc('role', 2),
				value: 'roles',
				sort: this.sortAdminRolesArrays,
			},
			{
				value: 'actions',
				text: '',
				sortable: false,
				align: 'end',
			},
		],

	}},
	
	methods: {
		customFilter(value, search, item) {
			return search !== null
				&& (
					item.name.toString().toLocaleLowerCase().indexOf(search.toLocaleLowerCase()) !== -1
					|| (
						search.replace(/\D/g,'') !== '' &&
						item.phone.toString().replace(/\D/g,'').indexOf(search.replace(/\D/g,'')) !== -1
					)
				)
		},

		deleteAdmin(admin) {
			this.editedAdmin = admin
			this.deleteDialog = true
		},

		confirmDeleteDialog() {
			this.$store.dispatch('general/deleteAdmin', {
				id: this.editedAdmin.id,
				then: ()=> this.deleteDialog = false
			})
		},
		
	},

	computed: {
		filter() {
			return this.$store.state.general.adminListFilter
		},

	},

	created() {
		this.$store.dispatch('general/getAdminList', {
			start: ()=> this.loading = true,
			finish: ()=> this.loading = false,
		})

	},

	watch: {
		'$store.state.general.adminList': {
			immediate: true,
			handler(v) {
				v.forEach(item => {
					item.roles = this.sortAdminRoles(item.roles)
				})
				this.adminList = JSON.parse(JSON.stringify(v))
			}
		},
		
	},

}
</script>
	
