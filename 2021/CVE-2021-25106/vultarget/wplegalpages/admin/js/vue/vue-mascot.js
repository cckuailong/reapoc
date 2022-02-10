
var vm = new Vue({
    el: '#wplegal-mascot-app',
    data: function() {
        return {
            showMenu: !1,
            isPro:data.is_pro
        }
    },
    computed: (
        {
            boxClass() {
                return {
                    'wplegal-mascot-quick-links wplegal-mascot-quick-links-open' : this.showMenu,
                    'wplegal-mascot-quick-links' : !this.showMenu,
                }
            },
            menuItems() {
                var mItems = [
                    {
                        icon: 'dashicons-lightbulb',
                        tooltip: data.menu_items.support_text,
                        link: data.menu_items.support_url,
                        key: 'support'
                    },
                    {
                        icon: 'dashicons-info',
                        tooltip: data.menu_items.faq_text,
                        link: data.menu_items.faq_url,
                        key: 'faq'
                    },
                    {
                        icon: 'dashicons-sos',
                        tooltip: data.menu_items.documentation_text,
                        link: data.menu_items.documentation_url,
                        key: 'documentation'
                    }
                ];
                if(!this.isPro) {
                    mItems.push({
                        icon: 'dashicons-star-filled',
                        tooltip: data.menu_items.upgrade_text,
                        link: data.menu_items.upgrade_url,
                        key: 'upgrade'
                    });
                }
                return mItems;
            }
        }
    ),
    methods:{
        buttonClick: function(){
            this.showMenu = !this.showMenu;
        },
        renderElements:function(createElement) {
            var html = [];
            if(this.showMenu) {
                this.menuItems.forEach((value, index) => {
                    html.push(createElement('a', {
                        key: value.key,
                        class: this.linkClass(value.key),
                        attrs: {
                            href: value.link,
                            'data-index': index,
                            target: '_blank'
                        }
                    }, [createElement('span', {
                        class: 'dashicons '+ value.icon
                    }), createElement('span', {
                        staticClass: 'wplegal-mascot-quick-link-title',
                        domProps: {
                            innerHTML: value.tooltip
                        }
                    })]));
                })
            }
            return html;
        },
        linkClass: function(key) {
            return 'wplegal-mascot-quick-links-menu-item wplegal-mascot-quick-links-item-' + key;
        },
        enter:function(t,e) {
            var n = 50 * t.dataset.index;
            setTimeout((function() {
                t.classList.add('wplegal-mascot-show'),
                    e()
            }), n)
        },
        leave:function(t,e) {
            t.classList.remove('wplegal-mascot-show'),
                setTimeout((function() {
                    e()
                }), 200)
        }
    },
    render(createElement){
        return createElement('div',{
            class: this.boxClass,
        }, [
            createElement('button', {
                class: 'wplegal-mascot-quick-links-label',
                on: {
                    click: this.buttonClick
                }
            },[
                createElement('span', {
                    class:'wplegal-mascot-bg-img wplegal-mascot-quick-links-mascot',
                }),
                createElement('span',{
                    class: 'wplegal-mascot-quick-link-title'
                }, data.quick_links_text)
            ]),
            createElement('transition-group', {
                staticClass: 'wplegal-mascot-quick-links-menu',
                attrs:{
                    tag: 'div',
                    name: 'wplegal-staggered-fade'
                },
                on: {
                    enter: this.enter,
                    leave: this.leave
                }
            }, this.renderElements(createElement))
        ]);
    },
});
