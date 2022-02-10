/**
 * Confirm Dialog Box Popup
 *
 * @since 4.0
 */
Vue.component('sb-confirm-dialog-component', {
    name: 'sb-confirm-dialog-component',
    template: '#sb-confirm-dialog-component',
    props: [
    	'dialogBox',
    	'sourceToDelete',
    	'genericText',
    	'svgIcons',
    	'parentType',
    	'parent'
    ],
    computed : {
    	dialogBoxElement :function(){
    		return this.dialogBox;
    	}
    },
    methods : {


    	/**
		 * Confirm Dialog Box
		 *
		 * @since 4.0
		 */
    	confirmDialogAction : function(){
			var self = this;
			self.$parent.confirmDialogAction();
			self.closeConfirmDialog();
		},

    	/**
		 * Close Dialog Box
		 *
		 * @since 4.0
		 */
    	closeConfirmDialog : function(){
			var self = this;
    		if( self.parentType == 'builder' ){
				self.$parent.sourceToDelete = {};
				self.$parent.feedToDelete = {};
    		}
			var dialogBox = {
				active : false,
				type : null,
				heading : null,
				description : null
			};
			self.$emit('update:dialogBox', dialogBox)
		},
    }
});