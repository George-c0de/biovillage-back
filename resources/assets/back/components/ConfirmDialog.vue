<template>
	<v-dialog
		:value="show"
		persistent
		:max-width="maxWidth"
		@click:outside="$emit('disagree')"
	>
		<v-card>
			<v-card-title class="headline justify-center">
				<slot name="title">
					{{ title }}
				</slot>
			</v-card-title>

			<v-card-text class="text-center">
				<slot>{{ $tc('areYouSure') + ' ' + $tc('performThisAction') }}</slot>
			</v-card-text>
			
			<v-card-actions class="pb-4" :style="{'overflow': 'hidden'}">
				
				<v-row justify="space-between" class="mx-0">
					<v-col>
						
						<v-btn
							block
							:color="agreeColor"
							large
							@click="$emit('agree')"
						>
							<v-icon v-if="agreeIcon" left>
								{{ agreeIcon }}
							</v-icon>
							{{ $tc(agreeText) }}
						</v-btn>

					</v-col>
					<v-col>

						<v-btn
							block
							text
							color="info"
							large
							@click="$emit('disagree')"
						>
							<v-icon left>
								cancel
							</v-icon>
							{{ $tc('no') }}
							<!-- <v-icon right>
								cancel
							</v-icon> -->
						</v-btn>
					</v-col>

				</v-row>

			</v-card-actions>
		</v-card>
	</v-dialog>
</template>

<script>
export default {
	props: {
		show: {
			type: Boolean,
			required: true,
		},
		title: {
			type: String,
			default: function() {
				this.$tc('confirmation')
			},
		},
		agreeText: {
			type: String,
			default: 'yes',
		},
		agreeColor: {
			type: String,
			default: 'error',
		},
		agreeIcon: {
			type: String,
			default: 'check'
		},
		maxWidth: {
			type: String,
			default: '400px'
		}
	},
	
}
</script>
