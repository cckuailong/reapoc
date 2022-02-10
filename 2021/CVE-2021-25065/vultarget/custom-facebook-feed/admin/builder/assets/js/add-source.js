var cffStorage = window.localStorage;
/**
 * Add Source Popup
 *
 * @since 4.0
 */
Vue.component('sb-add-source-component', {
    name: 'sb-add-source-component',
    template: '#sb-add-source-component',
    props: [
    'genericText',
    'links',
    'svgIcons',
    'viewsActive',
    'selectSourceScreen',
    'selectedFeed',
    'parent'
    ],
    data: function() {
        return{
            sourcesList : cff_source.sources,
            //Add New Source
            newSourceData        : cff_source.newSourceData ? cff_source.newSourceData : null,
            sourceConnectionURLs : cff_source.sourceConnectionURLs,
            returnedApiSourcesList : [],
            addNewSource : {
                typeSelected        : 'page',
                manualSourceID      : null,
                manualSourceToken   : null
            },
            selectedSourcesToConnect : [],
            loadingAjax : false
        }
    },
    computed : {

    },
    mounted : function(){
        var self = this;
        if(self.newSourceData != null){
            self.initAddSourceData();
        }
        self.processFBConnectSuccess();
    },
    methods : {
        /**
         * Return Page/Group Avatar
         *
         * @since 4.0
         *
         * @return string
         */
         returnGroupPageAvatar : function(source){
            var info = this.$parent.jsonParse(source.info);
            return (source.account_type == 'page') ? 'https://graph.facebook.com/'+source.account_id+'/picture' : (info.picture.data.url ? info.picture.data.url : '');
        },


        /**
         * Add Feed Source Manually
         *
         * @since 4.0
         */
        addSourceManually: function(isEventSource = false){
            var self = this,
            manualSourceData = {
                'action' : 'cff_source_builder_update',
                'type' : self.addNewSource.typeSelected,
                'id' : self.addNewSource.manualSourceID,
                'access_token' : self.addNewSource.manualSourceToken,
            };
            if(isEventSource){
                manualSourceData.privilege = 'events';
            }
            var alerts = document.querySelectorAll(".sb-alerts-wrap");
            if (alerts.length) {
                alerts[0].parentNode.removeChild(alerts[0]);
            }

            if(self.$parent.checkNotEmpty(self.addNewSource.manualSourceID) && self.$parent.checkNotEmpty(self.addNewSource.manualSourceToken) ){
                self.loadingAjax = true;
                self.$parent.ajaxPost(manualSourceData, function(_ref){
                    var data = _ref.data;
                    if (typeof data.success !== 'undefined' && data.success === false) {
                        //cff-fb-source-inputs cff-fb-fs
                        var inputs = document.querySelectorAll(".cff-fb-source-inputs")[0];

                        var div = document.createElement('div');
                        div.innerHTML = data.message;
                        while (div.children.length > 0) {
                            inputs.appendChild(div.children[0]);
                        }

                    } else {
                        self.addNewSource = {typeSelected : 'page', manualSourceID : null,manualSourceToken : null};
                        self.sourcesList = data;
                        self.$parent.sourcesList = data;
                        self.$parent.viewsActive.sourcePopup = false;
                        if(self.$parent.customizerFeedData){
                            self.$parent.activateView('sourcePopup', 'customizer');
                        }
                    }
                    self.loadingAjax = false;

                });
            }else{
                alert("Token or ID Empty")
            }
        },

        /**
         * Make sure something entered for manual connections
         *
         * @since 4.0
         */
        checkManualEmpty : function() {
            var self = this;
            return self.$parent.checkNotEmpty(self.addNewSource.manualSourceID) && self.$parent.checkNotEmpty(self.addNewSource.manualSourceToken);
        },

        /**
         * Init Add Source Action
         * Triggered when the connect button is returned
         *
         * @since 4.0
         */
         initAddSourceData : function(){
            var self = this;
            self.$parent.viewsActive.sourcePopup = true;
            self.$parent.viewsActive.sourcePopupScreen = 'step_2';
            if(self.newSourceData && !self.newSourceData.error){
                if(self.newSourceData.admin || self.newSourceData.member){
                    self.addNewSource.typeSelected = 'group';
                    self.newSourceData.admin.forEach(function(singleSource){
                        singleSource.admin = true;
                        self.returnedApiSourcesList.push(self.createSourceObject('group',singleSource));
                    });
                    self.newSourceData.member.forEach(function(singleSource){
                        //if(!self.checkObjectArrayElement(self.returnedApiSourcesList, self.newSourceData.admin, 'id')){
                            singleSource.admin = false;
                            self.returnedApiSourcesList.push(self.createSourceObject('group',singleSource));
                        //}
                    });
                }else{
                    self.newSourceData.pages.forEach(function(singleSource){
                        self.returnedApiSourcesList.push(self.createSourceObject('page',singleSource));
                    });
                }
            }
        },

        /**
         * Create Single Source Object
         *
         * @since 4.0
         *
         * @return Object
         */
         createSourceObject : function(type,object){
            return {
                account_id : object.id,
                access_token : object.access_token,
                account_type : type,
                info : (type == 'group' ? JSON.stringify(object) : '{}'),
                admin : (type == 'group' ? object.admin  : ''),
                username : object.name
            }
        },

        /**
         * Select Page/Group to Connect
         *
         * @since 4.0
         */
         selectSourcesToConnect : function(source){
            var self = this;

            if (typeof window.cffSelected === 'undefined') {
                window.cffSelected = [];
            }
            if(self.selectedSourcesToConnect.includes(source.account_id)){
                self.selectedSourcesToConnect.splice(self.selectedSourcesToConnect.indexOf(source.account_id), 1);
                window.cffSelected.splice(self.selectedSourcesToConnect.indexOf(source.admin), 1);
            }else{
                self.selectedSourcesToConnect.push(source.account_id);
                window.cffSelected.push(source.admin);
            }
        },

        /**
         * Select Page/Group to Connect
         *
         * @since 4.0
         */
         addSourcesOnConnect : function(){
            var self = this;
            if(self.selectedSourcesToConnect.length > 0){
                var sourcesListToAdd = [];
                self.selectedSourcesToConnect.forEach(function(accountID, index){
                    self.returnedApiSourcesList.forEach(function(source){
                        if(source.account_id === accountID)
                            sourcesListToAdd.push(source);
                    });
                });
                var connectSourceData = {
                    'action' : 'cff_source_builder_update_multiple',
                    'type' : self.addNewSource.typeSelected,
                    'sourcesList' : sourcesListToAdd
                };
                self.$parent.ajaxPost(connectSourceData, function(_ref){
                    var data = _ref.data;
                    self.sourcesList = data;
                    self.$parent.sourcesList = data;
                    self.$parent.viewsActive.sourcePopup = false;
                    if(self.$parent.customizerFeedData){
                        self.$parent.activateView('sourcePopup', 'customizer');
                    }
                });
            }
        },

        /**
         * Process Connect FB Button
         *
         * @since 4.0
         */
         processFBConnect : function(){
            var self = this,
            fbConnectURL = self.addNewSource.typeSelected == 'page' ? self.sourceConnectionURLs.page : self.sourceConnectionURLs.group,
            screenType = (self.$parent.customizerFeedData != undefined) ? 'customizer'  : 'creationProcess',
            appendURL = ( screenType == 'customizer' ) ? self.sourceConnectionURLs.stateURL + ',feed_id='+ self.$parent.customizerFeedData.feed_info.id : self.sourceConnectionURLs.stateURL;
            if(screenType != 'customizer'){
                self.createLocalStorage(screenType);
            }
            var finalUrl = fbConnectURL + "{'{url=" + appendURL + "}'}";
            window.location = finalUrl;
        },

        /**
         * Browser Local Storage for FB Connect
         *
         * @since 4.0
         */
         createLocalStorage : function(screenType){
            var self = this;
            switch (screenType) {
                case 'creationProcess':
                    cffStorage.setItem('selectedFeed', self.$parent.selectedFeed);
                break;
                case 'customizer':
                    cffStorage.setItem( 'feed_id', self.$parent.customizerFeedData.feed_info.id );
                break;
            }
            cffStorage.setItem( 'FBConnect', 'true' );
            cffStorage.setItem( 'screenType', screenType );
        },


        /**
         * Process FB Connect Success
         *
         * @since 4.0
         */
         processFBConnectSuccess : function(){
            var self = this;
            if( cffStorage.FBConnect === 'true' && cffStorage.screenType ){
                if( cffStorage.screenType == 'creationProcess' && cffStorage.selectedFeed ){
                    self.$parent.selectedFeed = cffStorage.selectedFeed;
                    self.$parent.viewsActive.pageScreen = 'selectFeed';
                    self.$parent.viewsActive.selectedFeedSection = 'selectSource';
                }
                if( cffStorage.screenType == 'customizer' && cffStorage.feed_id){
                    var urlParams = new URLSearchParams(window.location.search);
                    urlParams.set('feed_id', cffStorage.feed_id);
                    window.location.search = urlParams;
                }
            }
            localStorage.removeItem("FBConnect");
            localStorage.removeItem("screenType");
            localStorage.removeItem("selectedFeed");
            localStorage.removeItem("feed_id");
        },

        groupNext : function() {
             console.log('next');
        }
    }
});
