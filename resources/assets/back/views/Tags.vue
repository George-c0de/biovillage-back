<template>
	<v-container fluid>
		<h1 class="mb-3">{{ $tc('titles.tags') }}</h1>

		<v-row align="center" class="table-filters">
			<v-col cols="auto">

				<v-text-field
					dense outlined filled
					v-model="filter.search"
					prepend-inner-icon="search"
					:label="$tc('search')"
					single-line
					hide-details
					clearable
				></v-text-field>

			</v-col>
			<v-col cols="auto">

				<v-checkbox
					v-model="filter.activeState"
					:label="$tc('activeOnly')"
					single-line
					hide-details
					class="v-input--selection-controls--label-left mt-0"
				></v-checkbox>

			</v-col>
		</v-row>
		
		<v-data-table
			:headers="headers"
			:items="filteredTags"
			item-key="id"
			sort-by="fixedOrder"
			must-sort
			:items-per-page="filteredTags.length"
			:loading="loading"
			hide-default-footer
			fixed-header
			class="v-data-table--editable"
		>
			<template #body.prepend="{ isMobile }">

				<tr :class="{'v-data-table__mobile-table-row': isMobile}">
					<td :class="{'v-data-table__mobile-row': isMobile}">

						<div :class="{
							'v-data-table__mobile-row__header': isMobile,
							'd-none': !isMobile,
						}">
							{{ headers[0].text }}
						</div>

						<div :class="{'v-data-table__mobile-row__cell': isMobile}">
							<v-text-field
								v-model="newTag.name"
								:placeholder="$tc('new')"
								size="40"
								hide-details
								append-outer-icon="clear"
								class="v-input--changed v-input--hide-append success--text"
							></v-text-field>
						</div>

					</td>
					<td :class="{'v-data-table__mobile-row': isMobile}">

						<div :class="{
							'v-data-table__mobile-row__header': isMobile,
							'd-none': !isMobile,
						}">
							{{ headers[1].text }}
						</div>

						<div :class="{'v-data-table__mobile-row__cell': isMobile}">
							<v-text-field
								v-model="newTag.order"
								size="6"
								hide-details
								append-outer-icon="clear"
								class="v-input--changed v-input--hide-append success--text text-right"
							></v-text-field>
						</div>

					</td>
					<td :class="{'v-data-table__mobile-row': isMobile}">

						<div :class="{
							'v-data-table__mobile-row__header': isMobile,
							'd-none': !isMobile,
						}">
							{{ headers[2].text }}
						</div>

						<div :class="{'v-data-table__mobile-row__cell': isMobile}">
							<v-checkbox
								v-model="newTag.active"
								hide-details
								class="v-input--changed success--text mt-0 mb-n1"
							></v-checkbox>
						</div>

					</td>
					<td class="text-end" :class="{'v-data-table__mobile-row': isMobile}">

						<div :class="{
							'v-data-table__mobile-row__header': isMobile,
							'd-none': !isMobile,
						}">
							{{ headers[3].text }}
						</div>

						<div :class="{'v-data-table__mobile-row__cell': isMobile}">
							<v-icon
								color="success"
								:title="$tc('add')"
								:disabled="!newTag.name || !newTag.order"
								@click="addTag(newTag)"
							>
								add
							</v-icon>
						</div>

					</td>
				</tr>

			</template>
			<template #item.fixedName="{ item }">

				<v-text-field
					v-model="item.name"
					size="40"
					hide-details
					append-outer-icon="clear"
					class="primary--text"
					:class="[item.name === item.fixedName ? 'v-input--unchanged' : 'v-input--changed']"
					@click:append-outer="item.name = item.fixedName"
				></v-text-field>

			</template>
			<template #item.fixedOrder="{ item }">

				<v-text-field
					v-model="item.order"
					size="6"
					hide-details
					append-outer-icon="clear"
					class="primary--text text-right"
					:class="[item.order === item.fixedOrder ? 'v-input--unchanged' : 'v-input--changed']"
					@click:append-outer="item.order = item.fixedOrder"
				></v-text-field>

			</template>
			<template #item.fixedActive="{ item }">
				
				<v-checkbox
					v-model="item.active"
					hide-details
					class="primary--text mt-0 mb-n1"
					:class="[item.active === item.fixedActive ? 'v-input--unchanged' : 'v-input--changed']"
				></v-checkbox>

			</template>
			<template #item.actions="{ item }">

				<v-icon
					color="success"
					:title="$tc('save')"
					class="mr-2"
					@click="updateTag(item)"
				>
					save
				</v-icon>
				<v-icon
					color="error"
					:title="$tc('delete')"
					@click="deleteTag(item)"
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
			@disagree="closeDeleteDialog"
		>
			{{ $tc('areYouSure') + ' ' + $tc('delete').toLowerCase() + ' ' +  $tc('tag') }} <br /> <br /> <b>{{ editedTag.name }}</b>?
		</ConfirmDialog>

	</v-container>
</template>

<script>
import ConfirmDialog from '@bo/components/ConfirmDialog'

function defaultTag() { return {
	name: '',
	order: '100',
	active: true,
}}

export default {
	components: {
		ConfirmDialog,
	},
	
	data() { return {
		loading: false,
		deleteDialog: false,
		newTag: defaultTag(),
		editedTag: {},
		tags: [],

		headers: [
			{
				value: 'fixedName',
				text: this.$tc('title'),
			},
			{
				value: 'fixedOrder',
				text: this.$tc('sortOrder'),
				align: 'end',
			},
			{
				value: 'fixedActive',
				text: this.$tc('activity'),
				sortable: false,
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
		deleteTag(tag) {
			this.editedTag = Object.assign({}, tag)
			this.deleteDialog = true
		},
		
		confirmDeleteDialog() {
			this.$store.dispatch('general/deleteTag', this.editedTag.id)
			this.closeDeleteDialog()
		},
		
		closeDeleteDialog() {
			this.deleteDialog = false
			// this.editedTag = {}
		},
		
		addTag(tag) {
			this.$store.dispatch('general/addTag', {
				data: tag,
				then: ()=> this.newTag = defaultTag()
			})
		},
		
		updateTag(tag) {
			this.$store.dispatch('general/updateTag', {data: tag, id: tag.id})
		},
		
	},

	computed: {
		filter() {
			return this.$store.state.general.tagsFilter
		},

		filteredTags() {
			return this.tags.filter((item)=> Boolean(
				(!this.filter.search || item.name.toLowerCase().includes(this.filter.search.toLowerCase()))
				&& (this.filter.activeIndeterminate || item.fixedActive)
			))
		},
		
	},

	watch: {
		'$store.state.general.tags': {
			immediate: true,
			handler(v) {
				let tags = JSON.parse(JSON.stringify(v))
				// для отключения сортировки и фильтра "на лету"
				tags.forEach(item => {
					item.fixedOrder = item.order
					item.fixedName = item.name
					item.fixedActive = item.active
				})
				this.tags = tags
			}
		},

	},
	
	created() {
		this.$store.dispatch('general/getTags', {
			start: ()=> this.loading = true,
			finish: ()=> this.loading = false,
		})

	},
	
}
</script>
