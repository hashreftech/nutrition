define(
    [
        'underscore',
        'Magento_Ui/js/form/element/select',
       'uiRegistry'
    ],
    function (_, Element, reg) {
        'use strict';

        return Element.extend(
            {
                defaults: {
                    valuesForOptions: [],
                    platformForm: [],
                    imports          : {
                        toggleApi: '${$.ns}.${$.ns}.settings.use_api:value',
                        toggleByPlatform: 'import_job_form.import_job_form.settings.platforms:value'
                    },
                    apiOptions: null,
                    isShown: false,
                    inverseVisibility: false,
                    visible: true
                },

                toggleApi: function (value) {
                    if (this.apiOptions == null) {
                        this.apiOptions = [];
                        this.apiOptions.push(this.getOption('json'));
                        this.apiOptions.push(this.getOption('xml'));
                    }
                    if (value === "1") {
                        this.setOptions(this.apiOptions);
                    } else {
                        this.setOptions(this.initialOptions);
                    }
                },

                toggleVisibility: function (isShown) {
                    this.isShown = isShown;
                    this.visible(this.inverseVisibility ? !this.isShown : this.isShown);
                    if (!this.visible()) {
                        this.value('');
                    }
                },

                toggleByPlatform: function (selected) {
                    if (!selected || selected === undefined) {
                        this.toggleVisibility(true);
                        return;
                    }

                    var entity = reg.get(this.ns + '.' + this.ns + '.settings.entity');
                    if (entity === undefined) {
                        this.toggleVisibility(selected);
                        return;
                    }

                    var value = entity.value() + '_' + selected;
                    value = value in this.platformForm ? false : true;
                    this.toggleVisibility(value);
                }
            }
        );
    }
);
