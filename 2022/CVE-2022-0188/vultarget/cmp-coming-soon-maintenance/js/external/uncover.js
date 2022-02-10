/**
 * uncover.js
 * http://www.codrops.com
 *
 * Licensed under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 * 
 * Copyright 2018, Codrops
 * http://www.codrops.com
 */
{
    class Uncover {
        constructor(el, options) {
            this.DOM = {el: el};
            this.options = {
                // initially covered.
                covered: true,
                // total number of slices.
                slicesTotal: 11,
                // slices color.
                slicesColor: '#fff',
                // 'vertical' || 'horizontal'.
                orientation: 'horizontal',
                // 'bottom' || 'top' for vertical orientation and 'right' || 'left' for horizontal orientation.
                // need to define for both show and hide methods.
                // e.g. animate the slices in from left and hide them to the right side (for a horizontal layout)
                slicesOrigin: {
                    show: 'left',
                    hide: 'right'
                }
            };
            Object.assign(this.options, options);
            this.isCovered = this.options.covered;
            this.layout();
            if ( !this.isCovered ) {
                this.show();
            }
        }
        layout() {
            this.DOM.el.classList.add('uncover');
            let inner = '';
            inner += `<div class="uncover__img" style='background-image: ${this.DOM.el.style.backgroundImage}'></div>
                      <div class="uncover__slices uncover__slices--${this.options.orientation}">`;
            for (let i = 0; i <= this.options.slicesTotal - 1; ++i) {
                inner += `<div class="uncover__slice" style="color:${this.options.slicesColor}"></div>`;
            }
            inner += `</div>`;
            this.DOM.el.innerHTML = inner;
            this.DOM.img = this.DOM.el.querySelector('.uncover__img');
            this.DOM.slices = Array.from(this.DOM.el.querySelectorAll('.uncover__slice'));
            this.slicesTotal = this.DOM.slices.length;
        }
        show(animation = false, animationSettings = {}) {
            if ( !this.isCovered ) return;
            return this.toggle(animation,animationSettings);
        }
        hide(animation = false, animationSettings = {}) {
            if ( this.isCovered ) return;
            return this.toggle(animation,animationSettings);
        }
        toggle(animation,animationSettings) {
            this.isCovered = !this.isCovered;
            if ( !animation ) {
                this.DOM.slices.forEach((slice) => {
                    slice.style.transform = !this.isCovered ? 
                        this.options.orientation === 'vertical' ? 'translateY(100%)' : 'translateX(100%)' :
                        'none';
                });
            }
            else {
                let settings = {
                    slices: {
                        targets: this.DOM.slices,
                        duration: 800,
                        delay: (_,i) => i*80,
                        easing: 'easeInOutQuart',
                        translateX: this.options.orientation === 'vertical' ? '0%' : 
                            !this.isCovered ? 
                                this.options.slicesOrigin.show === 'right' ? '100%' : '-100%' : 
                                this.options.slicesOrigin.hide === 'right' ? ['100%','0%'] : ['-100%','0%'],
                                                                              
                        translateY: this.options.orientation === 'vertical' ? 
                            !this.isCovered ? 
                                this.options.slicesOrigin.show === 'bottom' ? '100%' : '-100%' :
                                this.options.slicesOrigin.hide === 'bottom' ? ['100%','0%'] : ['-100%','0%']
                            : '0%'
                    },
                    image: {
                        targets: this.DOM.img
                    }
                };
                Object.assign(settings.slices, animationSettings.slices);
                Object.assign(settings.image, animationSettings.image);
                
                anime.remove(this.DOM.slices);
                anime.remove(this.DOM.img);
                
                let promises = [anime(settings.slices).finished];
                if ( settings.image.duration ) {
                    promises.push(anime(settings.image).finished);
                }
                return Promise.all(promises);
            }
        }
    }
    window.Uncover = Uncover;
}