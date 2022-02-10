<script type="text/x-template" id="sb-confirm-dialog-component">
	<div class="sb-dialog-ctn sb-fs-boss cff-fb-center-boss" v-if="dialogBoxElement.active">
		<div class="sb-dialog-popup cff-fb-popup-inside">
			<div class="cff-fb-popup-cls" @click.prevent.default="closeConfirmDialog"><svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M14 1.41L12.59 0L7 5.59L1.41 0L0 1.41L5.59 7L0 12.59L1.41 14L7 8.41L12.59 14L14 12.59L8.41 7L14 1.41Z" fill="#141B38"/>
            </svg></div>
			<div class="sb-dialog-remove-source cff-fb-fs" v-if="dialogBoxElement.type == 'deleteSourceCustomizer'">
				<div class="cff-fb-srcs-item" :data-type="sourceToDelete.account_type">
					<div class="cff-fb-srcs-item-avatar">
						<img :src="typeof sourceToDelete.avatar_url !== 'undefined' && sourceToDelete.account_type === 'group' ? sourceToDelete.avatar_url : 'https://graph.facebook.com/'+sourceToDelete.account_id+'/picture'">
					</div>
					<div class="cff-fb-srcs-item-inf">
                        <div class="cff-fb-srcs-item-name"><span>{{sourceToDelete.username}}</span></div>
						<div class="cff-fb-srcs-item-type">
							<div v-html="sourceToDelete.account_type == 'group' ?  svgIcons['users'] : svgIcons['flag']"></div>
							<span v-html="sourceToDelete.account_type"></span>
						</div>
					</div>
					<div class="cff-fb-srcs-item-remove" v-html="svgIcons['delete']"></div>
				</div>
			</div>
			<div class="sb-dialog-popup-content cff-fb-fs">
				<strong v-html="dialogBoxElement.heading"></strong>
				<span v-html="dialogBoxElement.description"></span>
				<div class="sb-dialog-popup-actions cff-fb-fs">
					<button class="sb-btn sb-btn-red" @click.prevent.default="confirmDialogAction">{{genericText.confirm}}</button>
					<button class="sb-btn sb-btn-grey" @click.prevent.default="closeConfirmDialog">{{genericText.cancel}}</button>
				</div>
			</div>
		</div>
	</div>
</script>
