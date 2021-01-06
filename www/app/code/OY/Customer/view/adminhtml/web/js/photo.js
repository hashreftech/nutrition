/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'Magento_Ui/js/form/element/abstract',
    'faceApi',
    'jquery',
    'underscore',
    'domReady!'
], function (Abstract,faceApi,jQuery,_) {
    'use strict';



    return Abstract.extend({
        defaults: {
            elementTmpl: 'OY_Customer/input',
        },

        initObservable: function () {
            this._super();
            this.observe('value');
            return this;
        },

        /**
         * Invokes initialize method of parent class,
         * contains initialization logic
         */
        initialize: function () {

            this._super();
            var self = this;

            setTimeout(function () {

                jQuery('div[data-index="website_id"]').hide();
                jQuery('div[data-index="group_id"]').hide();
                jQuery('div[data-index="luxand_registry"]').hide();
                jQuery('div[data-index="prefix"]').hide();
                jQuery('div[data-index="middlename"]').hide();
                jQuery('div[data-index="suffix"]').hide();
                jQuery('div[data-index="extension_attributes.assistance_allowed"]').hide();
                jQuery('div[data-index="taxvat"]').hide();
                jQuery('div[data-index="sendemail_store_id"]').hide();
                jQuery('input[name="customer[disable_auto_group_change]"]').parent().parent().hide();

                jQuery('div[data-index="photo"]').hide();
                jQuery('div[data-index="luxand_id"]').hide();
                jQuery('input[name="customer[customer_image]"]').parent().parent().parent().parent().hide();

                var video = document.getElementById('customer-video');

                self.initVideo(video);

            }, 1000);

            jQuery(document).on('click', '#tab_customer', function (){

                setTimeout(function () {

                    jQuery('div[data-index="website_id"]').hide();
                    jQuery('div[data-index="group_id"]').hide();
                    jQuery('div[data-index="luxand_registry"]').hide();
                    jQuery('div[data-index="prefix"]').hide();
                    jQuery('div[data-index="middlename"]').hide();
                    jQuery('div[data-index="suffix"]').hide();
                    jQuery('div[data-index="extension_attributes.assistance_allowed"]').hide();
                    jQuery('div[data-index="taxvat"]').hide();
                    jQuery('div[data-index="sendemail_store_id"]').hide();
                    jQuery('input[name="customer[disable_auto_group_change]"]').parent().parent().hide();

                    jQuery('div[data-index="photo"]').hide();
                    jQuery('div[data-index="luxand_id"]').hide();
                    jQuery('input[name="customer[customer_image]"]').parent().parent().parent().parent().hide();

                    var video = document.getElementById('customer-video');

                    self.initVideo(video);

                    var path_photo = jQuery('input[name="customer[photo]"]').val();
                    var photoMediaUrl = location.protocol+'//'+location.hostname+'/pub/media'+path_photo;

                    jQuery("#show_photo").attr("src",photoMediaUrl);

                }, 500);
            })

            jQuery(document).on('click', '#made_photo', function (){

                var video = document.getElementById('customer-video');
                var imgBase64 = self.getImageFromVideo(video);
                self.savePhoto(imgBase64);

            })

            jQuery(document).on('click',function (){

                if(jQuery('div[data-index="photo"]').hasClass('_error')){
                    jQuery('.cl_photo-error').show();
                }else{
                    jQuery('.cl_photo-error').hide();
                }

            })

            return this;
        },

        savePhoto: function(photo){

            jQuery('body').trigger('processStart');
            var urlPhoto = jQuery('#urlphoto').val();
            jQuery.ajax({
                url: urlPhoto,
                type: "POST",
                data: {
                    photo: photo,
                    current:jQuery('input[name="customer[photo]"]').val()
                },
                dataType: 'json',
                success: function (data) {

                    jQuery('input[name="customer[photo]"]').val(data.path_photo).trigger('change');
                    var photoMediaUrl = location.protocol+'//'+location.hostname+'/pub/media'+data.path_photo;

                    jQuery("#show_photo").attr("src",photoMediaUrl);

                    jQuery('body').trigger('processStop');

                },
                error: function (request, error) {

                    jQuery('body').trigger('processStop');

                }
            });
        },

        getImageFromVideo: function getImageFromVideo(video) {
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d')
                .drawImage(video, 0, 0, canvas.width, canvas.height);
            return canvas.toDataURL().replace('data:image/png;base64,', '');
        },

        initListeners: function initListeners() {
            jQuery('[data-action="toggleRecognition"]').click(_.bind(Utils.toggleRecognition, Utils));
        },

        toggleRecognition: function toggleRecognition() {
            if (!window.faceApp.running) {
                window.faceApp.running = true;
                this.initFaceDetection()
            }
        },

        initFaceDetection: async function initFaceDetection() {
            const canvas = faceApi.createCanvasFromMedia(video);
            const displaySize = { width: video.videoWidth, height: video.videoHeight };
            var detections = null;
            var resizedDetections;

            faceApi.matchDimensions(canvas, displaySize);
            while (!window.faceApp.faceDetected) {
                jQuery('#video-header-card').text('Detectando...');
                detections = await faceApi.detectSingleFace(video, new faceApi.TinyFaceDetectorOptions());
                if (detections) {
                    resizedDetections = faceApi.resizeResults(detections, displaySize);
                    if (detections.score > 0.8) {
                        canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
                        faceApi.draw.drawDetections(canvas, resizedDetections);

                        window.faceApp.faceDetected = true;
                        window.faceApp.running = false;
                        window.faceApp.base64Image = Utils.getImageFromVideo();
                        jQuery('#video-header-card').text('Identificando...');
                        Utils.makeCallToApi();
                    }
                }
                setTimeout(function () {
                }, 100);
            }
        },

        initVideo: function initVideo(video) {
            var self = this;

            navigator.getUserMedia(
                { video: {} },
                function (stream) {
                    video.srcObject = stream;
                    //self.initListeners();
                },
                err => console.error(err)
            );
        },

        initApp: function initApp() {
            var self = this;
            var mediaUrl = location.protocol+'//'+location.hostname+'/pub/media';
            var video = document.getElementById('customer-video');
            return Promise.all([
                faceApi.nets.tinyFaceDetector.loadFromUri(mediaUrl + '/models'),
                faceApi.nets.faceLandmark68Net.loadFromUri(mediaUrl + '/models'),
                faceApi.nets.faceRecognitionNet.loadFromUri(mediaUrl + '/models')
            ]).then(function () {
                self.initVideo(video);
            });
        }


    });


});
