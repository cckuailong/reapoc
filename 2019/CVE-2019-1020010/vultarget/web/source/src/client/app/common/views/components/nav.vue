<template>
<span class="mk-nav">
	<a :href="aboutUrl">{{ $t('about') }}</a>
	<i>・</i>
	<a href="/dev" target="_blank">{{ $t('develop') }}</a>
</span>
</template>

<script lang="ts">
import Vue from 'vue';
import i18n from '../../../i18n';
import { lang } from '../../../config';

export default Vue.extend({
	i18n: i18n('common/views/components/nav.vue'),
	data() {
		return {
			aboutUrl: `/docs/${lang}/about`,
			repositoryUrl: 'https://github.com/',
			feedbackUrl: 'https://github.com/',
			ToSUrl: null
		}
	},

	mounted() {
		this.$root.getMeta(true).then(meta => {
			this.repositoryUrl = meta.repositoryUrl;
			this.feedbackUrl = meta.feedbackUrl;
			this.ToSUrl = meta.ToSUrl;
		})
	}
});
</script>

<style lang="stylus" scoped>
.mk-nav
	a
		color inherit
</style>
