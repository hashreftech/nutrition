define([
    'faceApi',
    'jquery',
    'vue',
    'domReady!'
], function jsAuth(
    faceApi,
    jQuery,
    Vue
) {

    'use strict';


    return function (config) {
        const STATE_ENUM = {
            LOADING_LIBS: 0,
            DETECTING: 1,
            DETECTING_SUCCESS: 2,
            IDENTIFIYNG: 3,
            IDENTIFIYNG_SUCCESS: 4,
            IDENTIFIYNG_FAIL: 5,
            REGISTERING: 6,
            REGISTERING_SUCCESS: 7,
            REGISTERING_FAIL: 8,
            DETECTING_FAIL: 9,
            INVALID_CI: 10,
        }
        if (faceApi.draw.DrawBox && faceApi.draw.DrawBox.prototype && faceApi.draw.DrawBox.prototype.draw) {
            faceApi.draw.DrawBox.prototype.draw = function draw(canvasArg) {
                const ctx = faceApi.getContext2dOrThrow(canvasArg)
            
                const { boxColor, lineWidth } = this.options
            
                const { x, y, width, height } = this.box
                ctx.fillStyle = "rgba(1, 255, 27, 0.2)"
                ctx.fillRect(x, y, width, height)
            
                const { label } = this.options
                if (label) {
                  new faceApi.draw.DrawTextField([label], { x: x - (lineWidth / 2), y }, this.options.drawLabelOptions).draw(canvasArg)
                }
              }
        }


        const vueApp = new Vue({
            el: '#appvue',
            mounted: function mounted() {
                const self = this;
                this.video = document.getElementById('main-video');
                this.setInfo(STATE_ENUM.LOADING_LIBS);
                return Promise.all([
                    faceApi.nets.tinyFaceDetector.loadFromUri(self.config.assetBaseUrl + '/models'),
                    faceApi.nets.ssdMobilenetv1.loadFromUri(self.config.assetBaseUrl + '/models'),
                    faceApi.nets.faceRecognitionNet.loadFromUri(self.config.assetBaseUrl + '/models')
                ]).then(function () {
                    self.initVideo();
                });
            },
            data: {
                config: config,
                video: null,
                faceDetected: null,
                identifiedPerson: null,
                state: null,
                info: { msg: 'Bienvenido', type: 'primary' },
                ci: null
            },
            computed: {
                disableRegisterWithFace: function disableRegisterWithFace() {
                    return this.state !== STATE_ENUM.DETECTING_SUCCESS;
                },
                isProcessing: function isProcessing() {
                    return this.state === STATE_ENUM.LOADING_LIBS || 
                        this.state === STATE_ENUM.IDENTIFIYNG ||
                        this.state === STATE_ENUM.REGISTERING;
                },
                showUserData: function showUserData() {
                    return this.state === STATE_ENUM.IDENTIFIYNG_SUCCESS || 
                        this.state === STATE_ENUM.REGISTERING || 
                        this.state === STATE_ENUM.REGISTERING_SUCCESS || 
                        this.state === STATE_ENUM.REGISTERING_FAIL;
                }
            }, 
            watch: {
                state: function (value) {
                    console.log('new state = > ' + this.state, this.info)
                }
            },          
            methods: {
                initVideo: function initVideo() {
                    var self = this; 
                    return navigator.getUserMedia(
                        { video: {} },
                        function (stream) {
                            self.video.srcObject = stream;
                            self.video.addEventListener('playing', () => {
                                self.setInfo(STATE_ENUM.DETECTING);
                                self.initFaceDetection()
                            });
                        },
                        err => console.error(err)
                    );
                },
                initFaceDetection: async function initFaceDetection() {
                    const displaySize = { 
                        width: this.video.clientWidth, 
                        height: this.video.clientHeight 
                    };
                    var detection = null;
                    var resizedDetections;
                    var self = this;
                    
                    this.faceDetected = null;
                    this.identifiedPerson = null;
                    this.canvas = faceApi.createCanvasFromMedia(this.video);
                    this.video.parentElement.append(this.canvas)
        
                    faceApi.matchDimensions(this.canvas, displaySize);

                    setInterval(async () => {
                        detection = await faceApi.detectSingleFace(self.video);
                        self.canvas.getContext('2d').clearRect(0, 0, self.canvas.width, self.canvas.height);
                        
                        if (detection && detection.score > 0.96) {
                            resizedDetections = faceApi.resizeResults([detection], displaySize);
                            faceApi.draw.drawDetections(self.canvas, resizedDetections);
                        }
                        if (self.state === STATE_ENUM.DETECTING || self.state === STATE_ENUM.DETECTING_SUCCESS || self.state === STATE_ENUM.DETECTING_FAIL) {
                            if (detection && detection.score > 0.96) {
                                self.setInfo(STATE_ENUM.DETECTING_SUCCESS);
                            } else {
                                self.setInfo(STATE_ENUM.DETECTING_FAIL);
                            }
                        }
                        
                    });
                },
                registerWithFace: function registerWithFace() {
                    this.faceDetected = this.getImageFromVideo();
                    this.setInfo(STATE_ENUM.IDENTIFIYNG);
                    this.checkFaceWithLuxand();
                },
                checkFaceWithLuxand: function checkFaceWithLuxand() {
                    const self = this;
                    var settings = {
                        "async": true,
                        "crossDomain": true,
                        "dataType": "json",
                        "url": this.config.luxandApiUrl,
                        "method": "POST",
                        "headers": {
                            "token": this.config.luxandToken
                        },
                        "data": {"photo": this.faceDetected }
                    }
                    return jQuery.ajax(settings)
                        .done(function(response) {
                            self.identifiedPerson = self.handleResponseApi(response);
                            if (self.identifiedPerson) {
                                self.setInfo(STATE_ENUM.REGISTERING);
                                self.accessRegister();
                            } else {
                                self.setInfo(STATE_ENUM.IDENTIFIYNG_FAIL);
                            }
                        })
                        .fail(function () {
                            self.setInfo(STATE_ENUM.IDENTIFIYNG_FAIL);
                        })
                },
                accessRegister: function accessRegister() {
                    const settings = {
                        "async": true,
                        "dataType": "json",
                        "url": this.config.registerUrl,
                        "method": "POST",
                        "data": { "customer_id": this.identifiedPerson.id }
                    }
                    const self = this;
                    return jQuery.ajax(settings)
                        .done(function (response) {
                            if (response.success) {
                                self.setInfo(STATE_ENUM.REGISTERING_SUCCESS);
                            } else {
                                self.setInfo(STATE_ENUM.REGISTERING_FAIL, response.msg);
                            }
                        })
                        .fail(function () {
                            self.setInfo(STATE_ENUM.REGISTERING_FAIL);
                        })
                },
                setInfo: function setInfo (state, msgAlt) {
                    var showSpinner;
                    var style;
                    var msg;
                    switch (state) {
                        case STATE_ENUM.LOADING_LIBS: {
                            msg = 'Cargando....'; 
                            showSpinner = true;
                            style = 'warning'
                            break;
                        }
                        case STATE_ENUM.DETECTING: {
                            msg = 'Detectando'; 
                            showSpinner = true;
                            style = 'warning'
                            break;
                        }
                        case STATE_ENUM.DETECTING_SUCCESS: {
                            msg = 'Detectando'; 
                            showSpinner = true;
                            style = 'warning'
                            break; 

                        }
                        case STATE_ENUM.DETECTING_FAIL: {
                            msg = 'Detectando'; 
                            showSpinner = true;
                            style = 'warning'
                            break; 

                        }
                        case STATE_ENUM.IDENTIFIYNG: {
                            msg = 'Identificando'; 
                            showSpinner = true;
                            style = 'warning'
                            break; 
                        }
                        case STATE_ENUM.IDENTIFIYNG_SUCCESS: {
                            msg = 'Identificando'; 
                            showSpinner = true;
                            style = 'warning'
                            break; 
                        }
                        case STATE_ENUM.IDENTIFIYNG_FAIL: {
                            msg = 'No se pudo identificar'; 
                            showSpinner = false;
                            style = 'danger'
                            break; 
                        }
                        case STATE_ENUM.REGISTERING: {
                            msg = 'Registrando el acceso'; 
                            showSpinner = true;
                            style = 'warning'
                            break; 
                        }
                        case STATE_ENUM.REGISTERING_SUCCESS: {
                            msg = 'Acceso registrado exitosamente'; 
                            showSpinner = false;
                            style = 'success'
                            break; 
                            
                        }
                        case STATE_ENUM.REGISTERING_FAIL: {
                            msg = msgAlt || 'No se pudo registrar el acceso, intente nuevamente'; 
                            showSpinner = false;
                            style = 'danger'
                            break; 
                        }
                        case STATE_ENUM.INVALID_CI: {
                            msg = msgAlt || 'Ingrese una cedula valida.'; 
                            showSpinner = false;
                            style = 'danger'
                            break; 
                        }
                    }
                    this.state = state;
                    this.info = Object.assign({}, {
                        msg: msg, type: style, showSpinner: showSpinner
                    })            
                },
                isValidCi: function isValidCi() {
                    if (!this.ci) {
                        return false;
                    }
                    if (isNaN(this.ci)) {
                        return false;
                    }
                    try {
                        return !!(parseInt(this.ci, 10) && parseInt(this.ci, 10) > 100);
                    } catch (e) {
                       console.log("invalid ci");     
                    }   
                    return false;
                },
                registerWithCi: function registerWithCi () {
                    this.resetValues();
                    const settings = {
                        "async": true,
                        "dataType": "json",
                        "url": this.config.registerUrl,
                        "method": "POST",
                        "data": { "ci": this.ci }
                    }
                    const self = this;
                   
                    if (!self.isValidCi()) {
                        self.setInfo(STATE_ENUM.INVALID_CI, 'Ingrerse una cedula valida.');
                        return null;
                    } else {
                        self.identifiedPerson = {};
                        self.setInfo(STATE_ENUM.REGISTERING);
                        return jQuery.ajax(settings)
                            .done(function (response) {
                                if (response && response.success) {
                                    self.setInfo(STATE_ENUM.REGISTERING_SUCCESS);
                                } else {
                                    self.setInfo(STATE_ENUM.REGISTERING_FAIL, response.msg);
                                }
                            })
                            .fail(function () {
                                self.setInfo(STATE_ENUM.REGISTERING_FAIL);
                            })
                    }
                    
                },
                resetValues: function resetValues() {
                    this.setInfo(STATE_ENUM.DETECTING);
                    this.faceDetected = null;
                    this.identifiedPerson = null;
                    return null;
                },
                getImageFromVideo: function getImageFromVideo() {
                    const canvas = document.createElement('canvas');
                    canvas.width = this.video.videoWidth;
                    canvas.height = this.video.videoHeight;
                    canvas.getContext('2d')
                        .drawImage(this.video, 0, 0, canvas.width, canvas.height);
                    return canvas.toDataURL().replace('data:image/png;base64,', '');
                },
                handleResponseApi: function handleResponseApi(response) {
                    var personArray;
                    var person;
                    try {
                        if (Array.isArray(response)) {
                            personArray = response.filter(person => person.probability > 0.9);
                            if (personArray) {
                                person = personArray && personArray[0] && JSON.parse(personArray[0].name);
                                person.image64 = this.faceDetected;
                                this.setInfo(STATE_ENUM.IDENTIFIYNG_SUCCESS);
                                return person;
                            }
                        }
                    } catch (e) {
                        console.log(e);
                    }
                    return false;
                }
                
            }
         });
    };

})
