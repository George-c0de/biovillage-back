<template>
	<v-container fluid v-if="admin.id || !id">
		<h1 class="mb-3">{{ title }}</h1>

		<v-row v-if="admin.id" align="end" class="mb-3">
			<v-col cols="12" sm="6" lg="auto">

				<v-text-field
					:value="admin.createdAt || ' '"
					:label="$tc('created')"
					placeholder=" "
					readonly
					append-outer-icon="check"
					hide-details
				></v-text-field>
			
			</v-col>
			<v-col cols="12" sm="6" lg="auto">

				<v-text-field
					:value="admin.updatedAt || ' '"
					:label="$tc('updated')"
					placeholder=" "
					readonly
					hide-details
					append-outer-icon="update"
				></v-text-field>

			</v-col>
		</v-row>
		
		<v-form @submit.prevent="saveAdmin($event.target)">

			<v-text-field
				v-model="admin.name"
				:label="$tc('name')"
				name="name"
				prepend-icon="perm_identity"
			></v-text-field>

			<v-text-field
				v-model="admin.phone"
				:label="$tc('phone')"
				name="phone"
				prepend-icon="phone"
			></v-text-field>

			<v-text-field
				v-model="admin.password"
				name="password"
				type="password"
				:label="$tc('password')"
				prepend-icon="lock"
			></v-text-field>

			<v-select
				v-model="admin.roles"
				name="roles[]"
				:items="adminRoles"
				:label="$tc('role', 1)"
				multiple
				clearable
				outlined
				class="mt-2"
			>
				<template #selection="{ item }">

					<v-chip
						:color="item.color"
						label
						close
						:ripple="false"
						@click:close="admin.roles.splice(admin.roles.indexOf(item.value), 1)"
					>{{ item.text }}</v-chip>

				</template>
			</v-select>

			<v-row class="align-self-start mt-auto mb-0">
				<v-col cols="auto">

					<v-btn
						type="submit"
						color="success"
						:disabled="disabledSave"
					>
						<v-icon left>
							save
						</v-icon>
						{{ $tc('save') }}
					</v-btn>

				</v-col>
				
				<v-col cols="auto" v-if="admin.id">

					<v-btn
						color="error"
						@click="deleteDialog = true"
					>
						<v-icon left>
							delete
						</v-icon>
						{{ $tc('delete') }}
					</v-btn>

				</v-col>
			</v-row>
			
		</v-form>
		
		<ConfirmDialog
			:show="deleteDialog"
			:title="$tc('deletion')"
			agreeIcon="delete"
			@agree="confirmDeleteDialog"
			@disagree="deleteDialog = false"
		>
			{{ $tc('areYouSure') + ' ' + $tc('delete').toLowerCase() + ' ' +  $tc('adminEdit').toLowerCase() }}  <br /> <b>{{ admin.name }}</b>?
		</ConfirmDialog>
		
	</v-container>
</template>

<script>
import ConfirmDialog from '@bo/components/ConfirmDialog'
import {adminRoles} from '@bo/mixins'

export default {
	props: ['id'],
	
	components: {
		ConfirmDialog,
	},

	mixins: [adminRoles],
	
	data() { return {
		title: '',
		titleCreate: this.$tc('add') + ' ' + this.$tc('adminEdit').toLowerCase(),
		admin: {},
		newAdmin: {},
		deleteDialog: false,
		disabledSave: false,

	}},
	
	methods: {
		saveAdmin(form) {
			// let data = new FormData(form),
			let data = this.admin,
				id = this.admin.id,
				finish = ()=> {
					this.disabledSave = false
				}

			this.disabledSave = true

			id
				? this.$store.dispatch('general/editAdmin', {id, data, finish})
				: this.$store.dispatch('general/createAdmin', {data, finish})
		},
		
		confirmDeleteDialog() {
			this.$store.dispatch('general/deleteAdmin', {
				id: this.admin.id,
				then: ()=> this.deleteDialog = false
			})
			
		},
		
	},
	
	watch: {
		'$store.state.general.adminAccount': {
			immediate: true,
			handler(v) {
				this.admin = JSON.parse(JSON.stringify(v.id ? v : this.newAdmin))

				this.title = v.name !== undefined ? v.name : this.titleCreate

				this.admin.roles = this.sortAdminRoles(v.roles)

			},
		},
	},
	
	created() {
		this.$store.dispatch('general/getAdminAccount', {id: this.id})
		
	},
	
}
</script>
