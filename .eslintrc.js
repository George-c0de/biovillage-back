module.exports = {
	env: {
		node: true,
		
	},

	extends: [
		// 'eslint:recommended',
		'plugin:vue/essential',

	],

	rules: {
		'vue/valid-v-slot': ['error', {
			'allowModifiers': true,
		}],
		
	},

}
