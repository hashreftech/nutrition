/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

define(
    [
        'Magento_Ui/js/form/components/fieldset',
        'uiRegistry'
    ],
    function (Fieldset, reg) {
        'use strict';
        return Fieldset.extend(
            {
                defaults: {
                    valuesForOptions: [],
                    imports: {
                        toggleVisibility: '${$.parentName}.source.check_button:showMap'
                    },
                    openOnShow: true,
                    isShown: false,
                    inverseVisibility: false
                },

                /**
                 * Toggle visibility state.
                 *
                 * @param {Number} selected
                 */
                toggleVisibility: function (selected) {
                    this.isShown = selected;
                    this.visible(this.inverseVisibility ? !this.isShown : this.isShown);
                    if (this.isShown) {
                        var map = reg.get(this.ns + '.' + this.ns + '.source_data_map_container.source_data_map');
                        if (map !== undefined) {
                            map.showSpinner(false);
                        }
                    }
                    if (this.openOnShow) {
                        this.opened(this.inverseVisibility ? !this.isShown : this.isShown);
                    }
                },
                initConfig: function () {
                    this._super();
                    return this;
                },
            }
        );
    }
);
