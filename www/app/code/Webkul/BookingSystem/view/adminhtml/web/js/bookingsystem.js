/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_BookingSystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
define(
    [
    "jquery",
    "jquery/validate", // Jquery Validation Library
    "jquery/ui",
    "mage/calendar",
    ],
    function ($) {
        'use strict';

        $.validator.addMethod(
            'validate-date-today',
            function (value) {
            var currentYear = new Date().getFullYear() + '';
            var v = value;
            var formattedDate = $.datepicker.formatDate('yy-mm-dd', new Date());
            var normalizedTime = function (v) {
                v = v.split(/[.\-]/);
                for (var i=0, len=v.length; i<len; i++) {
                    v[i] = parseInt(v[i]);
                }
                if (v[2] && v[2].length < 4) {
                    v[2] = currentYear.substr(0, v[2].length) + v[2];
                }
                return new Date(v.join('/')).getTime();
            };
            if (normalizedTime(v) < normalizedTime(formattedDate)) {
                return false;
            } else {
                return true;
            }

            },
            $.mage.__('Please enter today\'s date or future date.')
        );

        $.widget(
            'bookingsystem.bookingsystem',
            {
                options: {},
                _create: function () {
                    $("#start_date").calendar(
                        {
                            showsTime: false,
                            hideIfNoPrevNext: true,
                            buttonText: "Select Date",
                            minDate: new Date(),
                            dateFormat: 'yy-mm-dd',
                            onSelect: function(selectedDate) {
                                $("#end_date").datepicker("option", "minDate", selectedDate).datepicker("setDate", selectedDate);
                            }
                        }
                    );
                    $("#end_date").calendar(
                        {
                            showsTime: false,
                            hideIfNoPrevNext: true,
                            buttonText: "Select Date",
                            minDate: new Date(),
                            dateFormat: 'yy-mm-dd'
                        }
                    );
                    var self = this;
                    $(document).ready(
                        function () {
                            var startSelectHtml = self.options.startSelectHtml;
                            var endSelectHtml = self.options.endSelectHtml;
                            var selectCounts = self.options.selectCounts;
                            showBookingPanel();
                            manageRequiredFields();
                            $(document).on(
                                'change',
                                '#booking_type',
                                function () {
                                    showBookingPanel();
                                    manageRequiredFields();
                                }
                            );

                            $(document).on(
                                'click',
                                '.wk-obmd-m-inc',
                                function () {
                                    var val = $(this).parent().find("input").val();
                                    if ($.isNumeric(val)) {
                                        val++;
                                    } else {
                                        val = 0;
                                    }
                                    if (val >= 60) {
                                        val = 0;
                                    }
                                    $(this).parent().find("input").val(val);
                                }
                            );
                            $(document).on(
                                'click',
                                '.wk-obmd-m-dec',
                                function () {
                                    var val = $(this).parent().find("input").val();
                                    if ($.isNumeric(val)) {
                                        val--;
                                    } else {
                                        val = 0;
                                    }
                                    if (val < 0) {
                                        val = 59;
                                    }
                                    $(this).parent().find("input").val(val);
                                }
                            );
                            $(document).on(
                                'click',
                                '.wk-obmd-h-inc',
                                function () {
                                    var val = $(this).parent().find("input").val();
                                    if ($.isNumeric(val)) {
                                        val++;
                                    } else {
                                        val = 0;
                                    }
                                    if (val > 24) {
                                        val = 0;
                                    }
                                    $(this).parent().find("input").val(val);
                                }
                            );
                            $(document).on(
                                'click',
                                '.wk-obmd-h-dec',
                                function () {
                                    var val = $(this).parent().find("input").val();
                                    if ($.isNumeric(val)) {
                                        val--;
                                    } else {
                                        val = 0;
                                    }
                                    if (val < 0) {
                                        val = 24;
                                    }
                                    $(this).parent().find("input").val(val);
                                }
                            );
                            $(document).on(
                                'click',
                                '.wk-btn',
                                function () {
                                    selectCounts++;
                                    var startHtml = $(startSelectHtml).attr("name", "info[start][day]["+selectCounts+"]");
                                    var endHtml = $(endSelectHtml).attr("name", "info[end][day]["+selectCounts+"]");

                                    var html = $('<div/>', { class : 'wk-row wk-primary-row wk-text-center' });
                                    html.append(
                                        $('<div/>', { class : 'wk-one-booking-col' })
                                        .append(
                                            $('<div/>', { class : 'wk-col-wrapper' })
                                            .append(
                                                $('<div/>', { class : 'wk-input-col' })
                                                .append(startHtml)
                                            )
                                            .append(
                                                $('<div/>', { class : 'wk-input-col' })
                                                .append('<input data-form-part="product_form" class="admin__control-text" readonly type="text" name="info[start][hour]['+selectCounts+']" value="1">')
                                                .append($('<div/>', { class : 'wk-dec wk-obmd-h-dec', text : '-' }))
                                                .append($('<div/>', { class : 'wk-inc wk-obmd-h-inc', text : '+' }))
                                            )
                                            .append(
                                                $('<div/>', { class : 'wk-input-col' })
                                                .append('<input data-form-part="product_form" class="admin__control-text" readonly type="text" name="info[start][minute]['+selectCounts+']" value="0">')
                                                .append($('<div/>', { class : 'wk-dec wk-obmd-m-dec', text : '-' }))
                                                .append($('<div/>', { class : 'wk-inc wk-obmd-m-inc', text : '+' }))
                                            )
                                        )
                                    );
                                    html.append(
                                        $('<div/>', { class : 'wk-one-booking-remove-col wk-text-center' })
                                        .append('<div class="wk-remove">x</div>')
                                    );
                                    html.append(
                                        $('<div/>', { class : 'wk-one-booking-col' })
                                        .append(
                                            $('<div/>', { class : 'wk-col-wrapper' })
                                            .append(
                                                $('<div/>', { class : 'wk-input-col' })
                                                .append(endHtml)
                                            )
                                            .append(
                                                $('<div/>', { class : 'wk-input-col' })
                                                .append('<input data-form-part="product_form" class="admin__control-text" readonly type="text" name="info[end][hour]['+selectCounts+']" value="1">')
                                                .append($('<div/>', { class : 'wk-dec wk-obmd-h-dec', text : '-' }))
                                                .append($('<div/>', { class : 'wk-inc wk-obmd-h-inc', text : '+' }))
                                            )
                                            .append(
                                                $('<div/>', { class : 'wk-input-col' })
                                                .append('<input data-form-part="product_form" class="admin__control-text" readonly type="text" name="info[end][minute]['+selectCounts+']" value="0">')
                                                .append($('<div/>', { class : 'wk-dec wk-obmd-m-dec', text : '-' }))
                                                .append($('<div/>', { class : 'wk-inc wk-obmd-m-inc', text : '+' }))
                                            )
                                        )
                                    );
                                    $(".wk-one-booking-panel").append(html);
                                }
                            );
                            $(document).on(
                                'click',
                                '.wk-remove',
                                function () {
                                    $(this).parent().parent().remove();
                                }
                            );
                        }
                    );

                    $(document).on("mouseenter", "#start_date,#end_date", function () {
                        $(this).removeClass("validate-date-today");
                        $(this).addClass("validate-date-today");
                    });

                    function showBookingPanel()
                    {
                        var val = $('#booking_type').val();
                        if (val == 0) {
                            $(".wk-secondary-container").hide();
                        } else if (val == 1) {
                            $(".wk-secondary-container").show();
                            $(".wk-one-booking-container").hide();
                        } else {
                            $(".wk-secondary-container").show();
                            $(".wk-many-booking-container").hide();
                        }
                    }
                    function manageRequiredFields()
                    {
                        var val = $('#booking_type').val();
                        $(".wk-bs").removeClass("required-entry");
                        if (val == 1) {
                            $(".wk-bs").addClass("required-entry");
                        } else if (val == 2) {
                            $(".wk-bs").addClass("required-entry");
                            $(".wk-is").removeClass("required-entry");
                        }
                    }
                }
            }
        );
        return $.bookingsystem.bookingsystem;
    }
);
